<?php
/*
Plugin Name: jtb-australia-cust
Plugin URI:  https://www.nx.jtbtravel.com.au
Description: Customisations
Version:     1.0
Author:      Ben from Sydney office - JTB  
Domain Path: /languages
Text Domain: my-toolset
*/

// CLOSE ALL JR PAGES |||||| template-redirect jr page and not admin
// search for this to undo 
// JTB  ______________ J T B    T R A V E L   -   M A I N    R E T A I L   

defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); 
function get_timestamp(){ //current unix stamp - force refresh every 10 seconds.
    $force_refresh = "1705360416";  ///////@@@@@@@@@@@@@@@@@
    if( (int) time() - $force_refresh <6222 ){
        return (int) substr((string)time(), 0, -1) . "0";
    } return (int) substr((string)time(), 0, -3) . "000"; //1,000 seconds
}
//<b><br>*If you are departing within 7 days, please go directly to one of our JR issuing offices to purchase ($10 on the spot issuing fee will apply) – <a href="https://www.nx.jtbtravel.com.au/japan-rail-pass/jr-pass-collection/" target="_blank">Office Locations</a><br /><br />*Please do not use European-keyboard special characters (EG: use e, u, l - not é, ů, ł)</b>
function CloseTPbookingsSettings(){
    $closebookings=0; //set this to 1 to close bookings only for logged-out users
    if(is_user_logged_in()){$closebookings=0;} //leave this
    return array(
	
/*
        'warningMessageShow'=>0, // 1 for yes, show now, 0=X
        // what message to show
        'warningMessage'=>'The booking system of our website will be down for maintenance from 6pm tonight, 26th July <b>Email enquiries will still be available.</b>', 

        'closeBookingsNow'=>$closebookings, // close all TP bookings (power shutdown)
        //booking closed message
        'closeBookingsMessage' => 'The website booking system is currently down for maintenance and will resume shortly - please send email enquiries for the time being.',//The website booking system is currently down for maintenance and will resume after midnight - please send email enquiries for the time being.',
        'autoStartBookingClose' => '2023-07-26-18-00', // YYYY-mm-dd-HH-mm
        'autoFINISHBookingClose' => '2023-07-27-03-05', // YYYY-mm-dd-HH-mm
        
        'startShowingWarning' => '2019-10-18-09-05', // YYYY-mm-dd-HH-mm

 

*/

        'warningMessageShow'=>0, // 1 for yes, show now, 0=X
        // what message to show
        'warningMessage'=>'The booking system of our website will be down for maintenance from Friday the 28th of July, 7pm. <b>Email enquiries will still be available.</b>', 

        'closeBookingsNow'=>$closebookings, // close all TP bookings (power shutdown)
        //booking closed message
        'closeBookingsMessage' => 'The website booking system is currently down for maintenance and will resume shortly - please send email enquiries for the time being.',//The website booking system is currently down for maintenance and will resume after midnight - please send email enquiries for the time being.',
        'autoStartBookingClose' => '2023-07-28-18-55', // YYYY-mm-dd-HH-mm
        'autoFINISHBookingClose' => '2023-07-29-03-05', // YYYY-mm-dd-HH-mm
        
        'startShowingWarning' => '2019-10-18-09-05', // YYYY-mm-dd-HH-mm

        'customMessage' => 'The booking system of our website will be down for maintenance on Thursday the 19th of Dec. (8pm until Midnight). Email enquiries will still be available.',//The Melbourne office building will be locked for the week of Christmas - to gain access, please call Melbourne staff on (03) 8623-0000',//<b>JR Pass Notice:</b> Due to maintenance work in Japan, walk-in JR Pass purchases in our Sydney and Melbourne offices will not be available on Wednesday the 1st of November between 12:00 noon and 1pm.',
        'customMessageColour' => 'yellow',//green,red,yellow - only - noSpaces ?
        'customMessageEndDate' => '2020-01-01-17-35', // YYYY-mm-dd-HH-mm

        //'customTPmessage' => 'There is an upcoming scheduled JR Pass System Server Maintenance - 12 noon till 1pm, on Wed. 24th Jan. 2018, Sydney Time<br /><strong>This only affects walk-in customers - Sydney and Melbourne offices. <br />Online bookings will not be affected.</strong>',
        //We are currently experiencing issues with our booking system - please send enquiry emails - and check back for updates.
        'customTPmessage' => 'By using this page you agree with the <a href="https://www.nx.jtbtravel.com.au/japan-rail-pass/agreement/">JR Pass Agreement</a>',

        'customTPmessageColour' => 'yellow',//green,red,yellow - only - noSpaces ?
        'customTPmessageEndDate' => '2020-02-18-11-05' // YYYY-mm-dd-HH-mm

        );
}


if ( !class_exists( 'jtbau' ) ) {
    class jtbau
    {
        // Constructor
        function __construct()
        {
            $this->plugin_name = basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ); 

            //Ben's custom functions and triggers
            add_action('admin_init', 'wpb_imagelink_setup', 10); //no linking images to self
            add_action( 'login_enqueue_scripts', 'my_login_logo' );
            add_action( 'body_message', 'sda_body_message_print' );
            add_action( 'admin_menu', 'my_plugin_menu' );
            add_action( 'admin_bar_menu', 'jtbau_options_page_bar_menu', 999 );
            add_action( 'print_flight_home_data', 'print_flt_home_data' );
            add_action( 'print_after_body','after_body_print');
            add_action( 'print_after_body','after_body_popups');
            add_action( 'print_after_body','norinchukin_export');
            add_action( 'print_top_page_messages','top_page_messages_print');
            add_action( 'print_social_buttons','social_buttons_print');
            add_action( 'print_social_buttons2','social_buttons_print');
            add_action( 'admin_enqueue_scripts', 'jtb_admin_custom_css' );
            add_action( 'print_jtb_footer', 'jtb_footer_prnt');
            add_action( 'print_jtb_home', 'jtb_home_prnt');
            add_action( 'print_contact_page', 'contact_page_prnt');
            add_action( 'print_about_tc', 'about_tc_prnt');
            add_action( 'print_tour_template_custom', 'tours_template_custom_prnt');
            add_action( 'print_tours_top_custom', 'tours_top_custom_prnt');
            add_action( 'print_search', 'search_prnt'); 
         add_action( 'pre_get_posts','posts_per_search_page' );//posts per page 12
            add_action( 'get_footer','print_jr_footer',998 );
            add_action( 'jr_links','print_jr_links' );
            add_action( 'print_itinerary_buttons', 'itinerary_buttons_prnt'); 
            add_action( 'parse_query', 'my_global_vars' );
            add_action( 'print_jr_partial', 'jr_partial_prnt'); 
            add_action( 'print_default_template', 'default_template_prnt');
            add_action( 'print_day_tour_data', 'day_tour_data_prnt');
            add_action( 'print_post_reviews', 'post_reviews_prnt');
            add_action( 'print_post_template', 'post_article_prnt');
            add_action( 'print_drive_tour', 'drive_tour_prnt');

            add_action( 'print_category_reviews', 'category_reviews_prnt');
            add_action( 'print_post_tag', 'post_tag_prnt');
            add_action( 'print_tickets', 'tickets_prnt');
            add_action( 'print_tickets_top', 'tickets_top_prnt');
            add_action( 'print_product_second_lvl', 'product_second_lvl_prnt');
//add_action( 'pre_get_posts', 'wp_search_filter' ); // Hide Posts allow media search by filename
     //add_filter('pre_get_posts','wpshock_search_filter');
            add_action( 'print_itinerary_messagebox', 'itinerary_messagebox_prnt' );
            add_action( 'wp_enqueue_scripts', 'enqueue_files' );
           // add_action( 'admin_enqueue_scripts', 'enqueue_files_admin' ); 
			//add_action( 'template_redirect', 'my_redirect_if_user_not_logged_in' );
            //add_action( 'save_post', 'refresh_sitemap');
            add_action( 'template_redirect', 'booking_close_redirect' );
            add_action( 'plugins_loaded', 'booking_close_settings' );
            add_action( 'wp_head', 'tag_manager_setup_prnt',1 );
            //add_action( 'admin_init', 'wpdocs_theme_add_editor_styles' ); 
            add_action( 'wp_ajax_save_ghib', 'callback_save_ghib' );
            add_action( 'wp_ajax_nopriv_save_ghib', 'callback_save_ghib' );
            add_action( 'wp_ajax_send_email', 'callback_send_email' );
            add_action( 'wp_ajax_nopriv_send_email', 'callback_send_email' );
            add_action( 'wp_ajax_send_email_sector', 'callback_send_email_sector_ticket' );
            add_action( 'wp_ajax_nopriv_send_email_sector', 'callback_send_email_sector_ticket' );
			
			//callback_send_email_sector_ticket
            add_action( 'wp_ajax_mice_rsvp_mysql', 'callback_mice_rsvp_mysql' );
            add_action( 'wp_ajax_nopriv_mice_rsvp_mysql', 'callback_mice_rsvp_mysql' );
            add_action('wp_head','print_head',0);
            add_action('wp_head','print_head',999);
            add_action('admin_head','print_head',0);
            add_action('admin_head','print_head',999);
          //  add_action( 'get_header', 'print_header_message' );

            add_action( 'wp_ajax_send_email_test2', 'callback_send_email_test2' );
            add_action( 'wp_ajax_nopriv_send_email_test2', 'callback_send_email_test2' );
 
            add_action( 'wp_ajax_send_email2', 'callback_send_email2' );
            add_action( 'wp_ajax_nopriv_send_email2', 'callback_send_email2' );

            add_action( 'wp_ajax_send_email_rwc', 'callback_send_email_rwc' );
            add_action( 'wp_ajax_nopriv_send_email_rwc', 'callback_send_email_rwc' );


            add_action( 'wp_ajax_send_email_rwc2', 'callback_send_email_rwc2' );
            add_action( 'wp_ajax_nopriv_send_email_rwc2', 'callback_send_email_rwc2' );


            add_action( 'wp_ajax_send_email_escorted', 'callback_send_email_escorted' );
            add_action( 'wp_ajax_nopriv_send_email_escorted', 'callback_send_email_escorted' );


            add_action( 'wp_ajax_send_email_rwc_travel', 'callback_send_email_rwc_travel' );
            add_action( 'wp_ajax_nopriv_send_email_rwc_travel', 'callback_send_email_rwc_travel' );

            add_action( 'wp_ajax_send_email_usj', 'callback_send_email_usj' );
            add_action( 'wp_ajax_nopriv_send_email_usj', 'callback_send_email_usj' );

            add_action( 'wp_ajax_send_email_feedback', 'callback_send_feedback_data' );
            add_action( 'wp_ajax_nopriv_send_email_feedback', 'callback_send_feedback_data' );


add_action('init', 'my_pagination_rewrite');

            add_shortcode( 'jtb-widget', 'jtb_widget_print');
            add_shortcode( 'icon', 'icon_shortcode');
            add_shortcode( 'popup', 'popup_shortcode');
            add_shortcode( '2col', 'col2_shortcode');
            add_shortcode( 'gallery3', 'gallery3_shortcode');
            add_shortcode( 'img3', 'gallery3_shortcode');
            add_shortcode( '3img', 'gallery3_shortcode');
            add_shortcode( 'cruise', 'cruise_shortcode');
            add_shortcode( 'sim', 'sim_shortcode');
            add_shortcode( 'sim2', 'sim_shortcode2');
            add_shortcode( 'col2', 'col2_shortcode2' );
            add_shortcode( 'col1', 'col1_shortcode2' );
            add_shortcode( 'col3', 'col3_shortcode2' );
            add_shortcode( 'col22', 'col2_shortcode3' );
            add_shortcode( 'col11', 'col1_shortcode3' );
            add_shortcode( 'col33', 'col3_shortcode3' );

 
            add_filter( 'body_class', 'custom_checkboxes' );
  //  add_filter( 'posts_search', 'guid_search_so_14940004', 99, 2 );

//add_filter( 'posts_search', 'guid_search_so_14940004_2', 10, 2 );



            add_filter( 'excerpt_length', 'wpdocs_custom_excerpt_length', 999 );//26w
            //add_filter( 'the_content', 'remove_inline_styles', 20 );
            add_filter( 'mce_css', 'my_plugin_editor_style' );
 add_filter( 'pre_get_posts', 'wpse8170_pre_get_posts' ); //HIDDEN PAGES 
            add_filter( 'wpcf7_form_elements', 'mycustom_wpcf7_form_elements' );

 
            register_activation_hook(__FILE__, 'my_activation');

            if(wp_get_post_parent_id( get_the_ID() )==3338){
                global $jrheader;
                global $jrheadercount;
                $jrheader="12";
                $jrheadercount=0;
            }

            if ( function_exists( 'add_theme_support' ) ) {
				add_theme_support( 'post-thumbnails' );
			}

            if (!is_admin()){


			 set_theme_mod("footer-copyright",'© JTB Australia 1962 - ' . date("Y") . ' All rights reserved - <a href="/articles/">articles</a> - <a href="/site-map/">site map</a>');


            }else{
              	add_action( 'current_screen', 'revove_useless_postedit_current_screen' );
                add_action('post_submitbox_misc_actions', 'create_tour_checkbox');
                add_action('save_post', 'save_tour_checkbox');
                add_action('save_post', 'save_ghib_cal', '99', 1);
                add_action('admin_init','redirect_reports');
            }

        }
        
    } // End Class 
}




if ( class_exists( 'jtbau' ) ) {
    global $jtbau;
    $jtbau = new jtbau();
}


// Ben's Custom Functions



/**
 * Add Editor Style
 * add additional editor style for my-plugin
 * 
 * @since 0.1.0
 */
 

function enqueue_files() {

/*
materialize css

wp_enqueue_style ( 'materialize-css',  'https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css' );
wp_enqueue_script ( 'materialize-js', 'https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js' );

*/

//DISABLED before switching all of for SEED-ppl
//wp_enqueue_script ( 'tab-button-scroll', plugin_dir_url( __FILE__ ) .'js/tab-button-scroll.js',array(),get_timestamp() );
//wp_enqueue_script ( 'underscore-loadash-js', plugin_dir_url( __FILE__ ) .'js/underscore.js' );
//wp_enqueue_script ( 'lodash-js', "//cdnjs.cloudflare.com/ajax/libs/lodash.js/0.10.0/lodash.min.js" );
//<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/lodash.js/0.10.0/lodash.min.js"></script>
//wp_enqueue_script ( 'flight-search-js', plugin_dir_url( __FILE__ ) .'js/flight-search.js',array(),get_timestamp(),true);
//wp_enqueue_script ( 'jqueryui-js', plugin_dir_url( __FILE__ ) .'js/jquery-ui-1.10.4.custom.min.js',array(),false,true);
//wp_enqueue_script ( 'jqueryui-js', plugin_dir_url( __FILE__ ) .'js/jquery-ui.min.js',array(),false,true);
//wp_enqueue_script ( 'jqueryui-js',  'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js',array(),false,true);
//JTB JQUERY - breaks TP day tours h.k. new-look site
//wp_enqueue_script ( 'jquery-jtb2',  'https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js');
//wp_enqueue_style ( 'jqueryui-css', plugin_dir_url( __FILE__ ) .'css/jquery-ui-1.10.4.custom.min.css' );
//add_filter( 'wp_default_scripts', 'change_default_jquery_1_9_1' );
//DISABLED before switching all of for SEED-ppl





//wp_enqueue_style ( 'editor-style-css', plugin_dir_url( __FILE__ ) .'css/editor-style.css',array(),get_timestamp() );

wp_enqueue_script ( 'jtb-global-js', plugin_dir_url( __FILE__ ) .'js/jtb-global.js',array(),get_timestamp(),true);
wp_enqueue_script ( 'popup-box-js', plugin_dir_url( __FILE__ ) .'js/popup-box.js',array(),get_timestamp(),true);


if(  ($_SERVER['HTTP_HOST'] ==  '127.0.0.1' ) || ( $_SERVER['HTTP_HOST'] =='www.nx.jtbtravel.com.au')  ){
    //wp_enqueue_style ( 'roobix-style-css', plugin_dir_url( __FILE__ ) .'css/a-roobix-new-look.css',array(),get_timestamp() );
    wp_enqueue_script ( 'roobix-new-look-js', plugin_dir_url( __FILE__ ) .'js/a-roobix-new-look.js',array(),get_timestamp(),true);
}  

//wp_enqueue_style ( 'jtbtravel-css', plugin_dir_url( __FILE__ ) .'css/a-jtbtravel.css',array(),get_timestamp() );
//wp_enqueue_style ( 'mobile-css', plugin_dir_url( __FILE__ ) .'css/mobile.css',array(),get_timestamp() );

//wp_enqueue_style ( 'jqueryui-css',  'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css' );

wp_enqueue_style ( 'fontawesomeicon-css', plugin_dir_url( __FILE__ ) .'css/font-awesome-4.7.0/css/font-awesome.min.css' );

//wp_enqueue_style ( 'ie-css', plugin_dir_url( __FILE__ ) .'css/ie.css' ,array(),get_timestamp() );
//wp_style_add_data( 'ie-css', 'conditional', 'IE' );


global $current_user;
//get_currentuserinfo();
wp_get_current_user() ;

if(($current_user->user_email == "benjamin_g.au@jtbap.com")||($current_user->user_email == "chikanobu_t.auZZZ@jtbap.com")){
 	//wp_enqueue_style ( 'head-msg-css', plugin_dir_url( __FILE__ ) .'css/a-roobix-header-message.css',array(),get_timestamp() );
 	wp_enqueue_script ( 'head-msg-js', plugin_dir_url( __FILE__ ) .'js/a-roobix-header-msg.js',array(),get_timestamp());
}

if(($current_user->user_email == "benjamin_g.auZZZ@jtbap.com")||($current_user->user_email == "chikanobu_t.auZZZ@jtbap.com")){
	//wp_enqueue_style ( 'ben-css', plugin_dir_url( __FILE__ ) .'css/z_ben_test.css',array(),get_timestamp() );
 
}

  if ( is_page(21601 ) ) {
 wp_enqueue_script ( 'daytoursearch-js', plugin_dir_url( __FILE__ ) .'js/temp-daytour-contact-search.js',array(),get_timestamp(),true);
}
  if ( is_page(30146)||is_page(20511)||is_page(30123)||is_page(30077)||is_page(21332)||is_page(34804) ) {
 wp_enqueue_script ( 'usj-cal-js', plugin_dir_url( __FILE__ ) .'js/usj-cal.js',array(),get_timestamp(),true);
}  if ( is_page(24138)||is_page(30203)  ) {
 wp_enqueue_script ( 'ghibli-cal-js', plugin_dir_url( __FILE__ ) .'js/ghibli-cal.js',array(),get_timestamp(),true);
} if ( is_page(4608)  ) { // brochure agent
 wp_enqueue_script ( 'agent-brochure-js', plugin_dir_url( __FILE__ ) .'js/agent-brochure.js',array(),get_timestamp(),true);
}
if( (is_page(3338)) || (is_page(3343)) || (is_page(35533))  ){ //if JR national - open T/C
 wp_enqueue_script ( 'open-tc-js', plugin_dir_url( __FILE__ ) .'js/jr-national-open-t-c.js',array(),get_timestamp());
}
if(  is_page(30845)   ){ //if JR national - open T/C
 wp_enqueue_script ( 'open-tc-js', plugin_dir_url( __FILE__ ) .'js/jr-suica-open-tc.js',array(),get_timestamp());
}
if(is_page(32291)||is_page(3343)   ){
     wp_enqueue_script ( 'jr-calc', plugin_dir_url( __FILE__ ) .'js/jr-calc.js',array(),get_timestamp());
}
if( is_page(22779)  ){ //if agent jr booking form page
    wp_enqueue_script ( 'agent-jr-js', plugin_dir_url( __FILE__ ) .'js/agent-jr-form-submit.js',array(),get_timestamp());
}
if(  is_page(35517) ){ //if agent jr booking form page
    wp_enqueue_script ( 'agent-jr-js', plugin_dir_url( __FILE__ ) .'js/agent-jr-form-submit-2023.js',array(),get_timestamp());
}
if(  is_page(35870) || is_page(35813)   ){ //if agent jr booking form page
    wp_enqueue_script ( 'rail-p2p-js', plugin_dir_url( __FILE__ ) .'js/rail-p2p-2024-submit.js',array(),get_timestamp());
}
if( is_page(791) ){ //USJ EXPRESS - live 
    wp_enqueue_script ( 'usjsubmit-js', plugin_dir_url( __FILE__ ) .'js/usj-submit.js',array(),get_timestamp());
}
if( is_page(31880) ){ //USJ EXPRESS - live 
    wp_enqueue_script ( 'usjsubmit-js-gsheet', plugin_dir_url( __FILE__ ) .'js/usj-submit-g-sheets.js',array(),get_timestamp());
}
if( is_page(28693) ){ //USJ EXPRESS - test - 2
    wp_enqueue_script ( 'usj-submit-js', plugin_dir_url( __FILE__ ) .'js/usj-submit2.js',array(),get_timestamp());
}
if( is_page(23438) ){ //if agent jr booking form page
    wp_enqueue_script ( 'agent-jr-form-js', plugin_dir_url( __FILE__ ) .'js/agent-jr-form-submit2.js',array(),get_timestamp());
}
if( is_page(24379) ){ //if agent jr booking form page
    wp_enqueue_script ( 'rwc-js', plugin_dir_url( __FILE__ ) .'js/rwc-form-submit2.js',array(),get_timestamp());
}if( is_page(25211) ){ //if agent jr booking form page TEST
    wp_enqueue_script ( 'rwc-js', plugin_dir_url( __FILE__ ) .'js/rwc-form-submit2.js',array(),get_timestamp());
}if( is_page(31209) ){ //if agent jr booking form page TEST
    wp_enqueue_script ( 'rwc-tavel-pack', plugin_dir_url( __FILE__ ) .'js/rwc-travel-package.js',array(),get_timestamp());
}


add_filter( 'script_loader_tag', 'cameronjonesweb_add_script_handle', 10, 3 );


if( is_page(73) ){//flights - main 
    add_action('wp_print_scripts','remove_jquery',99);
    add_action('wp_head','flightjscss2',11);

}

if( is_page(26205) ){ // sabre new flights 




add_action('wp_print_scripts','remove_jquery',99);


add_action( 'wp_enqueue_scripts', 'flightjscss', 11 );





}

if( is_page(23199) ||is_page(30882) ){ //test page 
    wp_enqueue_script ( 'feedbackjs', plugin_dir_url( __FILE__ ) .'js/feedback.js',array(),get_timestamp());
}

if(  is_page(25049)){ // Escorted form
 wp_enqueue_script ( 'esc-form-js', plugin_dir_url( __FILE__ ) .'js/escorted-form.js',array(),get_timestamp());
}

if( (is_page(24893)) || (is_page(25268)) ){ // cruise book button
 wp_enqueue_script ( 'cruise-book-js', plugin_dir_url( __FILE__ ) .'js/cruise-book.js',array(),get_timestamp());
}

if( is_page(791)   ){ // cruise book button
 wp_enqueue_script ( 'usj-button-js', plugin_dir_url( __FILE__ ) .'js/usj-contact-button.js',array(),get_timestamp());
}

//RECAPTCHA CUSTOM - 
if(is_page(28693) || is_page(22779) || is_page(35517) || is_page(791) || is_page(35870) || is_page(35813)   ){
 wp_enqueue_script ( 'recap-goog-jtb',  'https://www.google.com/recaptcha/api.js' );
}




/* RWC CSS 
*/

if ((!get_post_meta( 24349, '_hide_from_search', true ))|| ($current_user->user_email == "benjamin_g.au@jtbap.com")){
  //wp_enqueue_style ( 'rwc-hide-css', plugin_dir_url( __FILE__ ) .'css/z_rwc.css',array(),get_timestamp() );
}
if (is_page(32291)||is_page(3343)  ){
  //wp_enqueue_style ( 'jr-calc-css', plugin_dir_url( __FILE__ ) .'css/jr-calc.css',array(),get_timestamp() );
}
/*checkout JS */
//3392
//3394
//23199 - test
//if( is_page(23199)  || is_page(3392)  ||  is_page(3394)  ){ // cruise book button
 //wp_enqueue_script ( 'checkout-js', plugin_dir_url( __FILE__ ) .'js/checkout.js',array(),get_timestamp());
//}

$area_buton_pages = array("3615", "3714", "3764", "3766", "3768", "3770", "3772", "3774", "3776", "3779", "3781", "3783", "3787", "3791", "22058" );
if( in_array ( get_the_ID() ,   $area_buton_pages   ) ){ //if agent jr booking form page
    wp_enqueue_script ( 'area-button-js', plugin_dir_url( __FILE__ ) .'js/area-info-button.js',array(),get_timestamp());
}


}


function change_default_jquery_1_9_1( &$scripts){
    if(!is_admin()){
        $scripts->remove( 'jquery');
        $scripts->add( 'jquery', false, array( 'jquery-core' ), '1.9.1' );
    }
}



function my_redirect_if_user_not_logged_in() {
	
  if (   !is_user_logged_in() && is_page()   && 
  ( !(is_page(34334)) ) && ( !(is_page(34347)) ) && ( !(is_page(34350)) )      
&& ( !(is_page(34368)) ) && ( !(is_page(34369)) )&& ( !(is_page(4798)) )   

  ){
     wp_redirect( 'https://au.jtbtrip.com/'); exit;// never forget
   }
  

}


function my_pagination_rewrite() {
    add_rewrite_rule('blog/page/?([0-9]{1,})/?$', 'index.php?category_name=blog&paged=$matches[1]', 'top');
}


function cameronjonesweb_add_script_handle( $tag, $handle, $src ) {
    return str_replace( '<script', sprintf(
        '<script data-handle="%1$s"',
        esc_attr( $handle )
    ), $tag );
}


function remove_jquery() {
    wp_dequeue_script('jqueryui-js');
    wp_deregister_script('jquery');
    wp_dequeue_script('vue');

    wp_deregister_script('bootstrap');
    wp_dequeue_script('bootstrap-datepicker');
    wp_dequeue_script('bootstrap-multiselect');
    wp_dequeue_script('bootstrap-datetimepicker');

    wp_dequeue_style('jqueryui-css');
    //wp_dequeue_script('');
    //wp_dequeue_script('');
    //wp_deregister_script('jquery');
}


function flightjscss2(){

?>


<link href="https://fonts.googleapis.com/icon?family=Material+Icons" type="text/css" rel="stylesheet" />

<script src="https://secl-au-jtb.azurewebsites.net/Resources/Shared/Scripts/angular.min.js?ts=20190603053856"></script>
<script src="https://secl-au-jtb.azurewebsites.net/Resources/Shared/Scripts/angular-animate.min.js?ts=20190603053856"></script>
<script src="https://secl-au-jtb.azurewebsites.net/Resources/Shared/Scripts/angular-aria.min.js?ts=20190603053856"></script>
<script src="https://secl-au-jtb.azurewebsites.net/Resources/Shared/Scripts/angular-messages.min.js?ts=20190603053856"></script>
<script src="https://secl-au-jtb.azurewebsites.net/Resources/Shared/Scripts/angular-route.min.js?ts=20190603053856"></script>
<script src="https://secl-au-jtb.azurewebsites.net/Resources/Shared/Scripts/angular-sanitize.min.js?ts=20190603053856"></script>

<script src="https://secl-au-jtb.azurewebsites.net/Resources/libraries/jQuery/01_09_01/jquery.js"></script>

<link href="https://secl-au-jtb.azurewebsites.net/Portals/_default/Skins/DEAgentBooking/bootstrap/css/bootstrap.min.css?ts=20190603053856" type="text/css" rel="stylesheet" />
<script src="https://secl-au-jtb.azurewebsites.net/Portals/_default/Skins/DEAgentBooking/bootstrap/js/bootstrap.min.js?ts=20190603053856"></script>
<script src="https://secl-au-jtb.azurewebsites.net/Resources/Shared/Scripts/ui-bootstrap-tpls-1.3.2.min.js"></script>

<link href="https://secl-au-jtb.azurewebsites.net/Resources/Shared/components/Angular-Material/1.1.5/angular-material.min.css" type="text/css" rel="stylesheet" />
<script src="https://secl-au-jtb.azurewebsites.net/Resources/Shared/components/Angular-Material/1.1.5/angular-material.min.js"></script>

<link href="https://secl-au-jtb.azurewebsites.net/Resources/Shared/components/angularjs-slider/6.4.3/rzslider.min.css" type="text/css" rel="stylesheet" />
<script src="https://secl-au-jtb.azurewebsites.net/Resources/Shared/components/angularjs-slider/6.4.3/rzslider.min.js"></script>
<script src="https://secl-au-jtb.azurewebsites.net/Resources/Shared/Scripts/moment-with-locales.js?ts=20190603053856"></script>


<script src="https://secl-au-jtb.azurewebsites.net/min/js/searchbox.js?ts=20190603053856"></script>
<script src="https://secl-au-jtb.azurewebsites.net/Style/agent.js?ts=20190603053856"></script>

<link href="https://secl-au-jtb.azurewebsites.net/min/css/searchbox.css?ts=20190603053856" type="text/css" rel="stylesheet" />
<link href="https://secl-au-jtb.azurewebsites.net/Style/agent.css?ts=20190603053856" type="text/css" rel="stylesheet" />



<?php


}

function flightjscss(){



}


function wpse8170_pre_get_posts( WP_Query $query ) {
    if ( $query->is_search() ) {
       // $query->set( 'post_type', array( 'resources' ) );
        $query->set( 'meta_query', array(
            array(
                'key' => '_hide_from_search',
                //'value' => true,
                'compare' => 'NOT EXISTS',
            )
        ) );
    }

    return $query;
}



/*GOOGLE FONTS
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,400;0,600;1,400;1,700&display=swap" rel="stylesheet">
*/

function enqueue_files_admin() {
wp_enqueue_style ( 'font-css', 'https://fonts.googleapis.com/css?family=Roboto:400,400i,700,700i|Material+Icons|PT+Mono+Rubik:ital,wght@0,400;0,600;1,400;1,600' ,array(),2385);


}


// Remove pointless post meta boxes
function revove_useless_postedit_current_screen() {
    // "This function is defined on most admin pages, but not all."
    if ( function_exists('get_current_screen')) {  

        $pt = get_current_screen()->post_type;
        if ( $pt != 'post') return;

        	//wp_enqueue_style ( 'post-edit-css', plugin_dir_url( __FILE__ ) .'css/post-edit.css',array(),get_timestamp() );
    }
}








function callback_send_feedback_data(){




$email = $_REQUEST['email']; //email subscribe 
if( (($email != "") && ($email != false) && ($email != null ) )|| 1 ){

$source = $_REQUEST['source'];

if( ($source == "") || ($source == false) || ($source == null ) ){
    $source = "";
}

$url = "https://docs.google.com/forms/d/1SRMQbdLim8-OPLR9LHOWfXZYtmv-Z0ms8Xdd0XQzf1k/formResponse";

// subscribe 
      //url: "https://docs.google.com/a/jtbap.com/forms/d/1PUqUTddB5SS3TMr2kbjhZok0FQfTkJdvIdATV2sTIcQ/formResponse",
          // url: "https://docs.google.com/a/jtbap.com/forms/d/1PUqUTddB5SS3TMr2kbjhZok0FQfTkJdvIdATV2sTIcQ/formResponse",
         

$stream_options = array(
    'http' => array(
        'method'  => 'GET',
        'header'  => 'Content-Type: text/html' . "\r\n",
        'content' => "" 
    ));
$context  = stream_context_create($stream_options);
$response = file_get_contents($url."?entry.1493792574=&entry.976961624=".$source."&entry.1411460999=".$email, null, $context);
}






// $email = $_REQUEST['source']; //hear about 
if( ($email != "") && ($email != false) && ($email != null )  && 0 ){

$source = $_REQUEST['source'];

if( ($source == "") || ($source == false) || ($source == null ) ){
    $source = "";
}

$url =  "https://docs.google.com/forms/d/1uqhBFCQu1ZjXVeW3hhNgoe8ZIN0-0b_Yne4fqTg6UPY/formResponse";

// hear about us 
     // url: "https://docs.google.com/a/jtbap.com/forms/d/1JH0gL0rQUTRoLDNqUMF5zVOJHHIN7rnakVJKg8paaUg/formResponse",
   

$stream_options = array(
    'http' => array(
        'method'  => 'GET',
        'header'  => 'Content-Type: text/html' . "\r\n",
        'content' => "" 
    ));
$context  = stream_context_create($stream_options);
$response = file_get_contents($url."?entry.308663305=".$hear_about, null, $context);//multiple
}









  die();
}
//END





function callback_mice_rsvp_mysql(){
  $time = time();
  $email = $_REQUEST['email'];
  $rsvp= $_REQUEST['rsvp'];
global $wpdb;
  //add to dbs mysql
$wpdb->insert( 'wp_jtbau_mice_rsvp', array( 
    'time' => $time, 
    'email' => $email,
    'rsvp' =>  $rsvp
  )); 

  die();
  
}



 


function wpshock_search_filter( $query ) {
    if ( $query->is_search ) {
        $query->set( 'post_type', array('post','page') );
    }
    return $query;
}

function callback_send_email_test2(){
  $to = "melres.au@jtbap.com";
$to = "benjamin_g.au@jtbap.com";
      $name = $_REQUEST['name'];
      $email = $_REQUEST['email'];
      $message= $_REQUEST['message'];
      $agency = $_REQUEST['agency'];
      $message = str_replace("[[","<",$message);
      $message = str_replace("]]",">",$message);
      //$message = str_replace("\'","'",$message);
      $message = str_replace("\\'","'",$message);
      $sales_id = (string)get_jragent_idno();
      $message = str_replace("@@#SALES_ID#@@",$sales_id,$message);
      date_default_timezone_set('Australia/Melbourne');
      $message = str_replace("@@#DATE_TIME#@@",date("Y-m-d h:i:s a", time()),$message);

      if((strpos($message, '.ru/') !== false)||
       ( (  substr($email, -3) == ".ru" ) && (strpos($message, 'и') !== false)  )||
        ( (  substr($email, -3) == ".ru" ) && (strpos($message, 'б') !== false)  )||
        ( (  substr($email, -3) == ".ru" ) && (strpos($message, 'я') !== false)  )||
        ( (  substr($email, -3) == ".ru" ) && (strpos($message, 'д') !== false)  )
    ){
        return false;   exit;   die;
      }

      $subject = "Agent JR Booking: ". $sales_id . " " . $agency ;
      $subject2 = "Confirmation Agent JR Booking: ".$name." ".$email;
      $headers  = "MIME-Version: 1.0" . "\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      $headers .= "From: JTB Mailer <mailer@nx.jtbtravel.com.au>\r\n";
      $headers .= "Cc: webmaster.au+agentjr@jtbap.com\r\n";
      $headers .= "Reply-To: $email \r\n";
      $headers2  = "MIME-Version: 1.0" . "\r\n";
      $headers2 .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      $headers2 .= "From: JTB Australia <mailer@nx.jtbtravel.com.au>\r\n";
      $headers2 .= "Reply-To: melres.au@jtbap.com\r\n";
      add_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
      $mail = wp_mail($to,$subject,$message,$headers); 
      if($mail){
        //echo "Email Sent Successfully";
        //continue;
      }else{echo " ##email_error_1## ";}
      $message2 = "This is a confirmation that JTB Australia has received your Agent JR Pass Booking.<br /><br />" . $message;
      $mail2 = wp_mail($email,$subject2,$message2,$headers2); 
      if($mail2){
        //echo "Email Sent Successfully";
        //continue;
      }
      remove_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
      //echo $sales_id;
      echo json_encode($sales_id);
      die();
}


function callback_save_ghib(){
    //$email = $_REQUEST['email'];

  $data = $_REQUEST['data2'];
    if(!get_post_meta(30203,'_ghib_cal')){
        add_post_meta(30203,'_ghib_cal','<div class="row">'.$data.'</div>');
    }else{
        update_post_meta(30203,'_ghib_cal','<div class="row">'.$data.'</div>');
    }

    die();

 
   

// get_post_meta(30203,'_ghib_cal')[0];
}//end ghib cal save

function callback_send_email(){
$to = "melres.au@jtbap.com";
//$to = "benjamin_g.au@jtbap.com";
      $name = $_REQUEST['name'];
      $email = $_REQUEST['email'];
      $message= $_REQUEST['message'];
      $agency = $_REQUEST['agency'];
      $message = str_replace("11__22","<",$message);
      $message = str_replace("11__33",">",$message);
      $message = str_replace("11__44","style=",$message);
      //$message = str_replace("\'","'",$message);
      $message = str_replace("\\'","'",$message);
      $sales_id = (string)get_jragent_idno();
      $message = str_replace("@@#SALES_ID#@@",$sales_id,$message);
      date_default_timezone_set('Australia/Melbourne');
      $message = str_replace("@@#DATE_TIME#@@",date("Y-m-d h:i:s a", time()),$message);


 $captcha = "";
    if (isset($_POST["recap"])){
        $captcha = $_POST["recap"];
    }
    if (!$captcha){
       echo "no-recap"; return false;   exit;   die;
    }
    // handling the captcha and checking if it's ok ######
    $secret = "6LcQZXUUAAAAAGa3iLEHFbcsyNPXOLoh5o7myMLQ";
    $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$captcha."&remoteip=".$_SERVER["REMOTE_ADDR"]), true);

    // if the captcha is cleared with google, send the mail and echo ok.
    if (intval($response["success"]) !== 1) {
        echo "wrong-recap"; return false;   exit;   die;
    }

            if((strpos($message, '.ru/') !== false)||
       ( (  substr($email, -3) == ".ru" ) && (strpos($message, 'и') !== false)  )||
        ( (  substr($email, -3) == ".ru" ) && (strpos($message, 'б') !== false)  )||
        ( (  substr($email, -3) == ".ru" ) && (strpos($message, 'я') !== false)  )||
        ( (  substr($email, -3) == ".ru" ) && (strpos($message, 'д') !== false)  )
    ){
        return false;   exit;   die;
      }

      $subject = "Agent JR Booking: ". $sales_id . " " . $agency ;
      $subject2 = "Confirmation Agent JR Booking: ".$name." ".$email;
      $headers  = "MIME-Version: 1.0" . "\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      $headers .= "From: JTB Mailer <mailer@nx.jtbtravel.com.au>\r\n";
      $headers .= "Cc: webmaster.au+agentjr@jtbap.com\r\n";
      $headers .= "Reply-To: $email \r\n";
      $headers2  = "MIME-Version: 1.0" . "\r\n";
      $headers2 .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      $headers2 .= "From: JTB Australia <mailer@nx.jtbtravel.com.au>\r\n";
      $headers2 .= "Reply-To: melres.au@jtbap.com\r\n";
      add_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
      $mail = wp_mail($to,$subject,$message,$headers); 
      if($mail){
        //echo "Email Sent Successfully";
        //continue;
      }else{echo " ##email_error_1## ";}
      $message2 = "This is a confirmation that JTB Australia has received your Agent JR Pass Booking.<br /><br />" . $message;
      $mail2 = wp_mail($email,$subject2,$message2,$headers2); 
      if($mail2){
        //echo "Email Sent Successfully";
        //continue;
      }
      remove_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
      //echo $sales_id;
      echo json_encode($sales_id);
      return true;
      die();
}







function callback_send_email_sector_ticket(){
$to = "sydres.au@jtbap.com";
//$to = "benjamin_g.au@jtbap.com";
      $name = $_REQUEST['name'];
      $email = $_REQUEST['email'];
      $message= $_REQUEST['message'];
      $agency = $_REQUEST['agency'];
      $message = str_replace("11__22","<",$message);
      $message = str_replace("11__33",">",$message);
      $message = str_replace("11__44","style=",$message);
      //$message = str_replace("\'","'",$message);
      $message = str_replace("\\'","'",$message);
      $sales_id = (string)get_jragent_idno();
      $message = str_replace("@@#SALES_ID#@@",$sales_id,$message);
      date_default_timezone_set('Australia/Melbourne');
      $message = str_replace("@@#DATE_TIME#@@",date("Y-m-d h:i:s a", time()),$message);


 $captcha = "";
    if (isset($_POST["recap"])){
        $captcha = $_POST["recap"];
    }
    if (!$captcha){
       echo "no-recap"; return false;   exit;   die;
    }
    // handling the captcha and checking if it's ok ######
    $secret = "6LcQZXUUAAAAAGa3iLEHFbcsyNPXOLoh5o7myMLQ";
    $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$captcha."&remoteip=".$_SERVER["REMOTE_ADDR"]), true);

    // if the captcha is cleared with google, send the mail and echo ok.
    if (intval($response["success"]) !== 1) {
        echo "wrong-recap"; return false;   exit;   die;
    }

            if((strpos($message, '.ru/') !== false)||
       ( (  substr($email, -3) == ".ru" ) && (strpos($message, 'и') !== false)  )||
        ( (  substr($email, -3) == ".ru" ) && (strpos($message, 'б') !== false)  )||
        ( (  substr($email, -3) == ".ru" ) && (strpos($message, 'я') !== false)  )||
        ( (  substr($email, -3) == ".ru" ) && (strpos($message, 'д') !== false)  )
    ){
        return false;   exit;   die;
      }

      $subject = "Sector Ticket: ". $sales_id . " " . $agency ;
      $subject2 = "Sector Ticket Confirmation: ".$name." ".$email;
      $headers  = "MIME-Version: 1.0" . "\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      $headers .= "From: JTB Mailer <mailer@nx.jtbtravel.com.au>\r\n";
      $headers .= "Cc: webmaster.au+sectorticket@jtbap.com\r\n";
      $headers .= "Reply-To: $email \r\n";
      $headers2  = "MIME-Version: 1.0" . "\r\n";
      $headers2 .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      $headers2 .= "From: JTB Australia <mailer@nx.jtbtravel.com.au>\r\n";
      $headers2 .= "Reply-To: sydres.au@jtbap.com\r\n";
      add_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
      $mail = wp_mail($to,$subject,$message,$headers); 
      if($mail){
        //echo "Email Sent Successfully";
        //continue;
      }else{echo " ##email_error_1## ";}
      $message2 = "This is a confirmation that JTB Australia has received your email regarding sector tickets.<br /><br />" . $message;
      $mail2 = wp_mail($email,$subject2,$message2,$headers2); 
      if($mail2){
        //echo "Email Sent Successfully";
        //continue;
      }
      remove_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
      //echo $sales_id;
      echo json_encode($sales_id);
      return true;
      die();
}







function callback_send_email_usj(){

//$to = "benjamin_g.au@jtbap.com";
      $name = $_REQUEST['name'];
      $email = $_REQUEST['email'];
      $message= $_REQUEST['message'];
      $state2 = $_REQUEST['state2'];
   $to = "sydres.au@jtbap.com";
   if(($state2 == "VIC" )|| ($state2 == "TRAVELAGENTS" )){
    $to = "melres.au@jtbap.com";
   }

//testing
//$to = "benjamin_g.au@jtbap.com";

 $captcha = "";
    if (isset($_POST["recap"])){
        $captcha = $_POST["recap"];
    }
    if (!$captcha){
       echo "no-recap"; return false;   exit;   die;
    }
    // handling the captcha and checking if it's ok
    $secret = "6LcQZXUUAAAAAGa3iLEHFbcsyNPXOLoh5o7myMLQ";
    $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$captcha."&remoteip=".$_SERVER["REMOTE_ADDR"]), true);

    // if the captcha is cleared with google, send the mail and echo ok.
    if (intval($response["success"]) !== 1) {
        echo "wrong-recap"; return false;   exit;   die;
    }


      $message = str_replace("11__22","<",$message);
      $message = str_replace("11__33",">",$message);
      $message = str_replace("11__44","style=",$message);
      //$message = str_replace("\'","'",$message);
      $message = str_replace("\\'","'",$message); 
      date_default_timezone_set('Australia/Melbourne');
      $message = str_replace("@@#DATE_TIME#@@",date("Y-m-d h:i:s a", time()),$message);

            if((strpos($message, '.ru/') !== false)||
       ( (  substr($email, -3) == ".ru" ) && (strpos($message, 'и') !== false)  )||
        ( (  substr($email, -3) == ".ru" ) && (strpos($message, 'б') !== false)  )||
        ( (  substr($email, -3) == ".ru" ) && (strpos($message, 'я') !== false)  )||
        ( (  substr($email, -3) == ".ru" ) && (strpos($message, 'д') !== false)  )
    ){
        return false;   exit;   die;
      }

      $subject = "Universal Studio Express Purchase Request - ".$name." ".$email;
      $subject2 = "Universal Studio Express Purchase Request - ".$name." ".$email;
      $headers  = "MIME-Version: 1.0" . "\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      $headers .= "From: JTB WebContact <sydres@nx.jtbtravel.com.au>\r\n";
      $headers .= "Cc: webmaster.au+usjexpress@jtbap.com\r\n";
      $headers .= "Reply-To: $email \r\n";
      $headers2  = "MIME-Version: 1.0" . "\r\n";
      $headers2 .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      $headers2 .= "From: JTB Australia <mailer@nx.jtbtravel.com.au>\r\n";
      $headers2 .= "Reply-To: sydres.au@jtbap.com\r\n";
      add_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
      $mail = wp_mail($to,$subject,$message,$headers); 
      if($mail){
        //echo "Email Sent Successfully";
        //continue;
      }else{echo " ##email_error_1## ";}
      $message2 = "Thank you for your request. JTB Australia has received your USJ Express Pass request form. A JTB staff member will be in contact with you within 1 - 2 business days with your quotation. <br /><br />" . $message;
      $mail2 = wp_mail($email,$subject2,$message2,$headers2); 
      if($mail2){
        //echo "Email Sent Successfully";
        //continue;
      }else{echo " ##email_error_1## ";}
      remove_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
 
       
      die();
}




function callback_send_email2(){
    $to = "benjamin_g.au@jtbap.com";
      $name = $_REQUEST['name'];
      $email = $_REQUEST['email'];
      $message= $_REQUEST['message'];
      $message = str_replace("[[","<",$message);
      $message = str_replace("]]",">",$message);
      $message = str_replace("@@#SALES_ID#@@",(string)get_jragent_idno(),$message);
      date_default_timezone_set('Australia/Melbourne');
      $message = str_replace("@@#DATE_TIME#@@",date("Y-m-d h:i:s a", time()),$message);

                  if((strpos($message, '.ru/') !== false)||
       ( (  substr($email, -3) == ".ru" ) && (strpos($message, 'и') !== false)  )||
        ( (  substr($email, -3) == ".ru" ) && (strpos($message, 'б') !== false)  )||
        ( (  substr($email, -3) == ".ru" ) && (strpos($message, 'я') !== false)  )||
        ( (  substr($email, -3) == ".ru" ) && (strpos($message, 'д') !== false)  )
    ){
        return false;   exit;   die;
      }

      $subject = "Agent JR Booking: ".$name." ".$email;
      $subject2 = "Confirmation - Agent JR Booking: ".$name." ".$email;
      $headers  = "MIME-Version: 1.0" . "\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      $headers .= "From: JTB Mailer <mailer@nx.jtbtravel.com.au>\r\n";
      //$headers .= "Cc: webmaster.au+agentjr@jtbap.com\r\n";
      $headers .= "Reply-To: $email \r\n";
      add_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
      $mail = wp_mail($to,$subject,$message,$headers); 
        if($mail){
              echo "Email Sent Successfully";
            }
        $message2 = "This is a confirmation email - JTB Australia has received your Agent JR Pass booking by email.<br /><br />" . $message;
      $mail2 = wp_mail($email,$subject2,$message2,$headers); 
        if($mail){
              echo "Email Sent Successfully";
            }

      remove_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );






}






function callback_send_email_rwc(){
$to = "webmaster.au+rwc@jtbap.com";
//$to = "benjamin_g.au@jtbap.com";
  $email = $_REQUEST['email'];
  $message= $_REQUEST['message'];  

 $captcha = "";
    if (isset($_POST["g-recaptcha-response"])){
        $captcha = $_POST["g-recaptcha-response"];
    }
    if (!$captcha){
        return false;   exit;   die;
    }
    // handling the captcha and checking if it's ok
    $secret = "6LcQZXUUAAAAAGa3iLEHFbcsyNPXOLoh5o7myMLQ";
    $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$captcha."&remoteip=".$_SERVER["REMOTE_ADDR"]), true);

    // if the captcha is cleared with google, send the mail and echo ok.
    if (intval($response["success"]) !== 1) {
        return false;   exit;   die;
    }

  $message = str_replace("[[","<",$message);
  $message = str_replace("]]",">",$message);
  //$message = str_replace("\'","'",$message);
  $message = str_replace("\\'","'",$message);

            if((strpos($message, '.ru/') !== false)||
       ( (  substr($email, -3) == ".ru" ) && (strpos($message, 'и') !== false)  )||
        ( (  substr($email, -3) == ".ru" ) && (strpos($message, 'б') !== false)  )||
        ( (  substr($email, -3) == ".ru" ) && (strpos($message, 'я') !== false)  )||
        ( (  substr($email, -3) == ".ru" ) && (strpos($message, 'д') !== false)  )
    ){
        return false;   exit;   die;
      }

  $subject = "RWC Form: ".  $email ;
  //$subject2 = "Confirmation Agent JR Booking: ".$name." ".$email;
  $headers  = "MIME-Version: 1.0" . "\r\n";
  $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
  $headers .= "From: JTB Mailer <mailer@nx.jtbtravel.com.au>\r\n";
  //$headers .= "Cc: webmaster.au+agentjr@jtbap.com\r\n";
  $headers .= "Reply-To: $email \r\n";
  $headers2  = "MIME-Version: 1.0" . "\r\n";
  $headers2 .= "Content-type:text/html;charset=UTF-8" . "\r\n";
  $headers2 .= "From: JTB Australia <mailer@nx.jtbtravel.com.au>\r\n";
  $headers2 .= "Reply-To: melres.au@jtbap.com\r\n";
  add_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
  $mail = wp_mail($to,$subject,$message,$headers); 
  if($mail){
    //echo "Email Sent Successfully";
    //continue;
  }else{echo " ##email_error_1## ";}

  remove_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
  //echo $sales_id;
  //echo json_encode($sales_id);
  die();
}



function col2_shortcode2($atts, $content = null){
    return '<div class="col-sm-6 col2shortcode">' . do_shortcode($content) . '</div>';
}
function col1_shortcode2($atts, $content = null){
    return '<div class="fullwidth">' . do_shortcode($content) . '</div>';
}
function col3_shortcode2($atts, $content = null){
    return '<div class="col-sm-4 col2shortcode">' . do_shortcode($content) . '</div>';
}


function col2_shortcode3($atts, $content = null){
    return '<div class="col-sm-6 col2shortcode">' . $content . '</div>';
}
function col1_shortcode3($atts, $content = null){
    return '<div class="fullwidth">' . $content . '</div>';
}
function col3_shortcode3($atts, $content = null){
    return '<div class="col-sm-4 col2shortcode">' . $content . '</div>';
}


function callback_send_email_escorted(){

$doc_body = return_output( 'docs/escorted-form.php'); 

$name =  strtoupper($_REQUEST['name']);
$email = $_REQUEST['email'];
$tour_name=strtoupper($_REQUEST['tour2']) ;
$tour_date = $_REQUEST['date'];
//$form_data= $_REQUEST['message'];

$doc_body = str_replace("zznamezz",$name  , $doc_body  );
$doc_body = str_replace("zzdatezz",  $tour_date   , $doc_body  );
$doc_body = str_replace("zztourzz",  $tour_name  , $doc_body  );
$doc_body = str_replace("zzdobzz",  $_REQUEST['dob']   , $doc_body  );
$doc_body = str_replace("zzagezz",  $_REQUEST['age']   , $doc_body  );
$doc_body = str_replace("zzemailzz",  strtolower($email)  , $doc_body  );

$doc_body = str_replace("zzphonezz",  $_REQUEST['phone']   , $doc_body  );
$doc_body = str_replace("zzmobilezz",  $_REQUEST['mobile']   , $doc_body  );
$doc_body = str_replace("zzinsurerzz",  strtoupper($_REQUEST['insurer'])   , $doc_body  );
$doc_body = str_replace("zzpolicyzz", $_REQUEST['policy'] , $doc_body  );
$doc_body = str_replace("zzemergencynamezz", strtoupper($_REQUEST['emergency_name']) , $doc_body  );
$doc_body = str_replace("zzrelationzz", strtoupper($_REQUEST['relation']) , $doc_body  );
$doc_body = str_replace("zzemergencynumberzz", $_REQUEST['emergency_number'] , $doc_body  );
$doc_body = str_replace("zzwanttoseezz", $_REQUEST['want_to_see'] , $doc_body  );
$doc_body = str_replace("zzbedsyesnozz", $_REQUEST['beds_yes_no'] , $doc_body  );
$doc_body = str_replace("zzmealrequirementszz", $_REQUEST['meal_requirements'] , $doc_body  );
$doc_body = str_replace("zzmedicalzz", $_REQUEST['medical'] , $doc_body  );
$doc_body = str_replace("zzwalkkzz", $_REQUEST['walk_5k'] , $doc_body  );
$doc_body = str_replace("zzcarryluggagezz", $_REQUEST['carry_luggage'] , $doc_body  );
$doc_body = str_replace("zzassistancezz", $_REQUEST['assistance'] , $doc_body  );

$filename = str_replace(" ","-",$name);
$filename=preg_replace("/[^A-Za-z0-9 ]/", '', $filename);
$unixtime = dechex(time()+36000);
$filename = $filename . "-" . $unixtime . ".rtf";
$userfile = fopen($filename, "w");
//chmod($filename, 0777 );
fwrite($userfile,$doc_body);
fclose($userfile);

$to = "webmaster.au+rwc@jtbap.com";
//$to = "benjamin_g.au@jtbap.com";
$to = "sydres.au@jtbap.com";
$to = "kate_d.au@jtbap.com";
$to = "minamino_y.au@jtbap.com";
//$to = "benjamin_g.au@jtbap.com";

$subject = $name ." - " . $tour_name." - ".$tour_date  ;
//$subject2 = "Confirmation Agent JR Booking: ".$name." ".$email;
$headers  = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: JTB Mailer <mailer@nx.jtbtravel.com.au>\r\n";
$headers .= "Cc: webmaster.au+escorted@jtbap.com\r\n";
$headers .= "Reply-To: ".$email." \r\n";
$body = "Customer details are attached in an RTF format word file.";

$subject2 = "Escorted Tour Your Details: ".  $email ;
$body2 = "This is a confirmation of your details - attached in an RTF format word file.<br/><br/>JTB Australia - https://www.nx.jtbtravel.com.au";
$headers2  = "MIME-Version: 1.0" . "\r\n";
$headers2 .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers2 .= "From: JTB Mailer <mailer@nx.jtbtravel.com.au>\r\n";
$headers2 .= "Reply-To: sydres.au@jtbap.com \r\n";
add_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
$more_error = "";
/*
$mail = wp_mail($email,$subject2,$body2,$headers2,$filename); //"/jtbtrave/public_html/wp-admin/".
if ($mail){
}else{
  $more_error = " - error-email-2";
}
*/
$mail2 = wp_mail($to,$subject,$body,$headers,$filename); //"/jtbtrave/public_html/wp-admin/".
if($mail2){

}else{echo " ##email_error_1## " . $more_error ;}

remove_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );

unlink ($filename);
  die();
}
//END





function callback_send_email_rwc_travel(){



$name =  strtoupper($_REQUEST['name']);
$email = $_REQUEST['email'];
$message=$_REQUEST['message'] ;
$state=$_REQUEST['state'];

$to = "sydres.au@jtbap.com";
if(($state=="VIC")||($state=="Travel_Agents")){
    $to = "melres.au@jtbap.com";
}
//$to = "benjamin_g.au@jtbap.com";

$message = str_replace("<","",$message);
$message = str_replace(">","",$message);
$message = str_replace("[[img src","<img src",$message);
$message = str_replace("\'","@@##@@",$message);
$message = str_replace("'","",$message);
$message = str_replace("@@##@@","'",$message);
$message = str_replace("/]]"," />",$message);
$message = str_replace("[br /]","<br />",$message);
$message = str_replace("[b]","<b>",$message);
$message = str_replace("[/b]","</b>",$message);

$search_array=["{","}","\\",";","$"];
$message = str_replace($search_array,"-",$message);


$message = str_replace("[","",$message);
$message = str_replace("]","",$message);

$message = str_replace(" - -"," - $",$message);
$message = str_replace("Total price estimate</b><br />","Total price estimate</b><br />$",$message);
$message = str_replace("From -178","From $178",$message);





$body2 = "<img src='https://www.nx.jtbtravel.com.au/wp-content/uploads/2017/10/admin-section.png' style='position:relative;float:right;display:inline-block;width:100px;height:auto;' /><h2>Form confirmation:</h2>" . $message;
$message = "<img src='https://www.nx.jtbtravel.com.au/wp-content/uploads/2017/10/admin-section.png' style='position:relative;float:right;display:inline-block;width:100px;height:auto;' />" . $message;



$subject = "RWC TravelPackage - " . $name ." - " . $email;
$subject2 = "RWC TravelPackage Confirmation - " . $name ." - " . $email;
//$subject2 = "Confirmation Agent JR Booking: ".$name." ".$email;
$headers  = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: JTB Mailer <mailer@nx.jtbtravel.com.au>\r\n";
$headers .= "Cc: webmaster.au+escorted@jtbap.com\r\n";
$headers .= "Reply-To: ".$email." \r\n";


$headers2  = "MIME-Version: 1.0" . "\r\n";
$headers2 .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers2 .= "From: JTB Mailer <mailer@nx.jtbtravel.com.au>\r\n";
$headers2 .= "Reply-To: sydres.au@jtbap.com \r\n";
add_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
$more_error = "";

$mail = wp_mail($email,$subject2,$body2,$headers2);
if ($mail){
}else{
  $more_error = " - error-email-2";
}

$mail2 = wp_mail($to,$subject,$message,$headers); 
if($mail2){

}else{echo " ##email_error_1## " . $more_error ;}

remove_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );


  die();
}
//END




function callback_send_email_rwc2(){

$doc_body = return_output( 'docs/e-rugby-form.php'); 
$customer_block = return_output( 'docs/e-rugby-form3.php'); 
$customer_block2 = return_output( 'docs/e-rugby-form2.php'); 
//customer body - remove  0 - @@@, and @@@ - 999 (regex)

//[[img src='https://www.nx.jtbtravel.com.au/wp-content/uploads/2017/10/admin-section.png' style='position:relative;float:right;display:inline-block;width:100px;height:auto;' /]]

//person 1
$customer_block = str_replace(PHP_EOL, '###newline###', $customer_block);
$customer_block = preg_replace ( "/@@##begin@@##.*?@@@/",  " " , "@@##begin@@##".$customer_block ,1 );
$customer_block = preg_replace ( "/@@@.*?@@##end@@##/",  " " ,  $customer_block . "@@##end@@##" ,1 );
$customer_block = str_replace('###newline###',"\n" , $customer_block);

//non-person-1
$customer_block2 = str_replace(PHP_EOL, '###newline###', $customer_block2);
$customer_block2 = preg_replace ( "/@@##begin@@##.*?@@@/",  " " , "@@##begin@@##".$customer_block2 ,1 );
$customer_block2 = preg_replace ( "/@@@.*?@@##end@@##/",  " " ,  $customer_block2 . "@@##end@@##" ,1 );
$customer_block2 = str_replace('###newline###',"\n" , $customer_block2);


/*

email0: $('#email0').val() , 
phone0: $('#phone0').val() , 
address0: $('#address0').val() , //only person 1  

title0: title0, //list of vals 
name0: name0, //##@@##
midname0: midname0,
lastname0: lastname0,
age0: age0,
DOB
res0: res0,
pass0: pass0 

*/


$email0 = $_REQUEST['email0'];
$phone0 = $_REQUEST['phone0'];
$address0 = $_REQUEST['address0'];
//$form_data= $_REQUEST['message'];

$title0 = $_REQUEST['title0'];
$name0 = $_REQUEST['name0'];
$midname0 = $_REQUEST['midname0'];
$lastname0 = $_REQUEST['lastname0'];
$age0 = $_REQUEST['age0'];
$dob0 = $_REQUEST['dob0'];
$res0 = $_REQUEST['res0'];
$pass0 = $_REQUEST['pass0']; 

$title1 = explode("##@@##", $title0);
$name1 = explode("##@@##", $name0);
$midname1 = explode("##@@##", $midname0);
$lastname1 = explode("##@@##", $lastname0);
$age1 = explode("##@@##", $age0);
$dob1 = explode("##@@##", $dob0);
$res1 = explode("##@@##", $res0);
$pass1 = explode("##@@##", $pass0); //arrays



$filename = "" . strtoupper(preg_replace("/[^A-Za-z0-9 ]/", '', $name1[0])) ."-". strtoupper(preg_replace("/[^A-Za-z0-9 ]/", '', $lastname1[0]));
$filename = str_replace(" ","-",$filename);

$customer_block = str_replace("zzemailzz",  strtolower($email0)   , $customer_block  );
$customer_block = str_replace("zznumberzz",  strtoupper($phone0)   , $customer_block  );
$customer_block = str_replace("zzaddresszz",  strtoupper($address0)   , $customer_block  );

$all_customer_data = "";


for ($i=0; $i < (count($title1)-1) ; $i++) { //start from p2 - add all.
	$temp_customer_block = $customer_block2;

	$temp_customer_block = str_replace("zztitlezz",  strtoupper($title1[$i])   , $temp_customer_block  );
  $temp_customer_block = str_replace("zznumberrzz",  (string)($i+1)   , $temp_customer_block  );
	$temp_customer_block = str_replace("zzfirstzz",  strtoupper($name1[$i])   , $temp_customer_block  );
	$temp_customer_block = str_replace("zzmiddlezz",  strtoupper($midname1[$i])   , $temp_customer_block  );
	$temp_customer_block = str_replace("zzlastzz",  strtoupper($lastname1[$i])   , $temp_customer_block  );
	$temp_customer_block = str_replace("zzagezz",  strtoupper($age1[$i])   , $temp_customer_block  );
  $temp_customer_block = str_replace("zzdobzz",  strtoupper($dob1[$i])   , $temp_customer_block  );
	$temp_customer_block = str_replace("zzresidentzz",  strtoupper($res1[$i])   , $temp_customer_block  );
	$temp_customer_block = str_replace("zzpassportzz",  strtoupper($pass1[$i])   , $temp_customer_block  );

	$all_customer_data .=$temp_customer_block;
}

$all_customer_data .= $customer_block; //insert contact details at end


$doc_body = str_replace("zzcustomerdetailszz",  $all_customer_data  , $doc_body  );




$unixtime = dechex(time()+36000);
$filename = $filename . "-" . $unixtime . ".rtf";
$userfile = fopen($filename, "w");
//chmod($filename, 0777 );
fwrite($userfile,$doc_body);
fclose($userfile);

$to = "webmaster.au+rwc@jtbap.com";

$to = "sydres.au@jtbap.com";
//$to = "benjamin_g.au@jtbap.com";



$subject = "RWC Form: ".  $email0 ;
//$subject2 = "Confirmation Agent JR Booking: ".$name." ".$email;
$headers  = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: JTB Mailer <mailer@nx.jtbtravel.com.au>\r\n";
$headers .= "Cc: webmaster.au+escorted@jtbap.com\r\n";
$headers .= "Reply-To: ".$email0." \r\n";
$body = "Customer T/C form is attached in an RTF format word file.";

//don't send to customer
add_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
$more_error = "";

$mail2 = wp_mail($to,$subject,$body,$headers,$filename); //"/jtbtrave/public_html/wp-admin/".
if($mail2){

}else{echo " ##email_error_1## " . $more_error ;}

remove_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );

unlink ($filename);
  die();

}
//END










function get_jragent_idno(){
    $string = '';
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $max = strlen($characters) - 1;
for ($i = 0; $i < 7; $i++) {
      $string .= $characters[mt_rand(0, $max)];
 }
    $phpid = $string . (string) uniqid();
    global $wpdb;
    $data = array("phpid"=>$phpid,"unixtime"=>time());
    $wpdb->insert( 'wp_jtbau_jragent', $data );
    //$results = $wpdb->get_results( 'SELECT * FROM wp_options WHERE option_id = 1', OBJECT );
    sleep(1);
    $newid = $wpdb->insert_id;
    if(($newid=="")||($newid==null)||($newid==undefined)||($newid==false)||($newid<33300)){ //mySql id create error - send back random string + unix time
        return $phpid . "000" . (string)time();
    }
    return $newid; // mySql auto-incr. Sales ID.
}
//SELECT * FROM 'wp_jtbau_jragent' //phpid - 13 char + 7 rand-char - ;
//autoinc 33300 - phpid 37462842734 - unixtime 1494379730
/*
function cron_clear_agentjr_sql(){
    global $wpdb;

}
*/


function wpdocs_set_html_mail_content_type() {
    return 'text/html';
} //send customer email as HTML email



function my_plugin_editor_style( $mce_css ){
    //remove_editor_styles();
    //remove_theme_support( 'post-formats' );
    //add_editor_style(  '../../plugins/jtb-australia-cust/css/editor-style.css' );

////$mce_css .= ', ' . plugins_url( 'css/editor-style.css', __FILE__ );
    return $mce_css;
}

/*
 * CUSTOM GLOBAL VARIABLES
 */

//only refresh the sitemap once every 10 min. -do it on page-save
function refresh_sitemap($post_id){// > 600)
    if ($post_id == 156) {
        return_output( 'docs/cron-job-generate-sitemap.php')  ;
        return_output( 'docs/cron-job-jr-price-list.php')  ;
        update_option("sitemap_refresh_timestamp",time());
    }
}

function redirect_reports(){
    $current_user = wp_get_current_user();
    if(($current_user->user_email=='webmaster.au+reports@jtbap.com')||($current_user->user_email=='webmaster.au+report@jtbap.com')||($current_user->user_email=='helpdesk.au+3@jtbap.com')||($current_user->user_email=='helpdesk.au@jtbap.com')){
        wp_redirect( 'https://report.nx.jtbtravel.com.au' ,   307 );
        exit;
    }
}


function add_content_editor() {
    add_post_type_support( 'post', 'editor' );
}

function my_global_vars() {
    global $post_id2;
    $wtnerd = array(
        'id'  => get_the_ID(),
    );
}

function custom_checkboxes($classes){
    $value = get_post_meta(get_the_ID(), '_tour_itinerary_no_images', true);
    if ($value == "1"){
        $classes[]="itinerary-no-images";
    }//$classes[]="itinerary-no-imagesxx"."-".$post_id."-".$value;
    return $classes;
}

function create_tour_checkbox(){
    $post_id = get_the_ID();
  
    if (get_post_type($post_id) != 'page') {
        return;
    }

    $img_checkbox = get_post_meta($post_id, '_tour_itinerary_no_images', true);
    $accent_col = get_post_meta($post_id, '_accent_colour_dropdown', true);
    $hide_search = get_post_meta($post_id, '_hide_from_search', true);
    wp_nonce_field('my_custom_nonce_'.$post_id, 'my_custom_nonce');
    ?>
    <div class="misc-pub-section misc-pub-section-last">
        <label><input type="checkbox" value="1" <?php checked($img_checkbox, true, true); ?> name="_tour_itinerary_no_images" /><?php _e('Show tour itinerary with no images', 'pmg'); ?></label>

        <p><strong>Theme accent colour</strong></p>
        <?php $option_values = array("default-blue","pink","orange","green"); ?>
        <select name="_accent_colour_dropdown" >
            <?php
                foreach($option_values as $key => $value) 
                {
                    if($value == $accent_col)
                    { ?>
                            <option selected><?php echo $value; ?></option>
                    <?php }
                    else
                    { ?>
                            <option><?php echo $value; ?></option>
                    <?php }
                } ?>
        </select>
        <p><label><input type="checkbox" value="1" <?php checked($hide_search, true, true); ?> name="_hide_from_search" /><?php _e('Hide page from search and sitemap', 'pmg'); ?></label></p>
    </div>
    <?php
}


function save_ghib_cal($post_id){
//save meta for Ghibli cal.


    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (
        !isset($_POST['my_custom_nonce']) ||
        !wp_verify_nonce($_POST['my_custom_nonce'], 'my_custom_nonce_'.$post_id)
    ) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

if($post_id==30203){ 
    $data2 =   file_get_contents('https://www.nx.jtbtravel.com.au/wp-content/plugins/jtb-australia-cust/docs/print-ghib-run.php');
    update_post_meta($post_id, '_ghib_cal', $data2);
}


}

function save_tour_checkbox($post_id){




    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (
        !isset($_POST['my_custom_nonce']) ||
        !wp_verify_nonce($_POST['my_custom_nonce'], 'my_custom_nonce_'.$post_id)
    ) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }


    if (isset($_POST['_tour_itinerary_no_images'])) {
        update_post_meta($post_id, '_tour_itinerary_no_images', $_POST['_tour_itinerary_no_images']);
    } else {
        delete_post_meta($post_id, '_tour_itinerary_no_images');
    }
    if (isset($_POST['_accent_colour_dropdown'])) {
        update_post_meta($post_id, '_accent_colour_dropdown', $_POST['_accent_colour_dropdown']);
    } else {
        delete_post_meta($post_id, '_accent_colour_dropdown');
    }
        if (isset($_POST['_hide_from_search'])) {
        update_post_meta($post_id, '_hide_from_search', $_POST['_hide_from_search']);
    } else {
        delete_post_meta($post_id, '_hide_from_search');
    }
}


function wp_search_filter( $query ) {
    $hideSearch = get_option("jtbau_hidden_pages_array");
    if ( ! $query->is_admin && $query->is_search && $query->is_main_query() ) {
        $query->set( 'post__not_in',  $hideSearch );
    }
}

function remove_inline_styles( $content ) {
    $content = preg_replace('#style=".*?"#i', ' ', $content);
    $content = preg_replace("#style='.*?'#i", ' ', $content);
    return $content;
}

function print_jr_footer(){ 
    global $wp_query; 
    $post_id = $wp_query->post->ID; 
    if ($post_id==3338 || wp_get_post_parent_id( $post_id )==3338 || !$post_id){
        echo return_output( 'docs/print-jr-footer.php');
    }
    //echo $post_id;
}


function tag_manager_setup_prnt(){

echo <<<EOL


<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:400,400i,700,700i|Material+Icons|PT+Mono+Rubik:ital,wght@0,400;0,500;1,400;1,500&display=swap" rel="stylesheet">

EOL;

if(is_page(2875)||is_page(2877)||is_page(2871)){

echo <<<EOL

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-11084913492"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'AW-11084913492');
</script>



<!-- Event snippet for Purchase (1) conversion page --> <script> gtag('event', 'conversion', { 'send_to': 'AW-11084913492/wXb0CPL_j48YENS22aUp', 'transaction_id': '' }); </script>

<!-- Event snippet for Purchase JR conversion page -->
<script>
  gtag('event', 'conversion', {
      'send_to': 'AW-604202231/u6UICKyd-ZAYEPfJjaAC',
      'value': 1.0,
      'currency': 'AUD',
      'transaction_id': ''
  });
</script>



EOL;


}	
	
	
	
echo <<<EOL

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-T6JTF6F');gtag('config', 'AW-11084913492');</script>
<!-- End Google Tag Manager -->

<link rel="shortcut icon" href="https://www.nx.jtbtravel.com.au/favicon.ico" />
<link rel="shortcut icon" type="image/x-icon" href="https://www.nx.jtbtravel.com.au/favicon.ico">

<link rel="icon" sizes="192x192" href="https://www.nx.jtbtravel.com.au/favicon.png">  
<link rel="icon" sizes="128x128" href="https://www.nx.jtbtravel.com.au/favicon.png">
<link rel="apple-touch-icon" sizes="128x128" href="https://www.nx.jtbtravel.com.au/favicon.png">
<link rel="apple-touch-icon-precomposed" sizes="128x128" href="https://www.nx.jtbtravel.com.au/favicon.png">


<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-3JKVZH4QN8"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-3JKVZH4QN8');
</script>

EOL;

 

}


function wpdocs_custom_excerpt_length( $length ) {
    return 66;
}

function print_jr_links(){

   // if(is_page(3343)||is_page(3434)){//JR Warning - emergency - Flooding
   //     echo return_output( 'docs/print-jr-notice-warning.php') ;
   // }
    
    echo '<div id="jranchors" class="temphide"></div>';
}


/** Step 1. */
function my_plugin_menu(){
    add_options_page( 'JTB Australia Options', 'JTB Shortcodes', 'edit_pages', 'jtbau-options', 'jtbau_options_page' );
}

function col2_shortcode($atts,$content = null){
    $temp = '<div class="brochurerequest"><div class="col-xs-12 col-sm-6"><div class="row">';
    $temp .= $content;
    $temp .= '</div></div>';
    return str_replace('[2colmid]', '</div></div><div class="col-xs-12 col-sm-6"><div class="row"></div>', $temp) ;
}



function mycustom_wpcf7_form_elements( $form ) {
$form = do_shortcode( $form );
return $form;
}

function cruise_shortcode($atts){
    $at = shortcode_atts( array(
        'n' => '1',
        ), $atts );
    $gal = $at['n'];
    $temp = "";
    $img11 = "";
    $alt11="";
    $paddtop="";
    if ($gal!="1"){
        $paddtop=" marginboth";
    }
    $temp .= '<div class="  cruise_group'.$paddtop.'">';
    $counter=0;
    $counterlvl2=0;
    $cmod=0;

    while ( have_rows('cruise_group') ) : the_row();
        $counterlvl2 +=1;
        if ($gal != $counterlvl2){
          continue;
        }

        $temp .= '<div class="row">';
 


$c=0;
$clear="";
while ( have_rows('cruise_product') ) : the_row();

$c += 1;
if ($c%2==0){
  $clear=' clear-right';
}else{
  $clear=' clear-left';
}
$temp .= '<div class="col-sm-6 col-xs-12'.$clear.'">';
$temp .= '<div class="cruise-cell">';
$temp .='<div class="col-xs-12 col-sm-6">';
$temp .= '<img src="' . get_sub_field('map') . '" />';
$temp .= get_sub_field('prices');

$temp .='</div><div class="col-xs-12 col-sm-6">';
$temp .= get_sub_field('description');

$temp .='</div>';
$temp .= '</div>';
$temp .= '</div>';
endwhile; 



        $temp .= '</div>';
    endwhile; 

    $temp .= '</div>';
    //wp_reset_query(); 
    return $temp;
}


 





function gallery3_shortcode($atts){
    $at = shortcode_atts( array(
        'n' => '1',
        ), $atts );
    $gal = $at['n'];
    $temp = "";
    $img11 = "";
    $alt11="";
    $paddtop="";
    if ($gal!="1"){
        $paddtop=" marginboth";
    }
    $temp .= '<div class="row image3list'.$paddtop.'">';
    $counter=0;
    $counterlvl2=0;
    $cmod=0;

    while ( have_rows('gallery') ) : the_row();
        $counterlvl2 +=1;
        while ( have_rows('3img') ) : the_row();
            if( (string) $counterlvl2 != $gal){
                continue; //if this is gallery 2, print only 2nd in the loop ~ 
            }
            $imgtemp=get_sub_field('img3');
            $img11 = $imgtemp['url'];
            if ($img11 == ''){
                $img11 = 'https://www.nx.jtbtravel.com.au/wp-content/uploads/2017/01/blank-tour-jtb-image-gallery.jpg';
                }
            $alt11 = $imgtemp['alt'];
            if($alt11 == ''){
                $alt11 = 'JTB Australia Tour';
            } 
            $counter +=1;
            $cmod = (($counter-1)%3)+1;
            $temp .= '<div class="col-sm-4 imageset'.$cmod.'"><div class="post"><div class="entry">';
            $temp .= '<img src="'.$img11.'" alt="'.$alt11.'"';
            if ($alt11 != 'JTB Australia Tour')
                { $temp .= 'title="'.$alt11.'" ';}
            $temp .= '>';
            if(get_sub_field('caption')!=""){
                $temp .= '<div class="caption">'.get_sub_field('caption').'</div>';
            }
            $temp .= '</div></div></div>';
        endwhile; 
    endwhile; 

    $temp .= '</div>';
    //wp_reset_query(); 
    return $temp;

}


function popup_shortcode($atts,$content = null){
    $at = shortcode_atts( array(
        'n' => 'Popup Box',
        't' => 'button',
        'm' => '1'
        ), $atts );
    $id = $at['m'];
    $x1= '<!-- Trigger/Open The Modal -->';
    if($at['t']=="link"){
        $x1 .= '<a id="wppopup'.$id.'">'.$at['n'].'</a>';
    }elseif ($at['t']=="blank") {
        
    }else  {
        $x1 .= '<button id="wppopup'.$id.'">'.$at['n'].'</button>';
    }
    $x1 .= '<!-- The Modal --><div id="myModal-'.$id.'" class="modal">';
    $x1 .= '<!-- Modal content --> <div class="modal-content"> <span class="close">×</span>';
    $x1 .= $content . ' </div></div> ' ;
    return $x1;
}



function icon_shortcode($atts){
    $at = shortcode_atts( array(
        'n' => 'blank',
        'w' => '50'
        ), $atts );
    $width="";
    if($at["w"]!="50"){
        $width=' style="width:'.$at['w'].'px;height:auto;" ';
    }
    $txt = "";
    $icons =  array(
        array('recommends','recommends.svg','Recommended by JTB'),
        array('culture','culture.svg','History and Culture'),
        array('bus','bus.svg','Bus transfer'),
        array('train','train.svg','Transfer by train'),
        array('walking','walking.svg','Walking'),
        array('cycling','cycling.svg','Cycling'),
        array('cruise','cruise.svg','Cruise'),
        array('experience','experience.svg','Experience'),
        array('meal','meal.svg','Meal'),
        array('guide','guide.svg','Guide'),
        array('heritage','heritage.svg','World Heritage site'),
        array('onsen','onsen.svg','Hot spring/ onsen'),
        array('vego','vego.svg','Vegetarian meal option'),
        array('hotel','hotel.svg','Hotel'),
        array('luggage','luggage.svg','Luggage transfer'),
        array('ryokan','ryokan.svg','Traditional Japanese Ryokan Inn'),
        array('na','na.svg','N/A')
    );//return "s";
    foreach ($icons as $a) {
        if($at["n"]==$a[0] ){
            $txt= '<img class="icon" '.$width.' title="'.$a[2].'" alt="'.$a[2].'" src="https://www.nx.jtbtravel.com.au/wp-content/uploads/svg/icons/'.$a[1].'" />';
        }
    }
    return $txt;
}

function print_all_icon_shortcode(){
    $txt="";
    $icons = array(
        array('recommends','recommends.svg','Recommended by JTB'),
        array('culture','culture.svg','History and Culture'),
        array('bus','bus.svg','Bus transfer'),
        array('train','train.svg','Transfer by train'),
        array('walking','walking.svg','Walking'),
        array('cycling','cycling.svg','Cycling'),
        array('cruise','cruise.svg','Cruise'),
        array('experience','experience.svg','Experience'),
        array('meal','meal.svg','Meal'),
        array('guide','guide.svg','Guide'),
        array('heritage','heritage.svg','World Heritage site'),
        array('onsen','onsen.svg','Hot spring/ onsen'),
        array('vego','vego.svg','Vegetarian meal option'),
        array('hotel','hotel.svg','Hotel'),
        array('luggage','luggage.svg','Luggage transfer'),
        array('ryokan','ryokan.svg','Traditional Japanese Ryokan Inn'),
        array('na','na.svg','N/A')
    );
    foreach ($icons as $a ) {
        $txt = $txt . '<div style="width: 180px;    position: relative;    float: left;    margin: 0 0 50px;">   <img class="icon" style="width:50px;height:auto;" title="'.$a[2].'" alt="'.$a[2].'" src="https://www.nx.jtbtravel.com.au/wp-content/uploads/svg/icons/'.$a[1].'" /> <input autocomplete="off" onclick="this.select()"  value=\'[icon n="'. $a[0].'"]\'  style="width:150px;">  </div>';
    }
    return $txt;
}
  

// CUSTOM FUNCTIONS //


function print_header_message(){ // ben message - disable for live 
    //if message, print - add a CSS file which shifts the pos absolute phone number and the mobile menu 
    $current_user = wp_get_current_user(); 
    if( $current_user->user_email == "benjamin_g.au@jtbap.com"){
    
		/*
<div class="header_message_jtb" id="header_message_jtb_head" >
<div class="collective container">
@@@ - test jtb
</div></div>
*/
     
    }
}



function print_head(){
if( !  ( strpos($_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'], 'www.nx.jtbtravel.com.au') !== false)   ){
    echo '<link rel="shortcut icon" href="/favicon2.ico" /><link rel="icon" href="/favicon2.ico" /><link rel="icon" sizes="32x32"  href="/favicon2.ico" /><link rel="icon" href="/favicon2.ico" sizes="192x192"  />';
}else{ 
    echo '<link rel="shortcut icon" href="/favicon.ico" /><link rel="icon" href="/favicon.ico" /><link rel="icon"  sizes="32x32" href="/favicon.ico" /><link rel="icon" href="/favicon.ico"  sizes="192x192" />';
}

//test page head
if(is_page(23199)){
 //echo "<script type='text/javascript' src='https://pushka.com/p/wp-content/plugins/WPushka/js/old/cross-origin-js-test.js'></script>";
}

}//end print head

function return_output($file){
    ob_start();
    include $file;
    return ob_get_clean();
}

function print_flt_home_data(){
    echo  return_output( 'docs/print-flight-home.php')  ;
}
function norinchukin_export(){
  if(is_page(23498)){
    echo  return_output( 'docs/print-norinchukin-export.php')  ;
  }
}
function after_body_print(){
    echo  return_output( 'docs/print-after-body.php')  ;
}
function after_body_popups(){
    echo  return_output( 'docs/print-popup-boxes.php')  ;
}
function top_page_messages_print(){
    echo  return_output( 'docs/print-top-page-messages.php')  ;
}
function social_buttons_print(){
    echo return_output( 'docs/print-social-buttons.php');
}
function jtb_home_prnt(){
    echo  return_output( 'docs/print-jtb-home.php')  ;
}
function contact_page_prnt(){
    echo  return_output( 'docs/print-contact-page.php')  ;
}
function about_tc_prnt(){
    echo  return_output( 'docs/print-about-tc-template.php')  ;
}
function default_template_prnt(){
    echo  return_output( 'docs/print-jtb-default-template.php')  ;
}
function drive_tour_prnt(){
    echo  return_output( 'docs/print-drive-tour.php')  ;
}
function day_tour_data_prnt(){
    echo  return_output( 'docs/print-day-tour-data.php')  ;
}
function post_reviews_prnt(){
    echo  return_output( 'docs/print-post-reviews.php')  ;
}
function post_article_prnt(){
    echo  return_output( 'docs/print-post-article.php')  ;
}
function post_tag_prnt(){
    echo  return_output( 'docs/print-post-tag.php')  ;
}
function tickets_prnt(){
    echo  return_output( 'docs/print-tickets.php')  ;
}
function tickets_top_prnt(){
    echo  return_output( 'docs/print-tickets-top.php')  ;
}
function product_second_lvl_prnt(){
    echo  return_output( 'docs/print-products-second-level.php')  ;
}
function itinerary_messagebox_prnt(){
    echo  return_output( 'docs/print-itinerary-messagebox.php')  ;
}

function category_reviews_prnt(){
    echo  return_output( 'docs/print-category-reviews.php')  ;
}
function search_prnt(){
    echo  return_output( 'docs/print-search.php')  ;
}
function tours_top_custom_prnt(){
    echo  return_output( 'docs/print-tours-top-custom.php')  ;
}
function tours_template_custom_prnt(){
    echo  return_output( 'docs/print-tours-template-custom.php')  ;
}
function itinerary_buttons_prnt(){
    echo  return_output( 'docs/print-itinerary-buttons.php')  ;
}
function jr_partial_prnt(){
    echo  return_output( 'docs/print-jr-partial.php')  ;
}
function jtb_footer_prnt(){ //change the search popup social ID
    $temp = return_output( 'docs/print-jtb-footer.php')  ;
    $temp = str_replace("websearchpopup", "websearchpopup2", $temp);
    $temp = preg_replace('/<div class="hearderwarning".*?<\/div>/', "", $temp);
    echo  $temp;
}


function jtbau_options_page() { //jtb clstik adkeh pagn wetd sdirtcidns
    if ( !current_user_can( 'edit_pages' ) )  { //admin and etitors
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    echo return_output( 'docs/jtb-admin-page.php');
}



 

function jtbau_options_page_bar_menu( $wp_admin_bar ) {
    $args = array(
        'id'    => 'admin_bar_menu_jtb_au',
        'title' => 'JTB Shortcodes',
        'href'  => '/wp-admin/options-general.php?page=jtbau-options',
        'meta'  => array( 'class' => 'my-toolbar-page' ,'target' => '_blank','title' => 'Opens in a new tab.')
    );
    $wp_admin_bar->add_node( $args );
}


function my_login_logo() {
    wp_enqueue_style ( 'z-login-customise', plugin_dir_url( __FILE__ ) .'css/login-a-global.css' );
    wp_enqueue_style ( 'z-login-jtb', plugin_dir_url( __FILE__ ) .'css/login-jtb.css' );

if( !  ( strpos($_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'], 'www.nx.jtbtravel.com.au') !== false)   ){
 wp_enqueue_style ( 'z-login-jtb-test', plugin_dir_url( __FILE__ ) .'css/login-jtb-test.css' );
}

}


function jtb_admin_custom_css() {
    $user = wp_get_current_user();
    if($user && isset($user->user_login) && 'BenjaminGibiec' == $user->user_login){
        //wp_enqueue_style( 'wp-admin-user-style-admin.css', plugin_dir_url( __FILE__ ) .'css/wp-admin-user-style-admin.css' );
    }
    else{
        //wp_enqueue_style( 'wp-admin-user-style-user.css', plugin_dir_url( __FILE__ ) .'css/wp-admin-user-style-user.css' );
    }
    //wp_enqueue_style( 'wp-admin-user-style-global.css', plugin_dir_url( __FILE__ ) .'css/wp-admin-user-style-global.css' );
if( !  ( strpos($_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'], 'www.nx.jtbtravel.com.au') !== false)   ){
 //wp_enqueue_style( 'wp-admin-user-style-test.css', plugin_dir_url( __FILE__ ) .'css/wp-admin-test-site.css' );
}

}


function sim_shortcode($atts){
    $at = shortcode_atts( array( 
    'name1' => 'Data Only',
    'price1' => '49',
    'expiry1' => '30 Days',
    'data1' => '3GB',
    'network1' => 'docomo 3G/LTE',
    'features1' => 'Internet',
    'name2' => 'Data & Call',
    'price2' => '59',
    'expiry2' => '1 Month',
    'data2' => '110MB/day',
    'network2' => 'docomo 3G/LTE',
    'features2' => 'Inernet, Call'
        ), $atts ); 
    $outputx = '';


$outputx .= '<div class="sim-div">';
    $outputx .= '<div class="thirdscol header"><p>'.$at["name1"].'</p> <span><i>$</i></span> <span>'.$at["price1"].'</span></div>';
    $outputx .= '<div class="thirdscol header"><img src="https://www.nx.jtbtravel.com.au/wp-content/uploads/svg/sim-card.svg" /></div>';
    $outputx .= '<div class="thirdscol header"><p>'.$at["name2"].'</p> <span><i>$</i></span> <span>'.$at["price2"].'</span></div>';

    $outputx .= '<div class="thirdscol">'.$at["expiry1"].'</div>';
    $outputx .= '<div class="thirdscol middle">Expiry</div>';
    $outputx .= '<div class="thirdscol">'.$at["expiry2"].'</div>';

    $outputx .= '<div class="thirdscol">'.$at["data1"].'</div>';
    $outputx .= '<div class="thirdscol middle">Data</div>';
    $outputx .= '<div class="thirdscol">'.$at["data2"].'</div>';

    $outputx .= '<div class="thirdscol">'.$at["network1"].'</div>';
    $outputx .= '<div class="thirdscol middle">Network</div>';
    $outputx .= '<div class="thirdscol">'.$at["network2"].'</div>';

    $outputx .= '<div class="thirdscol">'.$at["features1"].'</div>';
    $outputx .= '<div class="thirdscol middle">Features</div>';
    $outputx .= '<div class="thirdscol">'.$at["features2"].'</div>';

$outputx .= '</div>';

    return $outputx;
}







function sim_shortcode2($atts){
    $at = shortcode_atts( array( 
    'name1' => 'Data Only',
    'price1' => '49',
    'expiry1' => '30 Days',
    'data1' => '3GB',
    'network1' => 'docomo 3G/LTE',
    'features1' => 'Internet',
    'name2' => 'Data & Call',
    'price2' => '59',
    'expiry2' => '1 Month',
    'data2' => '110MB/day',
    'network2' => 'docomo 3G/LTE',
    'features2' => 'Inernet, Call'
        ), $atts ); 
    $outputx = '';


$outputx .= '<div class="sim-div2">';
    $outputx .= '<div class="thirdscol header"><p>'.$at["name1"].'</p> <span><i>$</i></span> <span>'.$at["price1"].'</span></div>';
    $outputx .= '<div class="thirdscol header"><img src="https://www.nx.jtbtravel.com.au/wp-content/uploads/svg/sim-card.svg" /></div>';
 

    $outputx .= '<div class="thirdscol">'.$at["expiry1"].'</div>';
    $outputx .= '<div class="thirdscol middle">Expiry</div>';
 

    $outputx .= '<div class="thirdscol">'.$at["data1"].'</div>';
    $outputx .= '<div class="thirdscol middle">Data</div>';
 

    $outputx .= '<div class="thirdscol">'.$at["network1"].'</div>';
    $outputx .= '<div class="thirdscol middle">Network</div>';
 

    $outputx .= '<div class="thirdscol">'.$at["features1"].'</div>';
    $outputx .= '<div class="thirdscol middle">Features</div>';
 

$outputx .= '</div>';

    return $outputx;
}


function is_tree($pid) {      // $pid = The ID of the page we're looking for pages underneath
    global $post;         // load details about this page
    if(is_page()&&($post->post_parent==$pid||is_page($pid))){
	return true; }  // we're at the page or at a sub page
    else{
	return false; } // we're elsewhere
}


function booking_close_settings(){
    $array=CloseTPbookingsSettings();
    if (!get_option('close_bookings_jtb')){
        add_option('close_bookings_jtb', $array);
    }else{
        update_option("close_bookings_jtb", $array);
    }
}
////
function booking_close_redirect(){
	
$array=get_option('close_bookings_jtb');
date_default_timezone_set('Australia/Sydney');
$nowTime = strtotime(date('m/d/Y h:i:s a', time()));

$bookCloseUNIX = date($array['autoStartBookingClose']);
$bookClose = strtotime(substr($bookCloseUNIX,0,10)." ".substr($bookCloseUNIX,11,2).":".substr($bookCloseUNIX,-2));

$bookOpenUNIX = date($array['autoFINISHBookingClose']);
$bookOpen = strtotime(substr($bookOpenUNIX,0,10)." ".substr($bookOpenUNIX,11,2).":".substr($bookOpenUNIX,-2));
if (($array['closeBookingsNow'] == 1) || (($nowTime<$bookOpen)&&($nowTime>$bookClose)) ){
	if( is_page( 3392  ) || is_page( 3394  )   ){
		wp_redirect( 'https://www.nx.jtbtravel.com.au/?booking=closed' );
		exit();
	}
}


/*
// CLOSE ALL JR PAGES |||||| template-redirect jr page and not admin
global $current_user;
wp_get_current_user() ;
 //get_currentuserinfo();
//if(($current_user->user_email == "benjamin_g.au@jtbap.com")||($current_user->user_email == "chikanobu_t.auZZZ@jtbap.com")){
//! current_user_can('editor') 
if ( ( (time()+1) > 1695909212 ) && //close midnight thurs 28th sep.
//( (time()+1) < 1696201212 ) && // Monday 9am - if Lorna fix the thing 
( (time()+1) < 1696298412 ) && // Tues 9am - if Lorna fix on MON 
 

//( $current_user->user_email != "benjamin_g.au@jtbap.com" )   && 
 ( $current_user->user_email != "benjamin_g.au@jtbap.com" ) &&
 ( $current_user->user_email != "kate_d.au@jtbap.com" ) &&
 ( $current_user->user_email != "lorna.withell@pa.tourplan.com" ) &&
 
( 
is_tree(3338 )//is jr page 
) ) {
	wp_redirect( 'https://www.nx.jtbtravel.com.au/jr-passes-closed/' );
	exit();
}
*/


} // end booking close redirect / page redirection 


function jtb_widget_print($atts){  
    //jtb-widget f="test"] 
    $at = shortcode_atts( array( 
        'f' => '1',
        'n' => 'blank'
        ), $atts ); 
    $jtbfunction = $at["f"];
    $namedata = $at["n"];
    $outputx = '';

if($jtbfunction=="test"){
    $outputx .= return_output( 'docs/print-test.php')  ;
}
    else if($jtbfunction=="x1"){ //@@@@@   jtb-widget f="test"]
        $outputx .= " "; //
    }else if($jtbfunction=="jr-tc"){ //@@@@@   jtb-widget f="jr-tc"]
		//34329 - national agreement page 
		if(is_page(34329)){
			$outputx .= get_post(34588)->post_content; //
					$outputx .= '<br /><br />';
		  if( current_user_can('editor') || current_user_can('administrator') ){
        	$outputx .= '<br /><a class="button" href="https://www.nx.jtbtravel.com.au/wp-admin/post.php?post=34588&action=edit" target="blank">Edit</a> <small>[only visible for admin login accounts]</small>';
             }
		}else{//is regional pass
			$outputx .= get_post(34629)->post_content; //
					$outputx .= '<br /><br />';
		  if( current_user_can('editor') || current_user_can('administrator') ){
        	$outputx .= '<br /><a class="button" href="https://www.nx.jtbtravel.com.au/wp-admin/post.php?post=34629&action=edit" target="blank">Edit</a> <small>[only visible for admin login accounts]</small>';
             }
		}
      
		//print jr text and edit button 
    }else if($jtbfunction=="acom-hotel"){ //   jtb-widget f="acom-hotel"]
        $outputx .= return_output( 'docs/a-roobix-hotels.php');  
    }else if($jtbfunction=="acom-hotel-apt"){ //    jtb-widget f="acom-hotel-apt"] jtb-widget f="acom-hotel"]
        $outputx .= return_output( 'docs/a-roobix-hotel-apt.php');
    }else if($jtbfunction=="print-newsletter-sub-data"){
        $outputx .= print_subscribe_to_newsletter();
    }else if($jtbfunction=="print-custom-php"){
 $outputx .= $namedata;
//jtb-widget f="print-custom-php" n=""]

    }else if($jtbfunction=="print-sim-button"){
        $outputx .= '<a   onclick="document.getElementById(\'tabbutton2\').click();">click here</a>' ;
    }else if($jtbfunction=="print-guided-tours"){
        $outputx .= return_output( 'docs/print-guided-tours.php'); // jtb-widget f="test"]
    }else if($jtbfunction=="tp-nx-loader"){//@@@
        $outputx .= return_output( 'docs/print-tp-nx-loader.php'); // jtb-widget f="tp-nx-loader"]
    }else if($jtbfunction=="print-fuji-tours2"){
        $outputx .= return_output( 'docs/print-fuji-tours.php'); // jtb-widget f="print-fuji-tours2"]
    }else if($jtbfunction=="print-payway-form"){
        $outputx .= return_output( 'docs/print-payway-form.php'); // jtb-widget f="test"]   
    }else if($jtbfunction=="xxxx2"){//jtb-widget f="xxxx"]
        $outputx .= return_output( 'docs/print-.php');
    }else if($jtbfunction=="a-roobix-home"){//jtb-widget f="a-roobix-home"]
        $outputx .= return_output( 'docs/a-roobix-home.php');
    }else if($jtbfunction=="roobix-footer"){//jtb-widget f="a-roobix-footer"]
        $outputx .= return_output( 'docs/a-roobix-footer.php');
    }else if($jtbfunction=="roobix-footer2"){//jtb-widget f="a-roobix-footer2"]
        $outputx .= return_output( 'docs/a-roobix-footer2.php');
    }else if($jtbfunction=="jr-tcs-part-1"){//jtb-widget f="jr-tcs-part-1"]
        $outputx .= return_output( 'docs/print-jr-tcs-part-1.php');
    }else if($jtbfunction=="copy-protect-3-img"){//jtb-widget f="copy-protect-3-img"]
        $outputx .= return_output( 'docs/print-copy-protect-3-img.php');
    }else if($jtbfunction=="print-accreditation"){
        $outputx .= return_output( 'docs/print-accreditation.php'); // jtb-widget f="test"]
    }else if($jtbfunction=="jr-pass-chart"){
        $outputx .= return_output( 'docs/print-jr-booking-flow.php'); // jtb-widget f="test"]
    }else if($jtbfunction=="jr-pass-chart2024"){
        $outputx .= return_output( 'docs/print-jr-booking-flow2024.php'); // jtb-widget f="test"]
    }else if($jtbfunction=="jr-pass-chart2"){
        $outputx .= return_output( 'docs/print-jr-booking-flow2.php'); // jtb-widget f="test"]
    }else if($jtbfunction=="jr-flowchart-4-jr-east-only"){
        $outputx .= return_output( 'docs/print-jr-flowchart-4.php'); // jtb-widget f="jr-flowchart-4"]
    }else if($jtbfunction=="jr-flowchart-5-jr-east-only"){
        $outputx .= return_output( 'docs/print-jr-flowchart-5.php'); // jtb-widget f="jr-flowchart-5"]
    }else if($jtbfunction=="print-sim-card-data"){
        $outputx .= return_output( 'docs/print-sim-card-data.php'); // jtb-widget f="test"]
    }else if($jtbfunction=="contact-form"){
        $outputx .= return_output( 'docs/print-contact-form-tours.php'); // jtb-widget f="test"]
    }else if($jtbfunction=="ghibli-booking-form"){
        $outputx .= return_output( 'docs/print-ghibli-booking-form.php'); // jtb-widget f="test"]
    }else if($jtbfunction=="sitemap"){
        $outputx .= return_output( 'docs/print-sitemap.php'); // jtb-widget f="test"]
    }else if($jtbfunction=="jr-train-yen-list"){
        $outputx .= return_output( 'docs/print-jr-yen-train-table.php'); // jtb-widget f="jr-train-yen-list"]
    }else if($jtbfunction=="print-page-top-link"){
        $outputx .= return_output( 'docs/print-page-top-link.php'); // jtb-widget f="test"]
    }else if($jtbfunction=="ghibli-price-load"){
        $outputx .= return_output( 'docs/ghibli-price-loader.php'); // jtb-widget f="test"]
        //srb=Tickets&productid=20&supplierid=15
    }else if($jtbfunction=="disney-selector"){
        $outputx .= return_output( 'docs/disney-selector.php'); // jtb-widget f="test"]
    }else if($jtbfunction=="flights"){
        $outputx .= return_output( 'docs/print-flight-box.php'); // jtb-widget f="flights"]
    }else if($jtbfunction=="ghib-cal-print"){
        $outputx .= get_post_meta(30203,'_ghib_cal')[0]; // jtb-widget f="ghib-cal-print"]
        if( current_user_can('editor') || current_user_can('administrator') ){
        	$outputx .= '<br /><a class="button" href="https://www.nx.jtbtravel.com.au/wp-admin/post.php?post=30203&action=edit#acf-group_5822553bf0549" target="blank">Edit</a>';
        }
    }else if($jtbfunction=="agent-jr-form"){
        $outputx .= return_output( 'docs/print-agent-jr-form.php'); // jtb-widget f="test"]
    }else if($jtbfunction=="agent-jr-form-2023"){
        $outputx .= return_output( 'docs/print-agent-jr-form-2023.php'); // jtb-widget f="test"]
    }else if($jtbfunction=="rail-p2p-2024"){
        $outputx .= return_output( 'docs/print-rail-p2p-2024.php'); // jtb-widget f="rail-p2p-2024"]
    }else if($jtbfunction=="agent-jr-form-g-sheets"){
        $outputx .= return_output( 'docs/print-agent-jr-form-g-sheets.php'); // jtb-widget f="agent-jr-form-g-sheets"]
    }else if($jtbfunction=="agent-jr-form2"){
        $outputx .= return_output( 'docs/print-agent-jr-form2.php'); // jtb-widget f="test"]
    }else if($jtbfunction=="usj-form"){
        $outputx .= return_output( 'docs/print-usj-express.php'); // jtb-widget f="usj-form"]
    }else if($jtbfunction=="usj-express-g-sheets"){
        $outputx .= return_output( 'docs/print-usj-express-g-sheets.php'); // jtb-widget f="usj-express-g-sheets"]
    }else if($jtbfunction=="usj-form2"){
        $outputx .= return_output( 'docs/print-usj-express2.php'); // jtb-widget f="usj-form2"]
    }else if($jtbfunction=="usj-date"){
        $outputx .= return_output( 'docs/print-usj-date.php'); // jtb-widget f="usj-date"]
    }else if($jtbfunction=="usj-cal"){
        $outputx .= return_output( 'docs/print-usj-cal.php'); // jtb-widget f="usj-cal"]
    }else if($jtbfunction=="ghibli-cal"){
        $outputx .= return_output( 'docs/print-ghibli-cal.php'); // jtb-widget f="ghibli-cal"]
    }else if($jtbfunction=="ewayfreepayment"){
        $outputx .= return_output( 'docs/a-eway-form.php'); // jtb-widget f="test"]
    }else if($jtbfunction=="ewayfreepayment2"){
        $outputx .= return_output( 'docs/a-eway-form2.php'); // jtb-widget f="test"]
    }else if($jtbfunction=="ewayfreepaymentinbound"){
        $outputx .= return_output( 'docs/a-eway-form-inbound.php');// jtb-widget f="test"]
    }else if($jtbfunction=="ewayerror"){
        $outputx .= return_output( 'docs/print-eway-error.php'); // jtb-widget f="test"]
    }else if($jtbfunction=="rwc-t-c"){
        $outputx .= return_output( 'docs/print-rwc-t-c.php'); // jtb-widget f="rwc-t-c"]
    }else if($jtbfunction=="rwc-header"){ 
        $outputx .= return_output( 'docs/print-rwc-header.php'); // jtb-widget f="rwc-header"]
    }else if($jtbfunction=="width50"){ 
        $outputx .= '<div class="width50">'; // jtb-widget f="width50"]
    }else if($jtbfunction=="width502"){ 
        $outputx .= '</div>'; // jtb-widget f="width502"]
    }else if($jtbfunction=="clearboth"){ 
        $outputx .= '<div style="clear:both;width:100%;"></div>'; // jtb-widget f="clearboth"]
    }else if($jtbfunction=="disneydateselect"){
        $outputx .= return_output( 'docs/print-disney-date-select.php'); // jtb-widget f="disneydateselect"]
    }else if($jtbfunction=="usj-contact-button"){
        $outputx .= '<br /><br /><a onclick="switch_book_button();" class=" wpcf7-form-control wpcf7-submit btnLarge  wpcf7-submit ">Request your Express Pass Now</a>'; // jtb-widget f="usj-contact-button"]
    }else if($jtbfunction=="disbook"){
        $links = array( "1"=>"/disney-land/", "2"=>"/disney-sea/", "3"=>"/disney-ticket/", "4"=>"/disney-sea-ticket/", "5"=>"/disney-land-2-day/", "6"=>"/disney-sea-2-day/", "7"=>"/disney-land-disney-sea-3-day-ticket/", "8"=>"/disney-sea-disney-land-3-day-ticket/", "9"=>"/disney-land-3-day-passport/", "10"=>"/disney-sea-3-day-passport/");//"1"=>"7183", "2"=>"7184", "3"=>"7186", "4"=>"7187", "5"=>"7185", "6"=>"7188", "7"=>"7190", "8"=>"7192", "9"=>"7191", "10"=>"7193", "11"=>"7195", "12"=>"7196", "13"=>"7194", "14"=>"7197" ); //product codes - insert into search URL
        $url = "https://www.nx.jtbtravel.com.au/tickets/tokyo-disney-resort" .$links[(string)$namedata];//'https://www.nx.jtbtravel.com.au/tickets/tokyo-disney-resort/disney-ticket/?supplierid=1231&amp;productid='.$links[(string)$namedata].'&amp;scu=1&amp;qty=1A&amp;srb=Tickets&amp;dst=Chiba&amp;searchurl=';
        /*if ( in_array((string)$namedata, array("1","5","9","13"), true ) ){ //only dis 
            $url = str_replace('/disney-ticket/', '/disney-land/', $url);
        }else if ( in_array((string)$namedata, array("2","6","10","14"), true ) ){ //only sea 
            $url = str_replace('/disney-ticket/', '/disney-sea/', $url);
        }*/
        $outputx .= '<a class="btnLarge wpcf7-form-control wpcf7-submit disubmit" href="'.$url.'">Book</a>';  
    }else if($jtbfunction=="jr-doc-buttons"){
        $outputx .= return_output( 'docs/print-jr-doc-buttons.php');
    }else if($jtbfunction=="escorted-form"){// jtb-widget f="escorted-form"]
        $outputx .= return_output( 'docs/escorted-form-html.php');
    }else if($jtbfunction=="cruise_book"){// jtb-widget f="cruise_book"]
        $outputx .= '<p><a class="submit wpcf7-form-control wpcf7-submit btnLarge" href="#" onclick="cruise_book();" >Enquire Now</a></p>';
    }else if($jtbfunction=="hide-flights"){//jtb-widget f="hide-flights"]
        $outputx .= return_output( 'docs/print-hide-flights.php');
    }else if($jtbfunction=="feedback"){//jtb-widget f="feedback"]
        $outputx .= return_output( 'docs/print-feedback.php');
    }else if($jtbfunction=="sabre"){//jtb-widget f="sabre"]
        $outputx .= return_output( 'docs/print-sabre.php');
    }else if($jtbfunction=="jr-price"){//jtb-widget f="jr-price"]
        $outputx .= return_output( 'docs/print-jr-price.php'); 
    }else if($jtbfunction=="kyushu"){//jtb-widget f="kyushu"]
        $outputx .= return_output( 'docs/print-kyushu.php'); 
    }else if($jtbfunction=="rwc-transport-package"){//jtb-widget f="rwc-transport-package"]
        $outputx .= return_output( 'docs/print-rwc-transport-package.php'); 
    }else if($jtbfunction=="expedia"){//jtb-widget f="expedia"]
        $outputx .= return_output( 'docs/print-expedia.php'); 
    }else if($jtbfunction=="hyperdia"){//jtb-widget f="hyperdia"]
       $outputx .= return_output( 'docs/print-hyperdia.php'); 
    }else if($jtbfunction=="jr-calc"){//jtb-widget f="jr-calc"]
        $outputx .= return_output( 'docs/print-jr-calc.php'); 
    }else if($jtbfunction=="xxxx"){//jtb-widget f="xxxx"]
        $outputx .= " ";
    }else if ($jtbfunction=="print-jr-booking-divs") {
  $outputx .=  <<<HEREDOC


<div id="product_content" class="tpproduct-rail-pass">
<div id="suppliersection">
<div id="productavailabilitysection"> </div>
<div id="products_section">
<h3>Change Details</h3>
<div id="productssection" class="productssection-rail-pass"> </div>
</div>

HEREDOC;
    }else if($jtbfunction=="reviews"){ //print reviews on tour pages 
        
        if ($tagname=="blank"){
            $outputx .= '<a href="/reviews">JTB Reviews</a>';
        }else{
           $tagnameslug =  get_term_by('name',$namedata, 'post_tag');

$lastposts =  array(
    'posts_per_page' => -1,
    'post_type'   => 'post',
    'post_status'    => 'publish',
    'category_name'       => 'reviews',
    'tag'       => $tagnameslug->slug ,
) ;
$outputx .='<div class="about-tc-template">';
$query2 = new WP_Query( $lastposts );

if ( $query2->have_posts() ) {
    // The 2nd Loop
    $first=true;
    while ( $query2->have_posts() ) {
        $query2->the_post();
        $tempid = $query2->post->ID ;
        if ($first){//change to H2 to remove the top line.
            $outputx .= '<h3>'.get_the_title($tempid).'</h3>';
            $first=false;
        }else{
            $outputx .= '<h3>'.get_the_title($tempid).'</h3>';
        }
        $outputx .= get_the_content($tempid);
        $outputx .= '';
    }
    // Restore original Post Data
    wp_reset_postdata();
}
} $outputx .='</div>';
}
   
    return $outputx;
}




function print_subscribe_to_newsletter(){
$outputx = ' <div class="hotel-detailed-data"> <div class="textwidget">';
$outputx .= return_output( 'docs/subscribe-to-newsletter.php')  ;
        wp_reset_query();
        //return $outputx;
        $outputx .= ' </div> </div> ';
        return $outputx;
} //end print_subscribe_to_newsletter



function wpb_imagelink_setup() {
    $image_set = get_option( 'image_default_link_type' );
    
    if ($image_set !== 'none') {
        update_option('image_default_link_type', 'none');
    }
}



function posts_per_search_page( $query ) {
  if ( !is_admin() && $query->is_main_query() ) {
    if ( $query->is_search ) $query->set( 'posts_per_page', 12 );
  }else if($query->is_main_query()){
  	if ( $query->is_search ) $query->set( 'posts_per_page', 24 );
  }
}

?>
