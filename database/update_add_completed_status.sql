-- Update reservations table to include 'completed' status
-- Run this to update your existing database

USE church_reservation;

-- Modify the status ENUM to include 'completed'
ALTER TABLE reservations 
MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'paid', 'cancelled', 'completed') DEFAULT 'pending';

-- Automatically mark past paid reservations as completed
UPDATE reservations 
SET status = 'completed' 
WHERE status = 'paid' 
AND CONCAT(reservation_date, ' ', reservation_time) < NOW();

-- Create an index for faster availability checks
CREATE INDEX idx_reservation_datetime ON reservations(reservation_date, reservation_time);
CREATE INDEX idx_reservation_status_date ON reservations(status, reservation_date);
