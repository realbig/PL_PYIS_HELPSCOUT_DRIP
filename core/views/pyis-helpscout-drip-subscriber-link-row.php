<?php
/**
 * HTML View for an Drip Subscriber Link within Helpscout
 *
 * @since 1.0.0
 *
 * @package PYIS_HelpScout_Drip
 * @subpackage PYIS_HelpScout_Drip/core/views
 */

defined( 'ABSPATH' ) || die();

?>
	
<?php do_action( 'pyis_helpscout_drip_before_subscriber_link', $subscriber_id, $subscriber_email ); ?>
	
<p>
	<a href="//www.getdrip.com/<?php echo PYISHELPSCOUTDRIP()->drip_api->account_id; ?>/subscribers/<?php echo $subscriber_id; ?>" target="_blank">
		<?php echo sprintf( _x( "View %s on Drip", 'Subscriber Link Text', PYIS_HelpScout_Drip_ID ), $subscriber_email ); ?>
	</a>
</p>

<?php do_action( 'pyis_helpscout_drip_after_subscriber_link', $subscriber_id, $subscriber_email ); ?>