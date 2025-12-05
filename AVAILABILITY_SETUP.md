# Quick Setup Guide - Availability System

## Step 1: Update Database Schema

Run this SQL command in phpMyAdmin or MySQL console:

```sql
USE church_reservation;

-- Add 'completed' status to reservations
ALTER TABLE reservations 
MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'paid', 'cancelled', 'completed') 
DEFAULT 'pending';

-- Add indexes for better performance
CREATE INDEX IF NOT EXISTS idx_reservation_datetime ON reservations(reservation_date, reservation_time);
CREATE INDEX IF NOT EXISTS idx_reservation_status_date ON reservations(status, reservation_date);

-- Auto-complete past paid reservations
UPDATE reservations 
SET status = 'completed' 
WHERE status = 'paid' 
AND CONCAT(reservation_date, ' ', reservation_time) < NOW();
```

## Step 2: Test the System

1. **Start your XAMPP server** (Apache + MySQL)

2. **Visit the services page**:
   ```
   http://localhost/church-reservation/user/index.php
   ```

3. **Check availability badges**:
   - All services should show "Available" (blue) or "Unavailable" (red)
   - If a service is unavailable, the "Book Now" button should be disabled

4. **Test booking process**:
   - Click on an available service
   - Select a future date and time
   - You should see: "✓ This time slot is available"
   - Try selecting a time close to an existing reservation
   - You should see: "✗ This time slot is already reserved"

## Step 3: Verify Automatic Status Updates

### Test Past Reservations:
```sql
-- Check if old paid reservations are auto-completed
SELECT id, reservation_date, reservation_time, status 
FROM reservations 
WHERE reservation_date < CURDATE() 
AND status IN ('paid', 'completed');
```

### Manually Test:
1. Create a test reservation for tomorrow at 2:00 PM
2. Approve it (admin panel)
3. Visit services page - service should show "Unavailable"
4. Wait until after the time passes (or manually change the date to past)
5. Refresh services page - service should show "Available" again

## How It Works

### Service Level Availability:
- **Blue "Available"** = No active future reservations
- **Red "Unavailable"** = Has pending/approved/paid future reservation
- **Button Enabled** = Can book
- **Button Disabled** = Currently booked

### Time Slot Level Availability:
- Checks 2-hour window for conflicts
- Shows real-time feedback in booking form
- Prevents double-booking

### Automatic Status Management:
```
When user pays → status = 'paid' → service unavailable
Event time passes → status = 'completed' → service available again
```

## Troubleshooting

### Issue: Services show "Checking..." forever
**Solution**: 
- Open browser console (F12)
- Check for JavaScript errors
- Verify API endpoint: `http://localhost/church-reservation/api/get_service_availability.php`
- Should return JSON with success: true

### Issue: Database error about 'completed' status
**Solution**: 
- Run the ALTER TABLE command from Step 1
- Restart MySQL service
- Clear any PHP opcache

### Issue: Time slots always show as unavailable
**Solution**:
- Check your server timezone settings
- Verify dates are in correct format (YYYY-MM-DD)
- Check that NOW() function returns correct time:
  ```sql
  SELECT NOW();
  ```

### Issue: Old paid reservations not auto-completing
**Solution**:
- Run the UPDATE query manually:
  ```sql
  UPDATE reservations 
  SET status = 'completed' 
  WHERE status = 'paid' 
  AND CONCAT(reservation_date, ' ', reservation_time) < NOW();
  ```

## Testing Checklist

- [ ] Database schema updated with 'completed' status
- [ ] Services page loads without errors
- [ ] Availability badges show correctly (blue/red)
- [ ] Can book available services
- [ ] Cannot book unavailable services (button disabled)
- [ ] Time slot checking works in booking form
- [ ] Real-time messages show (green ✓ or red ✗)
- [ ] Past paid reservations auto-complete
- [ ] Services become available after event completes

## Success!

Once all tests pass, your availability system is fully operational! 

Users will now see real-time availability and the system will automatically manage service bookings without manual intervention.
