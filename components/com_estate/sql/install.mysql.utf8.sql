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
-- Sample seed data (only if tables are empty)
-- ---------------------------------------------------------------------
INSERT INTO `#__estate_agents` (`name`, `email`, `phone`, `bio`, `is_active`) VALUES
('Grace Wanjiru', 'grace@horizonhomes.ke', '+254 700 123 456', 'Senior property consultant with 10 years of experience in Nairobi residential sales.', 1),
('Brian Otieno', 'brian@horizonhomes.ke', '+254 711 654 321', 'Commercial leasing specialist covering Westlands and the CBD.', 1),
('Amina Hassan', 'amina@horizonhomes.ke', '+254 722 987 654', 'Land and plot advisor helping families find their forever investment.', 1);

INSERT INTO `#__estate_listings`
(`title`, `alias`, `agent_id`, `property_type`, `status`, `sale_or_rent`, `price`, `bedrooms`, `bathrooms`, `area_sqft`, `address`, `city`, `description`, `featured`, `main_image`, `gallery_json`, `published`) VALUES
('Modern 4-Bedroom Villa in Karen', 'modern-4-bedroom-villa-in-karen', 1, 'house', 'available', 'sale', 28500000.00, 4, 4, 4200, 'Miotoni Lane, Karen', 'Nairobi',
'The house is an elegant villa located in a quiet leafy estate in Karen. An open plan living and dining area flows onto a landscaped garden with a private pool. Finished with imported fittings, solid wood flooring and a gourmet kitchen.', 1, 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1400&q=80', '["https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=1400&q=80"]', 1),

('Sunny 2-Bedroom Apartment, Westlands', 'sunny-2-bedroom-apartment-westlands', 2, 'apartment', 'available', 'rent', 85000.00, 2, 2, 1100, 'Ring Road Parklands', 'Nairobi',
'A bright and modern 2-bedroom apartment in a secure gated development. Features a spacious balcony, fitted kitchen, and access to gym and rooftop lounge. Ideal for professionals.', 1, 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1400&q=80', '["https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1400&q=80"]', 1),

('Prime Commercial Plot, Konza', 'prime-commercial-plot-konza', 3, 'land', 'available', 'sale', 12000000.00, 0, 0, 15000, 'Konza Technopolis', 'Machakos',
'A titled 1.5-acre commercial plot inside Konza Technopolis, perfect for mixed-use development. Ready title deed, flat terrain and close to the main highway.', 0, 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1400&q=80', '["https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1400&q=80"]', 1),

('Cozy 3-Bedroom Townhouse, Runda', 'cozy-3-bedroom-townhouse-runda', 1, 'house', 'pending', 'sale', 19500000.00, 3, 3, 2600, 'Runda Drive', 'Nairobi',
'A well-kept townhouse in a guarded community with clubhouse and children play area. Three en-suite bedrooms, servant quarters, and ample parking.', 0, 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=1400&q=80', '["https://images.unsplash.com/photo-1599809275671-b5942cabc7a2?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1400&q=80"]', 1),

('Studio Apartment, Kilimani', 'studio-apartment-kilimani', 2, 'apartment', 'available', 'rent', 45000.00, 1, 1, 450, 'Elgeyo Marakwet Road', 'Nairobi',
'Compact and fully finished studio near the Kilimani business district. Includes water heater, fibre internet ready and 24-hour security.', 0, 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1400&q=80', '["https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=1400&q=80","https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=1400&q=80"]', 1);
