<?php
/**
 * Provides helper functions.
 *
 * @since	  1.0.0
 *
 * @package	PYIS_HelpScout_Drip
 * @subpackage PYIS_HelpScout_Drip/core
 */
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Returns the main plugin object
 *
 * @since		1.0.0
 *
 * @return		PYIS_HelpScout_Drip
 */
function PYISHELPSCOUTDRIP() {
	return PYIS_HelpScout_Drip::instance();
}