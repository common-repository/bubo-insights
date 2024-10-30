<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php if( $bubo_insights_page == "livestats" ): ?>    
    <p class="navbar_menu_current_item livestats" 	>Stats</p>
<?php else: ?>
    <a class="navbar_menu_item livestats" 	        href="<?php echo esc_url( get_admin_url() ); ?>admin.php?page=bubo_insights"                >Stats</a>
<?php endif; ?>

<?php if( $bubo_insights_page == "handbook" ): ?>    
    <p class="navbar_menu_current_item handbook" 	>Learn</p>
<?php else: ?>
    <a class="navbar_menu_item handbook" 	        href="<?php echo esc_url( get_admin_url() ); ?>admin.php?page=bubo_insights_handbook"       >Learn</a>
<?php endif; ?>

<?php if( $bubo_insights_page == "settings" ): ?>
    <p class="navbar_menu_current_item settings" 	>Settings</p>
<?php else: ?>
    <a class="navbar_menu_item settings" 	        href="<?php echo esc_url( get_admin_url() ); ?>admin.php?page=bubo_insights_settings"       >Settings</a>
<?php endif; ?>
