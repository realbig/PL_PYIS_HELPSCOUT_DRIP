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

<div class="toggleGroup">
	
	<?php echo $tag; ?>

	<?php do_action( 'pyis_helpscout_drip_after_tag', $tag ); ?>

</div>

<div class="divider"></div>