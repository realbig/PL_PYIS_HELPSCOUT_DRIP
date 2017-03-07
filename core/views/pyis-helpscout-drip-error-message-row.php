<?php
/**
 * HTML View for an Error Message within Helpscout
 *
 * @since 1.0.0
 *
 * @package PYIS_HelpScout_Drip
 * @subpackage PYIS_HelpScout_Drip/core/views
 */

defined( 'ABSPATH' ) || die();

?>
	
<?php do_action( 'pyis_helpscout_drip_before_error_message', $error_message, $subscriber_email ); ?>
	
<p>
	<?php echo $error_message; ?>
</p>

<?php do_action( 'pyis_helpscout_drip_after_error_message', $error_message, $subscriber_email ); ?>