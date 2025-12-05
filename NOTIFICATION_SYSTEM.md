# Notification System Setup

## Overview
The notification system allows users to receive real-time notifications when their reservation requests are approved or rejected by the admin.

## Features
- 🔔 Notification bell icon with unread badge count
- ✅ Notifications for approved reservations
- ❌ Notifications for rejected reservations
- 📧 View all notifications in a dedicated page
- ✓ Mark notifications as read/unread
- 🗑️ Delete notifications

## Database Setup

### Step 1: Update the notifications table
Run the SQL script to update your database:

```sql
-- In phpMyAdmin or MySQL CLI, run:
source database/update_notifications.sql
```

Or manually run:
```sql
USE church_reservation;

DROP TABLE IF EXISTS notifications;

CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    reservation_id INT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
);
```

## How It Works

### Admin Side
1. Admin logs into the admin dashboard
2. Views pending reservations
3. Clicks "Approve" or "Reject" button
4. **Automatically creates a notification** for the user

### User Side
1. User sees notification bell icon in navigation
2. **Red badge** shows number of unread notifications
3. Clicks bell to view all notifications
4. Can mark as read or delete notifications

## Notification Types

### Approved Reservation
- **Title:** "Reservation Approved! 🎉"
- **Message:** "Your reservation for [Service] on [Date] has been approved. Please proceed with the payment."
- **Icon:** Green checkmark

### Rejected Reservation
- **Title:** "Reservation Rejected"
- **Message:** "Unfortunately, your reservation for [Service] on [Date] has been rejected. Please contact us for more information."
- **Icon:** Red X

## Files Modified/Created

### Modified Files:
1. `user/index.php` - Added notification bell and badge
2. `user/dashboard.php` - Added notification bell and badge
3. `api/reservations.php` - Added notification creation on status update
4. `database/schema.sql` - Updated notifications table structure

### New Files:
1. `user/notifications.php` - Notification center page
2. `database/update_notifications.sql` - Database migration script

## Testing

1. **Create a reservation as a user**
2. **Login as admin** (admin@church.com / admin123)
3. **Approve or reject** the reservation
4. **Login back as user**
5. **See the notification bell** with red badge
6. **Click bell** to view notification details

## Troubleshooting

### Issue: Warning "Undefined array key 'user_name'"
**Solution:** This has been fixed by adding:
```php
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest';
```

### Issue: Notifications not showing
**Solution:** Make sure you've run the database update script to add the `title` column.

### Issue: Badge not updating
**Solution:** Refresh the page after admin approves/rejects a reservation.

## Future Enhancements
- Real-time notifications using WebSockets
- Email notifications
- Push notifications for mobile
- Notification preferences/settings
