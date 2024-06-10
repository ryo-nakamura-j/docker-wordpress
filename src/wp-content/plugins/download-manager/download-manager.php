<?php
/*
Plugin Name: Download Manager
Plugin URI: https://www.wpdownloadmanager.com/purchases/
Description: Manage, Protect and Track file downloads, and sell digital products from your WordPress site. A complete digital asset management solution.
Author: W3 Eden, Inc.
Author URI: https://www.wpdownloadmanager.com/
Version: 3.2.73
Text Domain: download-manager
Domain Path: /languages
*/

namespace WPDM;

use WPDM\__\Apply;
use WPDM\__\Crypt;
use WPDM\__\DownloadStats;
use WPDM\__\Email;
use WPDM\__\FileSystem;
use WPDM\__\CronJobs;
use WPDM\__\Installer;
use WPDM\__\Messages;
use WPDM\__\Session;
use WPDM\__\Settings;
use WPDM\__\Template;
use WPDM\__\UI;
use WPDM\__\Updater;
use WPDM\__\UserAgent;
use WPDM\Admin\AdminController;
use WPDM\AssetManager\AssetManager;
use WPDM\Category\Category;
use WPDM\Category\CategoryController;
use WPDM\Admin\PackageTemplate;
use WPDM\MediaLibrary\MediaAccessControl;
use WPDM\MediaLibrary\MediaHandler;
use WPDM\Package\Package;
use WPDM\User\UserController;
use WPDM\Widgets\WidgetController;

global $WPDM;

define('WPDM_VERSION','3.2.73');

define('WPDM_TEXT_DOMAIN','download-manager');

$upload_dir = wp_upload_dir();
$upload_base_url = $upload_dir['baseurl'];
$upload_dir = $upload_dir['basedir'];

/**
 * Define plugin admin access capability.
 */
if(!defined('WPDM_ADMIN_CAP'))
    define('WPDM_ADMIN_CAP','manage_options');

/**
 * Define plugin menu access capability
 */
if(!defined('WPDM_MENU_ACCESS_CAP'))
    define('WPDM_MENU_ACCESS_CAP','manage_options');

/**
 * Plugin base dir
 */
define('WPDM_BASE_DIR',dirname(__FILE__).'/');
define('WPDM_SRC_DIR',dirname(__FILE__).'/src/');

define('WPDM_BASE_URL',plugins_url('/download-manager/'));
define('WPDM_ASSET_URL',plugins_url('/download-manager/assets/'));
define('WPDM_CSS_URL',plugins_url('/download-manager/assets/css/'));
define('WPDM_JS_URL',plugins_url('/download-manager/assets/js/'));

if(!defined('UPLOAD_DIR'))
    define('UPLOAD_DIR',$upload_dir.'/download-manager-files/');


if(!defined('WPDM_CACHE_DIR')) {
    define('WPDM_CACHE_DIR', $upload_dir . '/wpdm-cache/');
    define('WPDM_CACHE_URL', $upload_base_url . '/wpdm-cache/');
}

if(!defined('WPDM_TPL_FALLBACK'))
    define('WPDM_TPL_FALLBACK', dirname(__FILE__) . '/tpls/');

if(!defined('WPDM_TPL_DIR')) {
        define('WPDM_TPL_DIR', dirname(__FILE__) . '/tpls/');
}

if(!defined('UPLOAD_BASE'))
    define('UPLOAD_BASE',$upload_dir);

if(!defined('WPDM_USE_GLOBAL'))
    define('WPDM_USE_GLOBAL', 'WPDM_USE_GLOBAL');


if(!defined('WPDM_FONTAWESOME_URL'))
    define('WPDM_FONTAWESOME_URL', WPDM_BASE_URL.'assets/fontawesome/css/all.min.css');

if(!defined('NONCE_KEY') || !defined('NONCE_SALT')){
    //To avoid warning when not defined
    define('NONCE_KEY',       'Bm|_Ek@F|HdkA7)=alSJg5_<z-j-JmhK<l&*.d<J+/71?&7pL~XBXnF4jKz>{Apx');
    define('NONCE_SALT',       'XffybIqfklKjegGdRp7EU4kprZX00NESOE8olZ2BZ8+BQTw3bXXSbzeGssgZ');
    /**
     * Generate WordPress Security Keys and Salts from https://api.wordpress.org/secret-key/1.1/salt/ and place them in your wp-config.php
     */
}

if(!defined('WPDM_PUB_NONCE'))
    define('WPDM_PUB_NONCE',        'o($Vb ^[@EH83o2gb=,lt1JtBY]%i91|xu+]jnW9{*nMK@_z-AWwsyKEVx)/|p,P');
if(!defined('WPDM_PRI_NONCE'))
    define('WPDM_PRI_NONCE',        '.r&`|]S1GEAdm^hTA^XmE8vU3F^=K+)419alVN=EbDQ Z-pfl/nd-12^I&oRfDC]');

if(!defined('WPDM_CRON_KEY'))
	define('WPDM_CRON_KEY',        'mKNVRCdbJr1DiedHE18N');

@ini_set('upload_tmp_dir',WPDM_CACHE_DIR);


final class WordPressDownloadManager{

    public $session;
    public $user;
    public $apply;
    public $fileSystem;
    public $admin;
    public $package;
    public $category;
    public $categories;
    public $assetManager;
    public $asset;
    public $shortCode;
    public $template;
    public $packageTemplate;
    public $setting;
    public $email;
    public $crypt;
    public $downloadHistory;
    public $bsversion = '';
    public $userAgent;
    public $message;
	public $updater;
    public $ui;
    public $wpdm_urls;

    private static $wpdm_instance = null;

    public static function instance(){
        if ( is_null( self::$wpdm_instance ) ) {
            self::$wpdm_instance = new self();
        }
        return self::$wpdm_instance;
    }

    function __construct(){

        register_activation_hook(__FILE__, array($this, 'install'));

        add_action( 'upgrader_process_complete', array($this, 'update'), 10, 2);


        $this->bsversion = get_option('__wpdm_bsversion', '');

        add_action( 'init', array($this, 'registerPostTypeTaxonomy'), 1 );
        add_action( 'init', array($this, 'registerScripts'), 1 );

        add_action( 'plugins_loaded', array($this, 'loadTextdomain') );
        add_action( 'wp_enqueue_scripts', array($this, 'enqueueScripts') );

        add_action( 'wp_footer', array($this, 'wpFooter') );

        $this->autoLoadClasses();

        $this->wpdm_urls = array(
            'home' => esc_url_raw(home_url('/')),
            'site' => esc_url_raw(site_url('/')),
            'ajax' => esc_url_raw(admin_url('/admin-ajax.php'))
        );

        $this->session          = new Session();

        include_once(dirname(__FILE__) . "/src/wpdm-strings.php");
        include_once(dirname(__FILE__) . "/src/wpdm-functions.php");
	    include_once(dirname(__FILE__)."/src/wpdm-core.php");

        $this->user             = UserController::getInstance();
        $this->apply            = new Apply();
        $this->admin            = new AdminController();
                                  new MediaHandler();
                                  new MediaAccessControl();
        $this->package          = new Package();
        $this->category         = new Category();
        $this->categories       = new CategoryController();
        $this->setting          = new Settings();
        $this->fileSystem       = new FileSystem();
        $this->template         = new Template();
        $this->packageTemplate  = new PackageTemplate();
        $this->crypt            = new Crypt();
        $this->downloadHistory  = new DownloadStats();
        $this->userAgent        = new UserAgent();
        $this->message          = new Messages();
        $this->updater          = new Updater();
        $this->ui               = new UI();
        $this->email            = new Email();

        CronJobs::getInstance();
        WidgetController::instance();

        if (!defined('WPDM_ASSET_MANAGER') || WPDM_ASSET_MANAGER === true) {
            AssetManager::getInstance();
        }

    }

    /**
     * @usage Install Plugin
     */
    function install(){

        Installer::init();

        $this->registerPostTypeTaxonomy();

        flush_rewrite_rules();
        self::createDir();

    }

    /**
     * Update plugin
     * @param $upgrader_object
     * @param $options
     */
    function update( $upgrader_object, $options ) {
        $current_plugin_path_name = plugin_basename( __FILE__ );
        if(!is_array($options)) return;
        if ($options['action'] == 'update' && $options['type'] == 'plugin' ){
            if(isset($options['plugins']) && is_array($options['plugins'])) {
                foreach ($options['plugins'] as $each_plugin) {
                    if ($each_plugin == $current_plugin_path_name) {
                        if (Installer::dbUpdateRequired()) {
                            Installer::updateDB();
                            return;
                        }
                        //flush_rewrite_rules(true);
                    }
                }
            }
        }
    }


    /**
     * @usage Load Plugin Text Domain
     */
    function loadTextdomain(){
        load_plugin_textdomain('download-manager', WP_PLUGIN_URL . "/download-manager/languages/", 'download-manager/languages/');
    }

    /**
     * @usage Register WPDM Post Type and Taxonomy
     */
    public function registerPostTypeTaxonomy()
    {
	    $labels = array(
		    'name' => __('Downloads','download-manager'),
		    'singular_name' => __('File','download-manager'),
		    'add_new' => __('Add New','download-manager'),
		    'add_new_item' => __('Add New File','download-manager'),
		    'edit_item' => __('Edit File','download-manager'),
		    'new_item' => __('New File','download-manager'),
		    'all_items' => __('All Files','download-manager'),
		    'view_item' => __('View File','download-manager'),
		    'search_items' => __('Search Files','download-manager'),
		    'not_found' => __('No File Found','download-manager'),
		    'not_found_in_trash' => __('No Files found in Trash','download-manager'),
		    'parent_item_colon' => '',
		    'menu_name' => __('Downloads','download-manager')

	    );

        $tslug = 'download';
        if(!strpos("_$tslug", "%"))
            $slug = sanitize_title($tslug);
        else
            $slug = $tslug;

        $args = array(
            'labels'                => $labels,
            'public'                => true,
            'publicly_queryable'    => get_option('__wpdm_publicly_queryable', 1),
            'show_ui'               => true,
            'show_in_menu'          => true,
            'show_in_rest'          => (int)get_option('__wpdm_gutenberg_editor', 0),
            'show_in_nav_menus'     => true,
            'query_var'             => true,
            'rewrite'               => array('slug' => $slug, 'with_front' => (bool)get_option('__wpdm_purl_with_front', false)), //get_option('__wpdm_purl_base','download')
            'capability_type'       => 'post',
            'has_archive'           => (get_option('__wpdm_has_archive', false)==false?false:sanitize_title(get_option('__wpdm_archive_page_slug', 'all-downloads'))),
            'hierarchical'          => false,
            //'menu_icon'             => 'dashicons-download',
            'exclude_from_search'   => (bool)get_option('__wpdm_exclude_from_search', false),
            'supports'              => array('title', 'editor', 'publicize', 'excerpt', 'custom-fields', 'thumbnail', 'comments','author'),
            'menu_icon' => 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 327.3 327.3"><defs><style>.cls-1{fill:#ffffff;}.cls-2,.cls-3{fill:#239cef;}.cls-3{opacity:0.58;}</style><linearGradient id="linear-gradient" x1="11.99" y1="216.58" x2="263.62" y2="129.92" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#3c80e4"/><stop offset="1" stop-color="#229df0"/></linearGradient></defs><title>wpdm logo v3</title><g id="Layer_2" data-name="Layer 2"><g id="download-manager-logo"><g id="wpdm_logo_v3" data-name="wpdm logo v3"><path id="arrow" class="cls-2" d="M149.87,180.29l-91.41-91A20,20,0,0,1,58.39,61L64,55.36a20,20,0,0,1,28.29-.07l71.52,71.18,71-71.7a20,20,0,0,1,28.29-.13l5.68,5.62a20,20,0,0,1,.14,28.29l-90.75,91.64A20,20,0,0,1,149.87,180.29Z"/><path id="circle" d="M186.66,202.9a32,32,0,0,1-45.29.16L97.58,159.48a78,78,0,1,0,132.49-.41Z" style="fill: #a7aaad;"/></g></g></g></svg>')

        );

        $wpdm_tags = !defined('WPDM_USE_POST_TAGS') || WPDM_USE_POST_TAGS === false;


        register_post_type('wpdmpro', $args);

        $labels = array(
            'name'                  => __( "Categories" , "download-manager" ),
            'singular_name'         => __( "Category" , "download-manager" ),
            'search_items'          => __( "Search Categories" , "download-manager" ),
            'all_items'             => __( "All Categories" , "download-manager" ),
            'parent_item'           => __( "Parent Category" , "download-manager" ),
            'parent_item_colon'     => __( "Parent Category:" , "download-manager" ),
            'edit_item'             => __( "Edit Category" , "download-manager" ),
            'update_item'           => __( "Update Category" , "download-manager" ),
            'add_new_item'          => __( "Add New Category" , "download-manager" ),
            'new_item_name'         => __( "New Category Name" , "download-manager" ),
            'menu_name'             => __( "Categories" , "download-manager" ),
        );

        $args = array(
            'hierarchical'          => true,
            'labels'                => $labels,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'show_in_rest'          => (bool)((int)get_option('__wpdm_gutenberg_editor', 0)),
            'query_var'             => true,
            'rewrite'               => array('slug' => 'download-category'),
        );

        register_taxonomy('wpdmcategory', array('wpdmpro'), $args);

        if($wpdm_tags) {
            $labels = array(
                'name' => __("Tags", "download-manager"),
                'singular_name' => __("Tag", "download-manager"),
                'search_items' => __("Search Document Tags", "download-manager"),
                'all_items' => __("All Tags", "download-manager"),
                'edit_item' => __("Edit Tag", "download-manager"),
                'update_item' => __("Update Tag", "download-manager"),
                'add_new_item' => __("Add New Tag", "download-manager"),
                'new_item_name' => __("New Tag Name", "download-manager"),
                'menu_name' => __("Tags", "download-manager"),
            );

            $args = array(
                'hierarchical' => false,
                'labels' => $labels,
                'show_ui' => true,
                'show_admin_column' => true,
                'query_var' => true,
                'rewrite' => array('slug' => sanitize_title(get_option('__wpdm_turl_base', 'document-tag'))),
            );

            register_taxonomy('wpdmtag', array('wpdmpro'), $args);

            //unregister_taxonomy_for_object_type('post_tag', 'wpdmpro');
        }

    }

    /**
     * @usage Create upload dir
     */
    public static function createDir()
    {
        if (!file_exists(UPLOAD_BASE)) {
            @mkdir(UPLOAD_BASE, 0755);
            @chmod(UPLOAD_BASE, 0755);
        }
        if(!file_exists(UPLOAD_DIR)) {
            @mkdir(UPLOAD_DIR, 0755);
            @chmod(UPLOAD_DIR, 0755);
        }

        if(!file_exists(WPDM_CACHE_DIR)) {
            @mkdir(WPDM_CACHE_DIR, 0755);
            @chmod(WPDM_CACHE_DIR, 0755);
        }

        $_upload_dir = wp_upload_dir();
        $_upload_dir = $_upload_dir['basedir'];
        $tags_dir = $_upload_dir.'/wpdm-custom-tags/';
        if(!file_exists($tags_dir)) {
            @mkdir($tags_dir, 0755);
            @chmod($tags_dir, 0755);
            FileSystem::blockHTTPAccess($tags_dir);
        }

        if(!file_exists($_upload_dir.'/wpdm-file-type-icons/')) {
            @mkdir($_upload_dir.'/wpdm-file-type-icons/', 0755);
            @chmod($_upload_dir.'/wpdm-file-type-icons/', 0755);
        }

        self::setHtaccess();

    }


    /**
     * @usage Protect Download Dir using .htaccess rules
     */
    public static function setHtaccess()
    {
        FileSystem::blockHTTPAccess(UPLOAD_DIR);
    }

    function registerScripts(){

        if(is_admin()) return;

        wp_register_style('wpdm-front-bootstrap', plugins_url('/download-manager/assets/bootstrap/css/bootstrap.min.css'));
        wp_register_style('wpdm-font-awesome', WPDM_FONTAWESOME_URL);
        wp_register_style('wpdm-front3', plugins_url('/assets/css/front3.css', __FILE__));
        wp_register_style('wpdm-front', plugins_url('/assets/css/front.css', __FILE__) , 99999999);

        wp_register_script('wpdm-poper', plugins_url('/assets/bootstrap/js/popper.min.js', __FILE__), array('jquery'));
        wp_register_script('wpdm-front-bootstrap', plugins_url('/assets/bootstrap/js/bootstrap.min.js', __FILE__), array('jquery'));
        wp_register_script('jquery-validate', plugins_url('/assets/js/jquery.validate.min.js', __FILE__), array('jquery'));

    }

    /**
     * @usage Enqueue all styles and scripts
     */
    function enqueueScripts()
    {
        global $post;

        if(is_admin()) return;

        wp_enqueue_script('wp-i18n');
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-form');

        //wp_register_style('font-awesome', WPDM_BASE_URL . 'assets/font-awesome/css/font-awesome.min.css');

        $wpdmss = maybe_unserialize(get_option('__wpdm_disable_scripts', array()));

        if (is_array($wpdmss) && !in_array('wpdm-font-awesome', $wpdmss)) {
            wp_enqueue_style('wpdm-font-awesome');
        }


        if (is_array($wpdmss) && !in_array('wpdm-bootstrap-css', $wpdmss)) {
            wp_enqueue_style('wpdm-front-bootstrap' );
        }

        if (is_array($wpdmss) && !in_array('wpdm-front', $wpdmss)) {
            wp_enqueue_style('wpdm-front' );
        }


        if (is_array($wpdmss) && !in_array('wpdm-bootstrap-js', $wpdmss)) {
            wp_enqueue_script('wpdm-poper');
            wp_enqueue_script('wpdm-front-bootstrap' );
        }

        wp_register_script('wpdm-frontjs', plugins_url('/assets/js/front.js', __FILE__), array('jquery'), WPDM_VERSION);

        $wpdm_js = array(
            'spinner' => '<i class="fas fa-sun fa-spin"></i>'
        );
        $wpdm_js = apply_filters("wpdm_js_vars", $wpdm_js);


        wp_localize_script('wpdm-frontjs', 'wpdm_url', $this->wpdm_urls);

        wp_localize_script('wpdm-frontjs', 'wpdm_js', $wpdm_js);

        wp_enqueue_script('wpdm-frontjs');

        if(is_object($post) && substr_count($post->post_content, "wpdm_all_packages")){
            wp_enqueue_script("datatable", plugins_url('/assets/js/jquery.dataTables.min.js', __FILE__));
            wp_enqueue_script("datatable-bs4", plugins_url('/assets/js/dataTables.bootstrap4.min.js', __FILE__));
            wp_enqueue_style("datatable-css", plugins_url('/assets/css/jquery.dataTables.min.css', __FILE__));
        }


    }


    /**
     * @usage insert code in wp footer
     */
    function wpFooter(){
        global $post;
        $post_content = is_object($post) && isset($post) ? $post->post_content : '';

        //Enable/disable view count
        $view_count = !defined('WPDM_VIEW_COUNT') || WPDM_VIEW_COUNT === true;

        if(get_option('__wpdm_modal_login', 0)
            && !has_shortcode($post_content, 'wpdm_user_dashboard')
            && !has_shortcode($post_content, 'wpdm_frontend')
            && !has_shortcode($post_content, 'wpdm_login_form')
            && !has_shortcode($post_content, 'wpdm_reg_form')
        )
                echo $this->user->login->modalForm();
            ?>
            <script>
                jQuery(function($){

                    <?php if(is_singular('wpdmpro') && $view_count){ ?>
                    setTimeout(function (){
                        $.post(wpdm_url.ajax, { action: 'wpdm_view_count', __wpdm_view_count:'<?php echo wp_create_nonce(NONCE_KEY) ?>', id: '<?= get_the_ID() ?>' });
                    }, 2000);
                    <?php } ?>

                });
            </script>
            <div id="fb-root"></div>
            <?php
    }

    /**
     * Class autoloader
     */
    function autoLoadClasses()
    {
        spl_autoload_register(function ($class) {

            $prefix = 'WPDM\\';
            $base_dir = __DIR__ . '/src/';

            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }

            $relative_class = substr($class, $len);
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

            if (file_exists($file)) {
                require $file;
            }
        });
    }

}

$WPDM = WordPressDownloadManager::instance();

