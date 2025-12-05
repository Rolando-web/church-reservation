-- Create services table
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    description TEXT NOT NULL,
    features TEXT,
    image VARCHAR(255) DEFAULT 'church.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample services
INSERT INTO services (name, category, price, description, features, image) VALUES
('Basic Wedding Package', 'wedding', 15000, 'Perfect for intimate wedding ceremonies with essential services included.', 'Church venue for 3 hours\nPriest officiation\nBasic floral arrangement\nWedding certificate\nSound system', 'wedding-basic.jpg'),
('Premium Wedding Package', 'wedding', 35000, 'Our complete wedding package for your special day with enhanced services.', 'Church venue for 5 hours\nPriest officiation\nPremium floral decorations\nRed carpet aisle\nProfessional choir (4 members)\nSound and lighting system\nWedding certificate\nReception area access', 'wedding-premium.jpg'),
('Deluxe Wedding Package', 'wedding', 55000, 'The ultimate wedding experience with all premium services and amenities.', 'Church venue for full day\nPriest officiation\nLuxury floral arrangements\nRed carpet throughout\nProfessional choir (8 members)\nFull sound and lighting system\nWedding certificate\nReception hall access\nProfessional photography (4 hours)\nWedding coordinator\nComplimentary rehearsal', 'wedding-deluxe.jpg'),
('Infant Baptism', 'baptism', 3000, 'Traditional baptism ceremony for infants with family gathering.', 'Priest officiation\nBaptismal font use\nBaptismal candle and garment\nBaptism certificate\nSmall reception area (1 hour)', 'baptism-infant.jpg'),
('Adult Baptism', 'baptism', 5000, 'Baptism ceremony for adults including preparation and celebration.', 'Priest officiation\nPre-baptism counseling (2 sessions)\nBaptismal font use\nBaptismal certificate\nReception area (2 hours)\nCommunity welcome ceremony', 'baptism-adult.jpg'),
('Memorial Service', 'funeral', 8000, 'Dignified funeral service to honor and remember your loved one.', 'Church venue for 2 hours\nPriest officiation\nEucharistic celebration\nMemorial flowers\nSound system\nMemorial booklet printing', 'funeral-memorial.jpg'),
('Full Funeral Mass', 'funeral', 12000, 'Complete funeral mass with extended services and support.', 'Church venue for 3 hours\nPriest officiation\nFull Requiem Mass\nChoir service\nFloral arrangements\nSound system\nMemorial booklet printing\nReception area access\nGrief counseling referral', 'funeral-full.jpg'),
('First Holy Communion', 'communion', 4000, 'Special celebration for children receiving their First Holy Communion.', 'Priest officiation\nCommunion preparation classes (4 sessions)\nCommunion certificate\nCelebration mass\nReception area (2 hours)\nCommemorative photo', 'communion-first.jpg');
