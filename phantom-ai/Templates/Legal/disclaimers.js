/**
 * Legal Disclaimers for Phantom.ai
 * 
 * These are the authoritative legal text blocks to be used across the Phantom.ai interface.
 * 
 * @package PhantomAI\Templates\Legal
 */

const PhantomLegalDisclaimers = {
	/**
	 * Get current year for copyright notices
	 */
	getCurrentYear: () => new Date().getFullYear(),

	/**
	 * Login Screen Legal Disclaimer
	 */
	loginDisclaimer: () => {
		const currentYear = PhantomLegalDisclaimers.getCurrentYear();
		return `⚠️ PROPRIETARY SYSTEM

© 2013 – ${currentYear} My Deme, LLC. All Rights Reserved.

This system is the exclusive property of My Deme, LLC.
Unauthorized access, use, or attempt to access this system is strictly prohibited
and may result in civil and criminal legal action.`;
	},

	/**
	 * Full Legal / Intellectual Property Notice
	 */
	fullLegalNotice: () => {
		const currentYear = PhantomLegalDisclaimers.getCurrentYear();
		return `⚠️ PROPRIETARY INTELLECTUAL PROPERTY NOTICE

© 2013 – ${currentYear} My Deme, LLC. All Rights Reserved.

This software, system, server infrastructure, website, and all associated files,
source code, object code, documentation, designs, workflows, processes, and materials
(collectively, the "System") are the exclusive proprietary intellectual property of
My Deme, LLC, a Florida Limited Liability Company.

LEGAL PROTECTION:
This System is protected under the laws of the State of Florida and the United States
of America, including but not limited to:

• The U.S. Copyright Act (17 U.S.C. § 101 et seq.)
• The Digital Millennium Copyright Act (DMCA)
• The Computer Fraud and Abuse Act (18 U.S.C. § 1030)
• Florida Statutes Chapter 812 (Theft, Robbery, and Related Crimes)
• Applicable trade secret and unfair competition laws

UNAUTHORIZED ACCESS, USE, COPYING, REPRODUCTION, DISTRIBUTION, MODIFICATION,
REVERSE ENGINEERING, DISCLOSURE, OR DERIVATIVE USE OF THIS SYSTEM IS STRICTLY
PROHIBITED and may result in severe civil and criminal penalties under state and
federal law.

Violators will be pursued to the fullest extent permitted by law.

For licensing inquiries or authorized access, contact:
My Deme, LLC
Florida, United States`;
	},

	/**
	 * Footer Legal Disclaimer
	 */
	footerDisclaimer: () => {
		const currentYear = PhantomLegalDisclaimers.getCurrentYear();
		return `© 2013 – ${currentYear} My Deme, LLC. All Rights Reserved.

Phantom.ai

Protected under Florida State Law and United States Federal Law.

PROPRIETARY AND CONFIDENTIAL.
Unauthorized access, use, or disclosure is strictly prohibited.

This system is monitored.
All access attempts are logged and may be used as evidence
in civil or criminal legal proceedings.`;
	},

	/**
	 * Get disclaimer as HTML
	 */
	getLoginDisclaimerHTML: () => {
		const currentYear = PhantomLegalDisclaimers.getCurrentYear();
		return `<div class="phantom-legal-disclaimer login-disclaimer">
	<h3>⚠️ PROPRIETARY SYSTEM</h3>
	<p class="copyright">© 2013 – ${currentYear} My Deme, LLC. All Rights Reserved.</p>
	<p class="warning">
		This system is the exclusive property of My Deme, LLC.<br>
		Unauthorized access, use, or attempt to access this system is strictly prohibited<br>
		and may result in civil and criminal legal action.
	</p>
</div>`;
	},

	getFullLegalNoticeHTML: () => {
		const currentYear = PhantomLegalDisclaimers.getCurrentYear();
		return `<div class="phantom-legal-disclaimer full-legal-notice">
	<h2>⚠️ PROPRIETARY INTELLECTUAL PROPERTY NOTICE</h2>
	<p class="copyright">© 2013 – ${currentYear} My Deme, LLC. All Rights Reserved.</p>
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
	<p class="enforcement">Violators will be pursued to the fullest extent permitted by law.</p>
	<p class="contact">
		For licensing inquiries or authorized access, contact:<br>
		<strong>My Deme, LLC</strong><br>
		Florida, United States
	</p>
</div>`;
	},

	getFooterDisclaimerHTML: () => {
		const currentYear = PhantomLegalDisclaimers.getCurrentYear();
		return `<footer class="phantom-legal-disclaimer footer-disclaimer">
	<p class="copyright">© 2013 – ${currentYear} My Deme, LLC. All Rights Reserved.</p>
	<p class="brand"><strong>Phantom.ai</strong></p>
	<p class="legal-protection">Protected under Florida State Law and United States Federal Law.</p>
	<p class="confidentiality">
		<strong>PROPRIETARY AND CONFIDENTIAL.</strong><br>
		Unauthorized access, use, or disclosure is strictly prohibited.
	</p>
	<p class="monitoring">
		This system is monitored.<br>
		All access attempts are logged and may be used as evidence<br>
		in civil or criminal legal proceedings.
	</p>
</footer>`;
	}
};

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
	module.exports = PhantomLegalDisclaimers;
}
