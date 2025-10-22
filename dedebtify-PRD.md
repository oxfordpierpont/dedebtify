# **DeDebtify Product Requirements Document (PRD)**

# **DeDebtify \- Debt Management WordPress Plugin**

**Version:** 1.0  
 **Date:** October 21, 2025  
 **Project Type:** WordPress Plugin (MVP)  
 **Developer Level:** Junior Developer Friendly

**Developer Name:** Oxford Pierpont

---

## **Table of Contents**

1. [Project Overview](https://claude.ai/chat/a4b2bf4f-69c8-4653-b2e0-819543079d50#1-project-overview)  
2. [Technical Stack](https://claude.ai/chat/a4b2bf4f-69c8-4653-b2e0-819543079d50#2-technical-stack)  
3. [Plugin Architecture](https://claude.ai/chat/a4b2bf4f-69c8-4653-b2e0-819543079d50#3-plugin-architecture)  
4. [Database Schema](https://claude.ai/chat/a4b2bf4f-69c8-4653-b2e0-819543079d50#4-database-schema)  
5. [Feature Specifications](https://claude.ai/chat/a4b2bf4f-69c8-4653-b2e0-819543079d50#5-feature-specifications)  
6. [User Interface Requirements](https://claude.ai/chat/a4b2bf4f-69c8-4653-b2e0-819543079d50#6-user-interface-requirements)  
7. [Integration Requirements](https://claude.ai/chat/a4b2bf4f-69c8-4653-b2e0-819543079d50#7-integration-requirements)  
8. [Development Phases](https://claude.ai/chat/a4b2bf4f-69c8-4653-b2e0-819543079d50#8-development-phases)  
9. [Code Examples](https://claude.ai/chat/a4b2bf4f-69c8-4653-b2e0-819543079d50#9-code-examples)  
10. [Testing Requirements](https://claude.ai/chat/a4b2bf4f-69c8-4653-b2e0-819543079d50#10-testing-requirements)  
11. [Deployment Checklist](https://claude.ai/chat/a4b2bf4f-69c8-4653-b2e0-819543079d50#11-deployment-checklist)

---

## **1\. Project Overview**

### **1.1 Purpose**

DeDebtify is a WordPress plugin that transforms a static debt management spreadsheet into an interactive web application. Users can track credit cards, loans, mortgages, bills, and financial goals over multiple years.

### **1.2 Target Users**

* Individuals with debt wanting to become debt-free  
* People managing multiple financial obligations  
* Users who need long-term financial tracking (months to years)

### **1.3 Business Model**

* Monthly membership site  
* Multi-year user retention expected  
* PWA with push notifications for engagement

### **1.4 Success Criteria**

* Users can input and track all debt types  
* Data persists across sessions and years  
* Calculations are accurate and automatic  
* Data can be printed from any dashboard  
* Works seamlessly with Elementor Pro templates

---

## **2\. Technical Stack**

### **2.1 Required Technologies**

```
WordPress Core: 6.0+
PHP: 8.0+
MySQL: 5.7+
Elementor Pro: Latest version
JetEngine: Latest version (for CPT and dynamic content)
JavaScript: ES6+ (vanilla JS preferred for performance)
```

### **2.2 Optional Integrations (Phase 2+)**

* OneSignal (Push notifications)  
* n8n (Backend automation)  
* BuddyBoss (Community features)

### **2.3 Browser Support**

* Chrome, Firefox, Safari, Edge (latest 2 versions)  
* Mobile responsive (iOS Safari, Chrome Mobile)

---

## **3\. Plugin Architecture**

### **3.1 Plugin Structure**

```
dedebify/
│
├── dedebify.php                 (Main plugin file)
├── README.md                     (Plugin documentation)
├── uninstall.php                 (Cleanup on uninstall)
│
├── includes/
│   ├── class-dedebify.php               (Core plugin class)
│   ├── class-dedebify-activator.php     (Activation hooks)
│   ├── class-dedebify-deactivator.php   (Deactivation hooks)
│   ├── class-dedebify-cpt.php           (Custom Post Types)
│   ├── class-dedebify-calculations.php  (All calculation logic)
│   ├── class-dedebify-api.php           (REST API endpoints)
│   └── class-dedebify-elementor.php     (Elementor widgets)
│
├── assets/
│   ├── css/
│   │   ├── dedebify-admin.css    (Admin styles)
│   │   ├── dedebify-public.css   (Frontend styles)
│   │   └── dedebify-print.css    (Print styles)
│   │
│   ├── js/
│   │   ├── dedebify-admin.js     (Admin scripts)
│   │   ├── dedebify-public.js    (Frontend scripts)
│   │   └── dedebify-calculator.js (Calculation engine)
│   │
│   └── images/
│       └── (icons, logos, etc.)
│
├── templates/
│   ├── dashboard.php             (Main dashboard template)
│   ├── credit-cards.php          (Credit card list template)
│   ├── loans.php                 (Loans template)
│   ├── mortgage.php              (Mortgage template)
│   ├── bills.php                 (Bills template)
│   ├── goals.php                 (Goals template)
│   └── print/
│       └── (print-specific templates)
│
└── admin/
    ├── settings-page.php         (Plugin settings UI)
    └── partials/
        └── (admin UI components)
```

### **3.2 Plugin Header (dedebify.php)**

```php
<?php
/**
 * Plugin Name: DeDebtify
 * Plugin URI: https://yoursite.com/dedebify
 * Description: Comprehensive debt management and financial tracking plugin for WordPress. Track credit cards, loans, mortgages, bills, and goals over multiple years.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yoursite.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: dedebify
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Elementor tested up to: 3.20
 * Elementor Pro tested up to: 3.20
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Plugin version
define( 'DEDEBIFY_VERSION', '1.0.0' );
define( 'DEDEBIFY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DEDEBIFY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Activation and deactivation hooks
register_activation_hook( __FILE__, 'activate_dedebify' );
register_deactivation_hook( __FILE__, 'deactivate_dedebify' );

// Run the plugin
function run_dedebify() {
    require_once DEDEBIFY_PLUGIN_DIR . 'includes/class-dedebify.php';
    $plugin = new Dedebify();
    $plugin->run();
}
run_dedebify();
```

---

## **4\. Database Schema**

### **4.1 Custom Post Types (CPTs)**

We will create **6 Custom Post Types** to store user financial data:

#### **4.1.1 Credit Cards (`dd_credit_card`)**

**Purpose:** Store individual credit card information for each user

**Fields:**

| Field Name | Field Type | Required | Description | Example Value |
| ----- | ----- | ----- | ----- | ----- |
| card\_name | text | Yes | Name/identifier for the card | "Chase Freedom" |
| balance | number | Yes | Current balance in USD | 3500.00 |
| credit\_limit | number | Yes | Credit limit in USD | 5000.00 |
| interest\_rate | number | Yes | Annual interest rate (%) | 18.99 |
| minimum\_payment | number | Yes | Required minimum monthly payment | 75.00 |
| extra\_payment | number | No | Additional payment user wants to make | 200.00 |
| due\_date | number | No | Day of month payment is due (1-31) | 15 |
| auto\_pay | true/false | No | Whether auto-pay is enabled | true |
| status | select | Yes | Card status | "active" |

**Status Options:** active, paid\_off, closed

**Calculated Fields (not stored, calculated on-the-fly):**

* `utilization_percentage` \= (balance / credit\_limit) × 100  
* `months_to_payoff` \= Calculate using minimum \+ extra payment  
* `payoff_date` \= Current date \+ months\_to\_payoff  
* `total_interest` \= Total interest paid over payoff period

#### **4.1.2 Loans (`dd_loan`)**

**Purpose:** Store personal loans, auto loans, student loans, etc.

**Fields:**

| Field Name | Field Type | Required | Description | Example Value |
| ----- | ----- | ----- | ----- | ----- |
| loan\_name | text | Yes | Name of the loan | "Auto Loan \- Honda Civic" |
| loan\_type | select | Yes | Type of loan | "auto" |
| principal | number | Yes | Original loan amount | 25000.00 |
| current\_balance | number | Yes | Remaining balance | 18500.00 |
| interest\_rate | number | Yes | Annual interest rate (%) | 5.75 |
| term\_months | number | Yes | Original term in months | 60 |
| monthly\_payment | number | Yes | Required monthly payment | 480.00 |
| start\_date | date | Yes | Loan start date | 2023-06-15 |
| extra\_payment | number | No | Additional payment | 100.00 |

**Loan Type Options:** personal, auto, student, other

**Calculated Fields:**

* `months_remaining` \= Based on current balance and payments  
* `total_interest_remaining` \= Interest to be paid over remaining term  
* `payoff_date` \= Current date \+ months\_remaining

#### **4.1.3 Mortgage (`dd_mortgage`)**

**Purpose:** Store mortgage information (typically one per user)

**Fields:**

| Field Name | Field Type | Required | Description | Example Value |
| ----- | ----- | ----- | ----- | ----- |
| property\_address | text | No | Property address | "123 Main St" |
| loan\_amount | number | Yes | Original mortgage amount | 250000.00 |
| current\_balance | number | Yes | Remaining balance | 235000.00 |
| interest\_rate | number | Yes | Current interest rate (%) | 4.25 |
| term\_years | number | Yes | Original term in years | 30 |
| monthly\_payment | number | Yes | Monthly P\&I payment | 1230.00 |
| start\_date | date | Yes | Mortgage start date | 2020-03-01 |
| extra\_payment | number | No | Additional principal payment | 500.00 |
| property\_tax | number | No | Annual property tax | 3600.00 |
| homeowners\_insurance | number | No | Annual insurance | 1200.00 |
| pmi | number | No | Monthly PMI (if applicable) | 85.00 |

**Calculated Fields:**

* `total_monthly_payment` \= monthly\_payment \+ (property\_tax/12) \+ (insurance/12) \+ pmi  
* `months_remaining`  
* `payoff_date`  
* `total_interest_remaining`  
* `equity` \= property\_value \- current\_balance (if property\_value provided)

#### **4.1.4 Bills (`dd_bill`)**

**Purpose:** Track recurring monthly expenses

**Fields:**

| Field Name | Field Type | Required | Description | Example Value |
| ----- | ----- | ----- | ----- | ----- |
| bill\_name | text | Yes | Name of the bill | "Netflix Subscription" |
| category | select | Yes | Bill category | "entertainment" |
| amount | number | Yes | Bill amount | 15.99 |
| frequency | select | Yes | How often billed | "monthly" |
| due\_date | number | No | Day of month due (1-31) | 5 |
| auto\_pay | true/false | No | Auto-pay enabled | true |
| is\_essential | true/false | No | Essential vs. discretionary | false |

**Category Options:** housing, transportation, utilities, food, healthcare, insurance, entertainment, subscriptions, other

**Frequency Options:** weekly, bi-weekly, monthly, quarterly, annually

**Calculated Fields:**

* `monthly_equivalent` \= Convert all frequencies to monthly amount

#### **4.1.5 Goals (`dd_goal`)**

**Purpose:** Track savings goals and financial milestones

**Fields:**

| Field Name | Field Type | Required | Description | Example Value |
| ----- | ----- | ----- | ----- | ----- |
| goal\_name | text | Yes | Name of the goal | "Emergency Fund" |
| goal\_type | select | Yes | Type of goal | "savings" |
| target\_amount | number | Yes | Goal target amount | 10000.00 |
| current\_amount | number | Yes | Current saved amount | 3500.00 |
| monthly\_contribution | number | No | Monthly contribution | 250.00 |
| target\_date | date | No | Target completion date | 2026-12-31 |
| priority | select | No | Goal priority | "high" |

**Goal Type Options:** savings, emergency\_fund, debt\_payoff, investment, purchase, other

**Priority Options:** low, medium, high

**Calculated Fields:**

* `remaining_amount` \= target\_amount \- current\_amount  
* `months_to_goal` \= remaining\_amount / monthly\_contribution  
* `estimated_completion_date` \= current\_date \+ months\_to\_goal  
* `progress_percentage` \= (current\_amount / target\_amount) × 100

#### **4.1.6 Financial Snapshots (`dd_snapshot`)**

**Purpose:** Store monthly snapshots of user's complete financial picture for historical tracking

**Fields:**

| Field Name | Field Type | Required | Description | Example Value |
| ----- | ----- | ----- | ----- | ----- |
| snapshot\_date | date | Yes | Date of snapshot | 2025-10-01 |
| total\_debt | number | Yes | Total of all debts | 45000.00 |
| total\_credit\_card\_debt | number | Yes | Sum of all credit cards | 10700.00 |
| total\_loan\_debt | number | Yes | Sum of all loans | 18500.00 |
| total\_mortgage\_debt | number | Yes | Mortgage balance | 235000.00 |
| total\_monthly\_payments | number | Yes | All monthly debt payments | 2850.00 |
| total\_monthly\_bills | number | Yes | All monthly bills | 1850.00 |
| monthly\_income | number | Yes | User's monthly income | 4000.00 |
| debt\_to\_income\_ratio | number | Yes | DTI percentage | 68.75 |
| credit\_utilization | number | Yes | Overall credit utilization % | 45.5 |
| total\_assets | number | No | Total assets/savings | 12000.00 |
| net\_worth | number | No | Assets \- Debts | \-33000.00 |

**Purpose of Snapshots:**

* Track progress over time  
* Generate charts/graphs showing debt reduction  
* Compare month-to-month changes  
* Calculate trends and projections

### **4.2 User Meta Fields**

Store global user settings in WordPress user meta:

| Meta Key | Data Type | Description | Example |
| ----- | ----- | ----- | ----- |
| dd\_monthly\_income | number | User's monthly income | 4000.00 |
| dd\_target\_debt\_free\_date | date | Target debt-free date | 2028-12-31 |
| dd\_preferred\_payoff\_method | string | avalanche, snowball, or custom | "avalanche" |
| dd\_notification\_preferences | serialized array | Notification settings | array() |
| dd\_currency | string | User's currency | "USD" |

### **4.3 Database Relationships**

```
User (WordPress wp_users)
│
├── Has Many: Credit Cards (dd_credit_card posts)
├── Has Many: Loans (dd_loan posts)
├── Has Many: Mortgages (dd_mortgage posts)
├── Has Many: Bills (dd_bill posts)
├── Has Many: Goals (dd_goal posts)
└── Has Many: Snapshots (dd_snapshot posts)
```

**Important:** All CPTs must store the `post_author` field as the user ID to associate data with the correct user.

---

## **5\. Feature Specifications**

### **5.1 Dashboard Overview**

**Purpose:** Display a comprehensive summary of the user's financial situation

**Components:**

#### **5.1.1 Key Metrics (Top Cards)**

Display these calculated values prominently:

1. **Total Debt**

   * Formula: Sum of all credit card balances \+ loan balances \+ mortgage balance  
   * Display: Large number with currency formatting  
   * Color coding: Red if \> $50k, Orange if $20k-$50k, Green if \< $20k  
2. **Monthly Debt Payments**

   * Formula: Sum of all minimum payments \+ extra payments across all debts  
   * Display: Currency format  
   * Show breakdown on hover/click  
3. **Monthly Bills**

   * Formula: Sum of all bill monthly\_equivalent amounts  
   * Display: Currency format  
   * Separate essential vs. discretionary  
4. **Debt-to-Income Ratio (DTI)**

   * Formula: (total\_monthly\_payments / monthly\_income) × 100  
   * Display: Percentage with color coding  
   * \< 36% \= Green (good)  
   * 36-43% \= Yellow (fair)  
   * 43% \= Red (high)

5. **Credit Utilization**

   * Formula: (sum of all credit card balances / sum of all credit limits) × 100  
   * Display: Percentage with color coding  
   * \< 30% \= Green (good)  
   * 30-50% \= Yellow (fair)  
   * 50% \= Red (high)

6. **Projected Debt-Free Date**

   * Formula: Calculate based on current payment schedule  
   * Display: Date format (e.g., "March 2028")  
   * Show countdown (e.g., "42 months away")

#### **5.1.2 Debt Breakdown Chart**

**Visual:** Pie chart or bar chart showing debt by type

* Credit Cards: $X (Y%)  
* Loans: $X (Y%)  
* Mortgage: $X (Y%)

#### **5.1.3 Progress Over Time**

**Visual:** Line chart showing total debt reduction over time

* X-axis: Months (from oldest snapshot to present)  
* Y-axis: Total debt amount  
* Data source: dd\_snapshot posts

#### **5.1.4 Quick Actions**

Buttons for common tasks:

* "Add Credit Card"  
* "Add Bill"  
* "Make Extra Payment"  
* "Create Snapshot"  
* "View Action Plan"

**Expected Output Example:**

```
┌──────────────────────────────────────────────────────┐
│              DEBT FREEDOM DASHBOARD                   │
├──────────────────────────────────────────────────────┤
│                                                       │
│  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐│
│  │ $45,200 │  │  $2,850 │  │  $1,850 │  │   42%   ││
│  │Total Debt│  │ Monthly │  │ Monthly │  │   DTI   ││
│  │         │  │Payments │  │  Bills  │  │         ││
│  └─────────┘  └─────────┘  └─────────┘  └─────────┘│
│                                                       │
│  ┌─────────┐  ┌──────────────────────────────────┐ │
│  │   45%   │  │  Debt-Free Date: March 2028      │ │
│  │ Credit  │  │  (42 months remaining)            │ │
│  │  Util.  │  │                                   │ │
│  └─────────┘  └──────────────────────────────────┘ │
│                                                       │
│  Debt Breakdown:                                     │
│  ┌───────────────────────────────────────────────┐  │
│  │ [████████████] Credit Cards    $10,700 (24%)  │  │
│  │ [████████████████] Loans       $18,500 (41%)  │  │
│  │ [████████████████████] Mortgage $16,000 (35%) │  │
│  └───────────────────────────────────────────────┘  │
│                                                       │
│  [Chart: Debt Progress Over Time]                    │
│                                                       │
│  Quick Actions:                                      │
│  [+ Add Credit Card] [+ Add Bill] [View Plan]       │
└──────────────────────────────────────────────────────┘
```

### **5.2 Credit Card Manager**

**Purpose:** Allow users to add, edit, and track multiple credit cards with payoff calculations

#### **5.2.1 Credit Card List View**

Display all user's credit cards in a table/card layout:

| Card Name | Balance | Limit | Utilization | APR | Min Payment | Status | Actions |
| ----- | ----- | ----- | ----- | ----- | ----- | ----- | ----- |
| Chase Freedom | $3,500 | $5,000 | 70% | 18.99% | $75 | Active | Edit / Delete |

**Features:**

* Sort by: Balance (high to low), Interest Rate, Utilization  
* Filter by: Status (active, paid off, closed)  
* Color coding: Red for high utilization (\>70%), Yellow for medium (30-70%), Green for low (\<30%)

#### **5.2.2 Add/Edit Credit Card Form**

**Form Fields:**

1. Card Name (text input) \- Required  
2. Current Balance (number input, $) \- Required  
3. Credit Limit (number input, $) \- Required  
4. Interest Rate (number input, %) \- Required  
5. Minimum Payment (number input, $) \- Required  
6. Extra Payment (number input, $) \- Optional  
7. Due Date (number input, 1-31) \- Optional  
8. Auto-Pay Enabled (checkbox) \- Optional  
9. Status (dropdown: Active/Paid Off/Closed) \- Required

**Validation Rules:**

* Balance cannot exceed Credit Limit  
* Interest rate must be between 0-100%  
* Minimum payment must be \> 0  
* Due date must be between 1-31

**Save Behavior:**

* Create new CPT post (`dd_credit_card`)  
* Set `post_author` to current user ID  
* Save all fields as post meta  
* Show success message: "Credit card saved successfully\!"  
* Redirect to Credit Card List

#### **5.2.3 Payoff Calculator**

For each credit card, show three payoff scenarios:

**Scenario 1: Minimum Payment Only**

```
Monthly Payment: $75
Months to Pay Off: 87 months (7.3 years)
Total Interest Paid: $3,825
Payoff Date: January 2033
```

**Scenario 2: Minimum \+ Extra Payment**

```
Monthly Payment: $275 ($75 min + $200 extra)
Months to Pay Off: 15 months (1.25 years)
Total Interest Paid: $485
Payoff Date: January 2027
```

**Scenario 3: Debt Avalanche Method** (Applies all extra payments to highest interest card first)

```
Order in payoff queue: 2nd (after Chase Sapphire)
Estimated payoff: March 2027
Total Interest Saved: $1,240 (vs. minimum only)
```

**Calculation Formula for Months to Payoff:**

```javascript
function calculateMonthsToPayoff(balance, interestRate, monthlyPayment) {
    // Convert annual rate to monthly decimal
    const monthlyRate = (interestRate / 100) / 12;
    
    // Calculate months using logarithmic formula
    // n = -log(1 - (B * r / P)) / log(1 + r)
    // Where: B = balance, r = monthly rate, P = monthly payment
    
    if (monthlyPayment <= balance * monthlyRate) {
        return Infinity; // Payment doesn't cover interest
    }
    
    const months = -Math.log(1 - (balance * monthlyRate / monthlyPayment)) / 
                   Math.log(1 + monthlyRate);
    
    return Math.ceil(months);
}

function calculateTotalInterest(balance, interestRate, monthlyPayment, months) {
    const totalPaid = monthlyPayment * months;
    const totalInterest = totalPaid - balance;
    return Math.max(0, totalInterest);
}
```

#### **5.2.4 Bulk Actions**

* "Pay Off Selected" \- Mark multiple cards as paid off  
* "Delete Selected" \- Remove multiple cards  
* "Export to CSV" \- Download credit card data

**Expected Output Example:**

```
┌────────────────────────────────────────────────────────┐
│                 CREDIT CARD MANAGER                     │
├────────────────────────────────────────────────────────┤
│                                                         │
│  Total Credit Card Debt: $10,700                       │
│  Overall Utilization: 45.5%                            │
│  [+ Add New Credit Card]                               │
│                                                         │
│  ┌──────────────────────────────────────────────────┐ │
│  │ Chase Freedom                              ACTIVE │ │
│  │ Balance: $3,500 / $5,000 (70%)                   │ │
│  │ APR: 18.99%  |  Min Payment: $75                 │ │
│  │                                                   │ │
│  │ Payoff Scenarios:                                │ │
│  │ • Min Only: 87 months | $3,825 interest          │ │
│  │ • With Extra $200: 15 months | $485 interest     │ │
│  │                                                   │ │
│  │ [Edit] [Delete] [Make Payment]                   │ │
│  └──────────────────────────────────────────────────┘ │
│                                                         │
│  ┌──────────────────────────────────────────────────┐ │
│  │ Discover It                                ACTIVE │ │
│  │ Balance: $1,200 / $3,000 (40%)                   │ │
│  │ APR: 15.24%  |  Min Payment: $35                 │ │
│  │ [Edit] [Delete] [Make Payment]                   │ │
│  └──────────────────────────────────────────────────┘ │
│                                                         │
└────────────────────────────────────────────────────────┘
```

### **5.3 Loan Manager**

**Purpose:** Track and manage personal loans, auto loans, student loans

#### **5.3.1 Loan List View**

Similar to credit cards, display all loans:

| Loan Name | Type | Balance | Rate | Monthly Payment | Months Left | Actions |
| ----- | ----- | ----- | ----- | ----- | ----- | ----- |
| Auto Loan \- Honda | Auto | $18,500 | 5.75% | $480 | 42 | Edit / Delete |

#### **5.3.2 Add/Edit Loan Form**

**Form Fields:**

1. Loan Name (text) \- Required  
2. Loan Type (select: Personal, Auto, Student, Other) \- Required  
3. Original Amount (number, $) \- Required  
4. Current Balance (number, $) \- Required  
5. Interest Rate (number, %) \- Required  
6. Term in Months (number) \- Required  
7. Monthly Payment (number, $) \- Required  
8. Start Date (date picker) \- Required  
9. Extra Payment (number, $) \- Optional

**Auto-Calculate Feature:** If user enters original amount, rate, and term, auto-calculate monthly payment:

```javascript
function calculateLoanPayment(principal, annualRate, termMonths) {
    const monthlyRate = (annualRate / 100) / 12;
    const payment = principal * 
                   (monthlyRate * Math.pow(1 + monthlyRate, termMonths)) / 
                   (Math.pow(1 + monthlyRate, termMonths) - 1);
    return payment.toFixed(2);
}
```

#### **5.3.3 Loan Payoff Projections**

Show:

* Months remaining at current payment  
* Payoff date with current payment  
* Months remaining with extra payment  
* Interest saved with extra payment  
* Amortization schedule (optional, advanced feature)

### **5.4 Mortgage Manager**

**Purpose:** Track mortgage with property details and refinancing scenarios

#### **5.4.1 Mortgage Overview**

Display single mortgage (most users only have one):

* Property address  
* Original loan amount vs. current balance  
* Interest rate and term  
* Monthly P\&I payment  
* Additional costs (property tax, insurance, PMI)  
* Total monthly housing cost  
* Equity (if property value provided)

#### **5.4.2 Add/Edit Mortgage Form**

**Form Fields:**

1. Property Address (text) \- Optional  
2. Original Loan Amount (number, $) \- Required  
3. Current Balance (number, $) \- Required  
4. Interest Rate (number, %) \- Required  
5. Term in Years (number) \- Required  
6. Monthly Payment (number, $) \- Required  
7. Start Date (date) \- Required  
8. Extra Principal Payment (number, $) \- Optional  
9. Property Tax (annual, $) \- Optional  
10. Homeowners Insurance (annual, $) \- Optional  
11. PMI (monthly, $) \- Optional  
12. Estimated Property Value (number, $) \- Optional

**Calculated Display:**

* Total Monthly Housing Cost \= P\&I \+ (tax/12) \+ (insurance/12) \+ PMI  
* Home Equity \= Property Value \- Current Balance  
* Loan-to-Value Ratio \= (Current Balance / Property Value) × 100

#### **5.4.3 Refinance Calculator**

**Input Fields:**

1. New Interest Rate (number, %)  
2. New Term in Years (number)  
3. Closing Costs (number, $)  
4. New Monthly Payment (auto-calculated)

**Output:**

```
Current Loan:
  Monthly Payment: $1,230
  Remaining Term: 27 years
  Total Interest Remaining: $178,450

Refinanced Loan:
  New Monthly Payment: $1,085 (-$145/month)
  New Term: 30 years
  Total Interest: $155,600
  
Savings:
  Monthly Savings: $145
  Lifetime Interest Savings: $22,850
  Break-even Point: 18 months (closing costs / monthly savings)
  
Recommendation: ✓ Refinancing makes sense if you plan to stay 18+ months
```

### **5.5 Bill Tracker**

**Purpose:** Track all recurring monthly expenses

#### **5.5.1 Bill List View**

Display all bills with due dates highlighted:

| Bill Name | Category | Amount | Frequency | Due Date | Auto-Pay | Status | Actions |
| ----- | ----- | ----- | ----- | ----- | ----- | ----- | ----- |
| Netflix | Entertainment | $15.99 | Monthly | 5th | ✓ | Paid | Edit / Delete |
| Car Insurance | Insurance | $125.00 | Monthly | 15th | ✓ | Upcoming | Edit / Delete |

**Features:**

* Calendar view option (show bills on calendar by due date)  
* Filter by: Category, Essential vs. Discretionary  
* Sort by: Due Date, Amount, Name  
* Color coding:  
  * Green \= Paid this month  
  * Yellow \= Due within 7 days  
  * Red \= Overdue

#### **5.5.2 Add/Edit Bill Form**

**Form Fields:**

1. Bill Name (text) \- Required  
2. Category (select) \- Required  
   * Housing  
   * Transportation  
   * Utilities  
   * Food  
   * Healthcare  
   * Insurance  
   * Entertainment  
   * Subscriptions  
   * Other  
3. Amount (number, $) \- Required  
4. Frequency (select) \- Required  
   * Weekly  
   * Bi-weekly  
   * Monthly  
   * Quarterly  
   * Annually  
5. Due Date (day of month, 1-31) \- Optional  
6. Auto-Pay Enabled (checkbox) \- Optional  
7. Essential Bill (checkbox) \- Optional

**Calculated Field:**

* Monthly Equivalent \= Convert all frequencies to monthly amount  
  * Weekly: amount × 52 ÷ 12  
  * Bi-weekly: amount × 26 ÷ 12  
  * Monthly: amount  
  * Quarterly: amount ÷ 3  
  * Annually: amount ÷ 12

#### **5.5.3 Bill Summary**

Show totals:

* Total Monthly Bills: $1,850  
* Essential Bills: $1,450 (78%)  
* Discretionary Bills: $400 (22%)  
* Bills on Auto-Pay: 8 of 12  
* Upcoming in Next 7 Days: 3 bills, $285 total

### **5.6 Goals Tracker**

**Purpose:** Set and track financial goals

#### **5.6.1 Goal List View**

Display all active goals with progress bars:

```
┌─────────────────────────────────────────────────┐
│ Emergency Fund                                   │
│ ████████████░░░░░░░░  60% ($6,000 / $10,000)   │
│ Monthly: $250  |  26 months remaining           │
│ Target: Dec 2027                                 │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│ Vacation Fund                                    │
│ ████░░░░░░░░░░░░░░░░  25% ($750 / $3,000)      │
│ Monthly: $150  |  15 months remaining           │
│ Target: Jan 2027                                 │
└─────────────────────────────────────────────────┘
```

#### **5.6.2 Add/Edit Goal Form**

**Form Fields:**

1. Goal Name (text) \- Required  
2. Goal Type (select) \- Required  
   * Savings  
   * Emergency Fund  
   * Debt Payoff  
   * Investment  
   * Purchase  
   * Other  
3. Target Amount (number, $) \- Required  
4. Current Amount (number, $) \- Required  
5. Monthly Contribution (number, $) \- Optional  
6. Target Date (date picker) \- Optional  
7. Priority (select: Low, Medium, High) \- Optional

**Calculated Display:**

* Remaining Amount \= Target \- Current  
* Progress Percentage \= (Current / Target) × 100  
* Months to Goal \= Remaining / Monthly Contribution  
* Estimated Completion \= Current Date \+ Months to Goal  
* On Track Status \= Compare estimated date to target date

#### **5.6.3 Goal Update Modal**

Quick update modal to log progress:

* "Add to Goal" button  
* Input amount to add  
* Update current amount  
* Show new progress percentage  
* Celebrate milestones (25%, 50%, 75%, 100%)

### **5.7 Monthly Snapshot Feature**

**Purpose:** Capture and store a complete financial picture each month for historical tracking

#### **5.7.1 Create Snapshot Button**

* Prominent button on Dashboard  
* "Create Monthly Snapshot" or "Save Progress"  
* Can be automated (e.g., first of each month)

#### **5.7.2 Snapshot Creation Process**

When button clicked:

1. Calculate all current totals:  
   * Total debt (all sources)  
   * Credit card debt subtotal  
   * Loan debt subtotal  
   * Mortgage debt subtotal  
   * Total monthly debt payments  
   * Total monthly bills  
   * Monthly income (from user meta)  
   * DTI ratio  
   * Credit utilization  
2. Create new `dd_snapshot` CPT post  
3. Save all calculated values as post meta  
4. Set post\_date to current date  
5. Link to current user (post\_author)  
6. Show success message with summary

#### **5.7.3 View Snapshot History**

* List view of all snapshots by date  
* Click to see detailed snapshot  
* Compare two snapshots side-by-side  
* Export snapshot data to PDF/CSV

**Snapshot Comparison Example:**

```
September 2025  →  October 2025
────────────────────────────────────────
Total Debt:     $45,200  →  $43,850  ↓ $1,350 (3%)
Monthly Payments: $2,850  →  $2,850  → No change
DTI Ratio:        68%    →  66%     ↓ 2%
Credit Util:      45.5%  →  42%     ↓ 3.5%

Progress: ✓ You're on track! Keep it up!
```

### **5.8 Debt Action Plan**

**Purpose:** Generate personalized debt payoff strategy

#### **5.8.1 Plan Generator**

Based on user's data, generate a step-by-step action plan:

**Inputs:**

* All credit cards  
* All loans  
* All bills  
* Monthly income  
* Available extra payment amount  
* Preferred method (avalanche, snowball, or custom)

**Output Sections:**

1. **Current Situation Summary**

   * Total debt  
   * Monthly obligations  
   * DTI ratio  
   * Projected debt-free date (current pace)  
2. **Recommended Strategy**

   * Chosen method explanation  
   * Payoff order for all debts  
   * Monthly payment allocation  
3. **Action Steps (Prioritized)**

   * Step 1: Build $1,000 emergency fund  
   * Step 2: Pay minimums on all debts  
   * Step 3: Apply all extra payments to \[highest priority debt\]  
   * Step 4: Once paid off, roll payment to next debt  
   * Step 5: Cut discretionary spending by X%  
   * Step 6: Consider side income options  
   * etc.  
4. **Milestones**

   * Month 6: Credit Card A paid off  
   * Month 12: Credit utilization under 30%  
   * Month 18: Credit Card B paid off  
   * Month 24: All credit cards paid off  
   * Month 48: Debt-free\!  
5. **Motivation Section**

   * Interest savings vs. minimum-only approach  
   * Years saved vs. minimum-only approach  
   * Visualize debt-free life

**Debt Payoff Methods:**

**Avalanche Method:**

* Pay off debts in order of highest interest rate first  
* Mathematically optimal (saves most interest)  
* Order debts by interest rate (high to low)  
* Apply all extra payments to highest rate debt

**Snowball Method:**

* Pay off debts in order of smallest balance first  
* Psychologically motivating (quick wins)  
* Order debts by balance (low to high)  
* Apply all extra payments to smallest balance

**Custom Method:**

* User manually sets payoff order  
* Allows for personal priorities (e.g., pay off ex's loan first)

#### **5.8.2 Printable Action Plan**

* Clean, printer-friendly format  
* One-page summary  
* Can be posted on fridge/wall  
* Checkbox items to track progress

---

## **6\. User Interface Requirements**

### **6.1 Elementor Integration**

#### **6.1.1 Dynamic Tags**

Create custom Elementor dynamic tags for displaying user data:

**Available Dynamic Tags:**

* `{dd_total_debt}` \- Total debt amount  
* `{dd_monthly_payments}` \- Total monthly debt payments  
* `{dd_monthly_bills}` \- Total monthly bills  
* `{dd_dti_ratio}` \- Debt-to-income ratio  
* `{dd_credit_utilization}` \- Overall credit utilization  
* `{dd_debt_free_date}` \- Projected debt-free date  
* `{dd_months_to_debt_free}` \- Months until debt-free  
* `{dd_total_interest_savings}` \- Interest savings from extra payments  
* `{dd_credit_card_count}` \- Number of active credit cards  
* `{dd_loan_count}` \- Number of active loans  
* etc.

**Usage in Elementor:**

1. Add Heading widget  
2. Click dynamic tags icon  
3. Select "DeDebtify" category  
4. Choose desired tag  
5. Tag automatically pulls current user's data

#### **6.1.2 Custom Elementor Widgets**

Create custom widgets for complex components:

**Widget 1: Debt Dashboard Widget**

* Displays complete dashboard summary  
* Configurable in Elementor:  
  * Show/hide metrics  
  * Color scheme  
  * Layout (grid, list, cards)

**Widget 2: Credit Card List Widget**

* Displays user's credit cards  
* Configurable:  
  * Columns to show  
  * Sort order  
  * Show/hide payoff calculator

**Widget 3: Progress Chart Widget**

* Line chart of debt over time  
* Configurable:  
  * Chart type (line, bar, area)  
  * Time range (3 months, 6 months, 1 year, all time)  
  * Show/hide specific debt types

**Widget 4: Debt Breakdown Widget**

* Pie or donut chart  
* Shows debt by type  
* Configurable colors

**Widget 5: Quick Add Form Widget**

* Inline form to quickly add items  
* Choose form type (credit card, loan, bill, goal)  
* Minimal fields for speed

**Widget 6: Upcoming Bills Widget**

* Shows next 7 days of bills  
* Calendar-style or list view  
* Color-coded by status

#### **6.1.3 Elementor Templates**

Provide pre-built Elementor templates:

1. **Dashboard Page Template**

   * Full dashboard layout  
   * Metrics at top, charts below  
   * Quick action buttons  
2. **Credit Cards Page Template**

   * List of cards  
   * Add button  
   * Filtering options  
3. **Loans Page Template**

4. **Bills Page Template**

5. **Goals Page Template**

6. **Action Plan Page Template**

**Junior Developer Note:** Elementor templates are JSON files exported from Elementor. Include these in plugin's `/templates/elementor/` folder and provide import instructions.

### **6.2 Responsive Design Requirements**

All interfaces must work on:

* **Desktop:** 1920px, 1440px, 1280px  
* **Tablet:** 768px, 1024px  
* **Mobile:** 375px, 414px

**Mobile-Specific Considerations:**

* Stack cards vertically  
* Simplify tables (convert to cards on mobile)  
* Make buttons touch-friendly (min 44px height)  
* Hide less critical data, show on expand  
* Bottom navigation for quick access

### **6.3 Print Styles**

**Print Requirements:**

* Remove navigation, sidebars, footers  
* Black & white friendly (use patterns, not just colors)  
* Page breaks at logical points  
* Include header with user name and date  
* Footer with page numbers  
* QR code to return to online version (optional)

**Print CSS Example:**

```css
@media print {
    /* Hide non-essential elements */
    .site-header,
    .site-footer,
    .sidebar,
    .navigation,
    .print-hide {
        display: none !important;
    }
    
    /* Optimize for printing */
    body {
        font-size: 12pt;
        color: #000;
        background: #fff;
    }
    
    /* Ensure proper page breaks */
    .dd-credit-card,
    .dd-loan,
    .dd-bill {
        page-break-inside: avoid;
    }
    
    .dd-dashboard-section {
        page-break-after: always;
    }
    
    /* Show URLs for links */
    a[href]:after {
        content: " (" attr(href) ")";
    }
}
```

### **6.4 Accessibility Requirements**

**WCAG 2.1 Level AA Compliance:**

* Color contrast ratio: 4.5:1 minimum for text  
* All interactive elements keyboard accessible  
* ARIA labels on all icons  
* Form validation with clear error messages  
* Focus indicators visible  
* Screen reader friendly  
* Alt text on all images/charts

---

## **7\. Integration Requirements**

### **7.1 JetEngine Integration**

#### **7.1.1 CPT Registration**

Use JetEngine to register all custom post types through UI or programmatically.

**Programmatic CPT Registration Example:**

```php
function dedebify_register_cpts() {
    // Only register if JetEngine is not active
    if ( ! function_exists( 'jet_engine' ) ) {
        // Register CPTs using WordPress register_post_type()
        // (fallback method)
    }
    // If JetEngine is active, CPTs should be registered via JetEngine UI
}
add_action( 'init', 'dedebify_register_cpts' );
```

**JetEngine Meta Fields Setup:**

1. Navigate to JetEngine → Meta Boxes  
2. Create meta box for each CPT  
3. Add fields as specified in Database Schema section  
4. Set field types, validation rules  
5. Assign to respective post type

#### **7.1.2 Dynamic Visibility**

Use JetEngine's dynamic visibility conditions:

* Show "Paid Off" badge only if status \= paid\_off  
* Hide payoff calculator if balance \= 0  
* Show warning if DTI \> 43%

#### **7.1.3 Calculated Fields**

Use JetEngine callbacks for calculated fields:

**Example: Credit Utilization Callback**

```php
function dd_calculate_utilization( $field_value, $post_id ) {
    $balance = get_post_meta( $post_id, 'balance', true );
    $credit_limit = get_post_meta( $post_id, 'credit_limit', true );
    
    if ( $credit_limit > 0 ) {
        return round( ( $balance / $credit_limit ) * 100, 2 );
    }
    return 0;
}
add_filter( 'jet-engine/calculated-field/utilization_percentage', 'dd_calculate_utilization', 10, 2 );
```

### **7.2 OneSignal Integration (Phase 2\)**

**Notification Types:**

1. **Bill Reminders**

   * Trigger: 3 days before due date  
   * Message: "Your \[Bill Name\] payment of $\[Amount\] is due in 3 days"  
2. **Payment Success**

   * Trigger: After payment recorded  
   * Message: "Great job\! You paid $\[Amount\] toward \[Debt Name\]"  
3. **Milestone Celebrations**

   * Trigger: Goal reached, card paid off, etc.  
   * Message: "🎉 Congratulations\! You just paid off \[Card Name\]\!"  
4. **Monthly Snapshot Reminder**

   * Trigger: 1st of each month  
   * Message: "Time to create your monthly snapshot and track progress\!"  
5. **Debt-Free Projection Updates**

   * Trigger: Debt-free date changes significantly  
   * Message: "Your extra payments moved your debt-free date up by 6 months\!"

**Implementation:**

* Install OneSignal WordPress plugin  
* Set up OneSignal account and get API keys  
* Create custom notification triggers in DeDebtify  
* Use OneSignal REST API to send notifications  
* Allow users to manage notification preferences

### **7.3 n8n Automation (Phase 2\)**

**Automation Workflows:**

1. **Monthly Snapshot Auto-Creation**

   * Trigger: 1st of each month, 9:00 AM  
   * Action: Create snapshot for all active users  
   * Send summary email  
2. **Interest Calculation Updates**

   * Trigger: Daily at midnight  
   * Action: Recalculate interest accrued on all debts  
   * Update balances if interest compounds daily  
3. **Payment Reminders**

   * Trigger: Check daily for bills due in 3 days  
   * Action: Send email \+ push notification  
4. **Data Export**

   * Trigger: User requests export  
   * Action: Generate CSV/PDF of all data  
   * Email download link  
5. **Overdue Bill Alerts**

   * Trigger: Check daily for overdue bills  
   * Action: Send reminder notification

**n8n Setup:**

* Create webhook endpoints in n8n  
* DeDebtify sends data to n8n webhooks  
* n8n processes data, runs calculations  
* n8n sends results back to WordPress via REST API

---

## **8\. Development Phases**

### **Phase 1: Foundation (Week 1\)**

**Goal:** Basic plugin structure and CPT setup

**Tasks:**

1. Create plugin folder structure  
2. Set up main plugin file with header  
3. Create activation/deactivation hooks  
4. Register all 6 CPTs programmatically (fallback if no JetEngine)  
5. Add meta boxes for all CPT fields  
6. Test CPT creation and data storage

**Deliverables:**

* Plugin installs and activates without errors  
* All CPTs registered and visible in WordPress admin  
* Can manually create posts for each CPT type  
* Data saves correctly to post meta

**Testing Checklist:**

* \[ \] Plugin activates successfully  
* \[ \] All 6 CPTs appear in admin menu  
* \[ \] Can create credit card post and save data  
* \[ \] Can create loan post and save data  
* \[ \] Can create mortgage post and save data  
* \[ \] Can create bill post and save data  
* \[ \] Can create goal post and save data  
* \[ \] Can create snapshot post and save data  
* \[ \] Data persists after save  
* \[ \] CPTs are user-specific (only show user's own posts)

### **Phase 2: Core Calculations (Week 1-2)**

**Goal:** Implement all calculation logic

**Tasks:**

1. Create `class-dedebify-calculations.php`  
2. Implement credit card payoff calculations  
3. Implement loan amortization calculations  
4. Implement mortgage calculations  
5. Implement DTI and utilization calculations  
6. Create REST API endpoints for calculations  
7. Write unit tests for calculations

**Deliverables:**

* All calculation functions working accurately  
* REST API returns correct calculations  
* JavaScript can call calculations via AJAX

**Testing Checklist:**

* \[ \] Credit card payoff months calculated correctly  
* \[ \] Interest calculations accurate (validated against online calculators)  
* \[ \] Loan payment calculations correct  
* \[ \] Mortgage calculations include all components  
* \[ \] DTI ratio calculates correctly  
* \[ \] Credit utilization accurate  
* \[ \] REST API endpoints accessible and return JSON  
* \[ \] All edge cases handled (zero balance, no credit limit, etc.)

### **Phase 3: Dashboard UI (Week 2\)**

**Goal:** Build main dashboard interface

**Tasks:**

1. Create dashboard page template  
2. Build key metrics display (6 cards)  
3. Implement data fetching for current user  
4. Add basic styling (CSS)  
5. Make responsive for mobile  
6. Add loading states

**Deliverables:**

* Dashboard displays all 6 key metrics  
* Data loads from database  
* Responsive on all devices  
* Professional appearance

**Testing Checklist:**

* \[ \] Dashboard accessible via WordPress menu  
* \[ \] All metrics display correctly  
* \[ \] Data is user-specific  
* \[ \] Responsive on mobile (375px)  
* \[ \] Responsive on tablet (768px)  
* \[ \] No console errors  
* \[ \] Loading states show while fetching data

### **Phase 4: Credit Card Manager (Week 2-3)**

**Goal:** Complete credit card CRUD interface

**Tasks:**

1. Build credit card list view  
2. Create add/edit form  
3. Implement form validation  
4. Add save/update functionality  
5. Build payoff calculator interface  
6. Add delete functionality with confirmation  
7. Implement sorting and filtering

**Deliverables:**

* Can add new credit cards  
* Can edit existing credit cards  
* Can delete credit cards  
* Payoff calculator shows 3 scenarios  
* List view shows all cards with key info

**Testing Checklist:**

* \[ \] Add form validates required fields  
* \[ \] Edit form pre-populates with existing data  
* \[ \] Save creates new CPT post correctly  
* \[ \] Update modifies existing post  
* \[ \] Delete removes post from database  
* \[ \] Delete shows confirmation dialog  
* \[ \] Payoff calculations display correctly  
* \[ \] Can sort by balance, rate, utilization  
* \[ \] Can filter by status  
* \[ \] Mobile-friendly forms

### **Phase 5: Additional Managers (Week 3-4)**

**Goal:** Build Loan, Mortgage, Bill, Goal managers

**Tasks:**

1. Build Loan Manager (similar to Credit Card Manager)  
2. Build Mortgage Manager  
3. Build Bill Tracker  
4. Build Goals Tracker  
5. Ensure consistency across all managers

**Deliverables:**

* All 4 additional managers functional  
* CRUD operations work for all  
* Consistent UI/UX across all tools

**Testing Checklist:**

* \[ \] Loan Manager: Add, edit, delete loans  
* \[ \] Mortgage Manager: Add, edit mortgage  
* \[ \] Bill Tracker: Add, edit, delete bills  
* \[ \] Goals Tracker: Add, edit, delete, update goals  
* \[ \] All forms validate properly  
* \[ \] All calculations accurate  
* \[ \] Consistent styling across all tools

### **Phase 6: Snapshot Feature (Week 4\)**

**Goal:** Implement monthly snapshot system

**Tasks:**

1. Create "Create Snapshot" button on dashboard  
2. Build snapshot creation logic  
3. Create snapshot history view  
4. Build comparison interface  
5. Add charts showing progress over time

**Deliverables:**

* Snapshot creates complete financial picture  
* History view shows all past snapshots  
* Can compare two snapshots  
* Chart visualizes debt reduction

**Testing Checklist:**

* \[ \] Snapshot button visible on dashboard  
* \[ \] Click creates new snapshot post  
* \[ \] Snapshot captures all correct data  
* \[ \] History view lists all snapshots by date  
* \[ \] Comparison shows differences clearly  
* \[ \] Chart displays data accurately  
* \[ \] Chart responsive on mobile

### **Phase 7: Debt Action Plan (Week 4-5)**

**Goal:** Generate personalized debt payoff plans

**Tasks:**

1. Build plan generator logic  
2. Implement avalanche method  
3. Implement snowball method  
4. Create plan display interface  
5. Add print-friendly styling  
6. Generate milestones and timeline

**Deliverables:**

* Plan generator produces personalized plans  
* User can choose method  
* Plan displays clearly  
* Printable format works

**Testing Checklist:**

* \[ \] Avalanche method orders debts correctly  
* \[ \] Snowball method orders debts correctly  
* \[ \] Plan includes all required sections  
* \[ \] Milestones calculate correctly  
* \[ \] Print version formats nicely  
* \[ \] Can save/export plan as PDF

### **Phase 8: Elementor Integration (Week 5\)**

**Goal:** Create Elementor widgets and dynamic tags

**Tasks:**

1. Register Elementor widget category  
2. Create 6 custom widgets  
3. Create dynamic tags for all metrics  
4. Build example templates  
5. Test in Elementor editor

**Deliverables:**

* All widgets available in Elementor  
* Dynamic tags populate correctly  
* Templates import successfully

**Testing Checklist:**

* \[ \] Widgets appear in Elementor panel  
* \[ \] Widgets can be dragged to page  
* \[ \] Widget settings work correctly  
* \[ \] Dynamic tags show in dynamic menu  
* \[ \] Tags pull correct user data  
* \[ \] Templates import without errors  
* \[ \] Can edit widgets in Elementor

### **Phase 9: Print Functionality (Week 5-6)**

**Goal:** Add print styles for all pages

**Tasks:**

1. Create print.css stylesheet  
2. Add print styles for dashboard  
3. Add print styles for all tools  
4. Add "Print" button to each page  
5. Test on different browsers

**Deliverables:**

* Print button on every major page  
* Print formats are clean and professional  
* Works in all browsers

**Testing Checklist:**

* \[ \] Print preview looks professional  
* \[ \] Removes navigation/footer  
* \[ \] Optimized for black & white  
* \[ \] Page breaks in logical places  
* \[ \] Works in Chrome, Firefox, Safari  
* \[ \] Mobile print works

### **Phase 10: Notifications Setup (Week 6\)**

**Goal:** Integrate OneSignal for push notifications

**Tasks:**

1. Install OneSignal plugin  
2. Set up OneSignal account  
3. Create notification triggers  
4. Build user preference interface  
5. Test notifications

**Deliverables:**

* Push notifications functional  
* Users can opt in/out  
* Notifications sent at correct times

**Testing Checklist:**

* \[ \] OneSignal configured correctly  
* \[ \] Notifications appear on device  
* \[ \] Bill reminders sent 3 days before  
* \[ \] Milestone celebrations sent  
* \[ \] Monthly snapshot reminders sent  
* \[ \] Users can manage preferences  
* \[ \] Works on iOS and Android

### **Phase 11: Polish & Optimization (Week 6-7)**

**Goal:** Improve performance and user experience

**Tasks:**

1. Optimize database queries  
2. Add caching for calculations  
3. Minify CSS and JS  
4. Add loading animations  
5. Improve error messages  
6. Add help tooltips  
7. Conduct user testing

**Deliverables:**

* Plugin loads quickly  
* Smooth animations  
* Helpful error messages  
* Good user experience

**Testing Checklist:**

* \[ \] Page load time \< 2 seconds  
* \[ \] No N+1 query issues  
* \[ \] Calculations cached appropriately  
* \[ \] All JS/CSS minified  
* \[ \] Loading states smooth  
* \[ \] Error messages clear and helpful  
* \[ \] Tooltips explain complex features

### **Phase 12: Documentation & Launch (Week 7\)**

**Goal:** Prepare for launch

**Tasks:**

1. Write user documentation  
2. Create video tutorials  
3. Write developer documentation  
4. Final testing across devices  
5. Deploy to production

**Deliverables:**

* Complete user guide  
* Setup instructions  
* API documentation  
* Video walkthroughs

**Testing Checklist:**

* \[ \] All documentation complete  
* \[ \] Videos recorded and uploaded  
* \[ \] Final testing passed  
* \[ \] No critical bugs  
* \[ \] Ready for users

---

## **9\. Code Examples**

### **9.1 Main Plugin Class Structure**

**File:** `includes/class-dedebify.php`

```php
<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @since      1.0.0
 * @package    Dedebify
 * @subpackage Dedebify/includes
 */

class Dedebify {

    /**
     * The loader that's responsible for maintaining and registering all hooks.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Dedebify_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->version = DEDEBIFY_VERSION;
        $this->plugin_name = 'dedebify';

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_cpt_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        // Load CPT registration class
        require_once DEDEBIFY_PLUGIN_DIR . 'includes/class-dedebify-cpt.php';
        
        // Load calculations class
        require_once DEDEBIFY_PLUGIN_DIR . 'includes/class-dedebify-calculations.php';
        
        // Load REST API class
        require_once DEDEBIFY_PLUGIN_DIR . 'includes/class-dedebify-api.php';
        
        // Load Elementor integration class (if Elementor is active)
        if ( did_action( 'elementor/loaded' ) ) {
            require_once DEDEBIFY_PLUGIN_DIR . 'includes/class-dedebify-elementor.php';
        }
    }

    /**
     * Register all of the hooks related to the admin area functionality.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        // Enqueue admin styles and scripts
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        
        // Add admin menu pages
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
    }

    /**
     * Register all of the hooks related to the public-facing functionality.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        // Enqueue public styles and scripts
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_assets' ) );
    }

    /**
     * Register all hooks related to Custom Post Types.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_cpt_hooks() {
        $cpt = new Dedebify_CPT();
        
        // Register CPTs
        add_action( 'init', array( $cpt, 'register_post_types' ) );
        
        // Add meta boxes
        add_action( 'add_meta_boxes', array( $cpt, 'add_meta_boxes' ) );
        
        // Save meta data
        add_action( 'save_post', array( $cpt, 'save_meta_data' ) );
    }

    /**
     * Enqueue admin CSS and JavaScript.
     *
     * @since    1.0.0
     */
    public function enqueue_admin_assets() {
        wp_enqueue_style(
            $this->plugin_name . '-admin',
            DEDEBIFY_PLUGIN_URL . 'assets/css/dedebify-admin.css',
            array(),
            $this->version,
            'all'
        );

        wp_enqueue_script(
            $this->plugin_name . '-admin',
            DEDEBIFY_PLUGIN_URL . 'assets/js/dedebify-admin.js',
            array( 'jquery' ),
            $this->version,
            false
        );
    }

    /**
     * Enqueue public CSS and JavaScript.
     *
     * @since    1.0.0
     */
    public function enqueue_public_assets() {
        wp_enqueue_style(
            $this->plugin_name . '-public',
            DEDEBIFY_PLUGIN_URL . 'assets/css/dedebify-public.css',
            array(),
            $this->version,
            'all'
        );

        wp_enqueue_style(
            $this->plugin_name . '-print',
            DEDEBIFY_PLUGIN_URL . 'assets/css/dedebify-print.css',
            array(),
            $this->version,
            'print'
        );

        wp_enqueue_script(
            $this->plugin_name . '-public',
            DEDEBIFY_PLUGIN_URL . 'assets/js/dedebify-public.js',
            array( 'jquery' ),
            $this->version,
            false
        );

        wp_enqueue_script(
            $this->plugin_name . '-calculator',
            DEDEBIFY_PLUGIN_URL . 'assets/js/dedebify-calculator.js',
            array(),
            $this->version,
            false
        );

        // Localize script with data
        wp_localize_script(
            $this->plugin_name . '-public',
            'dedebifyData',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'restUrl' => rest_url( 'dedebify/v1/' ),
                'nonce' => wp_create_nonce( 'wp_rest' ),
                'userId' => get_current_user_id(),
            )
        );
    }

    /**
     * Add admin menu pages.
     *
     * @since    1.0.0
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'DeDebtify', 'dedebify' ),
            __( 'DeDebtify', 'dedebify' ),
            'manage_options',
            'dedebify',
            array( $this, 'display_admin_page' ),
            'dashicons-chart-line',
            30
        );

        add_submenu_page(
            'dedebify',
            __( 'Settings', 'dedebify' ),
            __( 'Settings', 'dedebify' ),
            'manage_options',
            'dedebify-settings',
            array( $this, 'display_settings_page' )
        );
    }

    /**
     * Display admin page.
     *
     * @since    1.0.0
     */
    public function display_admin_page() {
        include DEDEBIFY_PLUGIN_DIR . 'admin/dashboard.php';
    }

    /**
     * Display settings page.
     *
     * @since    1.0.0
     */
    public function display_settings_page() {
        include DEDEBIFY_PLUGIN_DIR . 'admin/settings-page.php';
    }

    /**
     * Run the plugin.
     *
     * @since    1.0.0
     */
    public function run() {
        // Plugin is loaded and hooks are registered
    }
}
```

### **9.2 CPT Registration Class**

**File:** `includes/class-dedebify-cpt.php`

```php
<?php
/**
 * Handle Custom Post Type registration and meta boxes.
 *
 * @since      1.0.0
 * @package    Dedebify
 * @subpackage Dedebify/includes
 */

class Dedebify_CPT {

    /**
     * Register all custom post types.
     *
     * @since    1.0.0
     */
    public function register_post_types() {
        // Only register if JetEngine is not handling it
        if ( function_exists( 'jet_engine' ) ) {
            // JetEngine will handle CPT registration via UI
            return;
        }

        // Register Credit Card CPT
        $this->register_credit_card_cpt();
        
        // Register Loan CPT
        $this->register_loan_cpt();
        
        // Register Mortgage CPT
        $this->register_mortgage_cpt();
        
        // Register Bill CPT
        $this->register_bill_cpt();
        
        // Register Goal CPT
        $this->register_goal_cpt();
        
        // Register Snapshot CPT
        $this->register_snapshot_cpt();
    }

    /**
     * Register Credit Card CPT.
     *
     * @since    1.0.0
     */
    private function register_credit_card_cpt() {
        $labels = array(
            'name'                  => _x( 'Credit Cards', 'Post Type General Name', 'dedebify' ),
            'singular_name'         => _x( 'Credit Card', 'Post Type Singular Name', 'dedebify' ),
            'menu_name'             => __( 'Credit Cards', 'dedebify' ),
            'name_admin_bar'        => __( 'Credit Card', 'dedebify' ),
            'archives'              => __( 'Credit Card Archives', 'dedebify' ),
            'attributes'            => __( 'Credit Card Attributes', 'dedebify' ),
            'parent_item_colon'     => __( 'Parent Credit Card:', 'dedebify' ),
            'all_items'             => __( 'All Credit Cards', 'dedebify' ),
            'add_new_item'          => __( 'Add New Credit Card', 'dedebify' ),
            'add_new'               => __( 'Add New', 'dedebify' ),
            'new_item'              => __( 'New Credit Card', 'dedebify' ),
            'edit_item'             => __( 'Edit Credit Card', 'dedebify' ),
            'update_item'           => __( 'Update Credit Card', 'dedebify' ),
            'view_item'             => __( 'View Credit Card', 'dedebify' ),
            'view_items'            => __( 'View Credit Cards', 'dedebify' ),
            'search_items'          => __( 'Search Credit Card', 'dedebify' ),
            'not_found'             => __( 'Not found', 'dedebify' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'dedebify' ),
        );

        $args = array(
            'label'                 => __( 'Credit Card', 'dedebify' ),
            'description'           => __( 'User credit card information', 'dedebify' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'author' ),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => 'dedebify',
            'show_in_admin_bar'     => false,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
            'rest_base'             => 'credit-cards',
        );

        register_post_type( 'dd_credit_card', $args );
    }

    /**
     * Register Loan CPT.
     *
     * @since    1.0.0
     */
    private function register_loan_cpt() {
        $labels = array(
            'name'                  => _x( 'Loans', 'Post Type General Name', 'dedebify' ),
            'singular_name'         => _x( 'Loan', 'Post Type Singular Name', 'dedebify' ),
            'menu_name'             => __( 'Loans', 'dedebify' ),
            // ... (similar structure to credit card)
        );

        $args = array(
            'label'                 => __( 'Loan', 'dedebify' ),
            'description'           => __( 'User loan information', 'dedebify' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'author' ),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => 'dedebify',
            'show_in_admin_bar'     => false,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
            'rest_base'             => 'loans',
        );

        register_post_type( 'dd_loan', $args );
    }

    /**
     * Register Mortgage CPT.
     *
     * @since    1.0.0
     */
    private function register_mortgage_cpt() {
        // Similar structure...
        $args = array(
            'label'                 => __( 'Mortgage', 'dedebify' ),
            // ... (similar to above)
            'rest_base'             => 'mortgages',
        );

        register_post_type( 'dd_mortgage', $args );
    }

    /**
     * Register Bill CPT.
     *
     * @since    1.0.0
     */
    private function register_bill_cpt() {
        // Similar structure...
        $args = array(
            'label'                 => __( 'Bill', 'dedebify' ),
            // ...
            'rest_base'             => 'bills',
        );

        register_post_type( 'dd_bill', $args );
    }

    /**
     * Register Goal CPT.
     *
     * @since    1.0.0
     */
    private function register_goal_cpt() {
        // Similar structure...
        $args = array(
            'label'                 => __( 'Goal', 'dedebify' ),
            // ...
            'rest_base'             => 'goals',
        );

        register_post_type( 'dd_goal', $args );
    }

    /**
     * Register Snapshot CPT.
     *
     * @since    1.0.0
     */
    private function register_snapshot_cpt() {
        // Similar structure...
        $args = array(
            'label'                 => __( 'Financial Snapshot', 'dedebify' ),
            // ...
            'rest_base'             => 'snapshots',
        );

        register_post_type( 'dd_snapshot', $args );
    }

    /**
     * Add meta boxes for all CPTs.
     *
     * @since    1.0.0
     */
    public function add_meta_boxes() {
        // Credit Card Meta Box
        add_meta_box(
            'dd_credit_card_meta',
            __( 'Credit Card Details', 'dedebify' ),
            array( $this, 'render_credit_card_meta_box' ),
            'dd_credit_card',
            'normal',
            'high'
        );

        // Loan Meta Box
        add_meta_box(
            'dd_loan_meta',
            __( 'Loan Details', 'dedebify' ),
            array( $this, 'render_loan_meta_box' ),
            'dd_loan',
            'normal',
            'high'
        );

        // Add more meta boxes for other CPTs...
    }

    /**
     * Render Credit Card meta box.
     *
     * @since    1.0.0
     * @param    WP_Post    $post    The post object.
     */
    public function render_credit_card_meta_box( $post ) {
        // Add nonce for security
        wp_nonce_field( 'dd_credit_card_meta_box', 'dd_credit_card_meta_box_nonce' );

        // Get existing values
        $balance = get_post_meta( $post->ID, 'balance', true );
        $credit_limit = get_post_meta( $post->ID, 'credit_limit', true );
        $interest_rate = get_post_meta( $post->ID, 'interest_rate', true );
        $minimum_payment = get_post_meta( $post->ID, 'minimum_payment', true );
        $extra_payment = get_post_meta( $post->ID, 'extra_payment', true );
        $due_date = get_post_meta( $post->ID, 'due_date', true );
        $auto_pay = get_post_meta( $post->ID, 'auto_pay', true );
        $status = get_post_meta( $post->ID, 'status', true );

        // Output form fields
        ?>
        <table class="form-table">
            <tr>
                <th><label for="dd_balance"><?php _e( 'Current Balance ($)', 'dedebify' ); ?></label></th>
                <td>
                    <input type="number" step="0.01" id="dd_balance" name="dd_balance" value="<?php echo esc_attr( $balance ); ?>" class="regular-text" required>
                </td>
            </tr>
            <tr>
                <th><label for="dd_credit_limit"><?php _e( 'Credit Limit ($)', 'dedebify' ); ?></label></th>
                <td>
                    <input type="number" step="0.01" id="dd_credit_limit" name="dd_credit_limit" value="<?php echo esc_attr( $credit_limit ); ?>" class="regular-text" required>
                </td>
            </tr>
            <tr>
                <th><label for="dd_interest_rate"><?php _e( 'Interest Rate (%)', 'dedebify' ); ?></label></th>
                <td>
                    <input type="number" step="0.01" id="dd_interest_rate" name="dd_interest_rate" value="<?php echo esc_attr( $interest_rate ); ?>" class="regular-text" required>
                </td>
            </tr>
            <tr>
                <th><label for="dd_minimum_payment"><?php _e( 'Minimum Payment ($)', 'dedebify' ); ?></label></th>
                <td>
                    <input type="number" step="0.01" id="dd_minimum_payment" name="dd_minimum_payment" value="<?php echo esc_attr( $minimum_payment ); ?>" class="regular-text" required>
                </td>
            </tr>
            <tr>
                <th><label for="dd_extra_payment"><?php _e( 'Extra Payment ($)', 'dedebify' ); ?></label></th>
                <td>
                    <input type="number" step="0.01" id="dd_extra_payment" name="dd_extra_payment" value="<?php echo esc_attr( $extra_payment ); ?>" class="regular-text">
                    <p class="description"><?php _e( 'Optional additional payment beyond the minimum.', 'dedebify' ); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="dd_due_date"><?php _e( 'Due Date (Day of Month)', 'dedebify' ); ?></label></th>
                <td>
                    <input type="number" min="1" max="31" id="dd_due_date" name="dd_due_date" value="<?php echo esc_attr( $due_date ); ?>" class="small-text">
                </td>
            </tr>
            <tr>
                <th><label for="dd_auto_pay"><?php _e( 'Auto-Pay Enabled', 'dedebify' ); ?></label></th>
                <td>
                    <input type="checkbox" id="dd_auto_pay" name="dd_auto_pay" value="1" <?php checked( $auto_pay, 1 ); ?>>
                </td>
            </tr>
            <tr>
                <th><label for="dd_status"><?php _e( 'Status', 'dedebify' ); ?></label></th>
                <td>
                    <select id="dd_status" name="dd_status">
                        <option value="active" <?php selected( $status, 'active' ); ?>><?php _e( 'Active', 'dedebify' ); ?></option>
                        <option value="paid_off" <?php selected( $status, 'paid_off' ); ?>><?php _e( 'Paid Off', 'dedebify' ); ?></option>
                        <option value="closed" <?php selected( $status, 'closed' ); ?>><?php _e( 'Closed', 'dedebify' ); ?></option>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Save credit card meta data.
     *
     * @since    1.0.0
     * @param    int    $post_id    The post ID.
     */
    public function save_meta_data( $post_id ) {
        // Check if this is an autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check post type
        $post_type = get_post_type( $post_id );
        
        if ( $post_type === 'dd_credit_card' ) {
            $this->save_credit_card_meta( $post_id );
        } elseif ( $post_type === 'dd_loan' ) {
            $this->save_loan_meta( $post_id );
        }
        // ... handle other post types
    }

    /**
     * Save credit card specific meta.
     *
     * @since    1.0.0
     * @param    int    $post_id    The post ID.
     */
    private function save_credit_card_meta( $post_id ) {
        // Verify nonce
        if ( ! isset( $_POST['dd_credit_card_meta_box_nonce'] ) || 
             ! wp_verify_nonce( $_POST['dd_credit_card_meta_box_nonce'], 'dd_credit_card_meta_box' ) ) {
            return;
        }

        // Check user permissions
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Save meta fields
        $fields = array(
            'balance',
            'credit_limit',
            'interest_rate',
            'minimum_payment',
            'extra_payment',
            'due_date',
            'status',
        );

        foreach ( $fields as $field ) {
            if ( isset( $_POST['dd_' . $field] ) ) {
                $value = sanitize_text_field( $_POST['dd_' . $field] );
                update_post_meta( $post_id, $field, $value );
            }
        }

        // Handle checkbox
        $auto_pay = isset( $_POST['dd_auto_pay'] ) ? 1 : 0;
        update_post_meta( $post_id, 'auto_pay', $auto_pay );
    }

    // Add similar methods for other CPTs...
}
```

### **9.3 Calculations Class**

**File:** `includes/class-dedebify-calculations.php`

```php
<?php
/**
 * Handle all financial calculations.
 *
 * @since      1.0.0
 * @package    Dedebify
 * @subpackage Dedebify/includes
 */

class Dedebify_Calculations {

    /**
     * Calculate months to pay off a credit card.
     *
     * @since    1.0.0
     * @param    float    $balance           Current balance
     * @param    float    $interest_rate     Annual interest rate (as percentage, e.g., 18.99)
     * @param    float    $monthly_payment   Monthly payment amount
     * @return   int|string                  Number of months, or 'Never' if payment doesn't cover interest
     */
    public static function calculate_months_to_payoff( $balance, $interest_rate, $monthly_payment ) {
        // Validate inputs
        if ( $balance <= 0 ) {
            return 0;
        }

        if ( $monthly_payment <= 0 ) {
            return 'Never';
        }

        // Convert annual interest rate to monthly decimal
        $monthly_rate = ( $interest_rate / 100 ) / 12;

        // Check if payment covers interest
        $monthly_interest = $balance * $monthly_rate;
        if ( $monthly_payment <= $monthly_interest ) {
            return 'Never';
        }

        // Calculate months using logarithmic formula
        // n = -log(1 - (B * r / P)) / log(1 + r)
        // Where: B = balance, r = monthly rate, P = monthly payment
        $numerator = log( 1 - ( $balance * $monthly_rate / $monthly_payment ) );
        $denominator = log( 1 + $monthly_rate );

        $months = -$numerator / $denominator;

        return ceil( $months );
    }

    /**
     * Calculate total interest paid over the payoff period.
     *
     * @since    1.0.0
     * @param    float    $balance           Current balance
     * @param    float    $monthly_payment   Monthly payment amount
     * @param    int      $months            Number of months to pay off
     * @return   float                       Total interest paid
     */
    public static function calculate_total_interest( $balance, $monthly_payment, $months ) {
        if ( $months === 'Never' || $months <= 0 ) {
            return 0;
        }

        $total_paid = $monthly_payment * $months;
        $total_interest = $total_paid - $balance;

        return max( 0, round( $total_interest, 2 ) );
    }

    /**
     * Calculate credit utilization percentage.
     *
     * @since    1.0.0
     * @param    float    $balance        Current balance
     * @param    float    $credit_limit   Credit limit
     * @return   float                    Utilization percentage
     */
    public static function calculate_utilization( $balance, $credit_limit ) {
        if ( $credit_limit <= 0 ) {
            return 0;
        }

        $utilization = ( $balance / $credit_limit ) * 100;

        return round( $utilization, 2 );
    }

    /**
     * Calculate overall credit utilization across all cards.
     *
     * @since    1.0.0
     * @param    int    $user_id    User ID
     * @return   float              Overall utilization percentage
     */
    public static function calculate_overall_utilization( $user_id ) {
        $args = array(
            'post_type'      => 'dd_credit_card',
            'author'         => $user_id,
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'     => 'status',
                    'value'   => 'active',
                    'compare' => '=',
                ),
            ),
        );

        $cards = get_posts( $args );

        $total_balance = 0;
        $total_limit = 0;

        foreach ( $cards as $card ) {
            $balance = floatval( get_post_meta( $card->ID, 'balance', true ) );
            $limit = floatval( get_post_meta( $card->ID, 'credit_limit', true ) );

            $total_balance += $balance;
            $total_limit += $limit;
        }

        if ( $total_limit <= 0 ) {
            return 0;
        }

        $utilization = ( $total_balance / $total_limit ) * 100;

        return round( $utilization, 2 );
    }

    /**
     * Calculate debt-to-income ratio.
     *
     * @since    1.0.0
     * @param    int    $user_id    User ID
     * @return   float              DTI ratio percentage
     */
    public static function calculate_dti( $user_id ) {
        // Get monthly income from user meta
        $monthly_income = floatval( get_user_meta( $user_id, 'dd_monthly_income', true ) );

        if ( $monthly_income <= 0 ) {
            return 0;
        }

        // Calculate total monthly debt payments
        $total_payments = self::calculate_total_monthly_payments( $user_id );

        $dti = ( $total_payments / $monthly_income ) * 100;

        return round( $dti, 2 );
    }

    /**
     * Calculate total monthly debt payments for a user.
     *
     * @since    1.0.0
     * @param    int    $user_id    User ID
     * @return   float              Total monthly payments
     */
    public static function calculate_total_monthly_payments( $user_id ) {
        $total = 0;

        // Credit card payments
        $credit_cards = get_posts( array(
            'post_type'      => 'dd_credit_card',
            'author'         => $user_id,
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'     => 'status',
                    'value'   => 'active',
                    'compare' => '=',
                ),
            ),
        ) );

        foreach ( $credit_cards as $card ) {
            $min_payment = floatval( get_post_meta( $card->ID, 'minimum_payment', true ) );
            $extra_payment = floatval( get_post_meta( $card->ID, 'extra_payment', true ) );
            $total += $min_payment + $extra_payment;
        }

        // Loan payments
        $loans = get_posts( array(
            'post_type'      => 'dd_loan',
            'author'         => $user_id,
            'posts_per_page' => -1,
        ) );

        foreach ( $loans as $loan ) {
            $monthly_payment = floatval( get_post_meta( $loan->ID, 'monthly_payment', true ) );
            $extra_payment = floatval( get_post_meta( $loan->ID, 'extra_payment', true ) );
            $total += $monthly_payment + $extra_payment;
        }

        // Mortgage payments
        $mortgages = get_posts( array(
            'post_type'      => 'dd_mortgage',
            'author'         => $user_id,
            'posts_per_page' => -1,
        ) );

        foreach ( $mortgages as $mortgage ) {
            $monthly_payment = floatval( get_post_meta( $mortgage->ID, 'monthly_payment', true ) );
            $extra_payment = floatval( get_post_meta( $mortgage->ID, 'extra_payment', true ) );
            $property_tax = floatval( get_post_meta( $mortgage->ID, 'property_tax', true ) ) / 12;
            $insurance = floatval( get_post_meta( $mortgage->ID, 'homeowners_insurance', true ) ) / 12;
            $pmi = floatval( get_post_meta( $mortgage->ID, 'pmi', true ) );
            
            $total += $monthly_payment + $extra_payment + $property_tax + $insurance + $pmi;
        }

        return round( $total, 2 );
    }

    /**
     * Calculate total debt for a user.
     *
     * @since    1.0.0
     * @param    int    $user_id    User ID
     * @return   float              Total debt amount
     */
    public static function calculate_total_debt( $user_id ) {
        $total = 0;

        // Credit card balances
        $credit_cards = get_posts( array(
            'post_type'      => 'dd_credit_card',
            'author'         => $user_id,
            'posts_per_page' => -1,
        ) );

        foreach ( $credit_cards as $card ) {
            $balance = floatval( get_post_meta( $card->ID, 'balance', true ) );
            $total += $balance;
        }

        // Loan balances
        $loans = get_posts( array(
            'post_type'      => 'dd_loan',
            'author'         => $user_id,
            'posts_per_page' => -1,
        ) );

        foreach ( $loans as $loan ) {
            $balance = floatval( get_post_meta( $loan->ID, 'current_balance', true ) );
            $total += $balance;
        }

        // Mortgage balances
        $mortgages = get_posts( array(
            'post_type'      => 'dd_mortgage',
            'author'         => $user_id,
            'posts_per_page' => -1,
        ) );

        foreach ( $mortgages as $mortgage ) {
            $balance = floatval( get_post_meta( $mortgage->ID, 'current_balance', true ) );
            $total += $balance;
        }

        return round( $total, 2 );
    }

    /**
     * Calculate payoff date based on current date and months.
     *
     * @since    1.0.0
     * @param    int    $months    Number of months until payoff
     * @return   string            Formatted date (Y-m-d)
     */
    public static function calculate_payoff_date( $months ) {
        if ( $months === 'Never' || $months <= 0 ) {
            return 'N/A';
        }

        $current_date = new DateTime();
        $current_date->modify( "+{$months} months" );

        return $current_date->format( 'Y-m-d' );
    }

    /**
     * Calculate loan payment (amortization).
     *
     * @since    1.0.0
     * @param    float    $principal      Loan amount
     * @param    float    $annual_rate    Annual interest rate (as percentage)
     * @param    int      $term_months    Term in months
     * @return   float                    Monthly payment
     */
    public static function calculate_loan_payment( $principal, $annual_rate, $term_months ) {
        if ( $term_months <= 0 || $principal <= 0 ) {
            return 0;
        }

        // Convert annual rate to monthly decimal
        $monthly_rate = ( $annual_rate / 100 ) / 12;

        // If interest rate is 0, simple division
        if ( $monthly_rate == 0 ) {
            return $principal / $term_months;
        }

        // Amortization formula: P = L[c(1 + c)^n]/[(1 + c)^n - 1]
        // Where: P = payment, L = loan amount, c = monthly rate, n = term
        $payment = $principal * 
                   ( $monthly_rate * pow( 1 + $monthly_rate, $term_months ) ) / 
                   ( pow( 1 + $monthly_rate, $term_months ) - 1 );

        return round( $payment, 2 );
    }

    /**
     * Generate avalanche payoff order (highest interest first).
     *
     * @since    1.0.0
     * @param    int    $user_id    User ID
     * @return   array              Ordered array of debts
     */
    public static function generate_avalanche_order( $user_id ) {
        $debts = array();

        // Get all credit cards
        $credit_cards = get_posts( array(
            'post_type'      => 'dd_credit_card',
            'author'         => $user_id,
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'     => 'status',
                    'value'   => 'active',
                    'compare' => '=',
                ),
            ),
        ) );

        foreach ( $credit_cards as $card ) {
            $debts[] = array(
                'id'            => $card->ID,
                'name'          => $card->post_title,
                'type'          => 'credit_card',
                'balance'       => floatval( get_post_meta( $card->ID, 'balance', true ) ),
                'interest_rate' => floatval( get_post_meta( $card->ID, 'interest_rate', true ) ),
            );
        }

        // Get all loans
        $loans = get_posts( array(
            'post_type'      => 'dd_loan',
            'author'         => $user_id,
            'posts_per_page' => -1,
        ) );

        foreach ( $loans as $loan ) {
            $debts[] = array(
                'id'            => $loan->ID,
                'name'          => $loan->post_title,
                'type'          => 'loan',
                'balance'       => floatval( get_post_meta( $loan->ID, 'current_balance', true ) ),
                'interest_rate' => floatval( get_post_meta( $loan->ID, 'interest_rate', true ) ),
            );
        }

        // Sort by interest rate (highest first)
        usort( $debts, function( $a, $b ) {
            return $b['interest_rate'] <=> $a['interest_rate'];
        } );

        return $debts;
    }

    /**
     * Generate snowball payoff order (smallest balance first).
     *
     * @since    1.0.0
     * @param    int    $user_id    User ID
     * @return   array              Ordered array of debts
     */
    public static function generate_snowball_order( $user_id ) {
        $debts = array();

        // Get all credit cards
        $credit_cards = get_posts( array(
            'post_type'      => 'dd_credit_card',
            'author'         => $user_id,
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'     => 'status',
                    'value'   => 'active',
                    'compare' => '=',
                ),
            ),
        ) );

        foreach ( $credit_cards as $card ) {
            $debts[] = array(
                'id'            => $card->ID,
                'name'          => $card->post_title,
                'type'          => 'credit_card',
                'balance'       => floatval( get_post_meta( $card->ID, 'balance', true ) ),
                'interest_rate' => floatval( get_post_meta( $card->ID, 'interest_rate', true ) ),
            );
        }

        // Get all loans
        $loans = get_posts( array(
            'post_type'      => 'dd_loan',
            'author'         => $user_id,
            'posts_per_page' => -1,
        ) );

        foreach ( $loans as $loan ) {
            $debts[] = array(
                'id'            => $loan->ID,
                'name'          => $loan->post_title,
                'type'          => 'loan',
                'balance'       => floatval( get_post_meta( $loan->ID, 'current_balance', true ) ),
                'interest_rate' => floatval( get_post_meta( $loan->ID, 'interest_rate', true ) ),
            );
        }

        // Sort by balance (smallest first)
        usort( $debts, function( $a, $b ) {
            return $a['balance'] <=> $b['balance'];
        } );

        return $debts;
    }
}
```

### **9.4 REST API Endpoints**

**File:** `includes/class-dedebify-api.php`

```php
<?php
/**
 * REST API endpoints for DeDebtify.
 *
 * @since      1.0.0
 * @package    Dedebify
 * @subpackage Dedebify/includes
 */

class Dedebify_API {

    /**
     * Register REST API routes.
     *
     * @since    1.0.0
     */
    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    /**
     * Register all routes.
     *
     * @since    1.0.0
     */
    public function register_routes() {
        $namespace = 'dedebify/v1';

        // Dashboard summary
        register_rest_route( $namespace, '/dashboard', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'get_dashboard_data' ),
            'permission_callback' => array( $this, 'check_user_permission' ),
        ) );

        // Credit card payoff calculation
        register_rest_route( $namespace, '/calculate-payoff', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'calculate_payoff' ),
            'permission_callback' => array( $this, 'check_user_permission' ),
            'args'                => array(
                'balance'          => array(
                    'required'          => true,
                    'type'              => 'number',
                    'validate_callback' => function( $param ) {
                        return is_numeric( $param ) && $param >= 0;
                    },
                ),
                'interest_rate'    => array(
                    'required'          => true,
                    'type'              => 'number',
                    'validate_callback' => function( $param ) {
                        return is_numeric( $param ) && $param >= 0 && $param <= 100;
                    },
                ),
                'monthly_payment'  => array(
                    'required'          => true,
                    'type'              => 'number',
                    'validate_callback' => function( $param ) {
                        return is_numeric( $param ) && $param > 0;
                    },
                ),
            ),
        ) );

        // Create snapshot
        register_rest_route( $namespace, '/snapshot', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'create_snapshot' ),
            'permission_callback' => array( $this, 'check_user_permission' ),
        ) );

        // Get snapshot history
        register_rest_route( $namespace, '/snapshots', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'get_snapshots' ),
            'permission_callback' => array( $this, 'check_user_permission' ),
        ) );

        // Debt action plan
        register_rest_route( $namespace, '/action-plan', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'get_action_plan' ),
            'permission_callback' => array( $this, 'check_user_permission' ),
            'args'                => array(
                'method' => array(
                    'default'           => 'avalanche',
                    'validate_callback' => function( $param ) {
                        return in_array( $param, array( 'avalanche', 'snowball', 'custom' ) );
                    },
                ),
            ),
        ) );
    }

    /**
     * Check if user has permission to access API.
     *
     * @since    1.0.0
     * @return   bool    True if user is logged in
     */
    public function check_user_permission() {
        return is_user_logged_in();
    }

    /**
     * Get dashboard summary data for current user.
     *
     * @since    1.0.0
     * @param    WP_REST_Request    $request    Request object
     * @return   WP_REST_Response               Response object
     */
    public function get_dashboard_data( $request ) {
        $user_id = get_current_user_id();

        $data = array(
            'total_debt'            => Dedebify_Calculations::calculate_total_debt( $user_id ),
            'monthly_payments'      => Dedebify_Calculations::calculate_total_monthly_payments( $user_id ),
            'monthly_bills'         => $this->calculate_total_bills( $user_id ),
            'dti_ratio'             => Dedebify_Calculations::calculate_dti( $user_id ),
            'credit_utilization'    => Dedebify_Calculations::calculate_overall_utilization( $user_id ),
            'credit_card_count'     => $this->get_credit_card_count( $user_id ),
            'loan_count'            => $this->get_loan_count( $user_id ),
            'total_goals'           => $this->get_goals_count( $user_id ),
        );

        return new WP_REST_Response( $data, 200 );
    }

    /**
     * Calculate payoff scenarios for a credit card.
     *
     * @since    1.0.0
     * @param    WP_REST_Request    $request    Request object
     * @return   WP_REST_Response               Response object
     */
    public function calculate_payoff( $request ) {
        $balance = floatval( $request->get_param( 'balance' ) );
        $interest_rate = floatval( $request->get_param( 'interest_rate' ) );
        $monthly_payment = floatval( $request->get_param( 'monthly_payment' ) );

        $months = Dedebify_Calculations::calculate_months_to_payoff( $balance, $interest_rate, $monthly_payment );
        $total_interest = Dedebify_Calculations::calculate_total_interest( $balance, $monthly_payment, $months );
        $payoff_date = Dedebify_Calculations::calculate_payoff_date( $months );

        $data = array(
            'months_to_payoff' => $months,
            'total_interest'   => $total_interest,
            'payoff_date'      => $payoff_date,
            'total_paid'       => $balance + $total_interest,
        );

        return new WP_REST_Response( $data, 200 );
    }

    /**
     * Create a financial snapshot for current user.
     *
     * @since    1.0.0
     * @param    WP_REST_Request    $request    Request object
     * @return   WP_REST_Response               Response object
     */
    public function create_snapshot( $request ) {
        $user_id = get_current_user_id();

        // Calculate all metrics
        $total_debt = Dedebify_Calculations::calculate_total_debt( $user_id );
        $monthly_payments = Dedebify_Calculations::calculate_total_monthly_payments( $user_id );
        $monthly_bills = $this->calculate_total_bills( $user_id );
        $dti_ratio = Dedebify_Calculations::calculate_dti( $user_id );
        $credit_utilization = Dedebify_Calculations::calculate_overall_utilization( $user_id );
        $monthly_income = floatval( get_user_meta( $user_id, 'dd_monthly_income', true ) );

        // Create snapshot post
        $snapshot_id = wp_insert_post( array(
            'post_type'   => 'dd_snapshot',
            'post_title'  => 'Snapshot - ' . date( 'F Y' ),
            'post_status' => 'publish',
            'post_author' => $user_id,
        ) );

        if ( is_wp_error( $snapshot_id ) ) {
            return new WP_REST_Response( array(
                'success' => false,
                'message' => 'Failed to create snapshot',
            ), 500 );
        }

        // Save meta data
        update_post_meta( $snapshot_id, 'snapshot_date', date( 'Y-m-d' ) );
        update_post_meta( $snapshot_id, 'total_debt', $total_debt );
        update_post_meta( $snapshot_id, 'total_monthly_payments', $monthly_payments );
        update_post_meta( $snapshot_id, 'total_monthly_bills', $monthly_bills );
        update_post_meta( $snapshot_id, 'monthly_income', $monthly_income );
        update_post_meta( $snapshot_id, 'debt_to_income_ratio', $dti_ratio );
        update_post_meta( $snapshot_id, 'credit_utilization', $credit_utilization );

        return new WP_REST_Response( array(
            'success'     => true,
            'snapshot_id' => $snapshot_id,
            'message'     => 'Snapshot created successfully',
        ), 200 );
    }

    /**
     * Get snapshot history for current user.
     *
     * @since    1.0.0
     * @param    WP_REST_Request    $request    Request object
     * @return   WP_REST_Response               Response object
     */
    public function get_snapshots( $request ) {
        $user_id = get_current_user_id();

        $snapshots = get_posts( array(
            'post_type'      => 'dd_snapshot',
            'author'         => $user_id,
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );

        $data = array();

        foreach ( $snapshots as $snapshot ) {
            $data[] = array(
                'id'                   => $snapshot->ID,
                'date'                 => get_post_meta( $snapshot->ID, 'snapshot_date', true ),
                'total_debt'           => floatval( get_post_meta( $snapshot->ID, 'total_debt', true ) ),
                'monthly_payments'     => floatval( get_post_meta( $snapshot->ID, 'total_monthly_payments', true ) ),
                'dti_ratio'            => floatval( get_post_meta( $snapshot->ID, 'debt_to_income_ratio', true ) ),
                'credit_utilization'   => floatval( get_post_meta( $snapshot->ID, 'credit_utilization', true ) ),
            );
        }

        return new WP_REST_Response( $data, 200 );
    }

    /**
     * Get debt action plan for current user.
     *
     * @since    1.0.0
     * @param    WP_REST_Request    $request    Request object
     * @return   WP_REST_Response               Response object
     */
    public function get_action_plan( $request ) {
        $user_id = get_current_user_id();
        $method = $request->get_param( 'method' );

        // Get payoff order based on method
        if ( $method === 'avalanche' ) {
            $debts = Dedebify_Calculations::generate_avalanche_order( $user_id );
        } elseif ( $method === 'snowball' ) {
            $debts = Dedebify_Calculations::generate_snowball_order( $user_id );
        }

        // Calculate totals
        $total_debt = Dedebify_Calculations::calculate_total_debt( $user_id );
        $monthly_payments = Dedebify_Calculations::calculate_total_monthly_payments( $user_id );

        $data = array(
            'method'           => $method,
            'total_debt'       => $total_debt,
            'monthly_payments' => $monthly_payments,
            'payoff_order'     => $debts,
        );

        return new WP_REST_Response( $data, 200 );
    }

    /**
     * Helper function to calculate total monthly bills.
     *
     * @since    1.0.0
     * @param    int    $user_id    User ID
     * @return   float              Total monthly bills
     */
    private function calculate_total_bills( $user_id ) {
        $bills = get_posts( array(
            'post_type'      => 'dd_bill',
            'author'         => $user_id,
            'posts_per_page' => -1,
        ) );

        $total = 0;

        foreach ( $bills as $bill ) {
            $amount = floatval( get_post_meta( $bill->ID, 'amount', true ) );
            $frequency = get_post_meta( $bill->ID, 'frequency', true );

            // Convert to monthly equivalent
            switch ( $frequency ) {
                case 'weekly':
                    $monthly = $amount * 52 / 12;
                    break;
                case 'bi-weekly':
                    $monthly = $amount * 26 / 12;
                    break;
                case 'monthly':
                    $monthly = $amount;
                    break;
                case 'quarterly':
                    $monthly = $amount / 3;
                    break;
                case 'annually':
                    $monthly = $amount / 12;
                    break;
                default:
                    $monthly = $amount;
            }

            $total += $monthly;
        }

        return round( $total, 2 );
    }

    /**
     * Helper function to get credit card count.
     *
     * @since    1.0.0
     * @param    int    $user_id    User ID
     * @return   int                Number of active credit cards
     */
    private function get_credit_card_count( $user_id ) {
        $cards = get_posts( array(
            'post_type'      => 'dd_credit_card',
            'author'         => $user_id,
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'     => 'status',
                    'value'   => 'active',
                    'compare' => '=',
                ),
            ),
        ) );

        return count( $cards );
    }

    /**
     * Helper function to get loan count.
     *
     * @since    1.0.0
     * @param    int    $user_id    User ID
     * @return   int                Number of loans
     */
    private function get_loan_count( $user_id ) {
        $loans = get_posts( array(
            'post_type'      => 'dd_loan',
            'author'         => $user_id,
            'posts_per_page' => -1,
        ) );

        return count( $loans );
    }

    /**
     * Helper function to get goals count.
     *
     * @since    1.0.0
     * @param    int    $user_id    User ID
     * @return   int                Number of goals
     */
    private function get_goals_count( $user_id ) {
        $goals = get_posts( array(
            'post_type'      => 'dd_goal',
            'author'         => $user_id,
            'posts_per_page' => -1,
        ) );

        return count( $goals );
    }
}

// Initialize API
new Dedebify_API();
```

### **9.5 JavaScript Calculator (Frontend)**

**File:** `assets/js/dedebify-calculator.js`

```javascript
/**
 * DeDebtify Calculator
 * Frontend calculations for immediate feedback
 */

const DedebtifyCalculator = {
    /**
     * Calculate months to pay off debt
     */
    calculateMonthsToPayoff: function(balance, interestRate, monthlyPayment) {
        // Validate inputs
        if (balance <= 0) return 0;
        if (monthlyPayment <= 0) return Infinity;

        // Convert annual rate to monthly decimal
        const monthlyRate = (interestRate / 100) / 12;

        // Check if payment covers interest
        const monthlyInterest = balance * monthlyRate;
        if (monthlyPayment <= monthlyInterest) {
            return Infinity;
        }

        // Calculate using logarithmic formula
        const numerator = Math.log(1 - (balance * monthlyRate / monthlyPayment));
        const denominator = Math.log(1 + monthlyRate);

        const months = -numerator / denominator;

        return Math.ceil(months);
    },

    /**
     * Calculate total interest paid
     */
    calculateTotalInterest: function(balance, monthlyPayment, months) {
        if (months === Infinity || months <= 0) return 0;

        const totalPaid = monthlyPayment * months;
        const totalInterest = totalPaid - balance;

        return Math.max(0, totalInterest);
    },

    /**
     * Calculate credit utilization
     */
    calculateUtilization: function(balance, creditLimit) {
        if (creditLimit <= 0) return 0;

        const utilization = (balance / creditLimit) * 100;

        return Math.round(utilization * 100) / 100;
    },

    /**
     * Calculate payoff date
     */
    calculatePayoffDate: function(months) {
        if (months === Infinity || months <= 0) return 'N/A';

        const date = new Date();
        date.setMonth(date.getMonth() + months);

        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long' });
    },

    /**
     * Calculate loan payment (amortization)
     */
    calculateLoanPayment: function(principal, annualRate, termMonths) {
        if (termMonths <= 0 || principal <= 0) return 0;

        const monthlyRate = (annualRate / 100) / 12;

        // If interest rate is 0, simple division
        if (monthlyRate === 0) {
            return principal / termMonths;
        }

        // Amortization formula
        const payment = principal * 
                       (monthlyRate * Math.pow(1 + monthlyRate, termMonths)) / 
                       (Math.pow(1 + monthlyRate, termMonths) - 1);

        return Math.round(payment * 100) / 100;
    },

    /**
     * Format currency
     */
    formatCurrency: function(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    },

    /**
     * Format percentage
     */
    formatPercentage: function(percent) {
        return percent.toFixed(2) + '%';
    }
};

// Make available globally
window.DedebtifyCalculator = DedebtifyCalculator;
```

---

## **10\. Testing Requirements**

### **10.1 Unit Testing**

**Tools:** PHPUnit for PHP, Jest for JavaScript

**Tests to Write:**

1. **Calculation Tests** (Priority: High)

   * Test `calculate_months_to_payoff()` with various inputs  
   * Test edge cases (zero balance, zero payment, payment \< interest)  
   * Test `calculate_total_interest()` accuracy  
   * Test loan payment calculations against known values  
   * Test DTI and utilization calculations  
2. **CPT Tests** (Priority: Medium)

   * Test CPT registration  
   * Test meta data saving  
   * Test data retrieval  
   * Test user-specific queries  
3. **API Tests** (Priority: High)

   * Test all REST endpoints  
   * Test authentication  
   * Test input validation  
   * Test error responses

### **10.2 Integration Testing**

**Scenarios to Test:**

1. **User Journey: Adding a Credit Card**

   * Navigate to Credit Cards page  
   * Click "Add New Card"  
   * Fill in form  
   * Submit  
   * Verify card appears in list  
   * Verify calculations display correctly  
2. **User Journey: Creating Snapshot**

   * Navigate to Dashboard  
   * Click "Create Snapshot"  
   * Verify snapshot created  
   * View snapshot history  
   * Verify data captured correctly  
3. **User Journey: Viewing Action Plan**

   * Navigate to Action Plan  
   * Select payoff method  
   * Verify debts ordered correctly  
   * Verify calculations accurate  
   * Print action plan  
   * Verify print layout

### **10.3 Browser Testing**

**Browsers to Test:**

* Chrome (latest)  
* Firefox (latest)  
* Safari (latest)  
* Edge (latest)  
* Mobile Safari (iOS)  
* Chrome Mobile (Android)

**Test Items:**

* All forms submit correctly  
* Calculations display properly  
* Responsive layouts work  
* Print styles apply  
* JavaScript executes without errors

### **10.4 Performance Testing**

**Metrics to Monitor:**

* Page load time \< 2 seconds  
* Time to interactive \< 3 seconds  
* Database queries \< 50 per page  
* No N+1 query issues  
* JavaScript bundle size \< 100KB  
* CSS bundle size \< 50KB

---

## **11\. Deployment Checklist**

### **11.1 Pre-Launch Checklist**

* \[ \] All PHP code follows WordPress Coding Standards  
* \[ \] All functions properly documented with DocBlocks  
* \[ \] All strings wrapped in translation functions (`__()`, `_e()`)  
* \[ \] All user inputs sanitized and validated  
* \[ \] All database queries use $wpdb-\>prepare()  
* \[ \] All nonces verified for form submissions  
* \[ \] Plugin has proper activation/deactivation hooks  
* \[ \] Plugin has uninstall.php for cleanup  
* \[ \] All assets (CSS/JS) properly enqueued  
* \[ \] All calculations tested and accurate  
* \[ \] All REST API endpoints secured  
* \[ \] Error logging implemented  
* \[ \] User permissions checked throughout  
* \[ \] README.md complete with installation instructions  
* \[ \] Changelog maintained  
* \[ \] Version number updated in plugin header

### **11.2 Installation Instructions**

**For Junior Developer:**

1. **Upload Plugin**

   * Zip the `dedebify` folder  
   * In WordPress admin, go to Plugins \> Add New \> Upload Plugin  
   * Select zip file and click Install Now  
   * Click Activate Plugin  
2. **Install Dependencies**

   * Ensure JetEngine is installed and activated  
   * Ensure Elementor Pro is installed and activated  
3. **Configure JetEngine CPTs** (if not using plugin's built-in registration)

   * Go to JetEngine \> Post Types  
   * Import CPT configuration from `/jetengine-exports/` folder  
   * Or manually create CPTs following Database Schema section  
4. **Create Pages**

   * Create new pages for:  
     * Dashboard  
     * Credit Cards  
     * Loans  
     * Mortgage  
     * Bills  
     * Goals  
     * Action Plan  
   * Assign appropriate templates to each page  
5. **Configure Permissions**

   * Ensure users can only see their own data  
   * Set up membership levels if needed  
6. **Test Everything**

   * Create test user account  
   * Add sample data (credit card, loan, etc.)  
   * Verify all calculations work  
   * Test all forms  
   * Create snapshot  
   * Generate action plan

### **11.3 Post-Launch Monitoring**

**Monitor:**

* Error logs for PHP errors  
* Browser console for JavaScript errors  
* User feedback and bug reports  
* Database performance  
* Page load times  
* User engagement metrics

---

## **12\. Future Enhancements (Post-MVP)**

### **Phase 2 Features:**

1. **Advanced Analytics**

   * Predictive modeling  
   * What-if scenarios  
   * Comparison with national averages  
2. **Automated Features**

   * Auto-import from bank accounts (Plaid integration)  
   * Auto-create monthly snapshots  
   * Auto-pay tracking  
3. **Social Features** (via BuddyBoss)

   * Accountability partners  
   * Success stories sharing  
   * Group challenges  
   * Forums for support  
4. **Gamification**

   * Badges for milestones  
   * Leaderboards  
   * Streaks for consistent tracking  
   * Rewards for debt payoff  
5. **AI Features**

   * Personalized recommendations  
   * Budget optimization suggestions  
   * Spending pattern analysis  
6. **Mobile App**

   * Native iOS/Android apps  
   * Push notifications  
   * Receipt scanning  
   * Bill reminders

---

## **13\. Support Resources for Junior Developer**

### **13.1 Learning Resources**

**WordPress Development:**

* WordPress Codex: https://codex.wordpress.org/  
* WordPress Developer Handbook: https://developer.wordpress.org/  
* WP Beginner: https://www.wpbeginner.com/

**Elementor Development:**

* Elementor Developers Docs: https://developers.elementor.com/  
* Creating Custom Widgets: https://developers.elementor.com/creating-a-new-widget/

**JetEngine:**

* JetEngine Documentation: https://crocoblock.com/knowledge-base/jetengine/  
* Custom Post Types Tutorial: https://crocoblock.com/knowledge-base/articles/how-to-create-custom-post-types-with-jetengine/

**PHP & JavaScript:**

* PHP Manual: https://www.php.net/manual/en/  
* MDN JavaScript: https://developer.mozilla.org/en-US/docs/Web/JavaScript

### **13.2 Common Pitfalls to Avoid**

1. **Not sanitizing user input** \- Always use `sanitize_text_field()`, `sanitize_email()`, etc.  
2. **Not verifying nonces** \- Always verify nonces for form submissions  
3. **Not checking user capabilities** \- Use `current_user_can()` before allowing actions  
4. **Direct database queries** \- Use `$wpdb->prepare()` to prevent SQL injection  
5. **Not escaping output** \- Use `esc_html()`, `esc_attr()`, `esc_url()` when outputting  
6. **Hardcoding strings** \- Always use translation functions for internationalization  
7. **Not using WordPress hooks** \- Use actions and filters instead of modifying core  
8. **Poor error handling** \- Always check for errors and log them appropriately

### **13.3 Debugging Tips**

**Enable Debug Mode:** Add to wp-config.php:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

**View Error Logs:**

* Check `/wp-content/debug.log`  
* Use browser console for JavaScript errors

**Useful Debugging Functions:**

```php
// Print variable and die
var_dump( $variable );
die();

// Print to debug log
error_log( print_r( $variable, true ) );

// WordPress debug functions
_doing_it_wrong();
_deprecated_function();
```

---

## **14\. Glossary of Terms**

**CPT (Custom Post Type):** A WordPress feature that allows creation of custom content types beyond posts and pages.

**DTI (Debt-to-Income Ratio):** Percentage of monthly income that goes toward debt payments.

**REST API:** Representational State Transfer API \- allows communication between frontend and backend.

**Avalanche Method:** Debt payoff strategy focusing on highest interest rate debts first.

**Snowball Method:** Debt payoff strategy focusing on smallest balance debts first.

**Utilization:** Percentage of available credit being used (balance / credit limit).

**Amortization:** Process of paying off debt through regular payments over time.

**JetEngine:** Crocoblock plugin for creating CPTs and dynamic content.

**Elementor:** Visual page builder for WordPress.

**PWA (Progressive Web App):** Web application that behaves like a native mobile app.

**n8n:** Open-source workflow automation tool.

---

## **15\. Version Control & Collaboration**

### **15.1 Git Workflow**

**Branch Structure:**

* `main` \- Production-ready code  
* `develop` \- Integration branch for features  
* `feature/*` \- Individual feature branches  
* `hotfix/*` \- Quick fixes for production bugs

**Commit Message Format:**

```
[Type] Brief description

Detailed explanation of changes

- Bullet points for key changes
- Reference ticket/issue number if applicable
```

**Types:**

* `[FEAT]` \- New feature  
* `[FIX]` \- Bug fix  
* `[REFACTOR]` \- Code refactoring  
* `[DOCS]` \- Documentation changes  
* `[STYLE]` \- Code style changes (formatting)  
* `[TEST]` \- Adding or updating tests

### **15.2 Code Review Checklist**

Before submitting code for review:

* \[ \] Code follows WordPress Coding Standards  
* \[ \] All functions have DocBlocks  
* \[ \] No debugging code left (console.log, var\_dump, etc.)  
* \[ \] All user inputs sanitized  
* \[ \] All outputs escaped  
* \[ \] Tested on multiple browsers  
* \[ \] No PHP/JavaScript errors  
* \[ \] Performance impact minimal  
* \[ \] Documentation updated

---

## **16\. Contact & Support**

**Project Manager:** \[Your Name\] **Email:** \[Your Email\] **Slack/Discord:** \[Your Channel\]

**Questions?**

* Check this PRD first  
* Search WordPress Codex/Stack Overflow  
* Ask in project chat  
* Schedule 1-on-1 review if stuck

**Weekly Check-ins:**

* Monday: Week planning  
* Wednesday: Mid-week progress check  
* Friday: Week wrap-up and demo

---

## **Appendix A: Sample Data for Testing**

### **Credit Card Sample Data**

```
Card 1:
- Name: Chase Freedom
- Balance: $3,500
- Limit: $5,000
- APR: 18.99%
- Min Payment: $75

Card 2:
- Name: Discover It
- Balance: $1,200
- Limit: $3,000
- APR: 15.24%
- Min Payment: $35

Card 3:
- Name: Capital One Quicksilver
- Balance: $6,000
- Limit: $10,000
- APR: 22.99%
- Min Payment: $150
```

### **Loan Sample Data**

```
Loan 1:
- Name: Auto Loan - Honda Civic
- Type: Auto
- Principal: $25,000
- Current Balance: $18,500
- APR: 5.75%
- Term: 60 months
- Monthly Payment: $480

Loan 2:
- Name: Personal Loan
- Type: Personal
- Principal: $10,000
- Current Balance: $7,200
- APR: 9.99%
- Term: 48 months
- Monthly Payment: $252
```

---

**End of PRD**

This document should be updated as the project evolves. Version history should be maintained at the top of the document.
