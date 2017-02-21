<?php
/*
Plugin Name: PyImageSearch HelpScout+Drip
Plugin URL: https://github.com/realbig/PL_PYIS_HELPSCOUT_DRIP
Description: Integrates HelpScout and Drip using WordPress as a Middleman
Version: 0.1.0
Text Domain: pyis-helpscout-drip
Author: Eric Defore
Author URL: http://realbigmarketing.com/
Contributors: d4mation
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'PYIS_HelpScout_Drip' ) ) {

	/**
	 * Main PYIS_HelpScout_Drip class
	 *
	 * @since	  1.0.0
	 */
	class PYIS_HelpScout_Drip {
		
		/**
		 * @var			PYIS_HelpScout_Drip $plugin_data Holds Plugin Header Info
		 * @since		1.0.0
		 */
		public $plugin_data;
		
		/**
		 * @var			PYIS_HelpScout_Drip $admin_errors Stores all our Admin Errors to fire at once
		 * @since		1.0.0
		 */
		private $admin_errors;
		
		/**
		 * @var			PYIS_HelpScout_Drip $admin Admin Settings
		 * @since		1.0.0
		 */
		public $admin;
		
		/**
		 * @var			PYIS_HelpScout_Drip $rest REST Endpoints
		 * @since		1.0.0
		 */
		public $rest;
		
		/**
		 * @var			PYIS_HelpScout_Drip $drip_api Drip API Class
		 * @since		1.0.0
		 */
		public $drip_api;

		/**
		 * Get active instance
		 *
		 * @access	  public
		 * @since	  1.0.0
		 * @return	  object self::$instance The one true PYIS_HelpScout_Drip
		 */
		public static function instance() {
			
			static $instance = null;
			
			if ( null === $instance ) {
				$instance = new static();
			}
			
			return $instance;

		}
		
		protected function __construct() {
			
			$this->setup_constants();
			$this->load_textdomain();
			
			$this->require_necessities();
			
			// Register our CSS/JS for the whole plugin
			add_action( 'init', array( $this, 'register_scripts' ) );
			
		}

		/**
		 * Setup plugin constants
		 *
		 * @access	  private
		 * @since	  1.0.0
		 * @return	  void
		 */
		private function setup_constants() {
			
			// WP Loads things so weird. I really want this function.
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . '/wp-admin/includes/plugin.php';
			}
			
			// Only call this once, accessible always
			$this->plugin_data = get_plugin_data( __FILE__ );
			
			if ( ! defined( 'PYIS_HelpScout_Drip_ID' ) ) {
				// Plugin Text Domain
				define( 'PYIS_HelpScout_Drip_ID', $this->plugin_data['TextDomain'] );
			}

			if ( ! defined( 'PYIS_HelpScout_Drip_VER' ) ) {
				// Plugin version
				define( 'PYIS_HelpScout_Drip_VER', $this->plugin_data['Version'] );
			}

			if ( ! defined( 'PYIS_HelpScout_Drip_DIR' ) ) {
				// Plugin path
				define( 'PYIS_HelpScout_Drip_DIR', plugin_dir_path( __FILE__ ) );
			}

			if ( ! defined( 'PYIS_HelpScout_Drip_URL' ) ) {
				// Plugin URL
				define( 'PYIS_HelpScout_Drip_URL', plugin_dir_url( __FILE__ ) );
			}
			
			if ( ! defined( 'PYIS_HelpScout_Drip_FILE' ) ) {
				// Plugin File
				define( 'PYIS_HelpScout_Drip_FILE', __FILE__ );
			}

		}

		/**
		 * Internationalization
		 *
		 * @access	  private 
		 * @since	  1.0.0
		 * @return	  void
		 */
		private function load_textdomain() {

			// Set filter for language directory
			$lang_dir = PYIS_HelpScout_Drip_DIR . '/languages/';
			$lang_dir = apply_filters( 'pyis_helpscout_drip_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), PYIS_HelpScout_Drip_ID );
			$mofile = sprintf( '%1$s-%2$s.mo', PYIS_HelpScout_Drip_ID, $locale );

			// Setup paths to current locale file
			$mofile_local   = $lang_dir . $mofile;
			$mofile_global  = WP_LANG_DIR . '/' . PYIS_HelpScout_Drip_ID . '/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/pyis-helpscout-drip/ folder
				// This way translations can be overridden via the Theme/Child Theme
				load_textdomain( PYIS_HelpScout_Drip_ID, $mofile_global );
			}
			else if ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/pyis-helpscout-drip/languages/ folder
				load_textdomain( PYIS_HelpScout_Drip_ID, $mofile_local );
			}
			else {
				// Load the default language files
				load_plugin_textdomain( PYIS_HelpScout_Drip_ID, false, $lang_dir );
			}

		}
		
		/**
		 * Include different aspects of the Plugin
		 * 
		 * @access	  private
		 * @since	  1.0.0
		 * @return	  void
		 */
		private function require_necessities() {
			
			if ( is_admin() ) {
				
				require_once PYIS_HelpScout_Drip_DIR . 'core/admin/pyis-helpscout-drip-admin.php';
				$this->settings = new PYIS_HelpScout_Drip_Admin();
				
			}
			
			$api_key = get_option( 'pyis_drip_api_key' );
			$api_key = ( $api_key ) ? $api_key : '';
			
			$account_id = get_option( 'pyis_drip_account_id' );
			$account_id = ( $account_id ) ? $account_id : '';
			
			$account_password = get_option( 'pyis_drip_account_password' );
			$account_password = ( $account_password ) ? $account_password : '';
			
			require_once PYIS_HelpScout_Drip_DIR . 'core/api/pyis-helpscout-drip-api-drip.php';
			$this->drip_api = new PYIS_HelpScout_Drip_API_Drip( $api_key, $account_id, $account_password );
			
			require_once PYIS_HelpScout_Drip_DIR . 'core/rest/pyis-helpscout-drip-helpscout-rest.php';
			$this->rest = new PYIS_HelpScout_Drip_REST();
			
		}
		
		/**
		 * Show admin errors.
		 * 
		 * @access	  public
		 * @since	  1.0.0
		 * @return	  HTML
		 */
		public function admin_errors() {
			?>
			<div class="error">
				<?php foreach ( $this->admin_errors as $notice ) : ?>
					<p>
						<?php echo $notice; ?>
					</p>
				<?php endforeach; ?>
			</div>
			<?php
		}
		
		/**
		 * Register our CSS/JS to use later
		 * 
		 * @access	  public
		 * @since	  1.0.0
		 * @return	  void
		 */
		public function register_scripts() {
			
			wp_register_style(
				PYIS_HelpScout_Drip_ID . '-admin',
				PYIS_HelpScout_Drip_URL . 'assets/css/admin.css',
				null,
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : PYIS_HelpScout_Drip_VER
			);
			
			wp_register_script(
				PYIS_HelpScout_Drip_ID . '-admin',
				PYIS_HelpScout_Drip_URL . 'assets/js/admin.js',
				array( 'jquery' ),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : PYIS_HelpScout_Drip_VER,
				true
			);
			
			wp_localize_script( 
				PYIS_HelpScout_Drip_ID . '-admin',
				'pYISHelpScoutDrip',
				apply_filters( 'pyis_helpscout_drip_localize_admin_script', array() )
			);
			
		}
		
	}
	
} // End Class Exists Check

/**
 * The main function responsible for returning the one true PYIS_HelpScout_Drip
 * instance to functions everywhere
 *
 * @since	  1.0.0
 * @return	  \PYIS_HelpScout_Drip The one true PYIS_HelpScout_Drip
 */
add_action( 'plugins_loaded', 'pyis_helpscout_drip_load' );
function pyis_helpscout_drip_load() {

	require_once __DIR__ . '/core/pyis-helpscout-drip-functions.php';
	PYISHELPSCOUTDRIP();

}
