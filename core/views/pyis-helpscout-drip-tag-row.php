<?php
/**
 * HTML View for Tags within Helpscout
 *
 * @since 1.0.0
 *
 * @package PYIS_HelpScout_Drip
 * @subpackage PYIS_HelpScout_Drip/core/views
 */

defined( 'ABSPATH' ) || die();

?>
	
<?php do_action( 'pyis_helpscout_drip_before_tag', $tag, $subscriber_email ); ?>
	
<span class="badge info" style="text-transform: none; margin: 0 0.5em 0.5em 0;">
	<?php echo $tag; ?>
</span>

<?php do_action( 'pyis_helpscout_drip_after_tag', $tag, $subscriber_email ); ?>