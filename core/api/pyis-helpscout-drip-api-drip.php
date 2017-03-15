<?php
/**
 * Drip API v3.0 Communication Class
 *
 * @since 1.0.0
 *
 * @package PYIS_HelpScout_Drip
 * @subpackage PYIS_HelpScout_Drip/core/api
 */

defined( 'ABSPATH' ) || die();

if ( ! class_exists( 'PYIS_HelpScout_Drip_API_Class' ) ) {
	require_once PYIS_HelpScout_Drip_DIR . 'core/api/pyis-helpscout-drip-api.php';
}

class PYIS_HelpScout_Drip_API_Drip extends PYIS_HelpScout_Drip_API_Class {

	/**
	 * @var			PYIS_HelpScout_Drip_API_Drip $api_key Holds set API Key
	 * @since		1.0.0
	 */
	private $api_key = '';
	
	/**
	 * @var			PYIS_HelpScout_Drip_API_Drip $account_id The Account ID the API Key belongs to. Yep, we need both.
	 * @since		1.0.0
	 */
	public $account_id = '';
	
	/**
	 * @var		PYIS_HelpScout_Drip_API_Drip $password The Account ID the API Key belongs to. Yep, we need both.
	 * @since		1.0.0
	 */
	private $password = '';
	
	/**
	 * @var			PYIS_HelpScout_Drip_API_Drip $api_endpoint Holds set API Endpoint
	 * @since		1.0.0
	 */
	public $api_endpoint = 'https://api.getdrip.com/v2/<account_id>';

	/**
	 * PYIS_HelpScout_Drip_API_Drip constructor.
	 * 
	 * @since		1.0.0
	 */
	function __construct( $api_key, $account_id, $password ) {

		$this->api_key = trim( $api_key );
		
		// Construct the appropriate API Endpoint		
		$this->account_id = trim( $account_id );
		$this->api_endpoint = str_replace( '<account_id>', $this->account_id, $this->api_endpoint );
		
		$this->password = $password;
		
		$this->set_headers( array(
			'Authorization' => 'Basic ' . base64_encode( $this->api_key . ':' . $this->password ),
			'Content-Type' => 'application/vnd.api+json',
		) );

	}

}