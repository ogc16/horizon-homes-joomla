# Horizon Homes Real Estate — Joomla 3-Tier Website

A real estate company website built on the **Joomla CMS** and structured around a
strict **3-tier architecture** (Presentation / Business Logic / Data).

## 🌐 The Three Tiers

This project keeps the three logical layers clearly separated, as Joomla expects:

| Tier | What it does | Where it lives |
|------|--------------|----------------|
| **1. Presentation** | Rendering & output formatting only. No business rules. | `templates/hornbill/` (site template) + `components/com_estate/site/tmpl/` (HTML tmpl files) |
| **2. Business Logic** | Models & controllers that hold rules (filtering, validation, security, access control). | `components/com_estate/site/src/` + `components/com_estate/admin/src/` |
| **3. Data** | Database schema, relationships and seed data. | `components/com_estate/sql/*.sql` + `admin/sql/` (MySQL) |

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
- [XAMPP](https://www.apachefriends.org/) (Apache + PHP 8.3+ + MySQL/MariaDB)
- Joomla 6.x

### 2. Get Joomla running first
1. Install XAMPP, start **Apache** and **MySQL** from the XAMPP Control Panel.
2. Download the [Joomla 6 package](https://downloads.joomla.org) and extract it to
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
1. Zip the **contents** of `components/com_estate` (the `estate.xml` manifest must
   sit at the zip root, alongside `site/`, `admin/` and `sql/`).
2. Log into the Joomla admin: `http://localhost/joomla/administrator`
3. **System → Install → Extensions → Upload Package File** and upload the zip.
   Joomla runs `sql/install.mysql.utf8.sql`, creating the three tables and
   seeding sample agents + listings.

> Alternative command line (XAMPP):
> `php C:\xampp\htdocs\joomla\cli\joomla.php extension:install --path=com_estate.zip`
>
> Alternative manual import: import `sql/install.mysql.utf8.sql` with phpMyAdmin,
> replacing `#__` with your Joomla table prefix (e.g. `jml_`).

### 5. Apply the site template
1. Admin → System → Site Templates → set **hornbill** as the default.
2. Publish menu items in **Menus → Main Menu** pointing to the component
   (listings view, and `task=listings.about` for the About page).

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
├── sql/                          # ★ DATA TIER schema + seed
│   ├── install.mysql.utf8.sql
│   └── uninstall.mysql.utf8.sql
├── site/                         # Site (Presentation + Business Logic)
│   ├── services/provider.php     # DI bootstrap
│   └── src/
│       ├── Controller/           # display + saveBooking (validation rules)
│       ├── Model/ListingsModel.php
│       └── View/Listings/HtmlView.php
│   └── tmpl/listings/            # default.php, item.php, about.php (markup)
└── admin/                        # Backend
    ├── services/provider.php     # DI bootstrap
    ├── src/Controller/ListingsController.php   # publish/unpublish/trash
    ├── src/Model/ListingsModel.php
    ├── src/View/Listings/HtmlView.php
    ├── tmpl/listings/default.php
    ├── forms/filter_listings.xml # searchtools filter form
    ├── config.xml, access.xml, estate.xml
    └── language/en-GB/...
```

## 🧭 Useful URLs (after install on XAMPP)
- Site listings: `http://localhost/joomla/index.php?option=com_estate&view=listings`
- Single listing: `http://localhost/joomla/index.php/component/estate?view=listings&alias=modern-4-bedroom-villa-in-karen`
- About: `http://localhost/joomla/index.php?option=com_estate&task=listings.about`
- Admin: `http://localhost/joomla/administrator`
