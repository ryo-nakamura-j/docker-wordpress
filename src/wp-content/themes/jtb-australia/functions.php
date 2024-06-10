<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

require_once("components/tp-wordpress-helper.php");

/* Add Latest Jquery */
if (!is_admin()) add_action("wp_enqueue_scripts", "my_jquery_enqueue", 11);
function my_jquery_enqueue() {
   wp_deregister_script('jquery');
   wp_register_script('jquery', get_template_directory_uri() . '/js/jQuery.min.js', false, null);
   wp_enqueue_script('jquery');
}

add_action('wp_enqueue_scripts', 'tourplan_enqueue_scripts');

function tourplan_fetch_scripts( $name, $dependency, $folder = '/js/tourplan/' ) {
  $handleName = 'tourplan-' . $name;
  $fileUrl = get_template_directory_uri() . $folder . $name . '.js';
  wp_register_script($handleName, $fileUrl, $dependency, null, true );
  // Fetch all scripts include those not needed for the page for now. There are room for optimizations.
  wp_enqueue_script($handleName);
}

function tourplan_enqueue_scripts() {
  // Library scripts
  wp_register_script('vue', get_template_directory_uri() . '/js/vue.js', false, null, true);
  wp_register_script('vee-validate', get_template_directory_uri() . '/js/vee-validate.js', false, null, true);
  wp_register_script('vue-scroll-magnet', get_template_directory_uri() . '/js/vue-scroll-magnet.js', false, null, true);
  wp_register_script('vue2-collapse', get_template_directory_uri() . '/js/vue2-collapse.js', false, null, true);
  wp_register_script('promise-polyfill', get_template_directory_uri() . '/js/promise-polyfill.min.js', false, null, true);
  wp_register_script('lodash', get_template_directory_uri() . '/js/lodash.js', false, null, true);
  wp_register_script('moment', get_template_directory_uri() . '/js/moment.js', false, null, true);
  wp_register_script('handlebars', "https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/3.0.3/handlebars.js", false, null, true);
  wp_register_script('bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), null, true);
  wp_register_script('modernizr', get_template_directory_uri() . '/js/modernizr.js', false, null, true);
  wp_register_script('bootstrap-datepicker', get_template_directory_uri() . '/js/bootstrap-datepicker.min.js', false, null, true);
  wp_register_script('bootstrap-multiselect', get_template_directory_uri() . '/js/bootstrap-multiselect.js', false, null, true);
  wp_register_script('pikaday', get_template_directory_uri() . '/js/pikaday.js', false, null, true);
  wp_register_script('intersection-observer', get_template_directory_uri() . '/js/intersection-observer.js', false, null, true);
  wp_register_script('pikaday-responsive', get_template_directory_uri() . '/js/pikaday-responsive.js', array('pikaday'), null, true);
  wp_register_script('jquery-ui', get_template_directory_uri() . '/js/jquery-ui.min.js', array('jquery'), null, true);
  wp_register_script('jquery-cookie', get_template_directory_uri() . '/js/js.cookie-2.2.0.min.js', array('jquery'), null, true );
  wp_register_script('bootstrap-datetimepicker', get_template_directory_uri() . '/js/bootstrap-datetimepicker.min.js', array('jquery','bootstrap','moment'), null, true);
  // Tourplan scripts
  $defaultDpd = array('tourplan-libs', 'tourplan-common-control', 'tourplan-retail-utilities');
  tourplan_fetch_scripts('libs', array('jquery', 'lodash', 'moment', 'handlebars', 'bootstrap', 'modernizr', 'bootstrap-datepicker', 'bootstrap-multiselect', 'pikaday-responsive', 'jquery-cookie', 'jquery-ui', 'bootstrap-datetimepicker','vue','vee-validate','vue2-collapse','promise-polyfill','vue-scroll-magnet','intersection-observer') );
  // Common Tourplan scripts
  tourplan_fetch_scripts('common-control', false);
  tourplan_fetch_scripts('retail-utilities', false);
  // Vue components
  
  tourplan_fetch_scripts('product-search-results', $defaultDpd, "/components/vue/");
  tourplan_fetch_scripts('product-search-panel', $defaultDpd, "/components/vue/");
  tourplan_fetch_scripts('accom-detail-panel', $defaultDpd, "/components/vue/");
  tourplan_fetch_scripts('day-tours-detail-panel', $defaultDpd, "/components/vue/");
  tourplan_fetch_scripts('booking-summery-panel', $defaultDpd, "/components/vue/");
  tourplan_fetch_scripts('tp-image', $defaultDpd, "/components/vue/");
  tourplan_fetch_scripts('tp-arrangement-input', $defaultDpd, "/components/vue/");
  tourplan_fetch_scripts('tp-group', $defaultDpd, "/components/vue/");
  tourplan_fetch_scripts('tp-scroll-magnet', $defaultDpd, "/components/vue/");
  tourplan_fetch_scripts('tp-slides', $defaultDpd, "/components/vue/");
  tourplan_fetch_scripts('tp-product-preview', $defaultDpd, "/components/vue/");
  tourplan_fetch_scripts('tp-pikaday-responsive', $defaultDpd, "/components/vue/");
  tourplan_fetch_scripts('tp-autocomplete', $defaultDpd, "/components/vue/");
  tourplan_fetch_scripts('tp-error', $defaultDpd, "/components/vue/");
  tourplan_fetch_scripts('v-lazy-image', $defaultDpd, "/components/vue/");
  // Basic Tourplan scripts
  tourplan_fetch_scripts('templates-helper', $defaultDpd);
  tourplan_fetch_scripts('amenity-filter', $defaultDpd);
  tourplan_fetch_scripts('availability', $defaultDpd);
  tourplan_fetch_scripts('date-control', $defaultDpd);
  tourplan_fetch_scripts('extension-functions', $defaultDpd);
  tourplan_fetch_scripts('product', $defaultDpd);
  tourplan_fetch_scripts('retail-interface', $defaultDpd);
  tourplan_fetch_scripts('non-accommodation-control', $defaultDpd);
  tourplan_fetch_scripts('serviceline', $defaultDpd);
  tourplan_fetch_scripts('supplier', $defaultDpd);
  tourplan_fetch_scripts('validation-helper-ctrl', $defaultDpd);
  tourplan_fetch_scripts('arrangement-panel-ctrl', $defaultDpd);
  // Vue mixin items
  tourplan_fetch_scripts('tp-cached-data', $defaultDpd, "/components/vue/");
  tourplan_fetch_scripts('tp-field-retain-mixin', $defaultDpd, "/components/vue/");
  // Vue components
  tourplan_fetch_scripts('tp-safe-text-input', $defaultDpd + array('tourplan-tp-field-retain-mixin'), "/components/vue/");
  tourplan_fetch_scripts('non-accom-product-panel', $defaultDpd, "/components/vue/");
  tourplan_fetch_scripts('serviceline-input-panel', $defaultDpd, "/components/vue/");
  // High-level tourplans cripts
  tourplan_fetch_scripts('arrangement-panel-ctrl', $defaultDpd + array( 'tourplan-validatioin-helper-ctrl'));
  tourplan_fetch_scripts('cart', $defaultDpd + array( 'tourplan-serviceline'));
  tourplan_fetch_scripts('multi-product-controller', $defaultDpd + array('tourplan-product-controller', 'tourplan-product'));
  tourplan_fetch_scripts('non-accommodation-control-group', $defaultDpd + array('tourplan-non-accommodation-control', 'tourplan-product'));
  tourplan_fetch_scripts('product-controller', $defaultDpd + array('tourplan-product', 'tourplan-supplier' ));
  tourplan_fetch_scripts('product-search-control', $defaultDpd + array('tourplan-amenity-filter', 'tourplan-availability', 'tourplan-date-control', 'tourplan-supplier' ) );

  // Tourplan main script
  wp_register_script('tourplan', get_template_directory_uri() . '/js/tourplan.js', $defaultDpd + array(
    'tourplan-cart', 'tourplan-retail-interface', 'templates-helper'), null, true);

  wp_enqueue_script('tourplan');
}


/* Add Menus */
function register_my_menus() {
  register_nav_menus(
    array(
      'main-menu' => __( 'Main Menu' ),
      'mobile-main-menu' => __( 'Mobile Main Menu' ),
      'secondary-menu' => __( 'Secondary Menu' ),
      'footer-menu' => __( 'Footer Menu' ),
    )
  );
}
add_action( 'init', 'register_my_menus' );

add_shortcode('tp-loading-image', 'tp_loading_image_shortcode');
function tp_loading_image_shortcode($atts, $content)
{
  return "<img src='" . get_bloginfo('template_directory') . "/images/loading.gif' id='" . $atts["id"] . "' class='" . $atts["class"] . "' />";
}


/**
 * Register widget area.
 *
 * @since Twenty Fifteen 1.0
 *
 * @link https://codex.wordpress.org/Function_Reference/register_sidebar
 */
function twentyfifteen_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Widget Area', 'twentyfifteen' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Add widgets here to appear in your sidebar.', 'twentyfifteen' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'twentyfifteen_widgets_init' );

function echoVar($var) {
  echo "<pre>" . var_export($var, true) . "</pre>";
}

// Register Custom Post Type
function custom_post_type() {

  $labels = array(
    'name'                => _x( 'Japan Rail Pass', 'Post Type General Name', 'text_domain' ),
    'singular_name'       => _x( 'Pass', 'Post Type Singular Name', 'text_domain' ),
    'menu_name'           => __( 'Regional Rail Pass', 'text_domain' ),
    'name_admin_bar'      => __( 'Post Type', 'text_domain' ),
    'parent_item_colon'   => __( 'Parent Pass:', 'text_domain' ),
    'all_items'           => __( 'All Passes', 'text_domain' ),
    'add_new_item'        => __( 'Add New Pass', 'text_domain' ),
    'add_new'             => __( 'Add New', 'text_domain' ),
    'new_item'            => __( 'New Pass', 'text_domain' ),
    'edit_item'           => __( 'Edit Pass', 'text_domain' ),
    'update_item'         => __( 'Update Pass', 'text_domain' ),
    'view_item'           => __( 'View Pass', 'text_domain' ),
    'search_items'        => __( 'Search Passes', 'text_domain' ),
    'not_found'           => __( 'Not found', 'text_domain' ),
    'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
  );
  $args = array(
    'label'               => __( 'japan_rail_pass', 'text_domain' ),
    'description'         => __( 'Pass Type Description', 'text_domain' ),
    'labels'              => $labels,
    'hierarchical'        => false,
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'menu_position'       => 5,
    'show_in_admin_bar'   => true,
    'show_in_nav_menus'   => true,
    'can_export'          => true,
    'has_archive'         => true,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'capability_type'     => 'page',
    'supports'            => array( 'title', 'editor', 'author', 'custom-fields', 'page-attributes' )
  );
  register_post_type( 'japan_rail_pass', $args );

  $labels = array(
    'name'                => _x( 'National Rail Pass', 'Post Type General Name', 'text_domain' ),
    'singular_name'       => _x( 'Pass', 'Post Type Singular Name', 'text_domain' ),
    'menu_name'           => __( 'National Rail Pass', 'text_domain' ),
    'name_admin_bar'      => __( 'Post Type', 'text_domain' ),
    'parent_item_colon'   => __( 'Parent Pass:', 'text_domain' ),
    'all_items'           => __( 'All Passes', 'text_domain' ),
    'add_new_item'        => __( 'Add New Pass', 'text_domain' ),
    'add_new'             => __( 'Add New', 'text_domain' ),
    'new_item'            => __( 'New Pass', 'text_domain' ),
    'edit_item'           => __( 'Edit Pass', 'text_domain' ),
    'update_item'         => __( 'Update Pass', 'text_domain' ),
    'view_item'           => __( 'View Pass', 'text_domain' ),
    'search_items'        => __( 'Search Passes', 'text_domain' ),
    'not_found'           => __( 'Not found', 'text_domain' ),
    'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' )
  );
  $args = array(
    'label'               => __( 'national_rail_pass', 'text_domain' ),
    'description'         => __( 'Pass Type Description', 'text_domain' ),
    'labels'              => $labels,
    'hierarchical'        => false,
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'menu_position'       => 5,
    'show_in_admin_bar'   => true,
    'show_in_nav_menus'   => true,
    'can_export'          => true,
    'has_archive'         => true,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'capability_type'     => 'post',
    'supports'            => array( 'title', 'editor', 'author', 'custom-fields', 'page-attributes' ),
    'rewrite'            => array( 'slug' => '/jr-rail-pass', 'with_front' => false)
  );
  register_post_type( 'national_rail_pass', $args );
}

// Hook into the 'init' action
//add_action( 'init', 'custom_post_type', 0 );



//add_filter('template_include', 'set_tourplan_templates');
function set_tourplan_templates($template_path) {
  $tp_post_types = array(
    'tp_accom_product',
    'tp_rail_pass_product',
    'tp_ticket_product'
    );
  if (in_array(get_post_type(), $tp_post_types)) {
    if (is_single()) {
      return get_template_directory() . '/tp_plugin_template.php';
    }
  }
  return $template_path;
}


function name_value_pair($name, $value) {
  return array('name' => $name, 'value' => $value);
}



add_filter('pll_get_post_types', 'my_pll_get_post_types');
function my_pll_get_post_types($types) {
  return array_merge($types, array('japan_rail_pass' => 'japan_rail_pass'));
}



/*
Plugin Name: Disable XML-RPC
*/
add_filter( 'xmlrpc_enabled', '__return_false' );


function admin_default_page() {
  return '/';
}

/* add_filter('login_redirect', 'admin_default_page'); */


function image_if_exists($image_object, $classes) {
  if (!empty($image_object['url'])) {
    echo "<img src=\"" . $image_object['url'] . "\" + class=\"" . $classes . "\" />"; 
  }
}



add_filter('acf/load_field/name=template_source', 'acf_load_template_choices');
function acf_load_template_choices($field)
{
  $field['choices'] = array();

  $path = get_template_directory();

  $files = scandir($path . DIRECTORY_SEPARATOR . "templates");
  $field['choices']['None'] = '';
  foreach ($files as $f) {
    if ($f != '.' && $f != '..' && strpos($f, '.php')) {
      $field['choices'][$f] = $f;
    }
  }
  return $field;
}

add_filter('acf/load_field/name=customer_field', 'acf_load_customer_fields');
function acf_load_customer_fields($field)
{
  $field['choices'] = array();
  $field['choices']['title'] = "Title";
  $field['choices']['firstname'] = "First Name";
  $field['choices']['middlename'] = "Middle Name";
  $field['choices']['lastname'] = "Last Name";
  $field['choices']['email'] = "Email";
  $field['choices']['email_confirm'] = "Email Confirm";
  $field['choices']['phone'] = "Phone";
  $field['choices']['address1'] = "Address 1";
  $field['choices']['address2'] = "Address 2";
  $field['choices']['address3'] = "Address 3";
  $field['choices']['address4'] = "Address 4";
  $field['choices']['address5'] = "Address 5";
  $field['choices']['postCode'] = "Post Code";
  $field['choices']['country'] = "Country";
  $field['choices']['branch'] = "Tourplan Branch";
  $field['choices']['address6'] = "Address 6";
  $field['choices']['address7'] = "Address 7";
  $field['choices']['text'] = "Text";

  return $field;
}

add_filter('acf/load_field/name=delivery_field', 'acf_load_delivery_fields');
function acf_load_delivery_fields($field)
{
  $field['choices'] = array();
  $field['choices']['deliveryAddress1'] = "Address 1";
  $field['choices']['deliveryAddress2'] = "Address 2";
  $field['choices']['deliveryAddress3'] = "Address 3";
  $field['choices']['deliveryAddress4'] = "Address 4";
  $field['choices']['deliveryAddress5'] = "Address 5";
  $field['choices']['deliveryPostCode'] = "Post Code";
  $field['choices']['deliveryCountry'] = "Country";
  $field['choices']['deliveryAddress6'] = "Address 6";
  $field['choices']['deliveryAddress7'] = "Address 7";
  $field['choices']['text'] = "Text";

  return $field;
}

function footer_widgets_init() {
  register_sidebar(array(
    "name" => "Footer 1 (far left)",
    "id"    => "footer_1",
    "before_widget" => "<div class='row'><div class='col-xs-12'>",
    "after_widget"  => "</div></div>",
    "before_title" => "<h4 class='footer_title'>",
    "after_title" => "</h4>"
  ));
  register_sidebar(array(
    "name" => "Footer 2 (centre left)",
    "id"    => "footer_2",
    "before_widget" => "<div class='row'><div class='col-xs-12'>",
    "after_widget"  => "</div></div>",
    "before_title" => "<h4 class='footer_title'>",
    "after_title" => "</h4>"
  ));
  register_sidebar(array(
    "name" => "Footer 3 (centre right)",
    "id"    => "footer_3",
    "before_widget" => "<div class='row'><div class='col-xs-12'>",
    "after_widget"  => "</div></div>",
    "before_title" => "<h4 class='footer_title'>",
    "after_title" => "</h4>"
  ));
  register_sidebar(array(
    "name" => "Footer 4 (far right)",
    "id"    => "footer_4",
    "before_widget" => "<div class='row'><div class='col-xs-12'>",
    "after_widget"  => "</div></div>",
    "before_title" => "<h4 class='footer_title'>",
    "after_title" => "</h4>"
  ));
}

add_action('widgets_init', 'footer_widgets_init');


class JTBTourplan_Customise {

  public static function register($wp_customize) {

    $wp_customize->add_setting('site_country',
      array(
        'default' => '',
        'type' =>'theme_mod'
      )
    );

    $wp_customize->add_control('site_country_control',
      array(
        'label' => __('Site Country', 'JTBTourplan'),
        'section' => 'title_tagline',
        'settings' => 'site_country'
      )
    );

    $wp_customize->add_setting('header-tagline',
      array(
        'default' => '',
        'type' => 'theme_mod'
      )
    );

    $wp_customize->add_control('header-tagline-control',
      array(
        'label' => __('Header Tagline', 'JTBTourplan'),
        'section' => 'title_tagline',
        'settings' => 'header-tagline'
      )
    );

    $wp_customize->add_setting('footer-copyright', 
      array(
        'default' => '',
        'type' => 'theme_mod'
      )
    );

    $wp_customize->add_control('footer-copyright-control',
      array(
        'label' => __('Footer Copyright', 'JTBTourplan'),
        'section' => 'title_tagline',
        'settings' => 'footer-copyright'
      )
    );

    $wp_customize->add_section('JTBTourplan_maps',
      array(
        'title' => __('Footer Maps', 'JTBTourplan'),
        'description' => __('Customise the maps that appear in the footer. <br/><br>Follow the instructions <a href=\'https://support.google.com/maps/answer/3045850?hl=en\'>here</a> and insert the \'src\' attribute only', 'JTBTourplan')
      )
    );

    $wp_customize->add_setting('footer_map_1',
      array(
        'type' => 'theme_mod'
      )
    );

    $wp_customize->add_setting('footer_map_2',
      array(
        'type' => 'theme_mod'
      )
    );

    $wp_customize->add_setting('footer_map_3',
      array(
        'type' => 'theme_mod'
      )
    );

    $wp_customize->add_setting('footer_map_4',
      array(
        'type' => 'theme_mod'
      )
    );

    $wp_customize->add_control('footer_map_1_control',
      array(
        'label' => __('Map 1', 'JTBTourplan'),
        'section' => 'JTBTourplan_maps',
        'settings' => 'footer_map_1',
        'description' => __('This will appear on the top right of the footer', 'JTBTourplan')
      )
    );

    $wp_customize->add_control('footer_map_2_control',
      array(
        'label' => __('Map 2', 'JTBTourplan'),
        'section' => 'JTBTourplan_maps',
        'settings' => 'footer_map_2',
        'description' => __('This will appear on the top center of the footer', 'JTBTourplan')
      )
    );

    $wp_customize->add_control('footer_map_3_control',
      array(
        'label' => __('Map 3', 'JTBTourplan'),
        'section' => 'JTBTourplan_maps',
        'settings' => 'footer_map_3',
        'description' => __('This will appear on the bottom right of the footer', 'JTBTourplan')
      )
    );

    $wp_customize->add_control('footer_map_4_control',
      array(
        'label' => __('Map 4', 'JTBTourplan'),
        'section' => 'JTBTourplan_maps',
        'settings' => 'footer_map_4',
        'description' => __('This will appear on the bottom center of the footer', 'JTBTourplan')
      )
    );

  }
}

add_action('customize_register', array('JTBTourplan_Customise', 'register'));

class JTBTourplan_ThemeSettings {
  public function theme_settings_menu() {
    add_theme_page("Theme Settings", "Theme Settings", "edit_theme_options", "jtbtourplan-theme-options", array($this, "theme_settings_page"));
    add_action('admin_init', array($this, 'register_theme_settings'));
  }

  public function register_theme_settings() {
    // tp_log("Registering theme settings");
    register_setting('tp-theme-settings-group', 'tp_google_analytics', '');
    register_setting('tp-theme-settings-group', 'tp_body_start', '');
    register_setting('tp-theme-settings-group', 'tp_ptengine_tracking', '');

    register_setting('tp-theme-settings-group', 'tp_site_search_enabled', '');
    register_setting('tp-theme-settings-group', 'tp_site_search_footer_enabled', '');

    register_setting('tp-theme-settings-group', 'tp_enhanced_footer_enabled', '');
  }

  public function theme_settings_page(){ ?>
    <div class="wrap">
    <h1>JTB Tourplan Theme Settings</h1>
    <form method="post" action="options.php">
      <?php settings_fields('tp-theme-settings-group'); ?>
      <table class="form-table">
        <tr valign="top">
          <th scope="row">Google Analytics Code</th>
          <td><textarea name="tp_google_analytics"><?php echo get_option('tp_google_analytics'); ?></textarea></td>
        </tr>

        <tr valign="top">
          <th scope="row">Code at start of body</th>
          <td><textarea name="tp_body_start"><?php echo get_option('tp_body_start'); ?></textarea></td>
        </tr>

        <tr valign="top">
          <th scope="row">PTEngine Tracking Code</th>
          <td><textarea name="tp_ptengine_tracking"><?php echo get_option('tp_ptengine_tracking'); ?></textarea></td>
        </tr>

        <tr valign="top">
          <th scope="row">Site Search Enabled</th>
          <td><input type="checkbox"
                    name="tp_site_search_enabled"
                    <?php echo get_option('tp_site_search_enabled') === 'true' ? 'checked="checked"' : ''; ?>
                    value="<?php echo get_option('tp_site_search_enabled') == 'true' ? 'true' : 'false'; ?>"
                    onclick="javascript:this.value=(this.value=='true'?'false':'true');" /></td>
        </tr>

        <tr valign="top">
          <th scope="row">Footer Site Search Enabled <br /> Note: This overrides Footer Map 2</th>
          <td><input type="checkbox"
                    name="tp_site_search_footer_enabled"
                    <?php echo get_option('tp_site_search_footer_enabled') === 'true' ? 'checked="checked"' : ''; ?>
                    value="<?php echo get_option('tp_site_search_footer_enabled') == 'true' ? 'true' : 'false'; ?>"
                    onclick="javascript:this.value=(this.value=='true'?'false':'true');" /></td>
        </tr>
        <tr valign="top">
          <th scopw="row">Enhanced Footer Enabled</th>
          <td><input type="checkbox"
                    name="tp_enhanced_footer_enabled"
                    <?php echo get_option('tp_enhanced_footer_enabled') === 'true' ? 'checked="checked"' : ''; ?>
                    value="<?php echo get_option('tp_enhanced_footer_enabled') == 'true' ? 'true' : 'false'; ?>"
                    onclick="javascript:this.value=(this.value=='true'?'false':'true');" /></td>
        </tr>
        <?php
        echo get_submit_button();
        ?>
    </form>
    </div>


  <?php
  }
}

$themeSettings = new JTBTourplan_ThemeSettings;
add_action('admin_menu', array($themeSettings, 'theme_settings_menu'));

require_once( 'components/tp-acf.php');
$tpAcf = new TpAcf();
$tpAcf->init();

require_once(dirname(__FILE__) . '/nav-walker.php');
include_once ( dirname(__FILE__) . '/widgets.php');

?>