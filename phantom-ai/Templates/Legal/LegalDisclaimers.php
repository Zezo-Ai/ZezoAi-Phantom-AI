/**
 * Legal Disclaimers Helper Class
 *
 * Provides methods to retrieve legal disclaimer text with auto-updating copyright year.
 *
 * @package PhantomAI\Templates\Legal
 */

namespace PhantomAI\Templates\Legal;

/**
 * LegalDisclaimers class
 */
class LegalDisclaimers {
	/**
	 * Get current year
	 *
	 * @return string Current year
	 */
	public static function get_current_year(): string {
		return date( 'Y' );
	}

	/**
	 * Get login disclaimer text
	 *
	 * @param bool $html Whether to return HTML formatted (default: false)
	 * @return string Disclaimer text
	 */
	public static function get_login_disclaimer( bool $html = false ): string {
		$current_year = self::get_current_year();
		
		if ( $html ) {
			ob_start();
			include __DIR__ . '/login-disclaimer.php';
			return ob_get_clean();
		}
		
		return "⚠️ PROPRIETARY SYSTEM\n\n" .
			"© 2013 – {$current_year} My Deme, LLC. All Rights Reserved.\n\n" .
			"This system is the exclusive property of My Deme, LLC.\n" .
			"Unauthorized access, use, or attempt to access this system is strictly prohibited\n" .
			"and may result in civil and criminal legal action.";
	}

	/**
	 * Get full legal notice text
	 *
	 * @param bool $html Whether to return HTML formatted (default: false)
	 * @return string Legal notice text
	 */
	public static function get_full_legal_notice( bool $html = false ): string {
		$current_year = self::get_current_year();
		
		if ( $html ) {
			ob_start();
			include __DIR__ . '/full-legal-notice.php';
			return ob_get_clean();
		}
		
		return "⚠️ PROPRIETARY INTELLECTUAL PROPERTY NOTICE\n\n" .
			"© 2013 – {$current_year} My Deme, LLC. All Rights Reserved.\n\n" .
			"This software, system, server infrastructure, website, and all associated files,\n" .
			"source code, object code, documentation, designs, workflows, processes, and materials\n" .
			"(collectively, the \"System\") are the exclusive proprietary intellectual property of\n" .
			"My Deme, LLC, a Florida Limited Liability Company.\n\n" .
			"LEGAL PROTECTION:\n" .
			"This System is protected under the laws of the State of Florida and the United States\n" .
			"of America, including but not limited to:\n\n" .
			"• The U.S. Copyright Act (17 U.S.C. § 101 et seq.)\n" .
			"• The Digital Millennium Copyright Act (DMCA)\n" .
			"• The Computer Fraud and Abuse Act (18 U.S.C. § 1030)\n" .
			"• Florida Statutes Chapter 812 (Theft, Robbery, and Related Crimes)\n" .
			"• Applicable trade secret and unfair competition laws\n\n" .
			"UNAUTHORIZED ACCESS, USE, COPYING, REPRODUCTION, DISTRIBUTION, MODIFICATION,\n" .
			"REVERSE ENGINEERING, DISCLOSURE, OR DERIVATIVE USE OF THIS SYSTEM IS STRICTLY\n" .
			"PROHIBITED and may result in severe civil and criminal penalties under state and\n" .
			"federal law.\n\n" .
			"Violators will be pursued to the fullest extent permitted by law.\n\n" .
			"For licensing inquiries or authorized access, contact:\n" .
			"My Deme, LLC\n" .
			"Florida, United States";
	}

	/**
	 * Get footer disclaimer text
	 *
	 * @param bool $html Whether to return HTML formatted (default: false)
	 * @return string Footer disclaimer text
	 */
	public static function get_footer_disclaimer( bool $html = false ): string {
		$current_year = self::get_current_year();
		
		if ( $html ) {
			ob_start();
			include __DIR__ . '/footer-disclaimer.php';
			return ob_get_clean();
		}
		
		return "© 2013 – {$current_year} My Deme, LLC. All Rights Reserved.\n\n" .
			"Phantom.ai\n\n" .
			"Protected under Florida State Law and United States Federal Law.\n\n" .
			"PROPRIETARY AND CONFIDENTIAL.\n" .
			"Unauthorized access, use, or disclosure is strictly prohibited.\n\n" .
			"This system is monitored.\n" .
			"All access attempts are logged and may be used as evidence\n" .
			"in civil or criminal legal proceedings.";
	}

	/**
	 * Render login disclaimer
	 *
	 * @return void
	 */
	public static function render_login_disclaimer(): void {
		include __DIR__ . '/login-disclaimer.php';
	}

	/**
	 * Render full legal notice
	 *
	 * @return void
	 */
	public static function render_full_legal_notice(): void {
		include __DIR__ . '/full-legal-notice.php';
	}

	/**
	 * Render footer disclaimer
	 *
	 * @return void
	 */
	public static function render_footer_disclaimer(): void {
		include __DIR__ . '/footer-disclaimer.php';
	}
}
