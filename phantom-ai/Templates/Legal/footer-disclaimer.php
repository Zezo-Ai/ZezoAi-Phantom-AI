<?php
/**
 * Footer Legal Disclaimer
 *
 * @package PhantomAI\Templates\Legal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) && ! defined( 'PHANTOM_AI_CLI' ) ) {
	exit;
}

$current_year = date( 'Y' );
?>
<footer class="phantom-legal-disclaimer footer-disclaimer">
	<p class="copyright">
		© 2013 – <?php echo htmlspecialchars( $current_year, ENT_QUOTES, 'UTF-8' ); ?> My Deme, LLC. All Rights Reserved.
	</p>
	
	<p class="brand">
		<strong>Phantom.ai</strong>
	</p>
	
	<p class="legal-protection">
		Protected under Florida State Law and United States Federal Law.
	</p>
	
	<p class="confidentiality">
		<strong>PROPRIETARY AND CONFIDENTIAL.</strong><br>
		Unauthorized access, use, or disclosure is strictly prohibited.
	</p>
	
	<p class="monitoring">
		This system is monitored.<br>
		All access attempts are logged and may be used as evidence<br>
		in civil or criminal legal proceedings.
	</p>
</footer>
