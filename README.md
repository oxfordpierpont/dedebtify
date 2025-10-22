# DeDebtify - WordPress Debt Management Plugin

**Version:** 1.0.0  
**Requires WordPress:** 6.0+  
**Requires PHP:** 8.0+  
**License:** GPL-2.0+  
**Author:** Oxford Pierpont

A comprehensive debt management and financial tracking plugin for WordPress that helps users become debt-free and maintain financial health over multiple years.

---

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start Guide](#quick-start-guide)
- [Plugin Architecture](#plugin-architecture)
- [Custom Post Types](#custom-post-types)
- [REST API Endpoints](#rest-api-endpoints)
- [Elementor Integration](#elementor-integration)
- [Calculation Methods](#calculation-methods)
- [Development](#development)
- [Testing](#testing)
- [Troubleshooting](#troubleshooting)
- [Roadmap](#roadmap)
- [Contributing](#contributing)
- [Support](#support)
- [License](#license)
- [Changelog](#changelog)

---

## 🎯 Overview

DeDebtify transforms debt management from a static spreadsheet into an interactive, long-term financial tracking platform. Built specifically for membership sites, it enables users to:

- Track multiple credit cards, loans, mortgages, bills, and financial goals
- Calculate accurate payoff timelines using avalanche, snowball, or custom methods
- Create monthly financial snapshots to visualize progress over years
- Generate personalized debt action plans
- Print professional reports from any dashboard
- Access their financial data through a Progressive Web App (PWA)

**Target Audience:** Individuals with debt who want to become debt-free, manage multiple financial obligations, and need long-term tracking (months to years).

**Business Model:** Monthly membership site with multi-year retention.

---

## ✨ Features

### Core Features (MVP)

#### 📊 Dashboard Overview
- Real-time financial metrics display
- Total debt across all sources
- Monthly payment obligations
- Debt-to-Income Ratio (DTI) calculation
- Credit utilization percentage
- Projected debt-free date
- Visual debt breakdown by type
- Progress charts showing debt reduction over time

#### 💳 Credit Card Manager
- Add unlimited credit cards
- Track balances, limits, interest rates, and payments
- Calculate utilization percentage per card and overall
- Three payoff scenarios:
  - Minimum payment only
  - Minimum + extra payment
  - Avalanche/Snowball method
- Visual indicators for utilization levels (good/fair/high)
- Sort and filter by multiple criteria

#### 💰 Loan Manager
- Track personal loans, auto loans, student loans
- Amortization calculations
- Payoff projections with extra payments
- Interest savings calculator
- Multiple loan comparison

#### 🏠 Mortgage Manager
- Complete mortgage tracking
- Include property tax, insurance, PMI
- Calculate total housing costs
- Home equity tracking
- Refinancing scenario calculator
- Break-even analysis

#### 📝 Bill Tracker
- Track all recurring expenses
- Multiple frequency support (weekly, monthly, annual, etc.)
- Convert all to monthly equivalents
- Due date tracking
- Auto-pay status
- Essential vs. discretionary categorization
- Calendar view option

#### 🎯 Goals Tracker
- Set multiple financial goals
- Track progress with visual indicators
- Calculate months to goal completion
- Goal prioritization
- Quick update feature
- Milestone celebrations

#### 📸 Monthly Snapshots
- Capture complete financial picture monthly
- Historical tracking over years
- Side-by-side snapshot comparison
- Progress visualization with charts
- Automatic or manual creation

#### 📋 Debt Action Plan
- Personalized debt payoff strategies
- Three method options:
  - **Avalanche:** Highest interest rate first (mathematically optimal)
  - **Snowball:** Smallest balance first (psychologically motivating)
  - **Custom:** User-defined priority order
- Step-by-step action items
- Milestone timeline
- Interest savings calculations
- Printable one-page summary

#### 🖨️ Print Functionality
- Print-optimized layouts for all dashboards
- Professional, clean formatting
- Black & white friendly
- Logical page breaks
- Header with user name and date

### Technical Features

#### 🔌 Elementor Integration
- Custom Elementor widgets for all tools
- Dynamic tags for all metrics
- Pre-built page templates
- Full visual editing support
- Drag-and-drop interface

#### 🔗 REST API
- Complete RESTful API
- Secure authentication
- JSON responses
- Comprehensive endpoints for all data
- Input validation and sanitization

#### 📱 PWA Ready
- Service worker support
- Offline capability preparation
- Push notification integration (OneSignal)
- App-like experience on mobile

#### 🔒 Security
- User-specific data isolation
- WordPress nonce verification
- Input sanitization and validation
- SQL injection prevention
- XSS protection
- Capability checks throughout

#### ⚡ Performance
- Optimized database queries
- Calculation caching
- Minified assets
- Lazy loading where appropriate
- < 2 second page load times

---

## 📦 Requirements

### WordPress Environment
- **WordPress:** 6.0 or higher
- **PHP:** 8.0 or higher
- **MySQL:** 5.7 or higher
- **HTTPS:** Required for PWA features

### Required Plugins
- **Elementor Pro:** Latest version
- **JetEngine (Crocoblock):** Latest version (for CPT management and dynamic content)

### Optional Integrations
- **OneSignal:** For push notifications
- **BuddyBoss:** For community features
- **n8n:** For workflow automation (self-hosted)

### Browser Support
- Chrome (latest 2 versions)
- Firefox (latest 2 versions)
- Safari (latest 2 versions)
- Edge (latest 2 versions)
- Mobile browsers (iOS Safari, Chrome Mobile)

### Server Requirements
- Memory: 256MB minimum (512MB recommended)
- Disk Space: 50MB for plugin files
- PHP Extensions: mysqli, json, mbstring

---

## 🚀 Installation

### Method 1: WordPress Admin Upload (Recommended for Production)

1. **Download the Plugin**
   - Download the latest `dedebify.zip` from releases
   - Or create zip from source: `zip -r dedebify.zip dedebify/`

2. **Upload to WordPress**
```
   WordPress Admin → Plugins → Add New → Upload Plugin
```
   - Click "Choose File" and select `dedebify.zip`
   - Click "Install Now"
   - Wait for upload and installation to complete

3. **Activate the Plugin**
   - Click "Activate Plugin" when installation completes
   - Or go to Plugins → Installed Plugins → Find "DeDebtify" → Click "Activate"

### Method 2: FTP/SFTP Upload

1. **Upload via FTP**
   - Extract `dedebify.zip`
   - Upload `dedebify/` folder to `/wp-content/plugins/` directory
   - Ensure correct file permissions (644 for files, 755 for directories)

2. **Activate via WordPress Admin**
```
   WordPress Admin → Plugins → Installed Plugins → DeDebtify → Activate
```

### Method 3: WP-CLI (For Developers)
```bash
# Navigate to WordPress installation
cd /path/to/wordpress

# Upload plugin folder to plugins directory
cp -r /path/to/dedebify wp-content/plugins/

# Activate the plugin
wp plugin activate dedebify

# Verify activation
wp plugin list
```

### Post-Installation Steps

1. **Verify Required Plugins**
```
   WordPress Admin → Plugins → Installed Plugins
```
   - Ensure Elementor Pro is active
   - Ensure JetEngine is active
   - If missing, install and activate them first

2. **Check Plugin Status**
   - Look for "DeDebtify" in admin sidebar (should appear below Dashboard)
   - You should see a new menu icon (chart line)

3. **Configure JetEngine CPTs** (if not auto-registered)
   - Option A: Import from `/jetengine-exports/` folder if provided
   - Option B: Plugin will auto-register CPTs (preferred method)
   - Go to JetEngine → Post Types to verify 6 CPTs exist:
     - dd_credit_card
     - dd_loan
     - dd_mortgage
     - dd_bill
     - dd_goal
     - dd_snapshot

4. **Create Required Pages**
```
   WordPress Admin → Pages → Add New
```
   Create the following pages (assign Elementor templates later):
   - Dashboard (slug: `debt-dashboard`)
   - Credit Cards (slug: `credit-cards`)
   - Loans (slug: `loans`)
   - Mortgage (slug: `mortgage`)
   - Bills (slug: `bills`)
   - Goals (slug: `goals`)
   - Action Plan (slug: `action-plan`)

5. **Import Elementor Templates** (if provided)
```
   Elementor → Templates → Import Templates
```
   - Import all provided template files
   - Assign templates to pages created above

6. **Set User Permissions**
   - Determine which user roles can access DeDebtify
   - Default: All logged-in users can access their own data
   - For membership site: Configure with your membership plugin

7. **Test Installation**
   - Log in as a test user
   - Navigate to Dashboard page
   - Try adding a credit card
   - Verify calculations work
   - Check data saves correctly

---

## 🎓 Quick Start Guide

### For Users (How to Use DeDebtify)

#### Step 1: Set Up Your Profile
1. Navigate to Dashboard
2. Enter your monthly income (used for DTI calculation)
3. Choose your preferred debt payoff method (Avalanche or Snowball)

#### Step 2: Add Your Debts

**Add Credit Cards:**
1. Go to Credit Cards page
2. Click "Add New Credit Card"
3. Fill in:
   - Card name (e.g., "Chase Freedom")
   - Current balance
   - Credit limit
   - Interest rate (APR)
   - Minimum payment
   - Optional: Extra payment amount
   - Optional: Due date
4. Click "Save"

**Add Loans:**
1. Go to Loans page
2. Click "Add New Loan"
3. Fill in loan details (type, balance, rate, payment)
4. Click "Save"

**Add Mortgage:**
1. Go to Mortgage page
2. Fill in mortgage information
3. Include property tax, insurance, PMI if applicable
4. Click "Save"

#### Step 3: Add Your Bills
1. Go to Bills page
2. Click "Add New Bill"
3. Enter bill details (name, category, amount, frequency)
4. Mark if essential or discretionary
5. Set due date and auto-pay status
6. Click "Save"

#### Step 4: Set Financial Goals
1. Go to Goals page
2. Click "Add New Goal"
3. Choose goal type (Emergency Fund, Savings, etc.)
4. Set target amount and current amount
5. Set monthly contribution
6. Click "Save"

#### Step 5: Create Your First Snapshot
1. Return to Dashboard
2. Review all your financial metrics
3. Click "Create Monthly Snapshot"
4. This captures your current financial state
5. Repeat monthly to track progress

#### Step 6: Generate Your Action Plan
1. Go to Action Plan page
2. Select payoff method (if not already set)
3. Review personalized strategy
4. Follow action steps
5. Print for reference

#### Step 7: Track Progress
- Update balances as payments are made
- Create monthly snapshots
- View progress charts on Dashboard
- Celebrate milestones!

### For Developers (How to Customize DeDebtify)

#### Access Data Programmatically

**Get User's Total Debt:**
```php
<?php
$user_id = get_current_user_id();
$total_debt = Dedebify_Calculations::calculate_total_debt( $user_id );
echo 'Total Debt: $' . number_format( $total_debt, 2 );
?>
```

**Get User's Credit Cards:**
```php
<?php
$user_id = get_current_user_id();
$credit_cards = get_posts( array(
    'post_type'      => 'dd_credit_card',
    'author'         => $user_id,
    'posts_per_page' => -1,
) );

foreach ( $credit_cards as $card ) {
    $balance = get_post_meta( $card->ID, 'balance', true );
    echo $card->post_title . ': $' . number_format( $balance, 2 ) . '<br>';
}
?>
```

**Use Calculation Functions:**
```php
<?php
// Calculate months to pay off
$months = Dedebify_Calculations::calculate_months_to_payoff( 
    3500,   // Balance
    18.99,  // Interest rate
    275     // Monthly payment
);

// Calculate DTI ratio
$dti = Dedebify_Calculations::calculate_dti( get_current_user_id() );

// Calculate credit utilization
$utilization = Dedebify_Calculations::calculate_overall_utilization( get_current_user_id() );
?>
```

#### Use REST API Endpoints

**Get Dashboard Data (JavaScript):**
```javascript
// Using Fetch API
fetch( dedebifyData.restUrl + 'dashboard', {
    method: 'GET',
    headers: {
        'X-WP-Nonce': dedebifyData.nonce
    }
})
.then( response => response.json() )
.then( data => {
    console.log( 'Total Debt:', data.total_debt );
    console.log( 'DTI Ratio:', data.dti_ratio );
})
.catch( error => console.error( 'Error:', error ) );
```

**Create Snapshot via API:**
```javascript
// Create monthly snapshot
fetch( dedebifyData.restUrl + 'snapshot', {
    method: 'POST',
    headers: {
        'X-WP-Nonce': dedebifyData.nonce,
        'Content-Type': 'application/json'
    }
})
.then( response => response.json() )
.then( data => {
    if ( data.success ) {
        alert( 'Snapshot created successfully!' );
    }
})
.catch( error => console.error( 'Error:', error ) );
```

#### Add Custom Elementor Widgets

**Register Custom Widget:**
```php
<?php
// In your theme's functions.php or custom plugin

add_action( 'elementor/widgets/register', function( $widgets_manager ) {
    
    class My_Custom_Debt_Widget extends \Elementor\Widget_Base {
        
        public function get_name() {
            return 'my_custom_debt_widget';
        }
        
        public function get_title() {
            return __( 'My Custom Debt Display', 'my-plugin' );
        }
        
        public function get_icon() {
            return 'eicon-number-field';
        }
        
        public function get_categories() {
            return [ 'dedebify' ];
        }
        
        protected function render() {
            $user_id = get_current_user_id();
            $total_debt = Dedebify_Calculations::calculate_total_debt( $user_id );
            
            echo '<div class="my-custom-debt-display">';
            echo '<h3>Your Total Debt</h3>';
            echo '<p class="amount">$' . number_format( $total_debt, 2 ) . '</p>';
            echo '</div>';
        }
    }
    
    $widgets_manager->register( new My_Custom_Debt_Widget() );
});
?>
```

---

## 🏗️ Plugin Architecture

### Directory Structure
```
dedebify/
│
├── dedebify.php                      # Main plugin file with header
├── README.md                          # This file
├── LICENSE.txt                        # GPL-2.0 license
├── uninstall.php                      # Cleanup on uninstall
├── .gitignore                         # Git ignore rules
│
├── includes/                          # Core plugin classes
│   ├── class-dedebify.php            # Main plugin class
│   ├── class-dedebify-activator.php  # Activation hooks
│   ├── class-dedebify-deactivator.php # Deactivation hooks
│   ├── class-dedebify-cpt.php        # Custom Post Types
│   ├── class-dedebify-calculations.php # Calculation engine
│   ├── class-dedebify-api.php        # REST API endpoints
│   └── class-dedebify-elementor.php  # Elementor integration
│
├── admin/                             # Admin-specific files
│   ├── dashboard.php                 # Admin dashboard page
│   ├── settings-page.php             # Settings interface
│   └── partials/                     # Admin UI components
│       ├── settings-general.php
│       └── settings-advanced.php
│
├── assets/                            # Frontend assets
│   ├── css/
│   │   ├── dedebify-admin.css        # Admin styles
│   │   ├── dedebify-public.css       # Public styles
│   │   └── dedebify-print.css        # Print styles
│   │
│   ├── js/
│   │   ├── dedebify-admin.js         # Admin scripts
│   │   ├── dedebify-public.js        # Public scripts
│   │   └── dedebify-calculator.js    # Calculation engine (JS)
│   │
│   └── images/                       # Images and icons
│       ├── logo.png
│       └── icons/
│
├── templates/                         # Template files
│   ├── dashboard.php                 # Dashboard template
│   ├── credit-cards.php              # Credit card list
│   ├── credit-card-single.php        # Single card view
│   ├── loans.php                     # Loans list
│   ├── mortgage.php                  # Mortgage view
│   ├── bills.php                     # Bills list
│   ├── goals.php                     # Goals list
│   ├── action-plan.php               # Action plan
│   │
│   ├── elementor/                    # Elementor templates
│   │   ├── dashboard-template.json
│   │   ├── credit-cards-template.json
│   │   └── ... (other templates)
│   │
│   └── print/                        # Print-specific templates
│       ├── dashboard-print.php
│       └── action-plan-print.php
│
├── jetengine-exports/                # JetEngine configuration
│   ├── cpt-credit-cards.json
│   ├── cpt-loans.json
│   └── ... (other CPT configs)
│
├── languages/                         # Translation files
│   ├── dedebify.pot                  # Template file
│   ├── dedebify-en_US.po            # English translations
│   └── dedebify-en_US.mo
│
└── tests/                            # Unit and integration tests
    ├── test-calculations.php
    ├── test-api.php
    └── test-cpt.php
```

### Class Structure

#### Main Classes

**Dedebify (Main Class)**
- Initializes plugin
- Loads dependencies
- Registers hooks
- Manages plugin lifecycle

**Dedebify_CPT (Custom Post Types)**
- Registers 6 custom post types
- Adds meta boxes
- Handles meta data saving
- Manages data validation

**Dedebify_Calculations (Calculation Engine)**
- All financial calculations
- Payoff calculations
- Debt ordering (avalanche/snowball)
- Metrics calculations (DTI, utilization)

**Dedebify_API (REST API)**
- RESTful endpoints
- Authentication
- Data retrieval
- Data manipulation

**Dedebify_Elementor (Elementor Integration)**
- Custom widgets
- Dynamic tags
- Template registration

### Data Flow
```
User Input (Form)
    ↓
WordPress (Nonce Verification)
    ↓
Dedebify_CPT (Sanitization & Validation)
    ↓
WordPress Database (wp_posts + wp_postmeta)
    ↓
Dedebify_Calculations (Process Data)
    ↓
Dedebify_API (Format Response)
    ↓
Frontend Display (Elementor Widgets/Templates)
```

---

## 📊 Custom Post Types

DeDebtify creates 6 custom post types to store user financial data:

### 1. Credit Cards (`dd_credit_card`)

**Purpose:** Store individual credit card information

**Meta Fields:**
- `balance` (number) - Current balance in USD
- `credit_limit` (number) - Credit limit in USD
- `interest_rate` (number) - Annual interest rate (%)
- `minimum_payment` (number) - Required minimum payment
- `extra_payment` (number) - Additional payment amount
- `due_date` (number) - Day of month (1-31)
- `auto_pay` (boolean) - Auto-pay enabled
- `status` (string) - active | paid_off | closed

**Calculated Fields (Not Stored):**
- `utilization_percentage` - (balance / credit_limit) × 100
- `months_to_payoff` - Based on payment schedule
- `payoff_date` - Estimated payoff date
- `total_interest` - Total interest to be paid

**Query Example:**
```php
<?php
// Get all active credit cards for current user
$cards = get_posts( array(
    'post_type'      => 'dd_credit_card',
    'author'         => get_current_user_id(),
    'posts_per_page' => -1,
    'meta_query'     => array(
        array(
            'key'     => 'status',
            'value'   => 'active',
            'compare' => '=',
        ),
    ),
) );
?>
```

### 2. Loans (`dd_loan`)

**Purpose:** Store personal loans, auto loans, student loans

**Meta Fields:**
- `loan_name` (text) - Name of loan
- `loan_type` (string) - personal | auto | student | other
- `principal` (number) - Original loan amount
- `current_balance` (number) - Remaining balance
- `interest_rate` (number) - Annual interest rate (%)
- `term_months` (number) - Original term in months
- `monthly_payment` (number) - Required monthly payment
- `start_date` (date) - Loan start date
- `extra_payment` (number) - Additional payment

**Calculated Fields:**
- `months_remaining` - Months until payoff
- `total_interest_remaining` - Interest to be paid
- `payoff_date` - Estimated payoff date

### 3. Mortgage (`dd_mortgage`)

**Purpose:** Store mortgage information

**Meta Fields:**
- `property_address` (text) - Property address
- `loan_amount` (number) - Original mortgage amount
- `current_balance` (number) - Remaining balance
- `interest_rate` (number) - Current interest rate (%)
- `term_years` (number) - Original term in years
- `monthly_payment` (number) - Monthly P&I payment
- `start_date` (date) - Mortgage start date
- `extra_payment` (number) - Additional principal
- `property_tax` (number) - Annual property tax
- `homeowners_insurance` (number) - Annual insurance
- `pmi` (number) - Monthly PMI
- `property_value` (number) - Estimated property value

**Calculated Fields:**
- `total_monthly_payment` - Including taxes, insurance, PMI
- `months_remaining` - Months until payoff
- `equity` - Property value - current balance
- `ltv_ratio` - Loan-to-value ratio

### 4. Bills (`dd_bill`)

**Purpose:** Track recurring monthly expenses

**Meta Fields:**
- `bill_name` (text) - Name of bill
- `category` (string) - housing | transportation | utilities | etc.
- `amount` (number) - Bill amount
- `frequency` (string) - weekly | bi-weekly | monthly | quarterly | annually
- `due_date` (number) - Day of month (1-31)
- `auto_pay` (boolean) - Auto-pay enabled
- `is_essential` (boolean) - Essential vs. discretionary

**Calculated Fields:**
- `monthly_equivalent` - Converted to monthly amount

### 5. Goals (`dd_goal`)

**Purpose:** Track savings goals and milestones

**Meta Fields:**
- `goal_name` (text) - Name of goal
- `goal_type` (string) - savings | emergency_fund | debt_payoff | etc.
- `target_amount` (number) - Goal target
- `current_amount` (number) - Current saved
- `monthly_contribution` (number) - Monthly contribution
- `target_date` (date) - Target completion date
- `priority` (string) - low | medium | high

**Calculated Fields:**
- `remaining_amount` - Target - current
- `months_to_goal` - Remaining / contribution
- `estimated_completion_date` - Current date + months
- `progress_percentage` - (Current / target) × 100

### 6. Financial Snapshots (`dd_snapshot`)

**Purpose:** Store monthly snapshots for historical tracking

**Meta Fields:**
- `snapshot_date` (date) - Date of snapshot
- `total_debt` (number) - Total of all debts
- `total_credit_card_debt` (number) - Sum of credit cards
- `total_loan_debt` (number) - Sum of loans
- `total_mortgage_debt` (number) - Mortgage balance
- `total_monthly_payments` (number) - All debt payments
- `total_monthly_bills` (number) - All bills
- `monthly_income` (number) - User's income
- `debt_to_income_ratio` (number) - DTI %
- `credit_utilization` (number) - Overall utilization %
- `total_assets` (number) - Total assets/savings
- `net_worth` (number) - Assets - debts

---

## 🔌 REST API Endpoints

All endpoints require authentication (logged-in user).

**Base URL:** `https://yoursite.com/wp-json/dedebify/v1/`

### GET /dashboard

Get complete dashboard summary for current user.

**Request:**
```javascript
GET /wp-json/dedebify/v1/dashboard
Headers: {
    'X-WP-Nonce': nonce_value
}
```

**Response:**
```json
{
    "total_debt": 45200.00,
    "monthly_payments": 2850.00,
    "monthly_bills": 1850.00,
    "dti_ratio": 68.75,
    "credit_utilization": 45.50,
    "credit_card_count": 3,
    "loan_count": 2,
    "total_goals": 3
}
```

### POST /calculate-payoff

Calculate credit card payoff scenarios.

**Request:**
```javascript
POST /wp-json/dedebify/v1/calculate-payoff
Headers: {
    'X-WP-Nonce': nonce_value,
    'Content-Type': 'application/json'
}
Body: {
    "balance": 3500,
    "interest_rate": 18.99,
    "monthly_payment": 275
}
```

**Response:**
```json
{
    "months_to_payoff": 15,
    "total_interest": 485.50,
    "payoff_date": "2027-01-15",
    "total_paid": 3985.50
}
```

### POST /snapshot

Create monthly financial snapshot.

**Request:**
```javascript
POST /wp-json/dedebify/v1/snapshot
Headers: {
    'X-WP-Nonce': nonce_value
}
```

**Response:**
```json
{
    "success": true,
    "snapshot_id": 12345,
    "message": "Snapshot created successfully"
}
```

### GET /snapshots

Get snapshot history for current user.

**Request:**
```javascript
GET /wp-json/dedebify/v1/snapshots
Headers: {
    'X-WP-Nonce': nonce_value
}
```

**Response:**
```json
[
    {
        "id": 12345,
        "date": "2025-10-01",
        "total_debt": 45200.00,
        "monthly_payments": 2850.00,
        "dti_ratio": 68.75,
        "credit_utilization": 45.50
    },
    {
        "id": 12344,
        "date": "2025-09-01",
        "total_debt": 46550.00,
        "monthly_payments": 2850.00,
        "dti_ratio": 70.20,
        "credit_utilization": 48.30
    }
]
```

### GET /action-plan

Get personalized debt action plan.

**Request:**
```javascript
GET /wp-json/dedebify/v1/action-plan?method=avalanche
Headers: {
    'X-WP-Nonce': nonce_value
}
```

**Parameters:**
- `method` (optional): `avalanche` | `snowball` | `custom` (default: avalanche)

**Response:**
```json
{
    "method": "avalanche",
    "total_debt": 45200.00,
    "monthly_payments": 2850.00,
    "payoff_order": [
        {
            "id": 123,
            "name": "Capital One Quicksilver",
            "type": "credit_card",
            "balance": 6000.00,
            "interest_rate": 22.99
        },
        {
            "id": 124,
            "name": "Chase Freedom",
            "type": "credit_card",
            "balance": 3500.00,
            "interest_rate": 18.99
        }
    ]
}
```

### Error Responses

**401 Unauthorized:**
```json
{
    "code": "rest_forbidden",
    "message": "Sorry, you are not allowed to do that.",
    "data": {
        "status": 401
    }
}
```

**400 Bad Request:**
```json
{
    "code": "rest_invalid_param",
    "message": "Invalid parameter(s): balance",
    "data": {
        "status": 400,
        "params": {
            "balance": "balance must be a positive number"
        }
    }
}
```

---

## 🎨 Elementor Integration

### Custom Widgets

DeDebtify provides 6 custom Elementor widgets:

#### 1. Debt Dashboard Widget
**Widget Name:** `dedebify-dashboard`

Displays complete dashboard summary with all key metrics.

**Settings:**
- Show/hide individual metrics
- Color scheme selection
- Layout style (grid, list, cards)
- Typography controls

**Usage in Elementor:**
1. Edit page with Elementor
2. Search for "Debt Dashboard" in widget panel
3. Drag to page
4. Customize in left sidebar
5. Preview and publish

#### 2. Credit Card List Widget
**Widget Name:** `dedebify-credit-cards`

Displays user's credit cards with sorting and filtering.

**Settings:**
- Columns to display
- Sort order (balance, rate, utilization)
- Show/hide payoff calculator
- Card style (table, grid, list)

#### 3. Progress Chart Widget
**Widget Name:** `dedebify-progress-chart`

Line chart showing debt reduction over time.

**Settings:**
- Chart type (line, bar, area)
- Time range (3 months, 6 months, 1 year, all time)
- Show/hide specific debt types
- Color customization

#### 4. Debt Breakdown Widget
**Widget Name:** `dedebify-debt-breakdown`

Pie or donut chart showing debt by type.

**Settings:**
- Chart type (pie, donut, bar)
- Show percentages or amounts
- Color scheme
- Legend position

#### 5. Quick Add Form Widget
**Widget Name:** `dedebify-quick-add`

Inline form to quickly add financial items.

**Settings:**
- Choose form type (credit card, loan, bill, goal)
- Minimal or full field set
- Button styling
- Success message

#### 6. Upcoming Bills Widget
**Widget Name:** `dedebify-upcoming-bills`

Shows next 7 days of bills due.

**Settings:**
- View style (calendar, list)
- Number of days to show
- Color coding options
- Show/hide paid bills

### Dynamic Tags

Use dynamic tags to display user-specific data anywhere in Elementor.

**Available Tags:**
- `{dd_total_debt}` - Total debt amount
- `{dd_monthly_payments}` - Total monthly debt payments
- `{dd_monthly_bills}` - Total monthly bills
- `{dd_dti_ratio}` - Debt-to-income ratio
- `{dd_credit_utilization}` - Overall credit utilization
- `{dd_debt_free_date}` - Projected debt-free date
- `{dd_months_to_debt_free}` - Months until debt-free
- `{dd_credit_card_count}` - Number of credit cards
- `{dd_loan_count}` - Number of loans
- `{dd_total_interest_savings}` - Interest saved with extra payments

**How to Use:**
1. Add any Elementor widget (Heading, Text, etc.)
2. Click the dynamic tags icon (database icon)
3. Select "DeDebtify" category
4. Choose desired tag
5. Tag automatically pulls current user's data

**Example:**
```
Heading widget: "You have {dd_total_debt} in total debt"
Displays as: "You have $45,200.00 in total debt"
```

### Pre-Built Templates

DeDebtify includes 7 pre-built Elementor page templates:

1. **Dashboard Template** - Complete dashboard with all metrics
2. **Credit Cards Template** - Credit card management interface
3. **Loans Template** - Loan tracking interface
4. **Mortgage Template** - Mortgage management
5. **Bills Template** - Bill tracker
6. **Goals Template** - Goals tracker
7. **Action Plan Template** - Debt action plan display

**Import Templates:**
```
Elementor → Templates → Import Templates → Choose file → Import
```

Templates are located in: `dedebify/templates/elementor/`

---

## 🧮 Calculation Methods

### Credit Card Payoff Calculation

**Formula:**
```
n = -log(1 - (B × r / P)) / log(1 + r)

Where:
n = number of months
B = balance
r = monthly interest rate (annual rate / 12 / 100)
P = monthly payment
```

**PHP Implementation:**
```php
public static function calculate_months_to_payoff( $balance, $interest_rate, $monthly_payment ) {
    if ( $balance <= 0 ) return 0;
    if ( $monthly_payment <= 0 ) return 'Never';
    
    $monthly_rate = ( $interest_rate / 100 ) / 12;
    $monthly_interest = $balance * $monthly_rate;
    
    if ( $monthly_payment <= $monthly_interest ) {
        return 'Never';
    }
    
    $numerator = log( 1 - ( $balance * $monthly_rate / $monthly_payment ) );
    $denominator = log( 1 + $monthly_rate );
    $months = -$numerator / $denominator;
    
    return ceil( $months );
}
```

### Total Interest Calculation

**Formula:**
```
Total Interest = (Monthly Payment × Number of Months) - Balance
```

### Credit Utilization

**Formula:**
```
Utilization % = (Balance / Credit Limit) × 100

Overall Utilization = (Sum of All Balances / Sum of All Limits) × 100
```

### Debt-to-Income Ratio (DTI)

**Formula:**
```
DTI % = (Total Monthly Debt Payments / Monthly Income) × 100

Total Monthly Debt Payments includes:
- Credit card minimum + extra payments
- Loan payments + extra payments
- Mortgage P&I + taxes + insurance + PMI + extra payments
```

**DTI Rating:**
- < 36% = Good (Green)
- 36-43% = Fair (Yellow)
- > 43% = High (Red)

### Loan Payment (Amortization)

**Formula:**
```
P = L × [c(1 + c)^n] / [(1 + c)^n - 1]

Where:
P = monthly payment
L = loan amount
c = monthly interest rate (annual rate / 12 / 100)
n = term in months
```

### Avalanche Method

Orders debts by **highest interest rate first**.

**Logic:**
1. List all debts with interest rates
2. Sort by interest rate (descending)
3. Apply all extra payments to highest rate debt
4. Once paid off, roll payment to next highest rate
5. Repeat until all debts paid

**Advantage:** Saves maximum interest

### Snowball Method

Orders debts by **smallest balance first**.

**Logic:**
1. List all debts with balances
2. Sort by balance (ascending)
3. Apply all extra payments to smallest balance
4. Once paid off, roll payment to next smallest
5. Repeat until all debts paid

**Advantage:** Psychological motivation from quick wins

---

## 💻 Development

### Setting Up Development Environment

#### Prerequisites
```bash
# PHP 8.0+
php -v

# Composer (for dependencies)
composer --version

# Node.js and npm (for asset building)
node -v
npm -v

# Local WordPress installation
# Recommended: Local by Flywheel, XAMPP, or MAMP
```

#### Clone Repository
```bash
git clone https://github.com/oxfordpierpont/dedebify.git
cd dedebify
```

#### Install Dependencies
```bash
# PHP dependencies (if any)
composer install

# JavaScript dependencies
npm install
```

#### Build Assets
```bash
# Development build (with source maps)
npm run dev

# Production build (minified)
npm run build

# Watch mode (auto-rebuild on changes)
npm run watch
```

### Coding Standards

DeDebtify follows **WordPress Coding Standards**.

#### PHP Coding Standards

**Install PHP_CodeSniffer:**
```bash
composer global require "squizlabs/php_codesniffer=*"
composer global require wp-coding-standards/wpcs
phpcs --config-set installed_paths /path/to/wpcs
```

**Check Code:**
```bash
phpcs --standard=WordPress dedebify/
```

**Auto-Fix Issues:**
```bash
phpcbf --standard=WordPress dedebify/
```

**Key Rules:**
- Use tabs for indentation
- Space after opening parenthesis and before closing
- Yoda conditions: `if ( 'value' === $variable )`
- Single quotes for strings (unless variables inside)
- Descriptive variable names: `$user_id` not `$uid`

#### JavaScript Coding Standards

**Install ESLint:**
```bash
npm install --save-dev eslint
```

**Check Code:**
```bash
npx eslint assets/js/
```

**Auto-Fix Issues:**
```bash
npx eslint assets/js/ --fix
```

### Documentation Standards

**DocBlock Format (PHP):**
```php
/**
 * Brief description of function.
 *
 * Detailed description if needed.
 *
 * @since    1.0.0
 * @param    int    $user_id    The user ID.
 * @param    array  $args       Optional arguments.
 * @return   float              The calculated value.
 */
public function my_function( $user_id, $args = array() ) {
    // Function code
}
```

**JSDoc Format (JavaScript):**
```javascript
/**
 * Calculate months to pay off debt.
 *
 * @param {number} balance - Current balance
 * @param {number} interestRate - Annual interest rate (%)
 * @param {number} monthlyPayment - Monthly payment amount
 * @return {number} Number of months to payoff
 */
function calculateMonthsToPayoff(balance, interestRate, monthlyPayment) {
    // Function code
}
```

### Git Workflow

#### Branch Naming
```
main                    # Production-ready code
develop                 # Integration branch
feature/card-manager    # New feature
fix/calculation-bug     # Bug fix
hotfix/security-patch   # Urgent production fix
```

#### Commit Messages
```
[TYPE] Brief description (50 chars or less)

Detailed explanation of changes (wrap at 72 chars)

- Bullet point for specific change
- Another specific change
- Reference issue: Fixes #123
```

**Types:**
- `[FEAT]` - New feature
- `[FIX]` - Bug fix
- `[REFACTOR]` - Code refactoring
- `[DOCS]` - Documentation changes
- `[STYLE]` - Formatting changes
- `[TEST]` - Adding/updating tests
- `[CHORE]` - Maintenance tasks

#### Pull Request Process
1. Create feature branch from `develop`
2. Make changes with clear commits
3. Write/update tests
4. Update documentation
5. Push branch and create PR
6. Request code review
7. Address feedback
8. Merge when approved

### Database Queries

**Best Practices:**
```php
<?php
// ✅ GOOD: Use $wpdb->prepare()
global $wpdb;
$user_id = 123;
$results = $wpdb->get_results( 
    $wpdb->prepare( 
        "SELECT * FROM {$wpdb->posts} WHERE post_author = %d", 
        $user_id 
    ) 
);

// ❌ BAD: Direct query (SQL injection risk)
$results = $wpdb->get_results( 
    "SELECT * FROM {$wpdb->posts} WHERE post_author = $user_id" 
);

// ✅ GOOD: Use get_posts() when possible
$posts = get_posts( array(
    'author'         => $user_id,
    'post_type'      => 'dd_credit_card',
    'posts_per_page' => -1,
) );
?>
```

### Security Checklist

**Always:**
- [ ] Sanitize input: `sanitize_text_field()`, `sanitize_email()`, etc.
- [ ] Validate input: Check types, ranges, formats
- [ ] Escape output: `esc_html()`, `esc_attr()`, `esc_url()`
- [ ] Verify nonces: `wp_verify_nonce()`
- [ ] Check capabilities: `current_user_can()`
- [ ] Prepare SQL queries: `$wpdb->prepare()`
- [ ] Use HTTPS for sensitive data
- [ ] Log errors securely (not to browser)

**Never:**
- [ ] Trust user input directly
- [ ] Echo unsanitized data
- [ ] Use `eval()` or `exec()`
- [ ] Store passwords in plain text
- [ ] Expose sensitive data in JavaScript
- [ ] Allow file uploads without validation

---

## 🧪 Testing

### Unit Tests

**Location:** `tests/`

**Running Tests:**
```bash
# Install PHPUnit
composer require --dev phpunit/phpunit

# Run all tests
vendor/bin/phpunit

# Run specific test file
vendor/bin/phpunit tests/test-calculations.php

# Run with coverage
vendor/bin/phpunit --coverage-html coverage/
```

**Example Test:**
```php
<?php
class Test_Calculations extends WP_UnitTestCase {
    
    public function test_calculate_months_to_payoff() {
        $months = Dedebify_Calculations::calculate_months_to_payoff( 
            3500,   // Balance
            18.99,  // Interest rate
            275     // Monthly payment
        );
        
        $this->assertEquals( 15, $months );
    }
    
    public function test_calculate_utilization() {
        $utilization = Dedebify_Calculations::calculate_utilization( 
            3500,   // Balance
            5000    // Credit limit
        );
        
        $this->assertEquals( 70.00, $utilization );
    }
}
?>
```

### Manual Testing Checklist

**Dashboard:**
- [ ] All metrics display correctly
- [ ] Data is user-specific
- [ ] Charts render properly
- [ ] Quick actions work

**Credit Cards:**
- [ ] Can add new card
- [ ] Can edit existing card
- [ ] Can delete card (with confirmation)
- [ ] Payoff calculations accurate
- [ ] Sorting/filtering works

**Loans:**
- [ ] Can add/edit/delete loans
- [ ] Amortization calculations correct
- [ ] Payment calculator works

**Mortgage:**
- [ ] Can add/edit mortgage
- [ ] All costs calculated correctly
- [ ] Refinance calculator works

**Bills:**
- [ ] Can add/edit/delete bills
- [ ] Frequency conversions correct
- [ ] Due date tracking works

**Goals:**
- [ ] Can add/edit/delete goals
- [ ] Progress calculations correct
- [ ] Quick update works

**Snapshots:**
- [ ] Can create snapshot
- [ ] All data captured correctly
- [ ] History displays properly
- [ ] Comparison works

**Action Plan:**
- [ ] Plan generates correctly
- [ ] All methods work (avalanche/snowball)
- [ ] Printable version formats well

**Elementor:**
- [ ] All widgets appear in panel
- [ ] Widgets display data correctly
- [ ] Dynamic tags work
- [ ] Templates import successfully

**Print:**
- [ ] Print button appears on all pages
- [ ] Print layout is clean
- [ ] Page breaks logical
- [ ] Works in all browsers

### Browser Testing

**Desktop:**
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)

**Mobile:**
- [ ] iOS Safari
- [ ] Chrome Mobile (Android)
- [ ] Responsive at 375px, 768px, 1024px

**Test Items:**
- [ ] Forms submit correctly
- [ ] Calculations display
- [ ] Navigation works
- [ ] Touch targets adequate (44px min)
- [ ] No horizontal scrolling

---

## 🔧 Troubleshooting

### Common Issues

#### Issue: Plugin won't activate

**Symptoms:**
- Error message on activation
- White screen
- Site crashes

**Solutions:**
1. Check PHP version (must be 8.0+)
```bash
   php -v
```

2. Check WordPress version (must be 6.0+)
```
   WordPress Admin → Dashboard → Updates
```

3. Check for plugin conflicts
   - Deactivate all other plugins
   - Activate DeDebtify
   - Reactivate other plugins one by one

4. Check PHP error logs
```
   /wp-content/debug.log
```

5. Increase PHP memory limit in wp-config.php:
```php
   define( 'WP_MEMORY_LIMIT', '256M' );
```

#### Issue: Custom Post Types not appearing

**Symptoms:**
- DeDebtify menu visible but no CPTs
- Cannot add credit cards, loans, etc.

**Solutions:**
1. Flush rewrite rules
```
   WordPress Admin → Settings → Permalinks → Click "Save Changes"
```

2. Verify JetEngine is active
```
   WordPress Admin → Plugins → Ensure JetEngine is activated
```

3. Re-save plugin settings
```
   WordPress Admin → DeDebtify → Settings → Save
```

4. Check CPT registration in database:
```php
   // Add to functions.php temporarily
   add_action( 'init', function() {
       global $wp_post_types;
       error_log( print_r( array_keys( $wp_post_types ), true ) );
   });
```

#### Issue: Calculations not working

**Symptoms:**
- Payoff months show as "NaN"
- DTI shows 0% when should have value
- Charts don't display

**Solutions:**
1. Check JavaScript console for errors
```
   Browser → F12 → Console tab
```

2. Verify data is being passed correctly
```javascript
   console.log( dedebifyData );
```

3. Clear browser cache
```
   Ctrl+Shift+Delete (or Cmd+Shift+Delete on Mac)
```

4. Check REST API is working
```
   Visit: https://yoursite.com/wp-json/dedebify/v1/dashboard
   Should return JSON, not error
```

5. Verify user is logged in
```php
   // Check current user
   if ( ! is_user_logged_in() ) {
       echo 'User not logged in';
   }
```

#### Issue: Data not saving

**Symptoms:**
- Form submits but data doesn't appear
- Changes don't persist
- "Success" message but no actual save

**Solutions:**
1. Check nonce verification
   - Look for "nonce verification failed" in debug.log

2. Check user permissions
```php
   if ( ! current_user_can( 'edit_posts' ) ) {
       echo 'User lacks permission';
   }
```

3. Check for database errors
```php
   global $wpdb;
   error_log( $wpdb->last_error );
```

4. Verify meta fields are being saved
```php
   // Check post meta
   $balance = get_post_meta( $post_id, 'balance', true );
   error_log( 'Balance: ' . $balance );
```

#### Issue: Elementor widgets not showing

**Symptoms:**
- DeDebtify widgets not in Elementor panel
- Dynamic tags missing

**Solutions:**
1. Verify Elementor Pro is active and updated

2. Clear Elementor cache
```
   Elementor → Tools → Regenerate CSS → Regenerate Files
```

3. Re-register widgets
   - Deactivate DeDebtify
   - Reactivate DeDebtify

4. Check for JavaScript errors in Elementor editor

#### Issue: Print styles not working

**Symptoms:**
- Print preview shows full page with navigation
- Layout breaks on print
- Wrong fonts/colors

**Solutions:**
1. Verify print CSS is loading
```html
   <link rel="stylesheet" href=".../dedebify-print.css" media="print">
```

2. Check browser print preview (Ctrl+P or Cmd+P)

3. Clear browser cache and try again

4. Test in different browser

#### Issue: Performance problems

**Symptoms:**
- Pages load slowly
- Database queries taking long
- Timeouts

**Solutions:**
1. Enable object caching
```php
   // In wp-config.php
   define( 'WP_CACHE', true );
```

2. Optimize database
```
   WordPress Admin → Tools → Database (with plugin like WP-Optimize)
```

3. Check for N+1 query issues
```php
   // Install Query Monitor plugin
   WordPress Admin → Plugins → Add New → Search "Query Monitor"
```

4. Increase PHP limits
```php
   // In wp-config.php
   define( 'WP_MEMORY_LIMIT', '512M' );
   define( 'WP_MAX_MEMORY_LIMIT', '512M' );
```

### Debug Mode

Enable debug mode to see detailed error messages:

**wp-config.php:**
```php
// Enable debug mode
define( 'WP_DEBUG', true );

// Log errors to file
define( 'WP_DEBUG_LOG', true );

// Don't display on screen (security)
define( 'WP_DEBUG_DISPLAY', false );
@ini_set( 'display_errors', 0 );

// Enable script debugging
define( 'SCRIPT_DEBUG', true );
```

**View logs:**
```
/wp-content/debug.log
```

### Getting Help

1. **Check Documentation**
   - Read this README thoroughly
   - Review PRD for detailed specs
   - Check code comments

2. **Search Issues**
   - GitHub Issues: [Link to repository issues]
   - WordPress Forums: https://wordpress.org/support/

3. **Ask for Help**
   - Create GitHub issue with:
     - WordPress version
     - PHP version
     - Plugin version
     - Error messages
     - Steps to reproduce
   - Include debug.log relevant entries

4. **Contact Support**
   - Email: support@dedebify.ai
   - Priority support for paying customers

---

## 🗺️ Roadmap

### Version 1.1 (Q1 2026)
- [ ] OneSignal push notification integration
- [ ] Email notification system
- [ ] Recurring bill payment tracking
- [ ] CSV/PDF export functionality
- [ ] Multi-currency support

### Version 1.2 (Q2 2026)
- [ ] n8n workflow automation
- [ ] Automated monthly snapshots
- [ ] Bill due date reminders
- [ ] Milestone celebration system
- [ ] Progress reports generation

### Version 1.3 (Q3 2026)
- [ ] Advanced analytics dashboard
- [ ] Predictive debt-free date modeling
- [ ] What-if scenario calculator
- [ ] Budget variance tracking
- [ ] Spending pattern analysis

### Version 2.0 (Q4 2026)
- [ ] BuddyBoss social features integration
- [ ] Accountability partner system
- [ ] Group debt challenges
- [ ] Success story sharing
- [ ] Community forums

### Version 2.1 (Q1 2027)
- [ ] Gamification system
- [ ] Achievement badges
- [ ] Leaderboards
- [ ] Streak tracking
- [ ] Reward system

### Future Considerations
- [ ] Plaid integration for bank account connection
- [ ] AI-powered recommendations
- [ ] Mobile native apps (iOS/Android)
- [ ] Receipt scanning
- [ ] Bill payment integration
- [ ] Credit score tracking
- [ ] Investment tracking
- [ ] Retirement planning tools

---

## 🤝 Contributing

We welcome contributions from the community!

### How to Contribute

1. **Fork the Repository**
```bash
   # Fork on GitHub, then clone your fork
   git clone https://github.com/oxfordpierpont/dedebify.git
   cd dedebify
```

2. **Create a Feature Branch**
```bash
   git checkout -b feature/your-feature-name
```

3. **Make Your Changes**
   - Follow coding standards
   - Write clear commit messages
   - Add tests if applicable
   - Update documentation

4. **Test Your Changes**
```bash
   # Run tests
   vendor/bin/phpunit
   
   # Check coding standards
   phpcs --standard=WordPress dedebify/
```

5. **Push and Create Pull Request**
```bash
   git push origin feature/your-feature-name
```
   - Go to GitHub and create Pull Request
   - Describe your changes
   - Reference any related issues

### Contribution Guidelines

**Code Contributions:**
- Follow WordPress Coding Standards
- Include DocBlocks for all functions
- Write unit tests for new features
- Ensure backward compatibility

**Bug Reports:**
- Use GitHub Issues
- Include WordPress/PHP versions
- Provide steps to reproduce
- Include error messages/logs

**Feature Requests:**
- Open GitHub Issue with "Feature Request" label
- Describe use case
- Explain expected behavior
- Include mockups if applicable

**Documentation:**
- Fix typos and improve clarity
- Add examples and tutorials
- Translate to other languages
- Update outdated information

### Code of Conduct

- Be respectful and inclusive
- Welcome newcomers
- Provide constructive feedback
- Focus on what is best for the community

---

## 💬 Support

### Documentation
- **Full Documentation:** [Link to docs site]
- **API Reference:** [Link to API docs]
- **Video Tutorials:** [Link to YouTube playlist]

### Community
- **GitHub Discussions:** [Link to discussions]
- **Discord Server:** [Link to Discord]
- **Facebook Group:** [Link to Facebook group]

### Professional Support
- **Email Support:** support@dedebify.ai
- **Priority Support:** Available for paying customers
- **Custom Development:** enterprise@dedebify.ai

### Reporting Security Issues
**Do not open GitHub issues for security vulnerabilities.**

Email security concerns to: security@dedebify.ai

We will respond within 48 hours.

---

## 📄 License

DeDebtify is licensed under the **GNU General Public License v2.0 or later**.

**Summary:**
- ✅ Free to use
- ✅ Free to modify
- ✅ Free to distribute
- ⚠️ Must maintain GPL license
- ⚠️ Must provide source code
- ⚠️ No warranty provided

**Full License:**
See [LICENSE.txt](LICENSE.txt) file.

**Third-Party Licenses:**
- WordPress: GPL v2.0
- Elementor: GPL v3.0
- JetEngine: GPL v2.0

---

## 📝 Changelog

### Version 1.0.0 (2025-10-21)
**Initial Release**

**Features:**
- Dashboard with 6 key financial metrics
- Credit Card Manager with payoff calculator
- Loan Manager with amortization
- Mortgage Manager with refinance calculator
- Bill Tracker with multiple frequencies
- Goals Tracker with progress monitoring
- Monthly Snapshot system
- Debt Action Plan generator (Avalanche/Snowball)
- 6 Elementor widgets
- Dynamic tags for all metrics
- REST API with 5 endpoints
- Print-optimized layouts
- Mobile responsive design
- Security hardened

**Technical:**
- 6 Custom Post Types
- Complete calculation engine
- User-specific data isolation
- WordPress Coding Standards compliant
- Comprehensive documentation

---

## 👥 Credits

**Development Team:**
- Lead Designer & Developer: Oxford Pierpont

**Special Thanks:**
- WordPress Core Team
- Elementor Team
- Crocoblock Team
- Beta Testers
- Community Contributors

**Built With:**
- WordPress
- PHP
- JavaScript (Vanilla)
- Elementor Pro
- JetEngine
- CSS3
- HTML5

---

## 📞 Contact

**Website:** https://dedebify.ai  
**Email:** hello@dedebify.ai  
**GitHub:** https://github.com/oxfordpierpont/dedebtify
**Twitter:** @dedebify  
**Facebook:** facebook.com/dedebify

---

**Made with ❤️ to help people achieve financial freedom**

---

*Last Updated: October 21, 2025*
