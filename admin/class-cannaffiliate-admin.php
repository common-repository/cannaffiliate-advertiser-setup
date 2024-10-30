<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://cannaffiliate.com/
 * @since      1.0.0
 *
 * @package    CannAffiliate
 * @subpackage CannAffiliate/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    CannAffiliate
 * @subpackage CannAffiliate/admin
 * @author     CannAffiliate <ryan@authoritynw.com>
 */
class CannAffiliate_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		add_action('admin_menu', array( $this, 'addPluginAdminMenu' ), 9);
		add_action('admin_init', array( $this, 'registerAndBuildFields' ));
		add_action('init', array( $this, 'save_transaction_id' ));
		add_action('woocommerce_thankyou', array( $this, 'Send_Postback_URL' ));
		
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name . '-admin-css', plugin_dir_url( __FILE__ ) . 'css/cannaffiliate-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name . '-admin-js', plugin_dir_url( __FILE__ ) . 'js/cannaffiliate-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function addPluginAdminMenu() {
		add_submenu_page( 'tools.php', 'CannAffiliate Advertiser Setup', 'CannAffiliate Setup', 'administrator', $this->plugin_name.'-settings', array( $this, 'displayPluginAdminSettings' ));
	}
	
	public function displayPluginAdminSettings() {
		$active_tab = sanitize_text_field(isset( $_GET['tab'] ) ? $_GET['tab'] : 'general');
		if (isset($_GET['error_message'])) {
			add_action('admin_notices', array($this,'pluginNameSettingsMessages'));
			do_action('admin_notices', sanitize_text_field($_GET['error_message']));
		}
		require_once 'partials/' . $this->plugin_name.'-admin-settings-display.php';
	}
	
	public function pluginNameSettingsMessages($error_message) {
		switch ($error_message) {
			case '1':
				$message = __( 'There was an error adding this setting. Please try again.  If this persists, shoot us an email.', 'my-text-domain' );                 
				$err_code = sanitize_text_field('cannaffiliate_offer_id');
				$setting_field = 'cannaffiliate_offer_id';
				break;
		}
		$type = 'error';
		add_settings_error(
			$setting_field,
			$err_code,
			$message,
			$type
		);
	}
	
	public function registerAndBuildFields() {
		add_settings_section(
			// ID used to identify this section and with which to register options
			'cannaffiliate_general_section', 
			// Title to be displayed on the administration page
			'',  
			// Callback used to render the description of the section
			array( $this, 'cannaffiliate_display_general_account' ),    
			// Page on which to add this section of options
			'cannaffiliate_general_settings'                   
		);
		
		unset($args_offer_id);
		
		$args_offer_id = array (
			'type'      => 'input',
			'subtype'   => 'text',
			'id'    => 'cannaffiliate_offer_id',
			'name'      => 'cannaffiliate_offer_id',
			'required' => 'true',
			'get_options_list' => '',
			'value_type'=>'normal',
			'wp_data' => 'option'
		);
		
		/*$args_delete_data = array (
			'type'      => 'input',
			'subtype'   => 'checkbox',
			'id'    => 'cannaffiliate_delete_data',
			'name'      => 'cannaffiliate_delete_data',
			'required' => 'false',
			'get_options_list' => '',
			'value_type'=>'normal',
			'wp_data' => 'option'
		);*/
			  
		add_settings_field(
			'cannaffiliate_offer_id',
			'Offer ID',
			array( $this, 'cannaffiliate_render_settings_field' ),
			'cannaffiliate_general_settings',
			'cannaffiliate_general_section',
			$args_offer_id
		);
		
		/*add_settings_field(
			'cannaffiliate_delete_data',
			'Delete data after uninstall?',
			array( $this, 'cannaffiliate_render_settings_field' ),
			'cannaffiliate_general_settings',
			'cannaffiliate_general_section',
			$args_delete_data
		);*/

		register_setting(
			'cannaffiliate_general_settings',
			'cannaffiliate_offer_id'
		);
		
		/*register_setting(
			'cannaffiliate_general_settings',
			'cannaffiliate_delete_data'
		);*/
	}
	
	public function cannaffiliate_display_general_account() {
		echo '
			<p>Your advertiser "Offer ID will be provided to you from CannAffiliate.</p>
			<p>Please reach out to <a href="mailto:support@cannaffiliate.com">support@cannaffiliate.com</a> with any questions.
		';
	}
	
	public function cannaffiliate_render_settings_field($args) {  
		if($args['wp_data'] == 'option'){
			$wp_data_value = get_option($args['name']);
		} elseif($args['wp_data'] == 'post_meta'){
			$wp_data_value = get_post_meta($args['post_id'], $args['name'], true );
		}

		switch ($args['type']) {
			case 'input':
				$value = ($args['value_type'] == 'serialized') ? serialize($wp_data_value) : $wp_data_value;
				if($args['subtype'] != 'checkbox'){
					$prependStart = (isset($args['prepend_value'])) ? '<div class="input-prepend"> <span class="add-on">'.$args['prepend_value'].'</span>' : '';
					$prependEnd = (isset($args['prepend_value'])) ? '</div>' : '';
					$step = (isset($args['step'])) ? 'step="'.$args['step'].'"' : '';
					$min = (isset($args['min'])) ? 'min="'.$args['min'].'"' : '';
					$max = (isset($args['max'])) ? 'max="'.$args['max'].'"' : '';
					if(isset($args['disabled'])){
						echo $prependStart.'<input type="'.$args['subtype'].'" id="'.$args['id'].'_disabled" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'_disabled" size="40" disabled value="' . sanitize_text_field($value) . '" /><input type="hidden" id="'.$args['id'].'" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'" size="40" value="' . sanitize_text_field($value) . '" />'.$prependEnd;
					} else {
						echo $prependStart.'<input type="'.$args['subtype'].'" id="'.$args['id'].'" "'.$args['required'].'" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'" size="40" value="' . sanitize_text_field($value) . '" />'.$prependEnd;
					}

				} else {
					$checked = ($value) ? 'checked' : '';
					echo '<input type="'.$args['subtype'].'" id="'.$args['id'].'" "'.$args['required'].'" name="'.$args['name'].'" size="40" value="1" '.$checked.' />';
				}
				break;
			default:
				break;
		}
	}
	
	public function save_transaction_id() {		
		if (isset($_GET['transaction_id'])) {
			$ip_address = $this->get_user_ip_address();
			$transaction_id = sanitize_text_field($_GET['transaction_id']);
			
			global $wpdb;
			$plugin_name_db_version = '1.0';
			$table_transactions = $wpdb->prefix . "cannaffiliate_transactions";
			
			$transaction_count = $wpdb->get_var(
				$wpdb->prepare(
					"
						SELECT Count(*)
						FROM $table_transactions
						WHERE transaction_id = %s
					",
					$transaction_id
				)
			);
			
			if (intval($transaction_count) > 0) {
				// Transaction ID already exists
			}
			else {
				// Set Cookie for Transaction ID
				setcookie('cannaffiliate_dsm_transaction_id', $transaction_id, time() + (365 * 24 * 60 * 60 * 30), "/");
				
				$wpdb->query(
					$wpdb->prepare(
						"INSERT INTO $table_transactions 
						(ip_address, transaction_id) 
						VALUES ( %s, %s )",
						$ip_address,
						$transaction_id
					)
				);
			}
		}
		else {
			// No transaction ID in URL
		}
	}
	
	public function Send_Postback_URL($order_id) {		
		if ( ! $order_id ) {
			return;
		}
		else {
			global $wpdb;
			$plugin_name_db_version = '1.0';
			$table_transactions = $wpdb->prefix . "cannaffiliate_transactions";
			$table_options = $wpdb->prefix . "options";
			
			$ip_address = $this->get_user_ip_address();
			
			$get_transaction = $wpdb->get_row("SELECT * FROM $table_transactions WHERE ip_address = '" . $ip_address . "' AND status = '1' ORDER BY created DESC LIMIT 1");
			
			if (isset($get_transaction)) {
				$transaction_table_id = $get_transaction->id;
				$transaction_id = $get_transaction->transaction_id;
				
				$db_offer_id = $wpdb->get_var($wpdb->prepare("SELECT option_value FROM $table_options WHERE option_name = 'cannaffiliate_offer_id'"));
				
				if (is_null($db_offer_id) != 1) {
					if ($db_offer_id != "") {
						if( ! get_post_meta( $order_id, '_thankyou_action_done', true ) ) {
							$order = wc_get_order( $order_id );
							
							$amount = $order->get_subtotal();
							$email = $order->get_billing_email();
							
							$request_url = 'https://tracking.cannaffiliate.com/aff_lsr?offer_id=' . $db_offer_id . '&amount=' . $amount .  '&transaction_id=' . $transaction_id . '&adv_sub4=' . $email . '&adv_sub=' . $order_id;
							
							$response = wp_remote_get( $request_url );
							if ( is_wp_error( $response ) ) {
								// GET Request Failed
							}
							else {
								// GET Request Successful
								// Update transaction status
								$wpdb->update($table_transactions, array('status' => '0'), array('id' => $transaction_table_id));
							}
							
							$order->update_meta_data( '_thankyou_action_done', true );
							$order->save();
						}
					}
				}
			}
			else if (isset($_COOKIE['cannaffiliate_dsm_transaction_id'])) {
				$transaction_id = sanitize_text_field($_COOKIE['cannaffiliate_dsm_transaction_id']);
				
				$db_offer_id = $wpdb->get_var($wpdb->prepare("SELECT option_value FROM $table_options WHERE option_name = 'cannaffiliate_offer_id'"));
				
				if (is_null($db_offer_id) != 1) {
					if ($db_offer_id != "") {
						if( ! get_post_meta( $order_id, '_thankyou_action_done', true ) ) {
							$order = wc_get_order( $order_id );
							
							$amount = $order->get_subtotal();
							$email = $order->get_billing_email();
							
							$request_url = 'https://tracking.cannaffiliate.com/aff_lsr?offer_id=' . $db_offer_id . '&amount=' . $amount .  '&transaction_id=' . $transaction_id . '&adv_sub4=' . $email . '&adv_sub=' . $order_id;
							
							$response = wp_remote_get( $request_url );
							if ( is_wp_error( $response ) ) {
								// GET Request Failed
							}
							else {
								// GET Request Successful
							}
						}
					}
				}
			}
			else {
				// Both DB & Cookie not set yet
			}
		}
	}
	
	public function get_user_ip_address() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			// check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		else if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			// to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		
		return apply_filters( 'wpb_get_ip', $ip );
	}
	
}
