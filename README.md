# Horizon Homes Real Estate — Joomla 3-Tier Website

A real estate company website built on the **Joomla CMS** and structured around a
strict **3-tier architecture** (Presentation / Business Logic / Data).

## 🌐 The Three Tiers

This project keeps the three logical layers clearly separated, as Joomla expects:

| Tier | What it does | Where it lives |
|------|--------------|----------------|
| **1. Presentation** | Rendering & output formatting only. No business rules. | `templates/hornbill/` (site template) + `components/com_estate/site/views/` (HTML tmpl files) |
| **2. Business Logic** | Models & controllers that hold rules (filtering, validation, security, access control). | `components/com_estate/site/models/` + `site/controllers/` and `admin/` |
| **3. Data** | Database schema, relationships and seed data. | `components/com_estate/admin/sql/*.sql` (MySQL) |

### Data flow
```
[Presentation Tier]  ──renders──►  [Business Logic Tier]  ──queries──►  [Data Tier]
   template + views                    models/controllers                 MySQL tables
        ▲                                    │                                 │
        └──────────── user actions ◄─────────┴──────────── results ◄──────────┘
```

## 🏗 What's included

- **`com_estate` component** (business + data tiers)
  - `#__estate_listings` — the properties (houses, apartments, land, commercial)
  - `#__estate_agents` — the agents who list & manage properties
  - `#__estate_bookings` — visitor "book a viewing" enquiries submitted via a form
  - Public gallery view with live search/filter (city, type, sale/rent)
  - Single-listing detail page with agent card + viewing-enquiry form
  - About page
  - Joomla admin backend to list/publish/unpublish/delete listings
- **`hornbill` template** (presentation tier)
  - Responsive, modern real-estate layout (search bar, property cards, detail & booking UI)

## 🚀 Install on XAMPP

### 1. Requirements
- [XAMPP](https://www.apachefriends.org/) (Apache + PHP 8.0+ + MySQL/MariaDB)
- Joomla 4.x

### 2. Get Joomla running first
1. Install XAMPP, start **Apache** and **MySQL** from the XAMPP Control Panel.
2. Download the [Joomla 4 package](https://downloads.joomla.org) and extract it to
   `C:\xampp\htdocs\joomla` (or copy this project folder there).
3. Browse to `http://localhost/joomla` and complete the Joomla installer
   (create a database, e.g. `joomla`, and use the `utf8mb4` collation).

### 3. Copy this project into htdocs
Place/replace the files from this folder into your Joomla install:

| This project folder | Destination |
|---------------------|-------------|
| `templates/hornbill` | `C:\xampp\htdocs\joomla\templates\hornbill` |
| `components/com_estate` | `C:\xampp\htdocs\joomla\components\com_estate` |

### 4. Install the component (creates the database tables)
1. Log into the Joomla admin: `http://localhost/joomla/administrator`
2. **System → Install → Extensions → Upload Package File** and upload
   `components/com_estate/estate.xml` (or zip the whole `com_estate` folder first).
3. Joomla runs `sql/install.mysql.utf8.sql`, creating the three tables and
   seeding sample agents + listings.

> Alternative: import `sql/install.mysql.utf8.sql` manually with phpMyAdmin,
> replacing `#__` with your Joomla table prefix (e.g. `jos_`).

### 5. Apply the site template
1. Admin → System → Site Templates → set **horhobill** as the default.
2. Publish the **com_estate** component as a menu item under
   **Menus → Main Menu → Add New → Menus → Menu Item Type → Estate → Properties**.

### 6. Add property photos
Drop listing images into `images/properties/` (e.g. `karen-villa.jpg`) and update
the `main_image` column, or upload via the Joomla Media Manager and reference the
returned path.

## 🔑 Default users / roles (3-tier style access)
- **Public visitor** — browse/listings, search, submit a viewing enquiry.
- **Registered user** — same as public (extendable: save favourites).
- **Super User / Estate Manager** — backend to manage listings & agents.

## 📁 Folder map (component)
```
components/com_estate/
├── estate.xml                    # Install manifest (runs SQL, registers menu)
├── com_estate.php                # Site entry point
├── site/
│   ├── controller.php            # Site MVC controller (Business Logic)
│   ├── controllers/listings.php  # display + saveBooking (validation rules)
│   ├── models/listings.php       # queries / data access (Business Logic → Data)
│   ├── views/listings/
│   │   ├── view.html.php         # Presentation view
│   │   └── tmpl/                 # default.php, item.php, about.php (markup)
│   └── language/en-GB/...
└── admin/
    ├── controller.php
    ├── controllers/listings.php  # publish/unpublish/trash actions
    ├── models/listings.php
    ├── views/listings/view.html.php + tmpl/default.php
    ├── sql/install.mysql.utf8.sql    # ★ DATA TIER schema + seed
    ├── sql/uninstall.mysql.utf8.sql
    └── language/en-GB/...
```

## 🧭 Useful URLs (after install on XAMPP)
- Site listings: `http://localhost/joomla/index.php?option=com_estate&view=listings`
- Single listing: `...&view=listings&alias=modern-4-bedroom-villa-in-karen`
- About: `...&task=listings.about`
- Admin: `http://localhost/joomla/administrator`
