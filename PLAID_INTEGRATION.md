# DeDebtify Plaid Integration Guide

## Overview
Complete Plaid integration for automatic financial account syncing. Users can securely connect their bank accounts, credit cards, loans, and mortgages to automatically sync balances and debt information.

## Security Features

### ✅ Implemented Security Measures

1. **Token Encryption**
   - All Plaid access tokens encrypted using AES-256-CBC before storage
   - WordPress salts used as encryption keys
   - Tokens decrypted only when needed for API calls

2. **Input Validation & Sanitization**
   - All user inputs sanitized with `sanitize_text_field()`
   - Token format validation with regex patterns
   - Item ID ownership verification before disconnect

3. **Output Escaping**
   - All outputs escaped with `esc_html()`, `esc_attr()`, `esc_url()`
   - Prevents XSS attacks

4. **Permission Checks**
   - All REST endpoints require user authentication
   - Users can only access their own accounts
   - Admin-only access to settings

5. **WordPress Nonces**
   - REST API nonce verification on all endpoints
   - Protects against CSRF attacks

## File Structure

```
dedebtify/
├── dedebtify.php                              # Main plugin file
├── includes/
│   ├── class-dedebtify-plaid.php             # Plaid API integration
│   ├── class-dedebtify-rest-api.php          # REST API endpoints
│   ├── class-dedebtify-helpers.php           # Helper functions
│   └── plaid-loader.php                      # Plaid initialization
├── templates/
│   └── account-sync.php                       # Account linking UI
├── assets/
│   └── js/
│       └── dedebtify-plaid.js                # Plaid Link frontend
└── admin/
    └── settings-page.php                      # Admin settings
```

## Installation & Setup

### 1. Get Plaid Credentials

1. Sign up at https://dashboard.plaid.com
2. Create a new application
3. Get your Client ID and Secret
4. Choose environment (Sandbox for testing, Production for live)

### 2. Configure Plugin

1. Go to WordPress Admin → DeDebtify → Settings
2. Scroll to "Plaid Financial Data Integration"
3. Enable Plaid Integration
4. Enter Client ID and Secret
5. Select Environment
6. Configure auto-sync settings
7. Click "Save Settings"

### 3. Create Account Sync Page

1. Create a new WordPress page
2. Add the shortcode: `[dedebtify_account_sync]`
3. Publish the page
4. Add page to menu (optional)

## Usage

### User Flow

1. **Link Account**
   - User visits Account Sync page
   - Clicks "Link New Account"
   - Plaid Link modal opens
   - User selects their financial institution
   - Authenticates with their credentials
   - Account is linked and data syncs automatically

2. **Manual Sync**
   - User clicks "Sync Now" on any linked account
   - Data is refreshed from Plaid
   - DeDebtify CPTs are updated

3. **Disconnect**
   - User clicks "Disconnect"
   - Plaid connection is removed
   - Historical data remains in DeDebtify

### Auto-Sync

Configured in admin settings:
- **Hourly**: Syncs every hour (high frequency)
- **Daily**: Syncs once per day (recommended)
- **Weekly**: Syncs once per week (low frequency)

## REST API Endpoints

All endpoints require authentication and use the namespace `dedebtify/v1/plaid/`

### Create Link Token
```http
POST /dedebtify/v1/plaid/create-link-token
```
Creates a Plaid Link token for the current user.

**Response:**
```json
{
  "link_token": "link-sandbox-abc123...",
  "expiration": "2024-01-01T12:00:00Z"
}
```

### Exchange Token
```http
POST /dedebtify/v1/plaid/exchange-token
Content-Type: application/json

{
  "public_token": "public-sandbox-xyz789..."
}
```
Exchanges public token for access token and triggers initial sync.

**Response:**
```json
{
  "success": true,
  "message": "Account linked successfully and data is syncing"
}
```

### Get Linked Accounts
```http
GET /dedebtify/v1/plaid/linked-accounts
```
Returns all linked accounts for the current user.

**Response:**
```json
[
  {
    "item_id": "item_abc123",
    "connected_at": "2024-01-01 12:00:00",
    "last_sync": "2024-01-02 13:30:00"
  }
]
```

### Sync Accounts
```http
POST /dedebtify/v1/plaid/sync
```
Manually triggers sync for all linked accounts.

**Response:**
```json
{
  "success": true,
  "message": "Synced 2 accounts. 0 errors occurred.",
  "results": {
    "success": 2,
    "errors": 0,
    "synced_items": ["item_abc123", "item_xyz789"]
  }
}
```

### Disconnect Account
```http
POST /dedebtify/v1/plaid/disconnect
Content-Type: application/json

{
  "item_id": "item_abc123"
}
```
Disconnects a linked account.

**Response:**
```json
{
  "success": true,
  "message": "Account disconnected successfully"
}
```

## Data Mapping

### Credit Cards
Plaid → DeDebtify CPT (`dd_credit_card`)
- `balances.current` → `balance`
- `balances.limit` → `credit_limit`
- `aprs[0].apr_percentage` → `interest_rate`
- Auto-calculated: `minimum_payment` (2% of balance or $25)

### Loans
Plaid → DeDebtify CPT (`dd_loan`)
- `balances.current` → `current_balance`
- `origination_principal_amount` → `principal`
- `interest_rate_percentage` → `interest_rate`
- `minimum_payment_amount` → `monthly_payment`

### Mortgages
Plaid → DeDebtify CPT (`dd_mortgage`)
- `balances.current` → `current_balance`
- `origination_principal_amount` → `principal`
- `interest_rate.percentage` → `interest_rate`
- `property_value` → `property_value`

## Troubleshooting

### Access Token Errors
If you see "invalid access token" errors:
1. Go to Account Sync page
2. Disconnect the affected account
3. Re-link the account
4. This will refresh the access token

### Sync Failures
Check WordPress debug log for errors:
1. Enable WP_DEBUG in wp-config.php
2. Check `/wp-content/debug.log`
3. Look for Plaid API errors

### Missing Data
If accounts aren't syncing:
1. Verify Plaid credentials are correct
2. Check environment setting (Sandbox vs Production)
3. Ensure account supports liabilities endpoint
4. Manual sync and check for errors

## Cron Jobs

Plugin creates these scheduled tasks:
- `dedebtify_plaid_auto_sync`: Main sync task (frequency configurable)
- `dedebtify_plaid_initial_sync`: One-time sync after linking

To manually trigger sync via WP-CLI:
```bash
wp cron event run dedebtify_plaid_auto_sync
```

## Privacy & Compliance

### User Data
- Access tokens stored encrypted in `wp_usermeta`
- Meta key: `dd_plaid_accounts`
- Only accessible by account owner

### GDPR Compliance
Users can:
- View all linked accounts
- Export their data (standard WordPress export)
- Delete their data (disconnect accounts)
- Right to be forgotten (delete user = delete all tokens)

### Data Retention
- Synced financial data remains in DeDebtify CPTs
- Disconnecting removes Plaid link but keeps data
- Deleting user removes all data via WordPress hooks

## Support

For issues:
1. Check WordPress debug log
2. Verify Plaid dashboard for API errors
3. Test in Sandbox environment first
4. Review this documentation

## Changelog

### Version 1.0.0
- Initial Plaid integration
- Support for credit cards, loans, mortgages
- Automatic and manual syncing
- Encrypted token storage
- Complete REST API
- Shadcn-styled UI
