<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php $bubo_insights_page = "settings";  ?>

<?php

check_admin_referer();

// CSV export mode
if( isset( $_GET['export'] ) ) {
	$report_name = sanitize_text_field( wp_unslash( $_GET['export'] ) );
    if( $report_name == 'event_log_backup' ) {
        bubo_insights_export_csv( 'bubo_insights_event_log', $report_name );
    }
    if( $report_name == 'visitors_log_backup' ) {
        bubo_insights_export_csv( 'bubo_insights_visitors_log', $report_name );
    }
}

?>

<main id="settings" class="bubo_insights_admin_page" data-ajax="<?php echo esc_url( get_site_url() ); ?>/wp-admin/admin-ajax.php">
    
    <div id="header">
        
        <nav id="bubo_insights_navbar">
            <?php do_action('bubo_insights_navbar', $bubo_insights_page ); ?>
        </nav>
		    
		<h1>Settings</h1>
		
	</div>
    
    <section>
        <h2>Backup the database</h2>
        <p>Export this plugins' database tables in .csv</p>
        <div class="flex gap10" >
            <a class="control blue" href="admin.php?page=bubo_insights_settings&export=event_log_backup&noheader=1">Event Log</a>
            <a class="control blue" href="admin.php?page=bubo_insights_settings&export=visitors_log_backup&noheader=1">Visitors Log</a>
        </div>
    </section>
    
    <section>
        <h2>Danger Zone [WARNING: permanent actions]</h2>
        <p>This action remove ALL data collected by the plugin from this website database.</p>
        <p>This is useful when data collected is slowing the website or when uninstalling this plugin, otherwise not recommended.</p>
        <p>It's always a good idea to download a backup before performing this action!</p>
        <button id="purge" class="control red" >PURGE PLUGIN DATABASE</button>
    </section>
    
</main>