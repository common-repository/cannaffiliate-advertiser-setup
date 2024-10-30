<?php

/**
 * Fired during plugin activation
 *
 * @link       https://cannaffiliate.com/
 * @since      1.0.0
 *
 * @package    CannAffiliate
 * @subpackage CannAffiliate/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    CannAffiliate
 * @subpackage CannAffiliate/includes
 * @author     CannAffiliate <ryan@authoritynw.com>
 */
class CannAffiliate_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		$plugin_name_db_version = '1.0';
		$table_name = $wpdb->prefix . "cannaffiliate_transactions";
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			created timestamp NOT NULL default CURRENT_TIMESTAMP,
			ip_address varchar(255) DEFAULT '' NOT NULL,
			transaction_id varchar(255) DEFAULT '' NOT NULL,
			status varchar(255) DEFAULT '1' NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		add_option( 'plugin_name_db_version', $plugin_name_db_version );
	}

}
