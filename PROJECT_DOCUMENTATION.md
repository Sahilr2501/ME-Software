Absolutely. Below is a cleaner, professional **project documentation** that you can use as `PROJECT_DOCUMENTATION.md`. I have also removed the **batch terminology** from the `can-lid-production` documentation and kept the system simple, as per your current project direction.

# ME Software — Project Documentation

**Project Name:** ME Software
**Technology:** PHP, MySQL, PDO, HTML5, CSS3, Bootstrap
**Environment:** XAMPP / Apache / MySQL
**Project Type:** Manufacturing Production & Inventory Management System

---

# 1. Project Overview

**ME Software** is a PHP-based manufacturing management system developed for managing the production and inventory operations of milk cans and lids.

The system consists of two independent applications located inside the same project:

### Application 1 — Can & Lid Production

The `can-lid-production` application manages:

* Can production
* Lid production
* Production stages
* Production quantities
* Completed quantities
* Rejected quantities
* Pending quantities
* Daily reports
* Monthly reports
* Stage-wise reports
* Excel export
* Product management

### Application 2 — Milk Can Inventory

The `milk-can-inventory` application manages:

* Products
* Stock
* Purchases
* Sales
* Stock movements
* Inventory production
* Production reports

The main project landing page provides access to both applications.

---

# 2. Technology Stack

| Technology  | Purpose                   |
| ----------- | ------------------------- |
| PHP         | Backend development       |
| MySQL       | Database                  |
| PDO         | Database connectivity     |
| HTML5       | Page structure            |
| CSS3        | Styling                   |
| Bootstrap 5 | User interface            |
| JavaScript  | Client-side functionality |
| XAMPP       | Local development server  |
| Apache      | Web server                |

---

# 3. Project Architecture

The complete project is organized as:

```text
ME Software/
│
├── index.php
├── ME-logo.jpeg
├── PROJECT_DOCUMENTATION.md
│
├── can-lid-production/
│   │
│   ├── index.php
│   │
│   ├── config/
│   │   └── database.php
│   │
│   ├── dashboard/
│   │   └── index.php
│   │
│   ├── production/
│   │   ├── index.php
│   │   ├── create.php
│   │   ├── edit.php
│   │   ├── view.php
│   │   ├── delete.php
│   │   └── stages.php
│   │
│   ├── products/
│   │   ├── index.php
│   │   ├── create.php
│   │   └── edit.php
│   │
│   ├── reports/
│   │   ├── daily.php
│   │   ├── monthly.php
│   │   ├── stage.php
│   │   └── export_excel.php
│   │
│   ├── assets/
│   │   ├── css/
│   │   │   └── style.css
│   │   ├── js/
│   │   └── images/
│   │
│   └── vendor/
│
└── milk-can-inventory/
    │
    ├── index.php
    │
    ├── config/
    │   └── database.php
    │
    ├── includes/
    │   ├── header.php
    │   ├── sidebar.php
    │   └── footer.php
    │
    ├── assets/
    │   └── css/
    │       └── style.css
    │
    ├── stock/
    │   ├── index.php
    │   ├── stock_in.php
    │   └── stock_out.php
    │
    ├── products/
    │   ├── index.php
    │   ├── create.php
    │   ├── edit.php
    │   └── delete.php
    │
    ├── sales/
    │   ├── index.php
    │   ├── create.php
    │   ├── view.php
    │   └── delete.php
    │
    ├── purchases/
    │   ├── index.php
    │   ├── create.php
    │   ├── view.php
    │   └── delete.php
    │
    ├── production/
    │   ├── index.php
    │   ├── create.php
    │   └── delete.php
    │
    ├── reports/
    │   └── production.php
    │
    └── database.sql
```

---

# 4. Root Application

## 4.1 `index.php`

The root `index.php` is the main landing page of ME Software.

It provides navigation to:

```text
ME Software
│
├── Can & Lid Production
│
└── Milk Can Inventory
```

The landing page uses `ME-logo.jpeg` as the project logo.

---

## 4.2 `ME-logo.jpeg`

The main logo used on the ME Software landing page.

---

# 5. Application 1 — Can & Lid Production

## 5.1 Purpose

The Can & Lid Production application is designed to track the manufacturing process of:

* Milk Cans
* Can Lids

The system records production quantities at each manufacturing stage and provides reports for monitoring production performance.

---

# 6. Can Manufacturing Process

The Can production process contains the following stages:

```text
1. Count of Circles
2. Circle Press 1 & 2
3. Daba Rolling
4. Daba Cutting
5. Daba Naking
6. Welding of Handle & Bottom
7. Nani Bhati
8. Anodizing
9. Moti Bhati
10. Finished Can
```

The Can is considered finished after completion of the final stage:

```text
Moti Bhati
     ↓
Finished Can
```

---

# 7. Lid Manufacturing Process

The Lid production process contains the following stages:

```text
1. Count Circles
2. Circle Press of Dish / Circle Press of Daba
3. Daba Cutting / Dish Beeding
4. Daba Hole / Dish Hole
5. Dish & Daba Welding
6. Nani Bhati
7. Anodizing
8. Moti Bhati
9. Finished Lid
```

The Lid is considered finished after:

```text
Moti Bhati
     ↓
Finished Lid
```

---

# 8. Can & Lid Production Workflow

The general workflow is:

```text
Product Selection
       ↓
Production Entry
       ↓
Production Target
       ↓
Stage Processing
       ↓
Completed Quantity
       ↓
Rejected Quantity
       ↓
Pending Quantity
       ↓
Final Stage
       ↓
Finished Product
       ↓
Production Reports
       ↓
Excel Export
```

---

# 9. Configuration

## `can-lid-production/config/database.php`

This file creates the PDO connection with MySQL.

Default configuration:

```text
Host: localhost
Username: root
Password: empty
Database: can_lid_production
```

PDO is used for database operations and prepared statements.

---

# 10. Navigation

## `assets/navbar.php`

The reusable navigation bar provides access to:

* Dashboard
* Production
* Products
* Daily Report
* Monthly Report
* Stage Report
* Main ME Software page

Bootstrap is used for the navigation interface.

---

# 11. Dashboard Module

## `dashboard/index.php`

The dashboard provides a quick overview of production.

Possible dashboard information includes:

### Production

```text
Today's Production
Monthly Production
Total Target
Total Finished
Total Rejected
Total Pending
```

### Products

```text
Total Products
Active Products
```

### Reports

Quick access to:

* Daily Report
* Monthly Report
* Stage Report
* Excel Export

The dashboard is intended to provide a quick overview without requiring the user to open individual reports.

---

# 12. Production Module

## `production/index.php`

Displays production history.

Features:

* Production listing
* Date filtering
* Product filtering
* Product type filtering
* Status filtering
* View production
* Edit production
* Delete production
* Stage management

---

## `production/create.php`

Used to create a new production entry.

Typical information:

```text
Production Date
Product
Product Type
Target Quantity
Operator
Remarks
Status
```

After the production entry is created, the application loads the appropriate production stages according to the selected product type.

---

## `production/edit.php`

Allows modification of production information.

Editable information may include:

```text
Production Date
Target Quantity
Operator
Status
Remarks
```

---

## `production/view.php`

Displays complete information about a production entry.

The page can show:

```text
Product
Production Date
Target Quantity
Completed Quantity
Rejected Quantity
Pending Quantity
Operator
Status
Remarks
```

It also displays stage-level production information.

---

## `production/delete.php`

Deletes a production record.

The deletion should also remove related stage records to prevent orphaned production data.

---

# 13. Stage Management

## `production/stages.php`

This is one of the most important modules in the system.

It tracks production at individual stages.

For example:

```text
Can Production

Stage                       Completed    Rejected

Count of Circles               500          2
Circle Press 1 & 2             495          3
Daba Rolling                   490          2
Daba Cutting                  480          4
Daba Naking                   475          3
Welding                        470          5
Nani Bhati                     465          2
Anodizing                      460          3
Moti Bhati                     450          1
```

The system calculates:

```text
Pending = Available Quantity - Completed Quantity - Rejected Quantity
```

The system should prevent:

```text
Completed + Rejected > Available Quantity
```

---

# 14. Product Module

## `products/index.php`

Displays all products.

Information includes:

```text
ID
Product Name
Product Type
Capacity
Status
```

---

## `products/create.php`

Allows creation of a new product.

Example:

```text
Product Name: Milk Can
Product Type: Can
Capacity: 40 L
Status: Active
```

---

## `products/edit.php`

Allows existing product information to be updated.

---

# 15. Reports Module

The production system provides multiple reports.

---

## 15.1 Daily Report

### `reports/daily.php`

Shows production for a selected date.

Example:

```text
Daily Production Report
12-08-2026

Product      Target    Finished    Rejected    Pending

Can            500        450          10          50
Lid            500        400           8         100

Total         1000        850          18         150
```

The report should provide an Excel download option.

---

# 16. Monthly Report

## `reports/monthly.php`

The monthly report provides a summary for a selected month.

Users can select:

```text
August 2026
```

The report calculates:

```text
Total Target
Total Finished
Total Rejected
Total Pending
Completion Percentage
Rejection Percentage
```

Example:

```text
Can & Lid Production Report
August 2026

Product      Target    Finished    Rejected    Pending

Can           5000       4600          50         400
Lid           5000       4800          30         200

TOTAL        10000       9400          80         600
```

### Completion Formula

```text
Completion % =
(Finished / Target) × 100
```

### Rejection Formula

```text
Rejection % =
(Rejected / Finished) × 100
```

---

# 17. Stage Report

## `reports/stage.php`

Provides production information grouped by manufacturing stage.

Example:

```text
Stage Report

Stage                    Completed    Rejected

Count of Circles             500          2
Circle Press                 495          3
Daba Rolling                 490          5
Daba Cutting                 480          4
Daba Naking                  475          3
Welding                      470          6
Nani Bhati                   465          2
Anodizing                    460          3
Moti Bhati                   450          1
```

This report helps identify production bottlenecks.

---

# 18. Excel Export

## `reports/export_excel.php`

The system provides Excel-compatible export.

The exported file contains:

```text
Product
Target
Finished
Rejected
Pending
Completion %
```

The file name is automatically generated based on the selected month.

Example:

```text
Can_Lid_Production_Report_August_2026.xls
```

No external Excel library is required for the current implementation.

---

# 19. Application 2 — Milk Can Inventory

## Purpose

The Milk Can Inventory application manages inventory and commercial operations related to milk cans.

The system includes:

```text
Products
Purchases
Stock
Sales
Production
Stock Movements
Reports
```

---

# 20. Inventory Configuration

## `milk-can-inventory/config/database.php`

Database:

```text
milk_can_inventory
```

Default connection:

```text
Host: localhost
Username: root
Password: empty
```

PDO is used for database operations.

---

# 21. Inventory Layout

The inventory application uses reusable layout components:

### `includes/header.php`

Contains:

* HTML header
* Page title
* CSS loading
* Main page wrapper

### `includes/sidebar.php`

Contains navigation to:

```text
Dashboard
Stock
Products
Sales
Production
Purchases
Reports
```

### `includes/footer.php`

Contains the footer and developer information.

---

# 22. Stock Management

## `stock/index.php`

Displays current inventory.

Information can include:

```text
Product
Capacity
Current Stock
Minimum Stock
Stock Status
```

Stock statuses can be:

```text
In Stock
Low Stock
Out of Stock
```

---

## `stock/stock_in.php`

Adds stock to a product.

The system:

1. Receives quantity.
2. Updates product stock.
3. Creates a stock movement.
4. Records movement type as `IN`.

---

## `stock/stock_out.php`

Removes stock.

The system validates that:

```text
Stock Out Quantity <= Available Stock
```

A stock movement is recorded as:

```text
OUT
```

---

# 23. Product Management

## `products/index.php`

Displays inventory products.

---

## `products/create.php`

Creates a product with information such as:

```text
Product Name
Capacity
Material
Stock Quantity
Minimum Stock
Status
```

---

## `products/edit.php`

Updates product information.

---

## `products/delete.php`

Deletes an inventory product.

Before deletion, the application should ensure that the product is not required by existing transaction records.

---

# 24. Sales Module

## `sales/create.php`

Creates a new sale.

The system:

1. Selects customer.
2. Selects product.
3. Enters quantity.
4. Validates stock.
5. Creates sale record.
6. Updates stock.
7. Creates stock movement.

---

## `sales/index.php`

Displays sales history.

Filters can include:

```text
Customer
Product
From Date
To Date
```

---

## `sales/view.php`

Displays complete sale information.

---

## `sales/delete.php`

Deletes a sale and restores the associated stock.

A stock movement is created to maintain transaction history.

---

# 25. Purchase Module

## `purchases/create.php`

Allows multiple products to be added to a single purchase.

The system stores:

```text
Purchase Header
+
Purchase Items
```

---

## `purchases/index.php`

Displays purchase history.

---

## `purchases/view.php`

Displays purchase details and individual items.

---

## `purchases/delete.php`

Deletes a purchase record.

The implementation should ensure stock adjustments remain consistent with the deleted purchase.

---

# 26. Inventory Production Module

## `production/index.php`

Displays production records related to inventory.

---

## `production/create.php`

Creates an inventory production record.

Production can increase the available stock of finished milk cans.

---

## `production/delete.php`

Deletes an inventory production record.

Stock adjustments should be handled carefully when deleting production records.

---

# 27. Inventory Reports

## `reports/production.php`

Provides production information for the inventory application.

Possible report information:

```text
Production Date
Product
Quantity
```

The report can be expanded to include:

```text
Monthly Production
Daily Production
Stock Generated
Production Cost
```

---

# 28. Database Structure

## `milk_can_inventory`

The inventory application includes a `database.sql` file.

Main tables:

```text
products
purchases
purchase_items
production
stock_movements
```

### Products

Stores product information and current stock.

### Purchases

Stores purchase headers.

### Purchase Items

Stores individual products belonging to a purchase.

### Production

Stores production quantities.

### Stock Movements

Maintains the history of inventory changes.

---

# 29. Can & Lid Production Database

The `can_lid_production` application expects tables such as:

```text
products
production
production_stages
production_stage_records
```

The database should maintain relationships between:

```text
Product
   ↓
Production
   ↓
Production Stages
   ↓
Stage Records
```

A dedicated `database.sql` file should be maintained for this application so the complete project can be installed easily on another XAMPP system.

---

# 30. Production Data Flow

The production application follows:

```text
Product
   ↓
Production Entry
   ↓
Target Quantity
   ↓
Stage 1
   ↓
Stage 2
   ↓
Stage 3
   ↓
...
   ↓
Final Stage
   ↓
Finished Quantity
```

Rejected quantities are recorded at the relevant stage.

---

# 31. Quantity Calculation

For each stage:

```text
Available Quantity
        -
Completed Quantity
        -
Rejected Quantity
        =
Pending Quantity
```

Example:

```text
Available:    500
Completed:    450
Rejected:      10
----------------
Pending:       40
```

The system should never allow:

```text
Completed + Rejected > Available
```

---

# 32. User Interface

The project uses a simple interface designed for local factory use.

The interface should prioritize:

* Large buttons
* Simple forms
* Clear labels
* Responsive tables
* Easy navigation
* Minimal unnecessary screens
* Clear production status

Bootstrap is used in the Can & Lid Production application.

---

# 33. Security

The current project is designed as a simple local XAMPP application and does not require authentication.

Recommended security improvements for a production/server deployment include:

* Authentication
* Role-based access
* CSRF protection
* Input validation
* Session security
* Database credential protection
* HTTPS
* Access logging

For a local factory computer, the current simple approach can remain.

---

# 34. Backup

Because the project uses MySQL, database backup is important.

Recommended backup process:

```text
MySQL Database
      ↓
Export SQL
      ↓
Backup File
      ↓
External Drive / Cloud Storage
```

Recommended databases to back up:

```text
can_lid_production
milk_can_inventory
```

---

# 35. Installation

## Step 1 — Install XAMPP

Install:

```text
Apache
MySQL
PHP
phpMyAdmin
```

---

## Step 2 — Start Services

Open XAMPP Control Panel and start:

```text
Apache
MySQL
```

---

## Step 3 — Copy Project

Place the project inside:

```text
C:\xampp\htdocs\
```

The final location should be:

```text
C:\xampp\htdocs\ME Software\
```

---

# 36. Database Setup

Open:

```text
http://localhost/phpmyadmin
```

Create:

```text
can_lid_production
milk_can_inventory
```

Import the appropriate SQL files.

For the inventory application:

```text
milk-can-inventory/database.sql
```

For the production application, create/import the corresponding production database schema.

---

# 37. Database Configuration

For:

```text
can-lid-production/config/database.php
```

use:

```php
$host = 'localhost';
$dbname = 'can_lid_production';
$username = 'root';
$password = '';
```

For:

```text
milk-can-inventory/config/database.php
```

use:

```php
$host = 'localhost';
$dbname = 'milk_can_inventory';
$username = 'root';
$password = '';
```

Change the username/password if MySQL is configured differently.

---

# 38. Running the Project

Open:

```text
http://localhost/ME%20Software/
```

The main page displays the two applications.

### Can & Lid Production

```text
http://localhost/ME%20Software/can-lid-production/
```

### Milk Can Inventory

```text
http://localhost/ME%20Software/milk-can-inventory/
```

---

# 39. Recommended Future Features

The following features can be added later without making the initial system unnecessarily complicated.

### High Priority

* Production dashboard
* Stage progress indicators
* Rejection reason tracking
* Quality control
* Finished Can/Lid stock
* Low-stock alerts
* Daily Excel reports
* Monthly Excel reports
* Stage-wise Excel reports
* Database backup

### Medium Priority

* Machine management
* Machine maintenance
* Operator tracking
* Material consumption
* Material wastage
* Supplier management
* Customer management
* Dispatch management

### Advanced Features

* User authentication
* Role-based permissions
* Production cost calculation
* Raw material inventory
* Purchase management
* Sales/invoice generation
* Barcode/QR code
* Automatic alerts
* Cloud database
* Multi-computer access
* Audit logs

---

# 40. Recommended Final System

The complete ME Software system can eventually follow this structure:

```text
                    ME SOFTWARE
                         │
             ┌───────────┴───────────┐
             │                       │
             ▼                       ▼
     CAN & LID PRODUCTION     MILK CAN INVENTORY
             │                       │
             │                       │
     ┌───────┼────────┐       ┌──────┼─────────┐
     ▼       ▼        ▼       ▼      ▼         ▼
 Production Products Reports  Stock Purchases Sales
     │                       │
     ▼                       ▼
   Stages                 Production
     │
     ▼
 Quality Control
     │
     ▼
 Finished Product
     │
     ▼
 Inventory
     │
     ▼
 Dispatch
```

---

# 41. Project Objective

The primary objective of **ME Software** is to provide a simple and practical digital system for managing manufacturing operations.

The system reduces dependence on manual registers and provides centralized information for:

* Production
* Stage progress
* Product information
* Quality
* Rejection
* Stock
* Purchases
* Sales
* Reports
* Excel exports

The system is designed to be **simple enough for daily factory use while remaining expandable for future manufacturing requirements**.

---

# 42. Conclusion

ME Software provides a foundation for managing the complete workflow of a milk-can manufacturing business.

The **Can & Lid Production** application focuses on the manufacturing process from raw production stages through finished products, while the **Milk Can Inventory** application manages stock, purchases, sales, and inventory movements.

The modular PHP structure allows additional functionality to be added later without rebuilding the entire project.

The recommended next development stage is to connect **finished production → inventory**, add **quality control**, and improve the **dashboard and stage-wise reporting**. This would turn the current project into a more complete small-scale manufacturing management system.
