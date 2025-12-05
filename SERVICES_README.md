# Services Management System

## Overview
Complete CRUD (Create, Read, Update, Delete) system for managing church services.

## Features
- ✅ Add new services with detailed information
- ✅ Edit existing services
- ✅ Delete services
- ✅ Dynamic service display on user booking page
- ✅ Category-based filtering (wedding, baptism, funeral, communion)
- ✅ Price management
- ✅ Features list (multi-line support)
- ✅ Image support with fallback

## Setup Instructions

### 1. Create the Database Table
Navigate to: `http://localhost/church-reservation/database/setup_services.php`

This will:
- Create the `services` table in your database
- Insert 8 sample services
- Show confirmation of successful setup

### 2. Access Services Management
Navigate to: `http://localhost/church-reservation/admin/services.php`

Admin features:
- View all services in a grid layout
- Click "Add New Service" to create new services
- Click "Edit" on any service card to modify details
- Click "Delete" to remove services (with confirmation)

### 3. User View
Navigate to: `http://localhost/church-reservation/user/index.php`

Users will see:
- All services loaded from database (not hardcoded anymore)
- Category filters (All, Weddings, Baptism, Funeral, Communion)
- Search functionality
- Book Now button opens modal with service details

## Database Schema

```sql
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    description TEXT NOT NULL,
    features TEXT,
    image VARCHAR(255) DEFAULT 'church.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Service Categories
- `wedding` - Wedding ceremonies and related services
- `baptism` - Baptism ceremonies (infant and adult)
- `funeral` - Funeral and memorial services
- `communion` - First communion and confirmation
- `other` - Other church services

## Adding a Service

1. Go to Admin > Services
2. Click "Add New Service"
3. Fill in the form:
   - **Service Name**: e.g., "Basic Wedding Package"
   - **Category**: Select from dropdown
   - **Price**: Enter amount in PHP (e.g., 15000)
   - **Description**: Brief description of the service
   - **Features**: Enter one feature per line
   - **Image Filename**: Image file in assets/images/ folder
4. Click "Save Service"

## Features Format
Enter features one per line in the textarea:
```
Church venue for 3 hours
Priest officiation
Basic floral arrangement
Wedding certificate
Sound system
```

## Image Management
- Images should be placed in: `assets/images/`
- Supported formats: JPG, PNG, WebP
- Recommended size: 800x600px
- If image not found, fallback to `church.png`

## API Endpoints

### api/services.php

**Create Service**
```
POST /api/services.php
action=create&name=...&category=...&price=...&description=...&features=...&image=...
```

**Update Service**
```
POST /api/services.php
action=update&id=...&name=...&category=...&price=...&description=...&features=...&image=...
```

**Delete Service**
```
POST /api/services.php
action=delete&id=...
```

**List Services**
```
GET /api/services.php?action=list
```

## Files Modified/Created

### New Files
- `admin/services.php` - Service management interface
- `api/services.php` - API endpoint for CRUD operations
- `database/create_services_table.sql` - SQL schema
- `database/setup_services.php` - Setup script with UI

### Modified Files
- `user/index.php` - Now loads services from database
- All admin pages - Added "Services" menu item

## Sample Services Included

1. **Basic Wedding Package** (₱15,000)
2. **Premium Wedding Package** (₱35,000)
3. **Deluxe Wedding Package** (₱55,000)
4. **Infant Baptism** (₱3,000)
5. **Adult Baptism** (₱5,000)
6. **Memorial Service** (₱8,000)
7. **Full Funeral Mass** (₱12,000)
8. **First Holy Communion** (₱4,000)

## Notes
- Admin authentication required for services management
- Service deletion is permanent (consider soft delete for production)
- Features are stored as newline-separated text
- Images use fallback if file not found
- All prices in Philippine Peso (₱)

## Troubleshooting

**Table doesn't exist**
- Run `database/setup_services.php` to create the table

**Services not showing on user page**
- Check if services exist in database
- Verify database connection in config/database.php
- Check PHP error logs

**Images not displaying**
- Verify image exists in assets/images/
- Check image filename matches database entry
- Fallback to church.png if image missing

## Future Enhancements
- Image upload functionality
- Service availability calendar
- Pricing variations
- Service packages/bundles
- SEO-friendly URLs
- Service ratings/reviews
