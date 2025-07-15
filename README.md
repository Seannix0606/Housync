# HouseSync - Property Management System

A comprehensive property management system built with Laravel, designed for landlords to manage their apartments and units efficiently.

## Features

### 🏢 **Multi-Role System**
- **Super Admin**: System administration and landlord approvals
- **Landlord**: Property and unit management
- **Tenant**: Access to rental information and services

### 🏠 **Property Management**
- **Apartment Management**: Create and manage apartment buildings
- **Unit Management**: Track individual rental units
- **Occupancy Tracking**: Monitor unit availability and occupancy rates
- **Revenue Tracking**: Calculate rental income and statistics

### 👥 **User Management**
- **Landlord Registration**: Self-registration with admin approval
- **Role-Based Access Control**: Secure access based on user roles
- **Status Management**: Pending, approved, rejected user states

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd Housesync
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Start the application**
   ```bash
   php artisan serve
   ```

## Default Credentials

### Super Admin
- **Email**: `admin@housesync.com`
- **Password**: `admin123`

*Note: Change these credentials immediately after first login*

## Usage

### For Landlords
1. **Register**: Visit `/landlord/register` to create an account
2. **Wait for Approval**: Admin must approve your registration
3. **Manage Properties**: Add apartments and units after approval
4. **Track Performance**: Monitor occupancy and revenue

### For Super Admins
1. **Login**: Use admin credentials to access the system
2. **Approve Landlords**: Review and approve pending registrations
3. **Manage Users**: Create, edit, and manage all system users
4. **Monitor System**: View system-wide statistics and reports

## Security Features

- **Role-Based Access Control**: Secure route protection
- **Authentication Middleware**: Protected admin and landlord areas
- **Input Validation**: Comprehensive form validation
- **CSRF Protection**: Built-in Laravel CSRF protection

## Technology Stack

- **Backend**: Laravel 11.x
- **Frontend**: Blade Templates, HTML5, CSS3, JavaScript
- **Database**: MySQL/SQLite
- **Authentication**: Laravel Auth
- **Styling**: Custom CSS with modern design

## Contributing

This is a production application. For feature requests or bug reports, please contact the development team.

## Firebase Integration

HouseSync supports Firebase integration for enhanced features like real-time notifications, cloud storage, and authentication.

### Setup Firebase

1. **Enable PHP Sodium Extension** (required for Firebase PHP SDK)
2. **Install Firebase PHP SDK**: `composer require kreait/firebase-php`
3. **Configure Firebase project** in Firebase Console
4. **Add Firebase credentials** to your `.env` file

For detailed setup instructions, see [FIREBASE_SETUP.md](FIREBASE_SETUP.md)

### Test Firebase Connection

After setup, test your Firebase connection by visiting:
```
http://your-domain/test-firebase
```

## License

Proprietary - All rights reserved.

## Support

For technical support, please contact the system administrator.

---

**HouseSync** - Streamlining Property Management
