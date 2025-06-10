# Fleetbase Vendor Management Extension

A comprehensive vendor management extension for the Fleetbase platform that provides CRUD functionality and QR code integration for managing suppliers and logistics partners.

## Features

- **Vendor CRUD Operations**: Create, read, update, and delete vendor records
- **QR Code Generation**: Automatic QR code generation for each vendor
- **QR Code Scanning**: Mobile-friendly QR code scanner for quick vendor verification
- **Responsive UI**: Built with Tailwind CSS for optimal mobile and desktop experience
- **Integration**: Seamlessly integrates with existing Fleetbase architecture

## Installation

### Prerequisites

- Fleetbase platform installed and running
- PHP 8.1+ with Laravel
- Node.js 16+ for Ember.js frontend
- MySQL or PostgreSQL database

### Backend Setup

1. Copy the extension files to your Fleetbase installation:
```bash
cp -r server/src/* /path/to/fleetbase/api/src/
```

2. Run the database migration:
```bash
php artisan migrate --path=database/migrations/vendor-for-suppliers
```

3. Install QR code generation package:
```bash
composer require simplesoftwareio/simple-qrcode
```

4. Add the extension routes to your API routes file:
```php
// In routes/api.php
Route::prefix('vendors')->group(function () {
    Route::get('/', [VendorController::class, 'index']);
    Route::post('/', [VendorController::class, 'store']);
    Route::get('/{id}', [VendorController::class, 'show']);
    Route::put('/{id}', [VendorController::class, 'update']);
    Route::delete('/{id}', [VendorController::class, 'destroy']);
    Route::post('/scan', [VendorController::class, 'scanQrCode']);
});
```

### Frontend Setup

1. Copy the Ember.js components to your console application:
```bash
cp -r console/app/* /path/to/fleetbase/console/app/
```

2. Install QR code scanning dependencies:
```bash
npm install jsqr
```

3. Add the vendor routes to your Ember router:
```javascript
// In app/router.js
this.route('vendors', function() {
  this.route('new');
  this.route('view', { path: '/:vendor_id' });
  this.route('edit', { path: '/:vendor_id/edit' });
});
```

## Usage

### Creating a Vendor

1. Navigate to the Vendors section in your Fleetbase dashboard
2. Click "Add Vendor"
3. Fill in the vendor details (name, email, phone, address)
4. Save the vendor - a QR code will be automatically generated

### Managing Vendors

- **View**: Click on any vendor to see detailed information and QR code
- **Edit**: Use the edit button to modify vendor information
- **Delete**: Remove vendors with confirmation dialog
- **Search**: Use the search bar to find specific vendors
- **Filter**: Filter vendors by status (active/inactive)

### QR Code Features

- **Download**: Download QR codes as PNG files
- **Scan**: Use the built-in scanner to verify vendors at entry points
- **Mobile Access**: QR codes link to mobile-friendly vendor detail pages

### API Endpoints

- `GET /api/vendors` - List all vendors
- `POST /api/vendors` - Create new vendor
- `GET /api/vendors/{id}` - Get vendor details
- `PUT /api/vendors/{id}` - Update vendor
- `DELETE /api/vendors/{id}` - Delete vendor
- `POST /api/vendors/scan` - Scan QR code

## Configuration

### Environment Variables

Add these to your `.env` file:

```env
# QR Code Storage
FILESYSTEM_DISK=public
QR_CODE_SIZE=300
QR_CODE_MARGIN=2
```

### Permissions

Ensure your web server has write permissions to the storage directory for QR code generation:

```bash
chmod -R 775 storage/app/public/qr-codes
```

## Security Considerations

- All vendor data is scoped to the authenticated company
- QR codes contain encrypted vendor information
- Input validation on all CRUD operations
- CSRF protection on all forms
- Rate limiting on API endpoints

## Customization

### Adding Custom Fields

To add custom fields to vendors:

1. Create a new migration:
```bash
php artisan make:migration add_custom_fields_to_vendors_table
```

2. Update the Vendor model's `$fillable` array
3. Update the frontend forms and display templates

### Styling

The extension uses Tailwind CSS classes that match Fleetbase's design system. Customize by modifying the Handlebars templates in the `console/app/templates` directory.

## Troubleshooting

### QR Code Generation Issues

- Ensure the `simplesoftwareio/simple-qrcode` package is installed
- Check storage permissions
- Verify the `FILESYSTEM_DISK` configuration

### Scanner Not Working

- Ensure HTTPS is enabled (required for camera access)
- Check browser camera permissions
- Verify the jsQR library is properly installed

### Database Issues

- Run `php artisan migrate:status` to check migration status
- Ensure database connection is properly configured
- Check that the vendors table was created successfully

## Support

For issues and support:

1. Check the Fleetbase documentation
2. Review the error logs in `storage/logs/laravel.log`
3. Open an issue in the Fleetbase repository

## License

This extension follows the same license as the Fleetbase platform (AGPL-3.0).
