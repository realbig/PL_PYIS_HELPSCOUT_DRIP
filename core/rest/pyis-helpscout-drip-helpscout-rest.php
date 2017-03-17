<?php
/**
 * Creates REST Endpoints
 *
 * @since 1.0.0
 *
 * @package PYIS_HelpScout_Drip
 * @subpackage PYIS_HelpScout_Drip/core/rest
 */

defined( 'ABSPATH' ) || die();

class PYIS_HelpScout_Drip_REST {
	
	/**
	 * @var			PYIS_HelpScout_Drip_REST $helpscout_data Holds the incoming JSON from HelpScout
	 * @since		1.0.0
	 */
	private $helpscout_data;
	
	/**
	 * @var			PYIS_HelpScout_Drip_REST $drip_data Holds the received JSON from Drip
	 * @since		1.0.0
	 */
	private $drip_data;

	/**
	 * PYIS_HelpScout_Drip_REST constructor.
	 *
	 * @since		1.0.0
	 */
	function __construct() {

		add_action( 'rest_api_init', array( $this, 'create_routes' ) );

	}

	/**
	 * Creates a WP REST API route for HelpScout to POST JSON to
	 * 
	 * @since		1.0.0
	 * @access		public
	 * @return		void
	 */
	public function create_routes() {

		register_rest_route( 'pyis/v1', '/helpscout/submit', array(
			'methods' => 'POST',
			'callback' => array( $this, 'endpoint' ),
		) );

	}

	/**
	 * Callback for our REST Endpoint
	 * 
	 * @param		object $request WP_REST_Request Object
	 *                                         
	 * @access		public
	 * @since		1.0.0
	 * @return		string JSON
	 */
	public function endpoint( $request ) {
		
		// Capture incoming JSON from HelpScout
		$this->helpscout_data = $this->get_incoming_data();

		// Ensure the request is valid. Also ensures random people aren't abusing the endpoint
		if ( ! $this->validate() ) {
			$this->respond( _x( 'Access Denied', 'Invalid Request Error', PYIS_HelpScout_Drip_ID ) );
			exit;
		}
		
		// Use Helpscout Data to get data from Drip
		$this->drip_data = PYISHELPSCOUTDRIP()->drip_api->get( 'subscribers/' . $this->helpscout_data['customer']['email'] );
		
		// Build HTML out of our data
		$html = $this->build_response_html();
		
		// Give HelpScout the HTML as JSON
		$this->respond( $html );

	}
	
	/**
	 * Captures incoming JSON
	 * Stored as an Associative Array so we can use isset() which is more precise for our needs
	 * 
	 * @acess		private
	 * @since		1.0.0
	 * @return		array Associative Array representation of the JSON
	 */
	private function get_incoming_data() {
		
		$json = file_get_contents( 'php://input' );
		
		return json_decode( $json, true );
		
	}
	
	/**
	 * Ensures the Request to the WP Site is valid
	 * 
	 * @access		private
	 * @since		1.0.0
	 * @return		boolean Valid/Invalid Request
	 */
	private function validate() {
		
		// we need at least this
		if ( ! isset( $this->helpscout_data['customer']['email'] ) && 
			! isset( $this->helpscout_data['customer']['emails'] ) ) {
			return false;
		}
		
		// check request signature
		if ( isset( $_SERVER['HTTP_X_HELPSCOUT_SIGNATURE'] ) && 
			$_SERVER['HTTP_X_HELPSCOUT_SIGNATURE'] == $this->hash_secret_key( get_option( 'pyis_helpscout_secret_key' ) ) ) {
			return true;
		}
		
		return false;
		
	}
	
	/**
	 * Hashes the Secret Key to match the Signature from HelpScout
	 * 
	 * @param		string $secret_key Secret Key stored in WP Database
	 *                                                    
	 * @access		private
	 * @since		1.0.0
	 * @return		string Hashed Secret Key
	 */
	private function hash_secret_key( $secret_key ) {
		
		return base64_encode( hash_hmac( 'sha1', json_encode( $this->helpscout_data ), $secret_key, true ) );
		
	}
	
	/**
	 * Constructs HTML for the Response to HelpScout
	 * 
	 * @access		private
	 * @since		1.0.0
	 * @return		string HTML
	 */
	private function build_response_html() {
		
		$subscriber_email = $this->helpscout_data['customer']['email'];
		
		// This is the only case where we immediately bail, for obvious reasons
		if ( property_exists( $this->drip_data, 'errors' ) ) {
			
			return '<div class="toggleGroup">' . str_replace( "\t", '', $this->error_message_row( sprintf( _x( '<a href="mailto:%s">%s</a> does not exist in Drip.', 'Email Address does not exist in Drip', PYIS_HelpScout_Drip_ID ), $subscriber_email, $subscriber_email ), $subscriber_email ) ) . '</div><div class="divider"></div>';
			
		}
		
		// Drip returns things as an Array here which holds other Arrays of Tags. To ensure we get them all no matter what, array_map
		// array_values() + reset() forces it all into a flat array and overwrites any duplicates
		$tags = array_values( reset( array_map( function( $subscriber ) {
			return $subscriber->tags;
		}, $this->drip_data->subscribers ) ) );
		
		$lead_scores = array_values( array_map( function( $subscriber ) {
			return $subscriber->lead_score;
		}, $this->drip_data->subscribers ) );
		
		// build HTML output
		$html = '<div class="toggleGroup">';
		
		// While there should technically never be more than one per Email, Drip is weird and returns Subscribers in an Array
		// I could always reset() it, but this way if there's some edge-case I'm not aware of it will still show the data
		foreach( $lead_scores as $lead_score ) {
			
			if ( $lead_score == NULL ) {
				
				$html .= str_replace( "\t", '', $this->error_message_row( sprintf( _x( 'No Lead Score for <a href="mailto:%s">%s</a> in Drip.', 'No Lead Score for Email Address in Drip', PYIS_HelpScout_Drip_ID ), $subscriber_email, $subscriber_email ), $subscriber_email ) );
				
				continue;
				
			}
			
			$html .= str_replace( "\t", '', $this->lead_score_row( $lead_score, $subscriber_email ) );
			
		}
		
		$acceptable_tags = get_option( 'pyis_helpscout_drip_acceptable_tags', '' );
		$acceptable_tags = array_filter( explode( ',', $acceptable_tags ) );
		
		if ( count( $tags ) == 0 ) {
			$html .= str_replace( "\t", '', $this->error_message_row( sprintf( _x( 'No Tags for <a href="mailto:%s">%s</a> in Drip', 'Email Address has no Tags in Drip', PYIS_HelpScout_Drip_ID ), $subscriber_email, $subscriber_email ), $subscriber_email ) );
		}
		
		foreach ( $tags as $tag ) {
			
			// If we're only allowing certain Tags through, we need to do some more processing
			if ( ! empty( $acceptable_tags ) ) {
				
				$match = false;
				foreach ( $acceptable_tags as $regex ) {
					
					// If the Tag matches a Tag Pattern, let the outter loop know we have a match
					if ( (bool) preg_match( '/' . $regex . '/i', $tag ) ) {
						$match = true;
						break;
					}
					
				}
				
				// If the Tag matched none of the Tag Patterns, skip it
				if ( ! $match ) continue;
				
			}
			
			$html .= str_replace( "\t", '', $this->tag_row( $tag, $subscriber_email ) );
			
		}
		
		// We only want to add the link back to Drip if the current HelpScout User is the same as the one who owns the Drip account
		// Otherwise the link goes nowhere, and that's no fun
		$helpscout_user_id = get_option( 'pyis_helpscout_user_id' );
		if ( trim( $helpscout_user_id ) == $this->helpscout_data['user']['id'] ) {
		
			// Add a link to the Subscriber in Drip
			$subscriber_id = reset( array_values( array_map( function( $subscriber ) {
				return $subscriber->id;
			}, $this->drip_data->subscribers ) ) );

			$html .= str_replace( "\t", '', $this->subscriber_link_row( $subscriber_id, $subscriber_email ) );
			
		}
		
		$html .= '</div><div class="divider"></div>';
		
		return $html;
		
	}
	
	/**
	 * Generates HTML for each Error Message
	 * 
	 * @param		string $error_message Error Message
	 * @param		string $subscriber_email Subscriber Email
	 *							  
	 * @access		public
	 * @since		1.0.0
	 * @return		string HTML
	 */
	public function error_message_row( $error_message, $subscriber_email ) {
		
		ob_start();
		
		include PYIS_HelpScout_Drip_DIR . 'core/views/pyis-helpscout-drip-error-message-row.php';
		
		$html = ob_get_clean();
		
		return $html;
		
	}
	
	/**
	 * Generates HTML for each Lead Score
	 * 
	 * @param		string $lead_score Lead Score from Drip
	 * @param		string $subscriber_email Subscriber Email
	 *							  
	 * @access		public
	 * @since		1.0.0
	 * @return		string HTML
	 */
	public function lead_score_row( $lead_score, $subscriber_email ) {
		
		ob_start();
		
		include PYIS_HelpScout_Drip_DIR . 'core/views/pyis-helpscout-drip-lead-score-row.php';
		
		$html = ob_get_clean();
		
		return $html;
		
	}
	
	/**
	 * Generates HTML for each Tag
	 * 
	 * @param		string $tag Tag from Drip
	 * @param		string $subscriber_email Subscriber Email
	 *							  
	 * @access		public
	 * @since		1.0.0
	 * @return		string HTML
	 */
	public function tag_row( $tag, $subscriber_email ) {
		
		ob_start();
		
		include PYIS_HelpScout_Drip_DIR . 'core/views/pyis-helpscout-drip-tag-row.php';
		
		$html = ob_get_clean();
		
		return $html;
		
	}
	
	/**
	 * Generates HTML for link to the Subscriber on Drip
	 * 
	 * @param		string $subscriber_id Subscriber ID
	 * @param		string $subscriber_email Subscriber Email
	 *							  
	 * @access		public
	 * @since		1.0.0
	 * @return		string HTML
	 */
	public function subscriber_link_row( $subscriber_id, $subscriber_email ) {
		
		ob_start();
		
		include PYIS_HelpScout_Drip_DIR . 'core/views/pyis-helpscout-drip-subscriber-link-row.php';
		
		$html = ob_get_clean();
		
		return $html;
		
	}
	
	/**
	 * Renders Response after a Request
	 * 
	 * @param		string  $html HTML to be sent to HelpScout
	 * @param		integer $code HTTP Response Code. Defaults to 200
	 *													   
	 * @access		private
	 * @since		1.0.0
	 * @return		void
	 */
	private function respond( $html, $code = 200 ) {
		
		$response = array( 'html' => $html );
		
		// Clear output, other plugins might have thrown dumb errors by now.
		if ( ob_get_level() > 0 ) {
			ob_end_clean();
		}
		
		status_header( $code );
		
		header( "Content-Type: application/json" );
		echo json_encode( $response );
		
		die();
		
	}

}