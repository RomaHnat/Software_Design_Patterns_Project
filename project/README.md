# Aquaua Water Management System

A professional water bottle inventory and sales management system built with PHP, following modern software architecture patterns and best practices.

## 🏗️ Architecture

This project demonstrates a well-structured enterprise PHP application using:

### Architectural Patterns
- **MVC (Model-View-Controller)** - Separation of concerns between business logic, presentation, and control flow
- **Repository Pattern** - Abstraction layer for data access operations
- **Service Layer** - Business logic encapsulation separate from controllers
- **Front Controller** - Single entry point for all HTTP requests through a router

### Design Patterns
- **Singleton** - Database connection management
- **Factory Method** - Object creation from database rows and form data
- **Decorator** - Enhanced repository with caching capabilities
- **Template Method** - Base controller with consistent response handling

## 📋 Features

### Category Management
- Add new water bottle categories with unique IDs
- Update category details (name, description, price)
- Add stock to warehouse inventory
- View all categories with real-time stock levels
- Automatic inventory tracking

### Customer Management
- Register new customers with email validation
- Update customer information
- Delete customers (with sale dependency checks)
- Email uniqueness enforcement
- View all registered customers

### Sales Processing
- Place multi-item sales orders
- Automatic stock reduction
- Price calculation based on quantities
- Sales history tracking
- Cancel sales with automatic stock restoration
- Foreign key integrity protection

## 🛠️ Technology Stack

- **PHP 8.0+** - Modern PHP with strict typing
- **MariaDB/MySQL** - Relational database with referential integrity
- **PDO** - Prepared statements for SQL injection prevention
- **PHPUnit 10** - Comprehensive unit testing
- **SonarQube/SonarCloud** - Code quality and security analysis
- **Xdebug** - Code coverage reporting

## 📦 Database Schema

```sql
categories
  - CategoryID (CHAR(2), Primary Key)
  - Name (VARCHAR(15))
  - Description (VARCHAR(25))
  - Number_Of (MEDIUMINT)
  - Price_Per (DECIMAL(5,2))

customers
  - CustomerID (SMALLINT, Auto Increment)
  - F_Name (VARCHAR(15))
  - S_Name (VARCHAR(20))
  - Email (VARCHAR(254), UNIQUE)

sales
  - SaleID (SMALLINT, Auto Increment)
  - Price (DECIMAL(9,2))
  - Date_Sold (DATE)
  - CustomerID (Foreign Key)

salesitems
  - SaleID (Foreign Key)
  - CategoryID (Foreign Key)
  - Number_Of_Cat (MEDIUMINT)
  - Composite Primary Key (SaleID, CategoryID)
```

## 🚀 Installation

### Prerequisites
- XAMPP (or similar PHP/MySQL stack)
- PHP 8.0 or higher
- Composer
- MariaDB/MySQL

### Setup Steps

1. **Clone or place the project in your web server directory**
   ```bash
   cd c:\xampp\htdocs\project
   ```

2. **Install dependencies**
   ```powershell
   cd newProject
   composer install
   ```

3. **Import the database**
   ```powershell
   C:\xampp\mysql\bin\mysql.exe -u root aquaua < ..\aquaua.sql
   ```
   
   Or create the database manually:
   ```sql
   CREATE DATABASE aquaua;
   USE aquaua;
   SOURCE aquaua.sql;
   ```

4. **Configure database connection** (Optional)
   
   By default, the application uses:
   - Host: `localhost`
   - Database: `aquaua`
   - Username: `root`
   - Password: `` (empty)
   
   To override, set environment variables:
   ```powershell
   $env:DB_HOST = "localhost"
   $env:DB_NAME = "aquaua"
   $env:DB_USER = "root"
   $env:DB_PASSWORD = ""
   ```

5. **Start XAMPP services**
   - Apache
   - MySQL

6. **Access the application**
   ```
   http://localhost/project/newProject/public/
   ```

## 🧪 Testing

### Run Unit Tests

Execute the test suite with detailed output:

```powershell
cd c:\xampp\htdocs\project\newProject
powershell -ExecutionPolicy Bypass -File run-tests.ps1
```

Or use PHPUnit directly:

```powershell
c:\xampp\php\php.exe vendor\bin\phpunit
```

### Test Coverage

The project includes comprehensive tests for:
- ✅ Category Service (validation, CRUD operations)
- ✅ Customer Service (email validation, duplicate checks)
- ✅ Sale Service (stock validation, transactions)
- ✅ Database connection
- ✅ Router functionality

Generate code coverage report (requires Xdebug):

```powershell
c:\xampp\php\php.exe -d xdebug.mode=coverage vendor\bin\phpunit
```

View coverage report at: `coverage/html/index.html`

## 📊 Code Quality

### SonarQube Analysis

Run static code analysis:

```powershell
cd c:\xampp\htdocs\project\newProject
powershell -ExecutionPolicy Bypass -File run-sonar.ps1
```

This analyzes:
- Code smells and technical debt
- Security vulnerabilities
- Code coverage metrics
- Code duplication
- Maintainability issues

**Project Key:** `water-management-system`

## 📁 Project Structure

```
newProject/
├── config/                 # Configuration files
│   ├── bootstrap.php       # Autoloader and initialization
│   ├── constants.php       # Application constants
│   └── Database.php        # Singleton database connection
├── controllers/            # Request handlers
│   ├── BaseController.php  # Abstract base with template methods
│   ├── CategoryController.php
│   ├── CustomerController.php
│   ├── HomeController.php
│   └── SaleController.php
├── models/                 # Domain entities
│   ├── Category.php
│   ├── Customer.php
│   ├── Sale.php
│   └── SaleItem.php
├── repositories/           # Data access layer
│   ├── CategoryRepository.php
│   ├── CachedCategoryRepository.php  # Decorator pattern
│   ├── CustomerRepository.php
│   └── SaleRepository.php
├── services/               # Business logic layer
│   ├── CategoryService.php
│   ├── CustomerService.php
│   └── SaleService.php
├── utils/                  # Utilities and helpers
│   ├── Exceptions.php      # Custom exception classes
│   └── Router.php          # Front controller routing
├── views/                  # Presentation layer
│   ├── categories/
│   ├── customers/
│   ├── sales/
│   ├── home/
│   ├── layouts/
│   └── errors/
├── tests/                  # PHPUnit tests
│   ├── models/
│   ├── services/
│   └── utils/
├── public/                 # Web root
│   ├── index.php          # Entry point
│   ├── css/
│   └── js/
├── coverage/              # Test coverage reports
├── composer.json          # Dependencies
├── phpunit.xml           # PHPUnit configuration
├── sonar-project.properties  # SonarQube settings
├── run-tests.ps1         # Test execution script
└── run-sonar.ps1         # Code analysis script
```

## 🔒 Security Features

- **Prepared Statements** - All database queries use PDO prepared statements
- **Email Validation** - Server-side email format validation using `FILTER_VALIDATE_EMAIL`
- **Input Sanitization** - Form data validation and sanitization
- **SQL Injection Prevention** - Parameterized queries throughout
- **Foreign Key Constraints** - Database-level referential integrity
- **Error Handling** - Custom exceptions for different error types
- **XSS Protection** - HTML entity encoding in views

## 🎯 Key Design Decisions

### Why Repository Pattern?
- Decouples business logic from data access
- Enables easy testing with mock repositories
- Centralizes query logic
- Supports caching through decoration

### Why Service Layer?
- Encapsulates complex business rules
- Manages transactions across repositories
- Keeps controllers thin and focused
- Reusable business logic

### Why Factory Methods?
- Consistent object creation
- Encapsulates instantiation logic
- Type safety with private constructors
- Separate concerns (DB vs Form data)

## 📝 API Routes

### Categories
- `GET /categories` - List all categories
- `POST /categories` - Create new category
- `POST /categories/warehouse` - Add stock to category
- `POST /categories/update` - Update category details

### Customers
- `GET /customers` - List all customers
- `POST /customers` - Register new customer
- `POST /customers/update` - Update customer information

### Sales
- `GET /sales` - List all sales
- `POST /sales` - Place new sale
- `POST /sales/cancel` - Cancel existing sale

## 🤝 Contributing

This project follows clean code principles:

1. **SOLID Principles** - Single responsibility, dependency injection
2. **DRY** - Don't repeat yourself
3. **KISS** - Keep it simple
4. **Type Safety** - Strict typing throughout
5. **PSR Standards** - PHP-FIG coding standards

## 📄 License

This is an educational project demonstrating enterprise PHP architecture patterns.

## 👥 Authors

- **romahnat** - Initial development and refactoring from legacy codebase

## 🔄 Migration from Old Project

This project is a complete refactoring of the `oldProject` directory, transforming procedural PHP into a modern, object-oriented architecture with proper separation of concerns, testability, and maintainability.

### Key Improvements
- ✅ Object-oriented architecture vs procedural code
- ✅ Service layer vs direct database calls in views
- ✅ Repository pattern vs scattered SQL queries
- ✅ Front controller vs multiple entry points
- ✅ Dependency injection vs global state
- ✅ Unit tests vs no testing
- ✅ Code quality analysis vs no analysis
- ✅ Type safety vs loose typing
- ✅ Error handling vs error suppression

