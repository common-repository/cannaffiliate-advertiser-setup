<?php

/**
 * Provide a admin area settings view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://cannaffiliate.com/
 * @since      1.0.0
 *
 * @package    CannAffiliate
 * @subpackage CannAffiliate/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap cannaffiliate-settings">
	<div class="settings-header">
		<img class="header-image" src="<?php echo plugin_dir_url(dirname(__FILE__)) . '/img/CannAffiliate-Logo.png'; ?>" alt="CannAffiliate Logo">
		<h1 class="header-title">Affiliate Marketing for the Cannabis Industry</h1>
	</div>
	<div class="settings-content">
		<h2>CannAffiliate Advertiser Setup</h2>  
		<?php settings_errors(); ?>  
		<form method="POST" action="options.php">  
			<?php 
			settings_fields( 'cannaffiliate_general_settings' );
			do_settings_sections( 'cannaffiliate_general_settings' ); 
			?>             
			<?php submit_button(); ?>  
		</form>
	</div>
</div>