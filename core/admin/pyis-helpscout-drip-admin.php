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
		
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

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
									<?php echo _x( 'HelpScout App Setup', 'HelpScout App Setup Label', PYIS_HelpScout_Drip_ID ); ?>
								</label>
							</th>
							
							<td>
								
								<p>
									<a href="//secure.helpscout.net/apps/custom/" target="_blank">
										<?php echo _x( 'Create a HelpScout Custom App with the following options:', 'HelpScout Custom App Instructions Label', PYIS_HelpScout_Drip_ID ); ?>
									</a>
								</p>
								
								<ul style="list-style: disc; margin-top: 0.5em; margin-left: 2em;">
									<li>
										<?php echo _x( 'App Name: <code>Drip Tags</code>', 'HelpScout App Name Label', PYIS_HelpScout_Drip_ID ); ?>
									</li>
									<li>
										<?php echo _x( 'Content Type: <code>Dynamic Content</code>', 'HelpScout App Content Type Label', PYIS_HelpScout_Drip_ID ); ?>
									</li>
									<li>
										<?php printf( _x( 'Callback URL: <code>%s/wp-json/pyis/v1/helpscout/submit</code>', 'HelpScout App Callback URL Label', PYIS_HelpScout_Drip_ID ), get_site_url() ); ?>
									</li>
									<li>
										<?php echo _x( 'Secret Key: The same value entered below.', 'HelpScout App Secret Key Label', PYIS_HelpScout_Drip_ID ); ?>
									</li>
								</ul>
								
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
									<?php echo _x( "This is used to help ensure people aren't abusing your API Endpoint.", 'HelpScout Secret Key Description', PYIS_HelpScout_Drip_ID ); ?>
								</p>
							</td>
						
						</tr>
						
						<tr>
							
							<th scope="row">
								<label for="pyis_helpscout_drip_acceptable_tags">
									<?php echo _x( 'Acceptable Drip Tags', 'Acceptable Drip Tags Label', PYIS_HelpScout_Drip_ID ); ?>
								</label>
							</th>
							
							<td>
								
								<div class="tagsdiv">
									
									<div class="jaxtag">
									
										<div class="nojs-tags hide-if-js">
											<p>
												<textarea name="pyis_helpscout_drip_acceptable_tags" rows="3" cols="20" class="the-tags" id="tax-input-post_tag" aria-describedby="new-tag-post_tag-desc">
													<?php echo ( $acceptable_tags = get_option( 'pyis_helpscout_drip_acceptable_tags' ) ) ? $acceptable_tags : ''; ?>
												</textarea>
											</p>
										</div>
									
										<div class="ajaxtag hide-if-no-js">

											<p>

												<input type="text" class="regular-text newtag" name="pyis_helpscout_drip_acceptable_tags_fake" />

												<input type="button" class="button tagadd" value="Add">

											</p>

										</div>
										
									</div>
									
									<div class="tagchecklist"></div>
									
								</div>
									
								<p class="description">
									<?php echo _x( "A list of acceptable Drip Tags to be shown within HelpScout. If left blank, all Tags are acceptable.", 'Acceptable Drip Tags Description', PYIS_HelpScout_Drip_ID ); ?>
								</p>
								
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
		
		if ( false === get_option( 'pyis_helpscout_drip_acceptable_tags' ) ) {
			add_option( 'pyis_helpscout_drip_acceptable_tags' );
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
		register_setting( 'pyis_helpscout_drip', 'pyis_helpscout_drip_acceptable_tags' );
		register_setting( 'pyis_helpscout_drip', 'pyis_drip_api_key' );
		register_setting( 'pyis_helpscout_drip', 'pyis_drip_account_id' );
		register_setting( 'pyis_helpscout_drip', 'pyis_drip_account_password' );
		
	}
	
	/**
	 * Enqueue our Styles/Scripts on only our Admin Page
	 * 
	 * @access		public
	 * @since		1.0.0
	 * @return		void
	 */
	public function admin_enqueue_scripts() {
		
		global $current_screen;
		
		if ( $current_screen->base == 'settings_page_pyis-helpscout-drip' ) {
			
			wp_enqueue_style( PYIS_HelpScout_Drip_ID . '-admin' );
			
			wp_enqueue_script( 'tags-box' );
			
			wp_enqueue_script( PYIS_HelpScout_Drip_ID . '-admin' );
			
		}
		
	}
	
}