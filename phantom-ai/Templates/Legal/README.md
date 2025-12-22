# Legal Disclaimers for Phantom.ai

This directory contains the authoritative legal disclaimer text blocks for use across the Phantom.ai interface.

## Files

### PHP Templates
- **login-disclaimer.php** - Login screen legal disclaimer
- **full-legal-notice.php** - Complete intellectual property notice
- **footer-disclaimer.php** - Footer legal disclaimer
- **LegalDisclaimers.php** - Helper class for PHP applications

### JavaScript
- **disclaimers.js** - JavaScript module for frontend applications

## Usage

### PHP Usage

#### Option 1: Direct Include
```php
// Include the disclaimer file directly
include __DIR__ . '/phantom-ai/Templates/Legal/login-disclaimer.php';
```

#### Option 2: Using Helper Class
```php
use PhantomAI\Templates\Legal\LegalDisclaimers;

// Render as HTML
LegalDisclaimers::render_login_disclaimer();
LegalDisclaimers::render_full_legal_notice();
LegalDisclaimers::render_footer_disclaimer();

// Get as string (plain text)
$login_text = LegalDisclaimers::get_login_disclaimer();

// Get as HTML string
$login_html = LegalDisclaimers::get_login_disclaimer(true);
```

### JavaScript Usage

```javascript
// Import the module
const PhantomLegalDisclaimers = require('./disclaimers.js');

// Get plain text
const loginText = PhantomLegalDisclaimers.loginDisclaimer();
const fullNotice = PhantomLegalDisclaimers.fullLegalNotice();
const footerText = PhantomLegalDisclaimers.footerDisclaimer();

// Get HTML
const loginHTML = PhantomLegalDisclaimers.getLoginDisclaimerHTML();
const fullNoticeHTML = PhantomLegalDisclaimers.getFullLegalNoticeHTML();
const footerHTML = PhantomLegalDisclaimers.getFooterDisclaimerHTML();

// Insert into DOM
document.getElementById('login-disclaimer').innerHTML = loginHTML;
document.getElementById('legal-notice').innerHTML = fullNoticeHTML;
document.querySelector('footer').innerHTML = footerHTML;
```

### CLI Usage

```bash
# Display disclaimers via CLI
php -r "require 'phantom-ai/Templates/Legal/LegalDisclaimers.php'; echo PhantomAI\Templates\Legal\LegalDisclaimers::get_login_disclaimer();"
```

## Dynamic Year

All disclaimer templates automatically include the current year in copyright notices using:
- PHP: `date('Y')`
- JavaScript: `new Date().getFullYear()`

The year range displays as: `© 2013 – 2025 My Deme, LLC. All Rights Reserved.`

## Legal Text

The legal text in these files is **authoritative** and must not be modified. The exact wording has been provided for:

1. **Login Screen** - Brief warning about unauthorized access
2. **Full Legal Notice** - Complete intellectual property and legal protection statement
3. **Footer** - Compact legal disclaimer with monitoring notice

## Styling

Suggested CSS classes are provided in the HTML output:

```css
.phantom-legal-disclaimer {
	font-family: system-ui, -apple-system, sans-serif;
	color: #333;
	line-height: 1.6;
}

.phantom-legal-disclaimer.login-disclaimer {
	text-align: center;
	padding: 2rem;
	background: #fff3cd;
	border: 2px solid #856404;
	border-radius: 4px;
}

.phantom-legal-disclaimer.full-legal-notice {
	max-width: 800px;
	margin: 2rem auto;
	padding: 2rem;
	background: #fff;
	border: 1px solid #ddd;
}

.phantom-legal-disclaimer.footer-disclaimer {
	text-align: center;
	padding: 1.5rem;
	background: #f8f9fa;
	border-top: 1px solid #dee2e6;
	font-size: 0.875rem;
}

.phantom-legal-disclaimer h2,
.phantom-legal-disclaimer h3 {
	color: #721c24;
	margin-top: 0;
}

.phantom-legal-disclaimer .copyright {
	font-weight: bold;
	margin: 1rem 0;
}

.phantom-legal-disclaimer .warning,
.phantom-legal-disclaimer .warning-text {
	color: #721c24;
	font-weight: 500;
}

.phantom-legal-disclaimer .legal-protections {
	list-style-type: disc;
	padding-left: 2rem;
	margin: 1rem 0;
}

.phantom-legal-disclaimer .confidentiality,
.phantom-legal-disclaimer .monitoring {
	font-size: 0.9rem;
	margin-top: 1rem;
}
```

## Requirements

- **PHP**: 7.4 or higher
- **JavaScript**: ES6+ (for arrow functions and template literals)

## License

These legal disclaimers are proprietary content of My Deme, LLC and must not be removed or modified.

© 2013 – 2025 My Deme, LLC. All Rights Reserved.
