<?php
/**
 * Full Legal / Intellectual Property Notice
 *
 * @package PhantomAI\Templates\Legal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) && ! defined( 'PHANTOM_AI_CLI' ) ) {
	exit;
}

$current_year = date( 'Y' );
?>
<div class="phantom-legal-disclaimer full-legal-notice">
	<h2>⚠️ PROPRIETARY INTELLECTUAL PROPERTY NOTICE</h2>
	
	<p class="copyright">
		© 2013 – <?php echo htmlspecialchars( $current_year, ENT_QUOTES, 'UTF-8' ); ?> My Deme, LLC. All Rights Reserved.
	</p>
	
	<p class="system-definition">
		This software, system, server infrastructure, website, and all associated files,
		source code, object code, documentation, designs, workflows, processes, and materials
		(collectively, the "System") are the exclusive proprietary intellectual property of
		My Deme, LLC, a Florida Limited Liability Company.
	</p>
	
	<h3>LEGAL PROTECTION:</h3>
	<p>
		This System is protected under the laws of the State of Florida and the United States
		of America, including but not limited to:
	</p>
	
	<ul class="legal-protections">
		<li>The U.S. Copyright Act (17 U.S.C. § 101 et seq.)</li>
		<li>The Digital Millennium Copyright Act (DMCA)</li>
		<li>The Computer Fraud and Abuse Act (18 U.S.C. § 1030)</li>
		<li>Florida Statutes Chapter 812 (Theft, Robbery, and Related Crimes)</li>
		<li>Applicable trade secret and unfair competition laws</li>
	</ul>
	
	<p class="warning-text">
		<strong>UNAUTHORIZED ACCESS, USE, COPYING, REPRODUCTION, DISTRIBUTION, MODIFICATION,
		REVERSE ENGINEERING, DISCLOSURE, OR DERIVATIVE USE OF THIS SYSTEM IS STRICTLY
		PROHIBITED</strong> and may result in severe civil and criminal penalties under state and
		federal law.
	</p>
	
	<p class="enforcement">
		Violators will be pursued to the fullest extent permitted by law.
	</p>
	
	<p class="contact">
		For licensing inquiries or authorized access, contact:<br>
		<strong>My Deme, LLC</strong><br>
		Florida, United States
	</p>
</div>
