# Horizon Homes Real Estate — Joomla 3-Tier Website

[![CI](https://github.com/ogc16/horizon-homes-joomla/actions/workflows/ci.yml/badge.svg)](https://github.com/ogc16/horizon-homes-joomla/actions/workflows/ci.yml)
[![CodeQL](https://github.com/ogc16/horizon-homes-joomla/actions/workflows/codeql.yml/badge.svg)](https://github.com/ogc16/horizon-homes-joomla/actions/workflows/codeql.yml)
[![Security](https://github.com/ogc16/horizon-homes-joomla/actions/workflows/security.yml/badge.svg)](https://github.com/ogc16/horizon-homes-joomla/actions/workflows/security.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![Joomla 6](https://img.shields.io/badge/Joomla-6.x-blue.svg)](https://www.joomla.org/)
[![PHP 8.3+](https://img.shields.io/badge/PHP-8.3%2B-purple.svg)](https://php.net/)

A real estate company website built on the **Joomla CMS** and structured around a
strict **3-tier architecture** (Presentation / Business Logic / Data).

> 📄 See [`problem.md`](problem.md) for the asynchronous listing-ingestion
> challenge this project is evolving toward.

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

## 📐 UML Diagrams

### Use Case Diagram

```mermaid
flowchart LR
    V(["Visitor"])
    M(["Manager"])
    S(["System"])

    subgraph HH["Horizon Homes"]
        direction TB
        UC1(["Browse Listings"])
        UC2(["Search / Filter"])
        UC3(["View Property Detail"])
        UC3b(["View Map (lat/lng)"])
        UC4(["Book a Viewing"])
        UC5(["Compare Properties"])
        UC6(["Switch Currency"])
        UC7(["Manage Listings"])
        UC8(["Publish / Unpublish"])
        UC9(["Edit Listing (lat/lng)"])
        UC10(["Ingest Listings (async)"])
    end

    V --> UC1
    V --> UC2
    V --> UC3
    UC3 --> UC3b
    V --> UC4
    V --> UC5
    V --> UC6

    M --> UC7
    M --> UC8
    M --> UC9

    S --> UC10
```

### Class Diagram (com_estate component)

```mermaid
classDiagram
    namespace Site {
        class ListingsController {
            +display()
            +home()
            +about()
            +offplan()
            +compare()
            +saveBooking()
        }
        class ListingsModel {
            +getListings(filters, limit) object[]
            +getListing(alias) object
            +getGallery(item) string[]
            +getAgents() object[]
            +getStats() object
            +getTour(id) object
            +countOffPlan() int
        }
        class ListingsHtmlView {
            +display(tpl)
            +formatPrice(price, currency) string
            +currencyLabel() string
            +tourConfig(tour) string
        }
        class CurrencyHelper {
            +format(price, currency) string$
        }
    }

    namespace Admin {
        class ListingController {
            +edit()
            +save()
            +apply()
            +cancel()
            -persist() int
        }
        class ListingModel {
            +getItem(id) object
            +getAgents() object[]
            +aliasInUse(alias, id) bool
            +nextFreeAlias(alias, id) string
            +save(data) int
        }
        class AdminListingsController {
            +publish()
            +unpublish()
            +trash()
        }
        class AdminListingsModel {
            +getItems() object[]
        }
    }

    namespace Templates {
        class TemplateHornbill {
            +index.php
            +template.css
            +estateCompareJs
            +estateI18nJs
        }
    }

    ListingsController --> ListingsModel : uses
    ListingsController --> ListingsHtmlView : renders
    ListingsHtmlView --> CurrencyHelper : formats prices
    ListingController --> ListingModel : uses
    AdminListingsController --> AdminListingsModel : uses
    ListingsController ..> TemplateHornbill : presentation
```

### Sequence Diagram (listing detail page request)

```mermaid
sequenceDiagram
    actor Browser
    participant Nginx
    participant JoomlaApp as Joomla (app.php)
    participant Ctrl as ListingsController
    participant Model as ListingsModel
    participant DB as MySQL
    participant View as ListingsHtmlView
    participant Tmpl as item.php

    Browser->>Nginx: GET /?view=listings&alias=karen-villa
    Nginx->>JoomlaApp: FastCGI pass (index.php)
    JoomlaApp->>JoomlaApp: Load autoload_psr4.php map<br/>(plugins incl. behaviour/taggable)
    JoomlaApp->>Ctrl: display()
    Ctrl->>Model: getListing(alias)
    Model->>DB: SELECT a.*, ag.name ... WHERE alias = :alias
    DB-->>Model: row
    Model-->>Ctrl: item
    Ctrl->>Model: getTour(id)
    Model-->>Ctrl: tour | null
    Ctrl->>View: set(item, tour) / setLayout('item')
    View->>Tmpl: render()
    Tmpl-->>Browser: HTML (gallery, price, specs,<br/>map (Leaflet lat/lng), enquiry form)
```

### Sequence Diagram (compare feature — client + server)

```mermaid
sequenceDiagram
    actor User
    participant Card as Card Checkbox
    participant JS as estate-compare.js
    participant LS as localStorage
    participant Tray as Compare Tray
    participant Ctrl as ListingsController::compare()
    participant DB as MySQL

    User->>Card: Check "Compare" on 2 properties
    Card->>JS: change event
    JS->>LS: toggleId() → [12, 34]
    JS->>Tray: renderTray() (count, thumbnails, Compare Now)
    User->>Tray: Click "Compare Now"
    Tray->>Ctrl: GET ?task=listings.compare&ids=12,34
    Ctrl->>DB: SELECT ... WHERE id IN (12,34) AND published=1
    DB-->>Ctrl: rows (ordered by id list)
    Ctrl-->>User: compare.php (side-by-side table)
```

### Component / Deployment Diagram

```mermaid
flowchart LR
    subgraph "Azure App Service (Linux, PHP 8.3)"
        NG[Nginx :8080]
        PHP[PHP-FPM<br/>Joomla 6 + com_estate + hornbill]
        NG --> PHP
    end

    subgraph "Data Tier"
        DB[(MySQL 8.4 Flexible<br/>#__estate_listings<br/>#__estate_agents<br/>#__estate_bookings<br/>#__estate_tours)]
    end

    subgraph "Async Ingestion (challenge)"
        API[Webhook / CSV / REST Producer]
        Q[(Queue: Redis Streams<br/>or Azure Service Bus)]
        W[Worker: validate → normalise → upsert]
        QUAR[Quarantine Table]
        API --> Q --> W
        W --> QUAR
    end

    B[(Visitor Browser)] --> NG
    PHP --> DB
    W --> DB

    subgraph "Repo layout"
        T[Presentation: templates/hornbill/]
        BL[Business: com_estate site/src + admin/src]
        D[Data: com_estate sql/]
        T --- BL --- D
    end
```

### State Diagram (listing lifecycle)

```mermaid
stateDiagram-v2
    [*] --> Draft : Admin creates listing
    Draft --> Published : publish
    Draft --> Trashed : trash
    Published --> Unpublished : unpublish
    Published --> Pending : status = pending
    Published --> Sold : status = sold
    Published --> Rented : status = rented
    Unpublished --> Published : republish
    Trashed --> Draft : restore
    Pending --> Published : status = available
    Sold --> [*]
    state "Ingested (async)" as Ingested
    [*] --> Ingested : ingestion service<br/>published = 0
    Ingested --> Published : operator approval<br/>or trusted source auto-publish
    Ingested --> Quarantined : validation failed
    Quarantined --> Ingested : corrected re-ingest
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

## 🐳 Run with Docker (recommended)

The whole stack — Joomla 6, the `com_estate` component and a MariaDB database
pre-loaded with the sample data — boots with one command. No local PHP,
Joomla or MySQL install required.

```bash
# Optional: override the default ports/credentials first
cp .env.example .env

docker compose up -d --build
```

Then open **http://localhost:8080** (admin at `/administrator`).

| Setting | Default |
|---------|---------|
| Site URL | http://localhost:8080 |
| Admin URL | http://localhost:8080/administrator |
| Admin user | `admin` |
| Admin password | `Admin1234!Horizon` |
| DB name / user / pass | `joomla` / `joomla` / `joomlapass` |

### How it works

- **`Dockerfile`** — based on the official `joomla:6.1-php8.3-apache` image.
  Custom files are staged under `/usr/src/custom` because the base image
  mounts `/var/www/html` as a volume.
- **`docker/entrypoint.sh`** — copies the Joomla core, syncs the component,
  template and assets into the webroot, removes the web installer, generates
  `configuration.php` from env vars, waits for the database and starts Apache.
- **`docker/mysql/initdb/01-joomla.sql`** — the database dump restored on the
  first boot (menus, template assignment and `#__estate_*` seed data included).
- **`docker-compose.yml`** — MariaDB 10.11 + the app, with health checks.

### Useful commands

```bash
docker compose logs -f joomla     # follow the app logs
docker compose down               # stop (keeps the database volume)
docker compose down -v            # stop and wipe the database
docker compose up -d --build      # rebuild after changing source files
```

> Need to reset to a clean database? `docker compose down -v && docker compose up -d`.

## ✅ Quality & continuous integration

Every push and pull request runs a full quality gate through GitHub Actions:

| Workflow | What it enforces |
|----------|------------------|
| `ci.yml` | PHP 8.3/8.4 syntax matrix, PHPCS (PSR-12), ShellCheck, Hadolint, EditorConfig, Yamllint, plus a Docker build with PHPStan (level 6) and an end-to-end smoke test |
| `codeql.yml` | CodeQL static analysis (JavaScript/TypeScript) |
| `security.yml` | Gitleaks secret scan, Trivy filesystem scan, container SBOM, OpenSSF Scorecard |
| `docker-publish.yml` | Multi-arch image (amd64/arm64) with provenance and SBOM, signed with Cosign (keyless); publishes a GitHub Release on `v*` tags |
| `deploy.yml` | Manual, environment-gated deployment to Azure App Service |

The same checks run locally through the `Makefile`:

```bash
make lint             # PHPCS, ShellCheck, Hadolint, EditorConfig, Yamllint
make lint-php         # PHPCS (PSR-12) only
make php-lint         # php -l over every component file
make phpstan          # static analysis inside the running container
make smoke            # route + content smoke test against the running stack
```

Linting and formatting rules live in `.editorconfig`, `phpcs.xml.dist`,
`.editorconfig-checker.json`, `.shellcheckrc`, `.hadolint.yaml` and
`.yamllint.yaml`. Install the git hooks with
`pip install pre-commit && pre-commit install`.

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
- Compare: `http://localhost/joomla/index.php?option=com_estate&view=listings&task=listings.compare&ids=1,2,3`
- Admin: `http://localhost/joomla/administrator`

## 🧩 Key features
- **Search & filter** — city, type, sale/rent, off-plan toggle
- **Property detail** — gallery + lightbox, 360° Pannellum tour, agent card, enquiry form
- **Map** — Leaflet/OpenStreetMap embed pinned to `latitude` / `longitude`
- **Compare** — select up to 4 listings and view a side-by-side table
- **Multi-currency** — KSh / USh / TSh / RWF with geo-IP auto-detection
- **Admin CRUD** — full backend to create, edit, publish, and trash listings

## 🤝 Contributing

Contributions are welcome! Please read [CONTRIBUTING.md](CONTRIBUTING.md) for
branch naming, coding conventions, and the PR checklist. All participation is
governed by our [Code of Conduct](CODE_OF_CONDUCT.md).

## 📄 License

This project is licensed under the [MIT License](LICENSE) — free to use, modify,
and distribute, with attribution.

