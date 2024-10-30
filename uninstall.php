<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://cannaffiliate.com/
 * @since      1.0.0
 *
 * @package    CannAffiliate
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {	
	exit;
}

global $wpdb;
$table_name = $wpdb->prefix . "cannaffiliate_transactions";

$check_delete_data = $wpdb->get_var("SELECT option_value FROM wp_options WHERE option_name = 'cannaffiliate_delete_data'");
if ($check_delete_data == "1") {
	$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
	$wpdb->query( "DELETE FROM wp_options WHERE option_name = 'cannaffiliate_offer_id' OR option_name = 'cannaffiliate_delete_data'" );
	delete_option("my_plugin_db_version");
}
