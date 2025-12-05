# Church Availability System

## Overview
This system automatically manages church service availability based on active reservations. Services become unavailable when booked and automatically return to available status once the reservation is completed.

## How It Works

### 1. **Service Availability Status**
- **Available** (Blue Badge): No active reservations exist for this service
- **Unavailable** (Red Badge): Service has an active pending, approved, or paid reservation

### 2. **Automatic Status Updates**
The system automatically marks reservations as "completed" when:
- The reservation has status = 'paid'
- The reservation date and time have passed
- This happens in real-time when checking availability

### 3. **Booking Flow**

#### User Perspective:
1. **Browse Services**: See real-time availability on service cards
2. **Unavailable Services**: 
   - Button shows "Currently Booked" 
   - Button is disabled (gray, non-clickable)
3. **Available Services**: 
   - Button shows "Book Now"
   - Can proceed with booking
4. **During Booking**:
   - Select date and time
   - System checks if time slot is available (2-hour window)
   - Shows green ✓ if available or red ✗ if occupied
   - Submit button disabled if time slot unavailable

#### Reservation Lifecycle:
```
Pending → Approved → Paid → [Event Happens] → Completed (Auto)
   ↓         ↓         ↓                           ↓
 Blocks   Blocks   Blocks                    Frees Up
 Service  Service  Service                   Service
```

### 4. **Time Slot Checking**
- When a user books a specific date/time, the system checks for conflicts
- Considers a 2-hour buffer window to prevent overlapping events
- Example: If someone books 2:00 PM, times from 12:00 PM to 4:00 PM are blocked

### 5. **Automatic Service Release**
Once a paid reservation's date/time passes:
- Status changes from "paid" → "completed"
- Service becomes available for new bookings
- Happens automatically when availability is checked
- No manual intervention needed

## Database Changes

### New Status: 'completed'
```sql
status ENUM('pending', 'approved', 'rejected', 'paid', 'cancelled', 'completed')
```

### To Update Your Database:
```bash
mysql -u your_username -p church_reservation < database/update_add_completed_status.sql
```

Or run manually in phpMyAdmin/MySQL Workbench:
```sql
ALTER TABLE reservations 
MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'paid', 'cancelled', 'completed') 
DEFAULT 'pending';
```

## API Endpoints

### 1. Check Service Availability
**Endpoint**: `GET /api/get_service_availability.php`

**Response**:
```json
{
  "success": true,
  "availability": {
    "1": {
      "name": "Wedding Ceremony",
      "available": false,
      "active_reservations": 1
    },
    "2": {
      "name": "Baptism Service",
      "available": true,
      "active_reservations": 0
    }
  }
}
```

### 2. Check Time Slot Availability
**Endpoint**: `GET /api/check_availability.php?date=2025-12-25&time=14:00`

**Response (Available)**:
```json
{
  "available": true,
  "message": "Time slot is available"
}
```

**Response (Unavailable)**:
```json
{
  "available": false,
  "message": "This time slot is already reserved",
  "reserved_by": "John Doe",
  "status": "paid"
}
```

## Features

### Real-Time Updates
- Service availability checked on page load
- Time slot availability checked when user selects date/time
- Visual feedback with badges and messages

### Conflict Prevention
- 2-hour buffer window prevents overlapping bookings
- Users can't submit booking if time slot unavailable
- Clear error messages guide users to choose different times

### Automatic Management
- No manual intervention needed
- Past reservations auto-complete
- Services auto-release when events finish

### User Experience
- Disabled buttons for unavailable services
- Color-coded badges (blue = available, red = unavailable)
- Real-time availability messages in booking form
- Smooth UI transitions

## Files Modified/Created

### New Files:
1. `api/check_availability.php` - Time slot availability checker
2. `api/get_service_availability.php` - Service availability status
3. `database/update_add_completed_status.sql` - Database update script
4. `AVAILABILITY_SYSTEM.md` - This documentation

### Modified Files:
1. `user/index.php` - Added availability checking and UI updates
2. Updated service cards with dynamic status badges
3. Added real-time time slot validation in booking form

## Testing

### Test Scenario 1: Book a Service
1. Go to services page
2. See all services show "Available" (blue badge)
3. Click "Book Now" on any service
4. Select a date and time
5. Should see "✓ This time slot is available" message

### Test Scenario 2: Double Booking Prevention
1. Create a reservation for Dec 25, 2025 at 2:00 PM
2. Try to book another service for Dec 25, 2025 at 2:00 PM (or nearby time)
3. Should see "✗ This time slot is already reserved" message
4. Submit button should be disabled

### Test Scenario 3: Service Unavailability
1. Create and approve a reservation
2. Refresh the services page
3. That service should show "Unavailable" (red badge)
4. Button should say "Currently Booked" and be disabled

### Test Scenario 4: Automatic Release
1. Create a reservation for a past date/time
2. Mark it as paid
3. Refresh the services page
4. Service should be "Available" again (system auto-completed it)

## Troubleshooting

### Services not updating availability:
- Check browser console for JavaScript errors
- Verify API endpoints are accessible
- Check database connection

### Time slots not being validated:
- Ensure reservation_date and reservation_time are properly set
- Check that CONCAT in SQL query works (MySQL/MariaDB)
- Verify timezone settings match server time

### Past reservations not auto-completing:
- Run the database update script
- Check that 'completed' status exists in ENUM
- Verify NOW() function returns correct server time

## Future Enhancements
- Email notifications when service becomes available
- Waitlist feature for unavailable services
- Admin dashboard showing service utilization
- Calendar view of all bookings
- Recurring event support
