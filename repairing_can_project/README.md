# Repairing Can PHP Project

## Requirements
- XAMPP / Apache
- PHP 7.4+ (PHP 8.x recommended)
- MySQL

## Installation
1. Copy the `repairing_can` folder into `C:\xampp\htdocs\`.
2. Start Apache and MySQL from XAMPP.
3. Open phpMyAdmin.
4. Import `database.sql`.
5. Open:
   `http://localhost/repairing_can/`

## Forms

### Form 1 - Can Received
Fields:
- Date
- Challa Num
- With Ring
- Without Ring
- Without Handle
- Total Can (automatic)

Total Can = With Ring + Without Ring + Without Handle.

### Form 2 - Repairing / Processing
Fields:
- Date
- New Handle
- New Bottom Ring
- New Bottom Dish
- Repairing
- Buffing Can
- Cleaning Can
- Total Can (automatic)
- Total Reject

Important business rule:
Pending Repairing = Form 1 Total Can - Form 2 Repairing.

Only the `Repairing` value in Form 2 reduces the pending repairing stock. The other Form 2 categories are processing/output counts and do not reduce the repairing balance.

The system prevents a Form 2 `Repairing` entry from exceeding the current pending repairing stock.

## Database settings
If your MySQL root account has a password, edit:
`config/database.php`
