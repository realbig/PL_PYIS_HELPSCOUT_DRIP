<?php
/**
 * Creates REST Endpoints
 *
 * @since 0.1.0
 *
 * @package PYIS_HelpScout_Drip
 * @subpackage PYIS_HelpScout_Drip/core/rest
 */

defined( 'ABSPATH' ) || die();

class PYIS_HelpScout_Drip_REST {


    /**
	 * PYIS_HelpScout_Drip_REST constructor.
	 *
	 * @since 0.1.0
	 */
    function __construct() {

        add_action( 'rest_api_init', array( $this, 'create_routes' ) );

    }

    /**
     * Creates a WP REST API route for HelpScout to POST JSON to
     * 
     * @since       0.1.0
     * @access      public
     * @return      void
     */
    public function create_routes() {

        register_rest_route( 'pyis/v1', '/helpscout/submit', array(
            'methods' => 'POST',
            'callback' => array( $this, 'send_to_helpscout' ),
        ) );

    }

    /**
     * Callback for our REST Endpoint
     * 
     * @param       object $request WP_REST_Request Object
     * @return      string JSON
     */
    public function send_to_helpscout( $request ) {

        $json = file_get_contents( 'php://input' );

        if ( empty( $json ) ) {
            return json_encode( array(
                'success' => false,
                'message' => _x( 'No data payload', 'No JSON Uploaded Error', PYIS_HelpScout_Drip_ID ),
            ) );
        }
        
        $json = json_decode( $json );
        
        return json_encode( array(
            'success' => true,
            'message' => __( 'Success!', PYIS_HelpScout_Drip_ID ),
        ) );

    }

}