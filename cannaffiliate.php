<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://cannaffiliate.com/
 * @since             1.0.0
 * @package           CannAffiliate
 *
 * @wordpress-plugin
 * Plugin Name:       CannAffiliate Advertiser Setup
 * Description:       Setup CannAffiliate conversion tracking to allow influencers and publishers to start driving customers to your site.
 * Version:           1.2
 * Author:            CannAffiliate
 * Author URI:        https://cannaffiliate.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cannaffiliate
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CannAffiliate_VERSION', '1.2' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cannaffiliate-activator.php
 */
function activate_CannAffiliate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cannaffiliate-activator.php';
	CannAffiliate_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cannaffiliate-deactivator.php
 */
function deactivate_CannAffiliate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cannaffiliate-deactivator.php';
	CannAffiliate_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_CannAffiliate' );
register_deactivation_hook( __FILE__, 'deactivate_CannAffiliate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cannaffiliate.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_CannAffiliate() {

	$plugin = new CannAffiliate();
	$plugin->run();

}
run_CannAffiliate();
