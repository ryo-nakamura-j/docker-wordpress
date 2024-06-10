<?php
/**
 * Plugin Name: TP Tabs Shortcodes JTB
 * Description: Adds a few shortcodes to allow for tabbed content.
 * Version: 1.0.2
 * Author: Phil Buchanan and BenjaminGibiec-edit
 * Author URI: http://philbuchanan.com
 */

# Make sure to not redeclare the class
if (!class_exists('Tabs_Shortcodes')) :


class Tabs_Shortcodes {

	static $add_script;
	static $tab_titles;
	
	function __construct() {
	
		$basename = plugin_basename(__FILE__);
		
		# Load text domain
		load_plugin_textdomain('tabs_shortcodes', false, dirname($basename) . '/languages/');
		
		# Register JavaScript
		add_action('wp_enqueue_scripts', array(__CLASS__, 'register_script'));
		
		# Add shortcodes
		add_shortcode('tabs', array(__CLASS__, 'tabs_shortcode'));
		add_shortcode('tab', array(__CLASS__, 'tab_shortcode'));
		
		# Print script in wp_footer
		add_action('wp_footer', array(__CLASS__, 'print_script'));
		
		# Add link to documentation
		add_filter("plugin_action_links_$basename", array(__CLASS__, 'add_documentation_link'));
		
		# Add activation notice (advising user to add CSS)
		register_activation_hook(__FILE__, array(__CLASS__, 'install'));
		add_action('admin_notices', array(__CLASS__, 'plugin_activation_notice'));
	
	}
	
	# Installation function
	static function install() {
	
		# Add notice option
		add_option('tabs_shortcodes_notice', 1, '', 'no');
	
	}
	
	# Add the activation notice
	static function plugin_activation_notice() {
	
		# Check for option before displaying notice
		if (get_option('tabs_shortcodes_notice')) {
		
			# We can now delete the option since the notice will be displayed
			delete_option('tabs_shortcodes_notice');
			
			# Generate notice
			$html = '<div class="updated"><p>';
			$html .= __('<strong>Important</strong>: Make sure to <a href="http://wordpress.org/plugins/tabs-shortcodes/other_notes/">add some CSS</a> to your themes stylesheet to ensure the tabs shortcodes display properly.', 'tabs_shortcodes');
			$html .= '</p></div>';
			
			# Display notice
			echo $html;
		
		}
	
	}
	
	# Registers the minified tabs JavaScript file
	static function register_script() {
	
		$min = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';
		wp_register_script('tabs-shortcodes-script', plugins_url('tabs' . $min . '.js', __FILE__), array(), '1.0.1', true);
	
	}
	
	# Prints the minified tabs JavaScript file in the footer
	static function print_script() {
	
		# Check to see if shortcodes are used on page
		if (!self::$add_script) return;
		
		wp_enqueue_script('tabs-shortcodes-script');
	
	}
	
	# Tabs wrapper shortcode
	static function tabs_shortcode($atts, $content = null) {
	
	//this adds a download button to 3 pages - hard coded because theres no easy way to add images into the plugin - you can just add an image to the top right of the page absolute class, floating on the header image, 2015-06-26 Kate requested adding the download button to the tabs 
$specialcss = " buttonexists";
$dnldbutton = "";
$urllow = $_SERVER['SCRIPT_URI'];
$urllow = strtolower($urllow);
if ((strpos($urllow,"nx.jtbtravel.com.au/japan-tours/escorted/fXXXoodies-japan-12-days-tour"))){
//do something...
	$dnldbutton = '<a  class="button" href="/wp-content/uploads/pdf/Foodies-Japan-12-Day-Tour.zip"><img class="alignnone wp-image-17137 size-full" title="Download a PDF brochure for this tour" alt="Download a PDF brochure for this tour" width="130" height="121" srcset="https://www.nx.jtbtravel.com.au/wp-content/uploads/2012/09/download-brochure-pdf.png, https://www.nx.jtbtravel.com.au/wp-content/uploads/2012/09/download-brochure-pdf@2x.png 2x"></a>';
}
else if ((strpos($urllow,"nx.jtbtravel.com.au/japan-tours/escorted/dXXiscover"))&&(!strpos($urllow,"nx.jtbtravel.com.au/japan-tours/escorted/discover-japan2"))){
//do something...
	$dnldbutton = '<a   class="button"  href="https://www.nx.jtbtravel.com.au/wp-content/uploads/pdf/Discover-Japan-13-Day-Tour.zip"><img class="alignnone wp-image-17137 size-full" title="Download a PDF brochure for this tour" alt="Download a PDF brochure for this tour" width="130" height="121" srcset="https://www.nx.jtbtravel.com.au/wp-content/uploads/2012/09/download-brochure-pdf.png, https://www.nx.jtbtravel.com.au/wp-content/uploads/2012/09/download-brochure-pdf@2x.png 2x"></a>';
}
else if ((strpos($urllow,"nx.jtbtravel.com.au/japan-tours/escorted/eXXXxplore-by-rail"))){
//do something...
	$dnldbutton = '<a   class="button"  href="/wp-content/uploads/pdf/Japan-by-Rail-21-Day-Tour.zip"><img class="alignnone wp-image-17137 size-full" title="Download a PDF brochure for this tour" alt="Download a PDF brochure for this tour" width="130" height="121" srcset="https://www.nx.jtbtravel.com.au/wp-content/uploads/2012/09/download-brochure-pdf.png, https://www.nx.jtbtravel.com.au/wp-content/uploads/2012/09/download-brochure-pdf@2x.png 2x"></a>';
}else{
	$specialcss = "";
}


		# The shortcode is used on the page, so we'll need to load the JavaScript
		self::$add_script = true;
		
		# Create empty titles array
		self::$tab_titles = array();
		
		extract(shortcode_atts(array(), $atts));
		
		# Get all individual tabs content
		$tab_content = do_shortcode($content);
		
		# Start the tab navigation
		$out = '<div id="tabs_container"><a id="tabsanchor" name="tabsanchor"></a>';
		$out .= '<ul class="tabs'.$specialcss.'">'.$dnldbutton;
		
		# Loop through tab titles ###

		foreach (self::$tab_titles as $key => $title) {
			$enqbuttonclass="";
			if (strpos(strtolower($title), 'enquir') !== false){
				$enqbuttonclass=" enquirebuttontab ";
			}
			$id = $key + 1;
			$out .= sprintf('<li><a rel="#%s"%s  id="tabbutton'.$id.'" class="tab'.$enqbuttonclass.'">%s</a></li>',
				'tab-' . $id,
				$id == 1 ? ' class="tab active"' : '',
				$title
			);
		}
		
		# Close the tab navigation container and add tab content
		$out .= '</ul> <div class="tab_contents_container">';
		$out .= $tab_content;
		$out .=  "</div></div>";
		return $out;
	
	}
	
	# Tab item shortcode
	static function tab_shortcode($atts, $content = null) {
	
		extract(shortcode_atts(array(
			'title' => ''
		), $atts));
		
		# Add the title to the titles array
		array_push(self::$tab_titles, $title);
		
		$id = count(self::$tab_titles);
		
		return sprintf('<div id="%s" class="tab_contents%s">%s</div>',
			'tab-' . $id,
			$id == 1 ? ' tab_contents_active' : '',
			do_shortcode($content)
		);
	
	}
	
	# Add documentation link on plugin page
	static function add_documentation_link($links) {
	
		array_push($links, sprintf('<a href="%s">%s</a>',
			'http://wordpress.org/plugins/tabs-shortcodes/',
			__('Documentation', 'tabs_shortcodes')
		));
		
		return $links;
	
	}

}

$Tabs_Shortcodes = new Tabs_Shortcodes;

endif;






/* SECOND ---- */

