<?php
/**
 * HTML View for Lead Score within Helpscout
 *
 * @since 1.0.0
 *
 * @package PYIS_HelpScout_Drip
 * @subpackage PYIS_HelpScout_Drip/core/views
 */

defined( 'ABSPATH' ) || die();

?>
	
<?php do_action( 'pyis_helpscout_drip_before_lead_score', $lead_score, $subscriber_email ); ?>
	
<p>
	<?php printf( _x( 'Lead Score: %s', 'Lead Score in Drip Output', PYIS_HelpScout_Drip_ID ), $lead_score ); ?>
</p>

<?php do_action( 'pyis_helpscout_drip_after_lead_score', $lead_score, $subscriber_email ); ?>