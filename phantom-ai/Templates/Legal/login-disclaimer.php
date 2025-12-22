<?php
/**
 * Login Screen Legal Disclaimer
 *
 * @package PhantomAI\Templates\Legal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) && ! defined( 'PHANTOM_AI_CLI' ) ) {
	exit;
}

$current_year = date( 'Y' );
?>
<div class="phantom-legal-disclaimer login-disclaimer">
	<h3>⚠️ PROPRIETARY SYSTEM</h3>
	
	<p class="copyright">
		© 2013 – <?php echo htmlspecialchars( $current_year, ENT_QUOTES, 'UTF-8' ); ?> My Deme, LLC. All Rights Reserved.
	</p>
	
	<p class="warning">
		This system is the exclusive property of My Deme, LLC.<br>
		Unauthorized access, use, or attempt to access this system is strictly prohibited<br>
		and may result in civil and criminal legal action.
	</p>
</div>
