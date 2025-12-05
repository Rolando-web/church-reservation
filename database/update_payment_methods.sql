-- Update payments table to include new payment methods
-- Run this to update your existing database

USE church_reservation;

-- Modify the payment_method ENUM to include PayMaya, GCash, and Credit Card
ALTER TABLE payments 
MODIFY COLUMN payment_method ENUM('cash', 'paymaya', 'gcash', 'credit_card', 'online') DEFAULT 'cash';

-- Note: 'online' is kept for backward compatibility with existing records
