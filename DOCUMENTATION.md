# MySchoolGH School Management System - Complete Documentation

## Table of Contents
1. [Project Overview](#project-overview)
2. [System Architecture](#system-architecture)
3. [Directory Structure](#directory-structure)
4. [Controllers](#controllers)
5. [Models](#models)
6. [Views](#views)
7. [Database Schema](#database-schema)
8. [Authentication & Authorization](#authentication--authorization)
9. [Key Features](#key-features)
10. [API Structure](#api-structure)
11. [Configuration](#configuration)
12. [Dependencies](#dependencies)
13. [Installation & Setup](#installation--setup)
14. [Development Guidelines](#development-guidelines)

---

## Project Overview

**MySchoolGH** is a comprehensive PHP-based School Management System designed for educational institutions. It provides a complete solution for managing students, staff, academic activities, financial operations, and administrative tasks.

### Version
- Current Version: **1.7.4**
- PHP Requirement: **PHP 7.1+** (Recommended: PHP 8.1+)

### Core Capabilities
- Multi-tenant architecture supporting multiple schools
- Role-based access control (Admin, Teacher, Student, Parent, Accountant, Support)
- Academic management (Classes, Courses, Subjects, Timetables)
- Financial management (Fees, Payments, Accounting, Payroll)
- Student information system
- Library management
- Attendance tracking
- Communication tools (SMS, Email, Notifications)
- Reporting and analytics
- Mobile-responsive design

---

## System Architecture

### Framework Pattern
The application follows a **custom MVC (Model-View-Controller)** pattern, similar to CodeIgniter but with custom implementation.

### Entry Point
- **Main Entry**: `index.php` (root directory)
- **Routing**: Handled by `system/config/settings.php` → `run()` function
- **Bootstrap**: `system/core/myschoolgh.php`

### Request Flow
```
index.php → settings.php (routing) → Controller → Model → View → Response
```

### Key Components

1. **Core System** (`system/`)
   - Configuration files
   - Core classes and libraries
   - Database connection handling
   - Session management

2. **Application Layer** (`application/`)
   - Controllers: Business logic
   - Models: Data access layer
   - Views: Presentation layer
   - Config: Application-specific configuration

3. **Assets** (`assets/`)
   - JavaScript files
   - CSS stylesheets
   - Images and media
   - Upload directories

---

## Directory Structure

```
SchoolManager/
├── application/
│   ├── config/              # Application configuration
│   ├── controllers/         # 70+ controller files
│   │   ├── auth.php         # Authentication & authorization
│   │   ├── users.php        # User management
│   │   ├── classes.php      # Class management
│   │   ├── students.php    # Student management
│   │   ├── fees.php         # Fees & payments
│   │   ├── payroll.php      # Payroll management
│   │   ├── attendance.php   # Attendance tracking
│   │   ├── library.php      # Library management
│   │   ├── accounting.php   # Accounting operations
│   │   ├── hospital/        # Hospital/Medical module
│   │   └── housing/         # Housing/Residence module
│   ├── models/              # 21 model files
│   │   ├── myschoolgh.php  # Base model class
│   │   ├── models.php      # Core model functionality
│   │   ├── api.php         # API handling
│   │   └── crons.php       # Cron job handlers
│   ├── views/
│   │   └── default/         # 100+ view templates
│   │       ├── dashboard.php
│   │       ├── login.php
│   │       ├── students.php
│   │       └── [many more...]
│   ├── logs/               # Application logs
│   └── sessions/            # Session files
├── system/
│   ├── config/              # System configuration
│   │   ├── settings.php     # Main settings & routing
│   │   └── config.php       # System config
│   ├── core/                # Core system files
│   └── libraries/          # System libraries
├── assets/
│   ├── css/                 # Stylesheets
│   ├── js/                  # JavaScript files
│   ├── images/              # Images
│   └── uploads/             # User uploads
├── vendor/                  # Composer dependencies
├── index.php                # Main entry point
├── .htaccess                # Apache configuration
└── db.ini                   # Database configuration (not in repo)
```

---

## Controllers

All controllers extend the `Myschoolgh` base class, which extends `Models`. Controllers handle business logic and coordinate between models and views.

### Core Controllers

#### 1. **Auth Controller** (`auth.php`)
Handles authentication, authorization, and user sessions.

**Key Methods:**
- `login($params)` - User login with attempt tracking
- `logout($params)` - User logout
- `send_password_reset_token($params)` - Password reset via email
- `reset_user_password($params)` - Password reset execution
- `change_password($params)` - In-app password change
- `validate_token($params)` - API token validation
- `create($params)` - School account registration
- `temporary_access($params)` - Generate API access tokens

**Security Features:**
- Login attempt limiting (10 attempts per 10 minutes)
- Password strength validation
- Session management
- Access token generation for API

#### 2. **Users Controller** (`users.php`)
Manages all user-related operations (students, staff, parents, admins).

**Key Methods:**
- `list($params)` - List users with filtering
- `add($params)` - Create new user
- `update($params)` - Update user information
- `view($params)` - View user details
- `delete($params)` - Soft delete user
- `assign_class($params)` - Assign students to classes
- `guardian_list($params)` - Get guardian information

**User Types:**
- `admin` - School administrators
- `teacher` - Teaching staff
- `student` - Students
- `parent` - Parents/Guardians
- `accountant` - Financial staff
- `employee` - Non-teaching staff
- `support` - System support staff

#### 3. **Classes Controller** (`classes.php`)
Manages class/grade operations.

**Key Methods:**
- `list($params)` - List classes with student counts
- `add($params)` - Create new class
- `update($params)` - Update class information
- `view($params)` - View class details with students
- `assign($params)` - Assign students to class
- `append_class_rooms($params)` - Assign rooms to classes

**Features:**
- Class teacher assignment
- Class assistant assignment
- Room allocation
- Department association
- Student count tracking

#### 4. **Fees Controller** (`fees.php`)
Handles all fee-related operations.

**Key Methods:**
- `list($params)` - List fee collections
- `allocate($params)` - Allocate fees to students/classes
- `payment($params)` - Process fee payments
- `reverse($params)` - Reverse fee payments (within 24 hours)
- `arrears($params)` - Calculate outstanding fees
- `category_list($params)` - List fee categories

**Features:**
- Multiple fee categories
- Termly/Yearly fee allocation
- Payment receipts
- Debtor tracking
- Payment reversals (time-limited)

#### 5. **Payroll Controller** (`payroll.php`)
Manages staff payroll and payslip generation.

**Key Methods:**
- `paysliplist($params)` - List payslips
- `generate($params)` - Generate payslip
- `bulk_generate($params)` - Bulk payslip generation
- `category_list($params)` - List allowance/deduction types
- `calculate($params)` - Calculate payroll amounts

**Features:**
- Statutory deductions (SSNIT, PAYE, Tier 2)
- Allowances and bonuses
- Tax calculations
- Payslip PDF generation
- Bulk processing

#### 6. **Attendance Controller** (`attendance.php`)
Tracks student and staff attendance.

**Key Methods:**
- `mark($params)` - Mark attendance
- `list($params)` - List attendance records
- `history($params)` - Attendance history
- `report($params)` - Generate attendance reports

**Features:**
- Daily attendance marking
- Attendance history
- Absentee tracking
- Attendance statistics

#### 7. **Library Controller** (`library.php`)
Manages library operations.

**Key Methods:**
- `books_list($params)` - List books
- `add_book($params)` - Add new book
- `issue($params)` - Issue book to student
- `return($params)` - Return book
- `stock($params)` - Manage book stock

**Features:**
- Book catalog management
- Book categories
- Issue/Return tracking
- Stock management
- Overdue tracking

#### 8. **Accounting Controller** (`accounting.php`)
Handles accounting operations.

**Key Methods:**
- `transaction_list($params)` - List transactions
- `income($params)` - Record income
- `expense($params)` - Record expense
- `account_type($params)` - Manage account types
- `reports($params)` - Financial reports

**Features:**
- Double-entry bookkeeping
- Income/Expense tracking
- Account types
- Financial reports
- Bank reconciliation

### Specialized Module Controllers

#### Hospital Module (`hospital/`)
- `Health.php` - Health records
- `Vitals.php` - Vital signs tracking
- `Consultation.php` - Medical consultations
- `Visits.php` - Hospital visits
- `Inventory.php` - Medical inventory
- `Medical.php` - Medical records

#### Housing Module (`housing/`)
- `Buildings.php` - Building management
- `Blocks.php` - Block management
- `Rooms.php` - Room management
- `Beds.php` - Bed allocation

### Other Important Controllers

- **Courses** (`courses.php`) - Course/subject management
- **Departments** (`departments.php`) - Department management
- **Sections** (`sections.php`) - Section management
- **Timetable** (`timetable.php`) - Timetable creation and management
- **Assignments** (`assignments.php`) - Assignment management
- **Events** (`events.php`) - Event management
- **Communication** (`communication.php`) - SMS/Email communication
- **Notification** (`notification.php`) - In-app notifications
- **Settings** (`settings.php`) - System settings
- **Analytics** (`analitics.php`) - Analytics and reporting
- **Files** (`files.php`) - File management
- **Search** (`search.php`) - Global search functionality
- **QR** (`qr.php`) - QR code generation
- **Cards** (`cards.php`) - ID card generation
- **Support** (`support.php`) - Support ticket system

---

## Models

### Base Model Classes

#### 1. **Models** (`models.php`)
Base model class providing common functionality.

**Key Properties:**
- `$global_limit = 3000` - Default query limit
- `$temporal_maximum = 200` - Temporal query limit
- `$maximum_class_count = 5000` - Maximum class size
- `$allowed_delete_range = 3` - Hours allowed for deletion
- `$max_attachment_size = 25` - Max file size in MB

#### 2. **Myschoolgh** (`myschoolgh.php`)
Main base class extending Models. All controllers extend this class.

**Key Properties:**
- `$db` - PDO database connection
- `$session` - Session object
- `$clientId` - Current client/school ID
- `$userId` - Current user ID
- `$baseUrl` - Application base URL
- `$appName` - Application name

**Key Methods:**
- `pushQuery($columns, $table, $where)` - Execute SELECT query
- `stringToArray($string, $delimiter, $keys)` - Convert string to array
- `inList($array)` - Format array for SQL IN clause
- `dateRange($range, $table, $column)` - Generate date range query
- `userLogs($type, $item_id, $prev_data, $message, $user_id)` - Log user activity
- `client_session_data($client_id, $refresh)` - Get client data

### Specialized Models

- **API** (`api.php`) - API request handling
- **API_Validate** (`api_validate.php`) - API token validation
- **Crons** (`crons.php`) - Cron job execution
- **Crons_Account** (`crons_account.php`) - Account-related cron jobs
- **Crons_AcademicCalendar** (`crons_academiccalendar.php`) - Academic calendar cron
- **Crons_Backup** (`crons_backup.php`) - Database backup
- **Handler** (`handler.php`) - Request handler
- **Sickbay** (`sickbay.php`) - Sickbay operations

---

## Views

Views are located in `application/views/default/` and are PHP templates that render HTML output.

### View Structure

Views typically include:
1. **Head Tags** (`headtags.php`) - HTML head section
2. **Page Content** - Main page content
3. **Foot Tags** (`foottags.php`) - HTML footer and scripts

### Key Views

#### Authentication Views
- `login.php` - Login page
- `register.php` - Registration page
- `signup.php` - School signup
- `forgot-password.php` - Password recovery
- `verify.php` - Email verification

#### Dashboard Views
- `dashboard.php` - Main dashboard
- `main.php` - Dashboard wrapper

#### Student Management
- `students.php` - Student list
- `student.php` - Student details
- `student_add.php` - Add student form
- `modify-student.php` - Edit student form
- `assign_class.php` - Assign class form

#### Academic Views
- `classes.php` - Class list
- `class.php` - Class details
- `courses.php` - Course list
- `sections.php` - Section list
- `departments.php` - Department list
- `timetable.php` - Timetable view
- `attendance.php` - Attendance marking
- `gradebook.php` - Gradebook

#### Financial Views
- `fees-payment.php` - Fee payment
- `fees-history.php` - Payment history
- `arrears.php` - Outstanding fees
- `accounting.php` - Accounting dashboard
- `incomes.php` - Income records
- `expenses.php` - Expense records
- `payroll.php` - Payroll management
- `payslips.php` - Payslip list

#### Library Views
- `books.php` - Book catalog
- `books_issue.php` - Issue books
- `books_issued.php` - Issued books list
- `books_stock.php` - Stock management

#### Other Views
- `settings.php` - System settings
- `notifications.php` - Notifications
- `profile.php` - User profile
- `support.php` - Support tickets
- `knowledgebase.php` - Knowledge base

---

## Database Schema

### Core Tables

#### User Management
- `users` - All users (students, staff, parents, admins)
- `users_roles` - User role permissions
- `users_access_attempt` - Login attempt tracking
- `users_login_history` - Login history
- `users_reset_request` - Password reset requests
- `users_api_keys` - API access tokens
- `users_activity_logs` - User activity logs
- `users_notification_types` - Notification types

#### School/Client Management
- `clients_accounts` - School/client accounts
- `clients_accounts_limit` - Account limits
- `clients_packages` - Subscription packages

#### Academic Management
- `classes` - Classes/grades
- `courses` - Courses/subjects
- `sections` - Sections
- `departments` - Departments
- `academic_terms` - Academic terms
- `timetable` - Timetable entries
- `assignments` - Assignments
- `attendance` - Attendance records

#### Student Management
- `users` (with `user_type='student'`)
- `guardian_relation` - Guardian relationships
- `exeats` - Leave/exeat requests
- `promotions` - Student promotions

#### Financial Management
- `fees_category` - Fee categories
- `fees_collection` - Fee payments
- `fees_allocation` - Fee allocations
- `accounts` - Chart of accounts
- `accounts_type_head` - Account types
- `accounts_transaction` - Financial transactions
- `payslips` - Payslips
- `payslips_details` - Payslip line items
- `payslips_allowance_types` - Allowance/deduction types

#### Library Management
- `books` - Book catalog
- `books_categories` - Book categories
- `books_stock` - Book inventory
- `books_issue` - Book issues

#### Communication
- `users_messaging_list` - Email/SMS queue
- `users_feedback` - User feedback/comments
- `notifications` - In-app notifications

#### Other Tables
- `events` - Events/calendar
- `incidents` - Incident reports
- `library` - Library resources
- `resources` - General resources
- `cron_scheduler` - Scheduled tasks
- `knowledge_base` - Knowledge base articles

### Database Connection

Configuration is stored in `db.ini` (not in repository):
```ini
hostname=localhost
database=myschoolgh
username=root
password=
base_url=http://localhost/myschoolgh/
```

Connection is handled by `system/core/db.php` using PDO.

---

## Authentication & Authorization

### Authentication Flow

1. **Login Process:**
   - User submits credentials
   - System checks login attempts (max 10 per 10 minutes)
   - Password verification using `password_verify()`
   - Session creation on success
   - Login history logging

2. **Session Management:**
   - Sessions stored in `application/sessions/`
   - Session variables:
     - `userLoggedIn` - Session token
     - `userId` - User ID
     - `userName` - Username
     - `clientId` - School/client ID
     - `userRole` - Access level ID
     - `user_type` - User type (admin, teacher, etc.)

3. **Password Security:**
   - Minimum 8 characters
   - At least 1 uppercase letter
   - At least 1 lowercase letter
   - At least 1 number
   - At least 1 special character
   - Stored using `password_hash()` with PASSWORD_DEFAULT

### Authorization (Access Control)

Access control is managed by the `Accesslevel` controller.

**Permission System:**
- Permissions stored in `users_roles` table
- JSON format: `{"module": {"action": true/false}}`
- Checked via: `$accessObject->hasAccess($action, $module)`

**User Roles:**
- **Admin** - Full system access
- **Accountant** - Financial operations
- **Teacher** - Academic operations
- **Student** - Limited to own data
- **Parent** - Limited to ward's data
- **Employee** - Staff operations
- **Support** - System support access

**Permission Checks:**
```php
if(!$accessObject->hasAccess("view", "fees")) {
    return ["code" => 400, "data" => $this->permission_denied];
}
```

### API Authentication

For API requests:
- Access token required in header: `Authorization: Bearer {token}`
- Tokens generated via `temporary_access()` method
- Token format: Base64 encoded `username:token`
- Tokens expire after 3 months
- Stored in `users_api_keys` table

---

## Key Features

### 1. Multi-Tenant Architecture
- Each school has unique `client_id`
- Data isolation per client
- Customizable preferences per school

### 2. Academic Management
- **Classes**: Create and manage classes
- **Courses**: Subject/course management
- **Timetable**: Schedule creation
- **Attendance**: Daily attendance tracking
- **Assignments**: Assignment management
- **Results**: Grade management and reporting

### 3. Financial Management
- **Fees**: Category-based fee allocation
- **Payments**: Payment processing with receipts
- **Accounting**: Double-entry bookkeeping
- **Payroll**: Staff salary management
- **Reports**: Financial reporting

### 4. Student Information System
- Student profiles
- Guardian management
- Academic history
- Attendance records
- Fee payment history
- Promotion management

### 5. Library Management
- Book catalog
- Issue/Return tracking
- Stock management
- Overdue tracking
- Category management

### 6. Communication
- **SMS**: SMS sending via mNotify
- **Email**: Email sending via PHPMailer
- **Notifications**: In-app notifications
- **Announcements**: School-wide announcements

### 7. Reporting & Analytics
- Student reports
- Financial reports
- Attendance reports
- Academic performance reports
- Custom report generation

### 8. Document Management
- File uploads
- Document storage
- PDF generation (using dompdf)
- Certificate generation

### 9. Additional Modules
- **Hospital/Sickbay**: Medical records and health tracking
- **Housing**: Residence/hostel management
- **QR Codes**: QR code generation for students
- **ID Cards**: Student/staff ID card generation
- **Events**: Event management and calendar

---

## API Structure

### API Endpoints

The application supports API access via the `api` controller and endpoints.

**Base URL**: `https://api.myschoolgh.com/` (or configured base URL)

**Authentication:**
```
Authorization: Bearer {access_token}
```

**Request Format:**
- Method: POST
- Content-Type: application/json
- Body: JSON object with parameters

**Response Format:**
```json
{
    "code": 200,
    "data": {...},
    "result": "Success message"
}
```

**Common Response Codes:**
- `200` - Success
- `201` - Created
- `400` - Bad Request / Validation Error
- `401` - Unauthorized
- `404` - Not Found
- `429` - Too Many Requests

### API Endpoints (Examples)

- `/api/auth/login` - User login
- `/api/users/list` - List users
- `/api/classes/list` - List classes
- `/api/fees/payment` - Process payment
- `/api/attendance/mark` - Mark attendance

---

## Configuration

### System Configuration

**File**: `system/config/settings.php`

Key configurations:
- `BASEPATH` - System directory path
- `APPPATH` - Application directory path
- `VIEWPATH` - Views directory path
- `version()` - Returns "1.7.4"

**File**: `system/config/config.php`

Application-specific configuration:
- Site name
- Base URL
- Database settings (from `db.ini`)

### Application Configuration

**File**: `application/config/config.php`

Application-level settings and preferences.

### Client Preferences

Stored in `clients_accounts.client_preferences` as JSON:

```json
{
    "labels": {
        "staff": "STU",
        "student": "STD",
        "parent": "PAR",
        "receipt": "RCP"
    },
    "sessions": {
        "session": "Term"
    },
    "academics": {
        "academic_year": "2024/2025",
        "academic_term": "1st",
        "term_starts": "2024-09-01",
        "term_ends": "2024-12-20"
    },
    "features_list": ["feature1", "feature2"]
}
```

---

## Dependencies

### Composer Packages

**Core Dependencies:**
- `dompdf/dompdf` - PDF generation
- `phpmailer/phpmailer` - Email sending
- `chillerlan/php-qrcode` - QR code generation
- `robthree/twofactorauth` - Two-factor authentication
- `testingmic/ipaddress` - IP address handling

### PHP Extensions Required

- **PDO** - Database access
- **PDO_MySQL** - MySQL driver
- **MBString** - Multibyte string handling
- **DOM** - XML/HTML processing
- **GD** or **Imagick** - Image processing (recommended)
- **OPcache** - Performance optimization (recommended)

### JavaScript Libraries

- jQuery - DOM manipulation and AJAX
- Bootstrap - UI framework
- Chart.js - Charting library
- DataTables - Table management
- Select2 - Enhanced select boxes

---

## Installation & Setup

### Prerequisites

1. **Web Server**: Apache/Nginx
2. **PHP**: 7.1+ (8.1+ recommended)
3. **Database**: MySQL 5.7+ / MariaDB 10.2+
4. **Composer**: For dependency management

### Installation Steps

1. **Clone/Download the repository**
   ```bash
   git clone https://github.com/testingmic/SchoolManager.git
   cd SchoolManager
   ```

2. **Install Composer dependencies**
   ```bash
   composer install
   ```

3. **Configure Database**
   - Create `db.ini` file in root directory:
     ```ini
     hostname=localhost
     database=myschoolgh
     username=root
     password=your_password
     base_url=http://localhost/myschoolgh/
     root_url=/path/to/project/
     ```

4. **Database Setup**
   - Import database schema (if available)
   - Or run migrations/setup scripts

5. **File Permissions**
   ```bash
   chmod -R 755 application/sessions
   chmod -R 755 assets/uploads
   ```

6. **Apache Configuration**
   - Ensure `.htaccess` is enabled
   - Enable `mod_rewrite`

7. **Access Application**
   - Navigate to configured base URL
   - Register a new school account or login

### First-Time Setup

1. Register a new school account via signup page
2. Verify email address
3. Complete initial setup:
   - Set academic year and terms
   - Create departments
   - Create classes
   - Add staff members
   - Add students

---

## Development Guidelines

### Code Structure

1. **Controllers**
   - Extend `Myschoolgh` class
   - Methods return arrays with `code` and `data` keys
   - Use PDO prepared statements
   - Validate input parameters
   - Check permissions before operations

2. **Models**
   - Extend `Models` or `Myschoolgh`
   - Use `pushQuery()` for SELECT queries
   - Use prepared statements for INSERT/UPDATE/DELETE
   - Implement proper error handling

3. **Views**
   - Include `headtags.php` at start
   - Include `foottags.php` at end
   - Use global variables: `$defaultUser`, `$clientData`, etc.
   - Escape output: `htmlspecialchars()` or `xss_clean()`

### Naming Conventions

- **Controllers**: PascalCase (e.g., `Users`, `Classes`)
- **Models**: PascalCase (e.g., `Myschoolgh`, `Models`)
- **Methods**: camelCase (e.g., `list()`, `add()`, `update()`)
- **Variables**: camelCase or snake_case
- **Database Tables**: snake_case (e.g., `users`, `fees_collection`)

### Security Best Practices

1. **Input Validation**
   - Always validate and sanitize user input
   - Use `xss_clean()` for string sanitization
   - Use prepared statements for database queries

2. **Authentication**
   - Check login status: `loggedIn()`
   - Verify permissions: `$accessObject->hasAccess()`
   - Limit login attempts

3. **Password Handling**
   - Use `password_hash()` for storage
   - Use `password_verify()` for validation
   - Enforce strong password policies

4. **File Uploads**
   - Validate file types
   - Check file sizes
   - Store outside web root when possible
   - Rename uploaded files

### Error Handling

- Use try-catch blocks for database operations
- Return standardized error responses:
  ```php
  return ["code" => 400, "data" => "Error message"];
  ```
- Log errors appropriately
- Don't expose sensitive information in errors

### Database Queries

- Always use prepared statements
- Use transactions for multiple related operations
- Implement proper indexing
- Avoid N+1 query problems
- Use LIMIT clauses appropriately

### Testing

- Test with different user roles
- Test permission checks
- Test input validation
- Test error scenarios
- Test with large datasets

---

## Additional Resources

### File Locations

- **Error Logs**: `errors_log` (root directory)
- **Application Logs**: `application/logs/`
- **Session Files**: `application/sessions/`
- **Uploads**: `assets/uploads/{user_id}/`

### Support

- GitHub Repository: https://github.com/testingmic/SchoolManager
- Wiki: https://github.com/testingmic/SchoolManager/wiki

### Version History

- **1.7.4** - Current version
- Previous versions available in repository tags

---

## Conclusion

This documentation provides a comprehensive overview of the MySchoolGH School Management System. For specific implementation details, refer to the source code and inline comments.

**Last Updated**: Based on codebase analysis
**Maintained By**: Development Team
