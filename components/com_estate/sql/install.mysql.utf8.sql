-- =====================================================================
-- DATA TIER - Real Estate Company (com_estate)
-- MySQL schema for the Joomla metadata prefix (replace #__ with your chosen
-- prefix, e.g. jos_, jml_ etc. Joomla does this automatically at install).
-- =====================================================================

-- ---------------------------------------------------------------------
-- Table: #__estate_agents
-- Business users who manage/list properties and take bookings.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__estate_agents` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Link to #__users Joomla account',
  `name` VARCHAR(120) NOT NULL DEFAULT '',
  `email` VARCHAR(120) NOT NULL DEFAULT '',
  `phone` VARCHAR(40) NOT NULL DEFAULT '',
  `bio` TEXT NULL,
  `headshot` VARCHAR(255) NOT NULL DEFAULT '',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `ordering` INT(11) NOT NULL DEFAULT 0,
  `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NULL DEFAULT NULL,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `modified` DATETIME NULL DEFAULT NULL,
  `modified_by` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table: #__estate_listings
-- The core property/listing records.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__estate_listings` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(200) NOT NULL DEFAULT '',
  `alias` VARCHAR(200) NOT NULL DEFAULT '',
  `agent_id` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `property_type` VARCHAR(40) NOT NULL DEFAULT 'house' COMMENT 'house, apartment, land, commercial',
  `status` VARCHAR(30) NOT NULL DEFAULT 'available' COMMENT 'available, pending, sold, rented',
  `sale_or_rent` VARCHAR(10) NOT NULL DEFAULT 'sale' COMMENT 'sale | rent',
  `price` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `currency` VARCHAR(3) NOT NULL DEFAULT 'KSH' COMMENT 'KSH | USD',
  `off_plan` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = off-plan investment opportunity',
  `developer` VARCHAR(120) NOT NULL DEFAULT '' COMMENT 'Developer/builder for off-plan units',
  `completion_date` DATE NULL DEFAULT NULL COMMENT 'Expected off-plan completion',
  `payment_plan` TEXT NULL COMMENT 'Staged payment terms for off-plan units',
  `bedrooms` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `bathrooms` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `area_sqft` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `address` VARCHAR(255) NOT NULL DEFAULT '',
  `city` VARCHAR(120) NOT NULL DEFAULT '',
  `latitude` VARCHAR(30) NOT NULL DEFAULT '',
  `longitude` VARCHAR(30) NOT NULL DEFAULT '',
  `description` MEDIUMTEXT NULL,
  `featured` TINYINT(1) NOT NULL DEFAULT 0,
  `published` TINYINT(1) NOT NULL DEFAULT 1,
  `publish_up` DATETIME NULL DEFAULT NULL,
  `publish_down` DATETIME NULL DEFAULT NULL,
  `main_image` VARCHAR(255) NOT NULL DEFAULT '',
  `gallery_json` TEXT NULL COMMENT 'JSON array of additional image paths',
  `hits` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `access` INT(10) UNSIGNED NOT NULL DEFAULT 1,
  `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NULL DEFAULT NULL,
  `ordering` INT(11) NOT NULL DEFAULT 0,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `modified` DATETIME NULL DEFAULT NULL,
  `modified_by` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_alias` (`alias`),
  KEY `idx_agent` (`agent_id`),
  KEY `idx_published` (`published`),
  KEY `idx_status` (`status`),
  KEY `idx_city` (`city`),
  KEY `idx_type` (`property_type`),
  KEY `idx_catid` (`access`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table: #__estate_bookings
-- Viewing-enquiry records a visitor makes for a listing.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__estate_bookings` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `listing_id` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `name` VARCHAR(120) NOT NULL DEFAULT '',
  `email` VARCHAR(120) NOT NULL DEFAULT '',
  `phone` VARCHAR(40) NOT NULL DEFAULT '',
  `message` TEXT NULL,
  `preferred_date` DATE NULL DEFAULT NULL,
  `ip` VARCHAR(45) NOT NULL DEFAULT '',
  `state` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0 new, 1 contacted, 2 done',
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_listing` (`listing_id`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table: #__estate_tours
-- A 360 virtual tour for a listing, assembled from ordered panorama shots.
-- state: 0 none, 1 drafting, 2 ready for review, 3 published.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__estate_tours` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `listing_id` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `state` TINYINT(1) NOT NULL DEFAULT 0,
  `notes` TEXT NULL,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_listing` (`listing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table: #__estate_tour_shots
-- One ordered panorama per capture position used to build the 3D tour.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__estate_tour_shots` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tour_id` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `listing_id` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `slot` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `room_label` VARCHAR(80) NOT NULL DEFAULT '',
  `position_key` VARCHAR(20) NOT NULL DEFAULT '',
  `instructions` TEXT NULL,
  `image` VARCHAR(255) NOT NULL DEFAULT '',
  `published` TINYINT(1) NOT NULL DEFAULT 1,
  `ordering` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_tour` (`tour_id`),
  KEY `idx_listing` (`listing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Sample seed data (only if tables are empty)
-- ---------------------------------------------------------------------
INSERT INTO `#__estate_agents` (`name`, `email`, `phone`, `bio`, `is_active`) VALUES
('Grace Wanjiru', 'grace@horizonhomes.ke', '+254 700 123 456', 'Senior property consultant with 10 years of experience in Nairobi residential sales.', 1),
('Brian Otieno', 'brian@horizonhomes.ke', '+254 711 654 321', 'Commercial leasing specialist covering Westlands and the CBD.', 1),
('Amina Hassan', 'amina@horizonhomes.ke', '+254 722 987 654', 'Land and plot advisor helping families find their forever investment.', 1);

INSERT INTO `#__estate_listings`
(`title`, `alias`, `agent_id`, `property_type`, `status`, `sale_or_rent`, `price`, `currency`, `bedrooms`, `bathrooms`, `area_sqft`, `address`, `city`, `description`, `featured`, `main_image`, `gallery_json`, `published`) VALUES
('Modern 4-Bedroom Villa in Karen', 'modern-4-bedroom-villa-in-karen', 1, 'house', 'available', 'sale', 28500000.00, 'KSH', 4, 4, 4200, 'Miotoni Lane, Karen', 'Nairobi',
'The house is an elegant villa located in a quiet leafy estate in Karen. An open plan living and dining area flows onto a landscaped garden with a private pool. Finished with imported fittings, solid wood flooring and a gourmet kitchen.', 1, 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1400&q=80', '["https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=1400&q=80"]', 1),

('Sunny 2-Bedroom Apartment, Westlands', 'sunny-2-bedroom-apartment-westlands', 2, 'apartment', 'available', 'rent', 85000.00, 'KSH', 2, 2, 1100, 'Ring Road Parklands', 'Nairobi',
'A bright and modern 2-bedroom apartment in a secure gated development. Features a spacious balcony, fitted kitchen, and access to gym and rooftop lounge. Ideal for professionals.', 1, 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1400&q=80', '["https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1400&q=80"]', 1),

('Prime Commercial Plot, Konza', 'prime-commercial-plot-konza', 3, 'land', 'available', 'sale', 12000000.00, 'KSH', 0, 0, 15000, 'Konza Technopolis', 'Machakos',
'A titled 1.5-acre commercial plot inside Konza Technopolis, perfect for mixed-use development. Ready title deed, flat terrain and close to the main highway.', 0, 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1400&q=80', '["https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1400&q=80"]', 1),

('Cozy 3-Bedroom Townhouse, Runda', 'cozy-3-bedroom-townhouse-runda', 1, 'house', 'pending', 'sale', 19500000.00, 'KSH', 3, 3, 2600, 'Runda Drive', 'Nairobi',
'A well-kept townhouse in a guarded community with clubhouse and children play area. Three en-suite bedrooms, servant quarters, and ample parking.', 0, 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=1400&q=80', '["https://images.unsplash.com/photo-1599809275671-b5942cabc7a2?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1400&q=80"]', 1),

('Studio Apartment, Kilimani', 'studio-apartment-kilimani', 2, 'apartment', 'available', 'rent', 45000.00, 'KSH', 1, 1, 450, 'Elgeyo Marakwet Road', 'Nairobi',
'Compact and fully finished studio near the Kilimani business district. Includes water heater, fibre internet ready and 24-hour security.', 0, 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1400&q=80', '["https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=1400&q=80"]', 1),

('Lakeside Villa, Munyonyo', 'lakeside-villa-munyonyo', 1, 'house', 'available', 'sale', 320000.00, 'USD', 5, 5, 5200, 'Church Road, Munyonyo', 'Kampala',
'An elegant lakeside villa on the shores of Lake Victoria in Munyonyo. Private jetty, infinity pool and landscaped tropical gardens. Open-plan living with floor-to-ceiling glass that frames the lake views.', 1, 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1400&q=80', '["https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1400&q=80"]', 1),

('Contemporary 2-Bed Apartment, Kololo', 'contemporary-2-bed-apartment-kololo', 2, 'apartment', 'available', 'rent', 120000.00, 'KSH', 2, 2, 1150, 'Prince Charles Drive, Kololo', 'Kampala',
'A modern two-bedroom apartment in the heart of Kololo, steps from Acacia Mall. Fitted kitchen, private balcony and secure basement parking with 24-hour security and backup power.', 0, 'https://images.unsplash.com/photo-1554995207-c18c203602cb?auto=format&fit=crop&w=1400&q=80', '["https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1400&q=80"]', 1),

('Commercial Office Space, Kampala CBD', 'commercial-office-space-kampala-cbd', 3, 'commercial', 'available', 'rent', 1850.00, 'USD', 0, 2, 2400, 'Plot 15, Parliamentary Avenue', 'Kampala',
'Fully finished Grade-A office floor in the CBD. Open-plan layout, meeting rooms and a boardroom, serviced lifts and ample visitor parking. Ideal for regional headquarters.', 0, 'https://images.unsplash.com/photo-1567767292278-a4f21aa2d36e?auto=format&fit=crop&w=1400&q=80', '["https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1497366811353-6870744d04b2?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1400&q=80"]', 1),

('Hillside Family House, Kacyiru', 'hillside-family-house-kacyiru', 1, 'house', 'available', 'sale', 270000.00, 'USD', 4, 4, 3800, 'KG 7 Avenue, Kacyiru', 'Kigali',
'A beautiful hillside family home with panoramic views over Kigali city. Spacious en-suite bedrooms, private garden, staff quarters and solar water heating.', 1, 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&w=1400&q=80', '["https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1400&q=80"]', 1),

('Serviced Apartment, Kimihurura', 'serviced-apartment-kimihurura', 2, 'apartment', 'available', 'rent', 900.00, 'USD', 2, 2, 980, 'KG 9 Avenue, Kimihurura', 'Kigali',
'Fully furnished serviced apartment in leafy Kimihurura. Includes housekeeping, high-speed fibre internet, gym and swimming pool access. One month deposit.', 0, 'https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde?auto=format&fit=crop&w=1400&q=80', '["https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=1400&q=80"]', 1),

('Development Plot, Gacuriro', 'development-plot-gacuriro', 3, 'land', 'available', 'sale', 8500000.00, 'KSH', 0, 0, 5400, 'Gacuriro Sector, Gasabo', 'Kigali',
'A serviced residential plot in Gacuriro with title deed. Flat terrain, gated neighbourhood with paved roads, drainage and street lighting. Ideal for a modern family home.', 0, 'https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=1400&q=80', '["https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1400&q=80"]', 1),

('Beachfront Villa, Oyster Bay', 'beachfront-villa-oyster-bay', 2, 'house', 'available', 'sale', 42000000.00, 'KSH', 6, 6, 6100, 'Haile Selassie Road, Oyster Bay', 'Dar es Salaam',
'A stunning beachfront villa in Oyster Bay with direct Indian Ocean access. Six en-suite bedrooms, private pool, staff quarters and rooftop terrace with sunset views.', 1, 'https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&w=1400&q=80', '["https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1400&q=80"]', 1),

('Modern Apartment, Masaki', 'modern-apartment-masaki', 1, 'apartment', 'available', 'rent', 1150.00, 'USD', 3, 3, 1650, 'Mikocheni Drive, Masaki', 'Dar es Salaam',
'Bright three-bedroom apartment in the vibrant Masaki district, close to restaurants and the waterfront. Balcony, fitted kitchen, borehole water and standby generator.', 0, 'https://images.unsplash.com/photo-1567496898669-ee935f5f647a?auto=format&fit=crop&w=1400&q=80', '["https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1400&q=80"]', 1),

('Upscale Office, Samora Avenue', 'upscale-office-samora-avenue', 3, 'commercial', 'available', 'rent', 1400.00, 'USD', 0, 2, 2100, 'Samora Avenue, City Centre', 'Dar es Salaam',
'Premium office suite right on Samora Avenue. Glass-fronted boardroom, meeting rooms, high-speed connectivity and professional reception. Perfect for corporate teams.', 0, 'https://images.unsplash.com/photo-1560184897-ae75f418493e?auto=format&fit=crop&w=1400&q=80', '["https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1497366811353-6870744d04b2?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1400&q=80"]', 1),

('Condo in Bole, Addis Ababa', 'condo-in-bole-addis-ababa', 1, 'apartment', 'available', 'sale', 185000.00, 'USD', 3, 3, 2400, 'Nigeria Street, Bole', 'Addis Ababa',
'Modern three-bedroom condominium in Bole with quick access to Bole International Airport. Gated compound, gym, children play area and 24-hour security.', 0, 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1400&q=80', '["https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=1400&q=80"]', 1),

('Serviced Apartment, Kazanchis', 'serviced-apartment-kazanchis', 2, 'apartment', 'available', 'rent', 2400.00, 'USD', 2, 2, 1350, 'Kazanchis, Haile G/Selassie Street', 'Addis Ababa',
'Executive serviced apartment in the international business district of Kazanchis. Includes cleaning, WiFi, airport transfers and access to the hotel-style pool and gym.', 0, 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1400&q=80', '["https://images.unsplash.com/photo-1554995207-c18c203602cb?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1400&q=80"]', 1),

('Commercial Plot, Legetafo', 'commercial-plot-legetafo', 3, 'land', 'available', 'sale', 15000000.00, 'KSH', 0, 0, 21500, 'Legetafo, North of Addis', 'Addis Ababa',
'A large titled commercial plot along the Legetafo road, ideal for warehouse or mixed-use development. Flat terrain, all-weather access and utilities to the gate.', 0, 'https://images.unsplash.com/photo-1600585154526-990dced4db0d?auto=format&fit=crop&w=1400&q=80', '["https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=1400&q=80"]', 1);

INSERT INTO `#__estate_listings`
(`title`, `alias`, `agent_id`, `property_type`, `status`, `sale_or_rent`, `price`, `currency`, `off_plan`, `developer`, `completion_date`, `payment_plan`, `bedrooms`, `bathrooms`, `area_sqft`, `address`, `city`, `description`, `featured`, `main_image`, `gallery_json`, `published`, `ordering`) VALUES
('Skyline Heights Residences, Upper Hill', 'skyline-heights-residences-upper-hill', 2, 'apartment', 'available', 'sale', 14500000.00, 'KSH', 1, 'Horizon Builds Ltd', '2027-06-30', 'Reserve from KSh 500,000. Pay a 30% deposit, then staged instalments through construction, with the balance on completion.', 3, 3, 2100, 'Upper Hill Road', 'Nairobi', 'A 24-storey off-plan development in Nairobi''s business district. Each residence features floor-to-ceiling glass, private balconies, rooftop gardens, gym, pool and a business lounge. Investment opportunity with flexible staged payment.', 0, 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=1400&q=80', '["https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1400&q=80"]', 0, 22),
('Riverside Terraces, Ntinda', 'riverside-terraces-ntinda', 3, 'apartment', 'available', 'sale', 185000.00, 'USD', 1, 'Nile Crest Developments', '2027-12-15', 'USD 10,000 reservation fee; 40% payable during construction in tranches; balance on handover.', 2, 2, 1900, 'Ntinda - Kisaasi Road', 'Kampala', 'Riverside Terrace apartments are a gated off-plan community along the Ntinda ridge. Private balconies overlooking the valley, borehole water, standby power and a children''s park. Strong rental yields expected on completion.', 0, 'https://images.unsplash.com/photo-1554995207-c18c203602cb?auto=format&fit=crop&w=1400&q=80', '["https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1400&q=80"]', 0, 23);

-- Demo 3D tour for the first off-plan listing (sample 360 panoramas).
INSERT INTO `#__estate_tours` (`listing_id`, `state`, `notes`)
SELECT id, 3, 'Demo tour using sample 360 panoramas.'
FROM `#__estate_listings`
WHERE `alias` = 'skyline-heights-residences-upper-hill'
LIMIT 1;
SET @estate_tour = LAST_INSERT_ID();
INSERT INTO `#__estate_tour_shots` (`tour_id`, `listing_id`, `slot`, `room_label`, `position_key`, `instructions`, `image`, `published`, `ordering`) VALUES
(@estate_tour, (SELECT id FROM `#__estate_listings` WHERE `alias` = 'skyline-heights-residences-upper-hill' LIMIT 1), 1, 'Entrance & Lobby', 'A1', 'Stand at the entrance door, camera at 1.5 m height, and shoot a full 360 panorama facing INTO the space before stepping in.', 'https://pannellum.org/images/alma.jpg', 1, 1),
(@estate_tour, (SELECT id FROM `#__estate_listings` WHERE `alias` = 'skyline-heights-residences-upper-hill' LIMIT 1), 2, 'Living / Lounge', 'A2', 'Move to the centre of the living area. Overlap at least 30% with the previous shot and keep the nodal point level.', 'https://pannellum.org/images/from-tree.jpg', 1, 2),
(@estate_tour, (SELECT id FROM `#__estate_listings` WHERE `alias` = 'skyline-heights-residences-upper-hill' LIMIT 1), 3, 'Kitchen & Dining', 'B1', 'Place the camera above the counter line, away from mirrors and shiny appliances, and capture the full space.', 'https://pannellum.org/images/charles-street.jpg', 1, 3),
(@estate_tour, (SELECT id FROM `#__estate_listings` WHERE `alias` = 'skyline-heights-residences-upper-hill' LIMIT 1), 4, 'Bedroom', 'B2', 'Shoot from the foot of the bed at eye level so the bed faces the camera; avoid duvets and clutter.', 'https://pannellum.org/images/cerro-toco-0.jpg', 1, 4),
(@estate_tour, (SELECT id FROM `#__estate_listings` WHERE `alias` = 'skyline-heights-residences-upper-hill' LIMIT 1), 5, 'Balcony / View', 'C1', 'Aim the camera level with the railing and stitch the view back into the room for a seamless walk-out.', 'https://pannellum.org/images/tocopilla.jpg', 1, 5);
