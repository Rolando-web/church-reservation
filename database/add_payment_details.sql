-- Add payment details columns to payments table
-- Run this to update your existing database

USE church_reservation;

-- Add new columns for payment details
ALTER TABLE payments 
ADD COLUMN payment_phone VARCHAR(20) NULL AFTER payment_method,
ADD COLUMN payment_reference VARCHAR(100) NULL AFTER payment_phone,
ADD COLUMN card_last4 VARCHAR(4) NULL AFTER payment_reference,
ADD COLUMN card_holder VARCHAR(100) NULL AFTER card_last4;

-- Add index for reference number lookups
CREATE INDEX idx_payment_reference ON payments(payment_reference);
