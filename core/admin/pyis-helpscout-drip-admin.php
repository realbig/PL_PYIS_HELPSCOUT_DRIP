<?php
/**
 * PyImageSearch Helpscout+Drip Settings
 *
 * @since 1.0.0
 *
 * @package PYIS_HelpScout_Drip
 * @subpackage PYIS_HelpScout_Drip/core/admin
 */

defined( 'ABSPATH' ) || die();

class PYIS_HelpScout_Drip_Admin {

	/**
	 * PYIS_HelpScout_Drip_Admin constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {

		add_action( 'admin_menu', array( $this, 'create_admin_page' ) );
		
		add_action( 'admin_init', array( $this, 'register_options' ) );

	}
	
	/**
	 * Create the Admin Page to hold our Settings
	 * 
	 * @access		public
	 * @since		1.0.0
	 * @return		void
	 */
	public function create_admin_page() {
		
		$submenu_page = add_submenu_page(
			'options-general.php',
			_x( 'PyImageSearch HelpScout+Drip', 'Admin Page Title', PYIS_HelpScout_Drip_ID ),
			_x( 'HelpScout+Drip', 'Admin Menu Title', PYIS_HelpScout_Drip_ID ),
			'manage_options',
			'pyis-helpscout-drip',
			array( $this, 'admin_page_content' )
		);
		
	}
	
	/**
	 * Create the Content/Form for our Admin Page
	 * 
	 * @access		public
	 * @since		1.0.0
	 * @return		void
	 */
	public function admin_page_content() { ?>

		<div class="wrap pyis-helpscout-drip">
			<h1><?php echo _x( 'HelpScout+Drip Integration Settings', 'Admin Page Title', PYIS_HelpScout_Drip_ID ); ?></h1>

			<form method="post" action="options.php">

				<?php settings_fields( 'pyis_helpscout_drip' ); ?>

				<table class="form-table">
					
					<tbody>
						
						<tr>
							
							<th scope="row">
								<label for="helpscout_instructions">
									<?php echo _x( 'HelpScout API Setup', 'HelpScout API Setup Label', PYIS_HelpScout_Drip_ID ); ?>
								</label>
							</th>
							
							<td>
								<ol>
									<li>
										<?php printf( __( 'Place <code>%s/wp-json/pyis/v1/helpscout/practical-python-open-cv-hardcopy/submit</code> into the "Submit Entry Endpoint" text input and save your changes.', PYIS_HelpScout_Drip_ID ), get_site_url() ); ?>
									</li>
								</ol>
							</td>
						
						</tr>
						
						<tr>
							
							<th scope="row">
								<label for="pyis_helpscout_secret_key">
									<?php echo _x( 'HelpScout Secret Key', 'HelpScout Secret Key Label', PYIS_HelpScout_Drip_ID ); ?> <span class="required">*</span>
								</label>
							</th>
							
							<td>
								<input required type="text" class="regular-text" name="pyis_helpscout_secret_key" value="<?php echo ( $secret_key = get_option( 'pyis_helpscout_secret_key' ) ) ? $secret_key : ''; ?>" /><br />
								<p class="description">
									<?php echo _x( "This is used to help ensure people aren't abusing your API Endpoint.", 'Cognito Forms Secret Key Description', PYIS_HelpScout_Drip_ID ); ?>
								</p>
								<ol>
									<li>
										<?php echo __( 'Edit your Form.', PYIS_HelpScout_Drip_ID ); ?>
									</li>
									<li>
										<?php echo __( 'Click a "+" button at the bottom of your Form to add a Field.', PYIS_HelpScout_Drip_ID ); ?>
									</li>
									<li>
										<?php echo __( 'Choose a Basic Form Input, such as Text or Number.', PYIS_HelpScout_Drip_ID ); ?>
									</li>
									<li>
										<?php echo __( 'Set "Label" to <code>Secret</code>.', PYIS_HelpScout_Drip_ID ); ?>
									</li>
									<li>
										<?php printf( __( 'Set "Default Vaue" to the same value entered above.', PYIS_HelpScout_Drip_ID ), get_site_url() ); ?>
									</li>
									<li>
										<?php printf( __( 'Set "Show This Field" to <em>Never</em> and "Require This Field" to <em>Always</em>.', PYIS_HelpScout_Drip_ID ), get_site_url() ); ?>
									</li>
									<li>
										<?php printf( __( 'This value is only sent in the JSON Data, so it cannot be found by Inspecting the Page Source or by Viewing the Entry.', PYIS_HelpScout_Drip_ID ), get_site_url() ); ?>
									</li>
								</ol>
							</td>
						
						</tr>
						
						<tr>
							
							<th scope="row">
								<label for="pyis_drip_api_key">
									<?php echo _x( 'Drip API Token', 'Drip API Key Label', PYIS_HelpScout_Drip_ID ); ?> <span class="required">*</span>
								</label>
							</th>
							
							<td>
								<input required type="text" class="regular-text" name="pyis_drip_api_key" value="<?php echo ( $api_key = get_option( 'pyis_drip_api_key' ) ) ? $api_key : ''; ?>" /><br />
								<p class="description">
									<a href="//www.getdrip.com/user/edit" target="_blank">
										<?php echo _x( 'Find your API Token Here', 'API Key Link Text', PYIS_HelpScout_Drip_ID ); ?>
									</a>
								</p>
							</td>
						
						</tr>
						
						<tr>
							
							<th scope="row">
								<label for="pyis_drip_account_id">
									<?php echo _x( 'Drip Account ID', 'Drip Account ID Label', PYIS_HelpScout_Drip_ID ); ?> <span class="required">*</span>
								</label>
							</th>
							
							<td>
								<input required type="text" class="regular-text" name="pyis_drip_account_id" value="<?php echo ( $account_id = get_option( 'pyis_drip_account_id' ) ) ? $account_id : ''; ?>" /><br />
								<p class="description">
									<?php echo _x( 'Your Account ID is found in the Address Bar after logging in. <code>https://www.getdrip.com/&lt;account_id&gt;/</code>', 'Account ID Example Text', PYIS_HelpScout_Drip_ID ); ?>
								</p>
							</td>
						
						</tr>
						
						<tr>
							
							<th scope="row">
								<label for="pyis_drip_account_password">
									<?php echo _x( 'Drip Account Password', 'Drip Account Password Label', PYIS_HelpScout_Drip_ID ); ?> <span class="required">*</span>
								</label>
							</th>
							
							<td>
								<input required type="password" class="regular-text" name="pyis_drip_account_password" value="<?php echo ( $account_password = get_option( 'pyis_drip_account_password' ) ) ? $account_password : ''; ?>" /><br />
								<p class="description">
									<?php echo _x( 'Your Password is needed to Authenticate the API Request.', 'Account Password Explaination Text', PYIS_HelpScout_Drip_ID ); ?>
								</p>
							</td>
						
						</tr>
						
						<tr>
							
							<th scope="row">
								<label for="PYIS_HelpScout_Drip_admin_email">
									<?php echo _x( 'Send Notification Emails To:', 'Admin Email Label', PYIS_HelpScout_Drip_ID ); ?>
								</label>
							</th>
							
							<td>
								<input type="text" class="regular-text" name="PYIS_HelpScout_Drip_admin_email" value="<?php echo ( $admin_email = get_option( 'PYIS_HelpScout_Drip_admin_email' ) ) ? $admin_email : ''; ?>" placeholder="<?php echo ( $default_admin_email = get_option( 'admin_email' ) ) ? $default_admin_email : ''; ?>" /><br />
								<p class="description">
									<?php printf( _x( 'This will default to the Admin Email: %s.', 'Admin Email Explaination Text', PYIS_HelpScout_Drip_ID ), $default_admin_email ); ?>
								</p>
							</td>
						
						</tr>
						
					</tbody>
					
				</table>

				<?php submit_button(); ?>

			</form>

		</div>

		<?php
		
	}
	
	/**
	 * Register our Options so the Admin Page knows what to Save
	 * 
	 * @access		public
	 * @since		1.0.0
	 * @return		void
	 */
	public function register_options() {
		
		if ( false === get_option( 'pyis_helpscout_secret_key' ) ) {
			add_option( 'pyis_helpscout_secret_key' );
		}
		
		if ( false === get_option( 'pyis_drip_api_key' ) ) {
			add_option( 'pyis_drip_api_key' );
		}
		
		if ( false === get_option( 'pyis_drip_account_id' ) ) {
			add_option( 'pyis_dripaccount_id' );
		}
		
		if ( false === get_option( 'pyis_drip_account_password' ) ) {
			add_option( 'pyis_drip_account_password' );
		}
		
		add_settings_section(
			'pyis_helpscout_drip',
			__return_null(),
			'__return_false',
			'pyis-address-collection'
		);
		
		register_setting( 'pyis_helpscout_drip', 'pyis_helpscout_secret_key' );
		register_setting( 'pyis_helpscout_drip', 'pyis_drip_api_key' );
		register_setting( 'pyis_helpscout_drip', 'pyis_drip_account_id' );
		register_setting( 'pyis_helpscout_drip', 'pyis_drip_account_password' );
		
	}
	
}