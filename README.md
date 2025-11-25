# DeDebtify - WordPress Debt Management Plugin

**Version:** 1.0.0
**Author:** Oxford Pierpont
**Requires:** WordPress 6.0+, PHP 8.0+
**License:** GPL-2.0+

## Description

DeDebtify is a comprehensive debt management and financial tracking plugin for WordPress. It helps users track credit cards, loans, mortgages, bills, and financial goals over multiple years with powerful calculation tools and progress tracking.

## Features

### Phase 1 (Foundation) - Completed ✓

- **6 Custom Post Types:**
  - Credit Cards (dd_credit_card)
  - Loans (dd_loan)
  - Mortgages (dd_mortgage)
  - Bills (dd_bill)
  - Goals (dd_goal)
  - Financial Snapshots (dd_snapshot)

- **Core Calculations Engine:**
  - Credit card payoff calculations
  - Loan amortization
  - Mortgage calculations
  - DTI (Debt-to-Income) ratio
  - Credit utilization
  - Bill frequency conversion

- **REST API Endpoints:**
  - Dashboard data
  - Credit cards, loans, bills, goals
  - Payoff calculations
  - Snapshot creation
  - Debt avalanche & snowball ordering

- **Admin Dashboard:**
  - Overview statistics
  - Quick actions
  - System information

- **Responsive UI:**
  - Modern, clean design
  - Mobile-friendly
  - Print-optimized styles

## Installation

1. Upload the `dedebtify` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to DeDebtify in the admin menu to configure settings

## Usage

### Basic Setup

1. Go to **DeDebtify > Settings** to configure your preferences
2. Add your credit cards, loans, and bills through the admin menu
3. Use the shortcode `[dedebtify_dashboard]` on any page to display the user dashboard

### For Users

Users can:
- Track multiple credit cards, loans, and mortgages
- Monitor recurring bills and expenses
- Set and track financial goals
- Create monthly snapshots to track progress over time
- View debt payoff projections
- Calculate different payoff scenarios (Avalanche vs Snowball)

### Shortcodes

- `[dedebtify_dashboard]` - Display the full financial dashboard

## Technical Stack

- **WordPress Core:** 6.0+
- **PHP:** 8.0+
- **MySQL:** 5.7+
- **JavaScript:** ES6+ (jQuery)
- **CSS:** Modern CSS3 with CSS Grid

## File Structure

```
dedebtify/
├── dedebtify.php                 # Main plugin file
├── uninstall.php                 # Uninstall cleanup
├── includes/
│   ├── class-dedebtify.php              # Core plugin class
│   ├── class-dedebtify-activator.php    # Activation hooks
│   ├── class-dedebtify-deactivator.php  # Deactivation hooks
│   ├── class-dedebtify-cpt.php          # Custom Post Types
│   ├── class-dedebtify-calculations.php # Calculation engine
│   └── class-dedebtify-api.php          # REST API endpoints
├── assets/
│   ├── css/
│   │   ├── dedebtify-admin.css
│   │   ├── dedebtify-public.css
│   │   └── dedebtify-print.css
│   └── js/
│       ├── dedebtify-admin.js
│       ├── dedebtify-public.js
│       └── dedebtify-calculator.js
├── admin/
│   └── settings-page.php
└── templates/
    └── dashboard.php
```

## REST API Endpoints

All endpoints are under the namespace `dedebtify/v1`:

- `GET /dashboard` - Get user dashboard data
- `POST /snapshot` - Create financial snapshot
- `POST /calculate-payoff` - Calculate debt payoff scenarios
- `GET /payoff-order/{method}` - Get debt payoff order (avalanche/snowball)
- `GET /statistics` - Get user statistics
- `GET /credit-cards` - Get all credit cards
- `GET /loans` - Get all loans
- `GET /bills` - Get all bills
- `GET /goals` - Get all goals
- `GET /snapshots` - Get snapshot history

## Calculations

### Credit Card Payoff

Uses the standard credit card payoff formula:
```
n = -log(1 - (B * r / P)) / log(1 + r)
```
Where: B = balance, r = monthly rate, P = monthly payment

### Loan Payment

Uses the amortization formula:
```
P = L[c(1 + c)^n]/[(1 + c)^n - 1]
```
Where: L = principal, c = monthly rate, n = term in months

### DTI Ratio

```
DTI = (Total Monthly Debt Payments / Monthly Income) × 100
```

### Credit Utilization

```
Utilization = (Total Balances / Total Credit Limits) × 100
```

## Upcoming Features (Phase 2+)

- [ ] Elementor integration with custom widgets
- [ ] JetEngine integration for advanced CPT management
- [ ] OneSignal push notifications
- [ ] n8n automation workflows
- [ ] Debt action plan generator
- [ ] Charts and visualization
- [ ] PDF export functionality
- [ ] BuddyBoss community integration

## Changelog

### 1.0.0 - Phase 1 (2025-10-22)
- Initial release
- Complete plugin foundation
- 6 Custom Post Types with meta boxes
- Core calculations engine
- REST API implementation
- Admin dashboard and settings
- Public user dashboard template
- Responsive CSS with print styles
- JavaScript calculation library

## Support

For support, please contact: support@yoursite.com

## License

This plugin is licensed under the GPL-2.0+ license.
