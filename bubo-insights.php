<?php

/**
 * @link              https://github.com/pizza2mozzarella/bubo_insights
 * @since             1.0.0
 * @package           bubo-insights
 * @wordpress-plugin
 * Plugin Name:       Bubo Insights
 * Plugin URI:        https://github.com/pizza2mozzarella/bubo_insights
 * Description:       Bubo Insights tracks and displays the most useful user navigation data without using cookies or violating privacy. Simple, useful, effective.
 * Version:           1.0.13
 * Author:            pizza2mozzarella
 * Author URI:        https://github.com/pizza2mozzarella/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bubo-insights
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'BUBO_INSIGHTS_VERSION', '1.0.13' );
define( 'BUBO_INSIGHTS_URL' , plugin_dir_url( __FILE__ ) );
define( 'BUBO_INSIGHTS_URI' , plugin_dir_path( __FILE__ ) );

// bubo insights dependencies - enqueuing jQuery... 
function bubo_insights_needs_jquery() { 
    wp_enqueue_script( 'jquery' );
}    
add_action('init', 'bubo_insights_needs_jquery');

// bubo insights admin area - enqueuing the plugin admin page styles and scripts...
function bubo_insights_admin_styles() {
    if( isset( $_GET['page'] ) ) $bubo_admin_page = sanitize_key( wp_unslash( $_GET['page'] ) );
    if(substr( $bubo_admin_page, 0, 13 ) == 'bubo_insights') {
        
        //jQuery UI
        wp_enqueue_script(
            array(
                'jquery-ui-core',
                'jquery-ui-draggable',
                'jquery-ui-sortable',
                'jquery-ui-tabs'
            )
        );
        
        // plugin wide custom scripts
        wp_register_style( 'bubo_insights_admin_style', plugins_url( '/admin/css/admin.css', __FILE__), '', time() );
        wp_enqueue_style( 'bubo_insights_admin_style' );
        wp_register_script('bubo_insights_admin_script', plugins_url('/admin/js/admin.js', __FILE__), array('jquery'), time(), true);
        wp_enqueue_script('bubo_insights_admin_script');
        
        // page specific custom scripts
        $scripts_chart = array(
            "bubo_insights"             => array( "anchor" => "bubo_insights_livestats_script", "url" => "/admin/js/livestats.js" ),
            "bubo_insights_livestats"   => array( "anchor" => "bubo_insights_livestats_script", "url" => "/admin/js/livestats.js" ),
            "bubo_insights_handbook"    => array( "anchor" => "bubo_insights_handbook_script", "url" => "/admin/js/handbook.js" ),
            "bubo_insights_settings"    => array( "anchor" => "bubo_insights_settings_script", "url" => "/admin/js/settings.js" )
        );
        foreach(array_keys($scripts_chart) as $script) {
            if($bubo_admin_page == $script) {
                wp_register_script( 
                    $scripts_chart[$script]['anchor'],
                    plugins_url( $scripts_chart[$script]['url'], __FILE__),
                    array('jquery'),
                    time(),
                    true
                );
                wp_enqueue_script( $scripts_chart[$script]['anchor'] );
            }
        }
        
    }
}
add_action('admin_enqueue_scripts', 'bubo_insights_admin_styles');

// plugin admin page customizations
if( isset( $_GET['page'] ) ) $bubo_admin_page = sanitize_key( wp_unslash( $_GET['page'] ) );
if(substr( $bubo_admin_page, 0, 13 ) == 'bubo_insights') {
    
    add_action('admin_init', 'bubo_insights_hide_all_admin_notices');
    add_filter('admin_footer_text', 'bubo_insights_footer_admin');
}
// airplane mode for other plugins' notices on plugin pages
function bubo_insights_hide_all_admin_notices() {
    global $wp_filter;

    // Check if the WP_Admin_Bar exists, as it's not available on all admin pages.
    if (isset($wp_filter['admin_notices'])) {
        // Remove all actions hooked to the 'admin_notices' hook.
        unset($wp_filter['admin_notices']);
    }
}
// plugin admin footer thankyou
function bubo_insights_footer_admin () {
    if( isset( $_GET['page'] ) ) $bubo_admin_page = sanitize_key( wp_unslash( $_GET['page'] ) );
    if(substr( $bubo_admin_page, 0, 13 ) == 'bubo_insights') :
    ?>
       <p id="footer-thankyou">Enjoying <strong>Bubo Insights</strong>? Please rate <a href="https://wordpress.org/plugins/bubo-insights/#reviews" target="_blank" rel="noopener noreferrer">★★★★★</a> on <a <a href="https://wordpress.org/plugins/bubo-insights/#reviews" target="_blank" rel="noopener">WordPress.org</a> to help us spread the word. Thank you!</p>
	<?php
	endif;
}

// bubo insights tracking engine - enqueuing the logging scripts...
function bubo_insights_tracking_scripts() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/inline_tracking_code.php';

    wp_register_script( 'bubo_insights_tracking_script_inline', '', array('jquery'), time(), true );
    wp_enqueue_script( 'bubo_insights_tracking_script_inline'  );
    wp_add_inline_script( 'bubo_insights_tracking_script_inline', bubo_insights_inline_tracking_script(), 'after');
}
add_action( 'wp_enqueue_scripts', 'bubo_insights_tracking_scripts' );

// bubo insights tracking engine
require_once plugin_dir_path( __FILE__ ) . 'includes/tracking_engine.php';
register_activation_hook( __FILE__, 'bubo_insights_register_db_tables' );

// plugin navbar
function bubo_insights_navbar($bubo_insights_page) {
    require BUBO_INSIGHTS_URI . 'admin/partials/navbar.php';
}
add_action( 'bubo_insights_navbar', 'bubo_insights_navbar', 11, 1 );

// admin area - main page
function bubo_insights_main_page() {
	add_menu_page(
      'Bubo Insights',
      'Bubo Insights',
      'publish_pages',
      'bubo_insights',
      'bubo_insights_livestats_page_contents',
      'dashicons-chart-bar',
      3
	);
}
add_action( 'admin_menu', 'bubo_insights_main_page', 11 );

// admin area - livestats page
function bubo_insights_livestats_page() {
	add_submenu_page(
      'bubo_insights',
      'Stats',
      'Stats',
      'publish_pages',
      'bubo_insights',
      'bubo_insights_livestats_page_contents',
      'dashicons-schedule',
      1
	);
}
add_action( 'admin_menu', 'bubo_insights_livestats_page', 11 );

// admin area - handbook page
function bubo_insights_handbook_page() {
	add_submenu_page(
      'bubo_insights',
      'Handbook',
      'Handbook',
      'publish_pages',
      'bubo_insights_handbook',
      'bubo_insights_handbook_page_contents',
      'dashicons-schedule',
      2
	);
}
add_action( 'admin_menu', 'bubo_insights_handbook_page', 11 );

// admin area - settings page
function bubo_insights_settings_page() {
	add_submenu_page(
      'bubo_insights',
      'Settings',
      'Settings',
      'publish_pages',
      'bubo_insights_settings',
      'bubo_insights_settings_page_contents',
      'dashicons-schedule',
      3
	);
}
add_action( 'admin_menu', 'bubo_insights_settings_page', 11 );

// dashboard page
function bubo_insights_dashboard_page_contents() {
//    require_once plugin_dir_path( __FILE__ ) . 'admin/partials/livestats.php';
}

// livestats page 
// page contents
function bubo_insights_livestats_page_contents() {
    require_once plugin_dir_path( __FILE__ ) . 'admin/partials/livestats.php';
}
// AJAX
function bubo_insights_livestats_query_callback() { 
	require_once plugin_dir_path( __FILE__ ) . 'admin/partials/livestats-ajax.php';
	die();
}
add_action('wp_ajax_bubo_insights_livestats_query', 'bubo_insights_livestats_query_callback');

function bubo_insights_livestats_defaults_callback() {
    $userid = '';
    if( isset($_REQUEST['userid']) ) $userid = sanitize_text_field( wp_unslash( $_REQUEST['userid'] ) );
    $defaults = array();
    if( isset($_REQUEST['defaults']['multibarsorder']) )    $defaults['multibarsorder']     = array_map( 'sanitize_text_field' , wp_unslash( $_REQUEST['defaults']['multibarsorder'] ) );
    if( isset($_REQUEST['defaults']['inactivemetrics']) )   $defaults['inactivemetrics']    = array_map( 'sanitize_text_field' , wp_unslash( $_REQUEST['defaults']['inactivemetrics'] ) );
    if( isset($_REQUEST['defaults']['who']) )               $defaults['who']                = array_map( 'sanitize_text_field' , wp_unslash( $_REQUEST['defaults']['who'] ) );
    if( isset($_REQUEST['defaults']['whotab']) )            $defaults['whotab']             = array_map( 'sanitize_text_field' , wp_unslash( $_REQUEST['defaults']['whotab'] ) );
    if( isset($_REQUEST['defaults']['when']) )              $defaults['when']               = array_map( 'sanitize_text_field' , wp_unslash( $_REQUEST['defaults']['when'] ) );
    if( isset($_REQUEST['defaults']['wherepage']) )         $defaults['wherepage']          = array_map( 'sanitize_text_field' , wp_unslash( $_REQUEST['defaults']['wherepage'] ) );
    if( isset($_REQUEST['defaults']['wherefrom']) )         $defaults['wherefrom']          = array_map( 'sanitize_text_field' , wp_unslash( $_REQUEST['defaults']['wherefrom'] ) );
    if( isset($_REQUEST['defaults']['wheregoto']) )         $defaults['wheregoto']          = array_map( 'sanitize_text_field' , wp_unslash( $_REQUEST['defaults']['wheregoto'] ) );
    if( isset($_REQUEST['defaults']['wheretab']) )          $defaults['wheretab']           = array_map( 'sanitize_text_field' , wp_unslash( $_REQUEST['defaults']['wheretab'] ) );
    
    update_user_meta( $userid, 'wp_bubo_insights_livestats_defaults', wp_json_encode( $defaults ) );
  
    echo wp_json_encode( get_user_meta( $userid, 'wp_bubo_insights_livestats_defaults', true ) );
	die();
}
add_action('wp_ajax_bubo_insights_livestats_defaults', 'bubo_insights_livestats_defaults_callback');


// liverecords page
// page contents
function bubo_insights_liverecords_page_contents() {
    require_once plugin_dir_path( __FILE__ ) . 'admin/partials/liverecords.php';
}
// AJAX
function bubo_insights_liverecords_query_callback() { 
	// the live records page AJAX response
    require_once plugin_dir_path( __FILE__ ) . 'admin/partials/liverecords-ajax.php';
    die();
}
add_action('wp_ajax_bubo_insights_liverecords_query', 'bubo_insights_liverecords_query_callback');


// handbook page
// page contents
function bubo_insights_handbook_page_contents() {
    require_once plugin_dir_path( __FILE__ ) . 'admin/partials/handbook.php';
}


// settings page
// page contents
function bubo_insights_settings_page_contents() {
    require_once plugin_dir_path( __FILE__ ) . 'admin/partials/settings.php';
}
// AJAX Drop all tables function
function bubo_insights_drop_all_tables_callback() {
	bubo_insights_drop_all_tables();
	bubo_insights_eventlog_table();
	bubo_insights_visitorslog_table();
	echo wp_json_encode('All tables dropped!');
  	die();
}
add_action('wp_ajax_bubo_insights_drop_all_tables', 'bubo_insights_drop_all_tables_callback');
// AJAX CSV export function
function bubo_insights_export_csv( $table = 'bubo_insights_event_log', $reportname = 'event_log_backup' ) {
    
    global $wpdb;
	
	$results = '';
	if( $table == 'bubo_insights_event_log' ){
		$results = $wpdb->get_results( 
			$wpdb->prepare( 
				"SELECT * FROM `wp_bubo_insights_event_log` ORDER BY %s DESC", 
				array( "id" )
			)
		);
	}
	else if( $table == 'bubo_insights_visitors_log' ){
		$results = $wpdb->get_results( 
			$wpdb->prepare( 
				"SELECT * FROM `wp_bubo_insights_visitors_log` ORDER BY %s DESC", 
				array( "id" )
			)
		);		
	}
	
	$csv = '';
	if( ! empty($results[0]) ){
		$csv .= implode(',' , array_keys(get_object_vars($results[0])));
	}
    $csv .= "\n"; // important! Make sure to use use double quotation marks.
    foreach( $results as $result ) {
        $csv .= implode(',' , get_object_vars($result));
        $csv .= "\n"; // important! Make sure to use use double quotation marks.
    }
	$site_url_sanitized = 'your_wp_website';
	if( isset( $_SERVER['SERVER_NAME'] ) ) $site_url_sanitized = str_replace( '.' , '-' , sanitize_url( wp_unslash( $_SERVER['SERVER_NAME'] ) ) );
    $date = gmdate("YMd");
    $filename = 'bubo_insights_' . $reportname . '_of_' . $date . '_for_' . $site_url_sanitized . '.csv';
    header( 'Content-Type: text/csv' ); // tells browser to download
    header( 'Content-Disposition: attachment; filename="' . $filename .'"' );
    header( 'Pragma: no-cache' ); // no cache
    header( "Expires: Sat, 01 Jan 1990 05:00:00 GMT" ); // expire date

    echo esc_textarea($csv);
    exit;
}


//end of bubo insights plugin