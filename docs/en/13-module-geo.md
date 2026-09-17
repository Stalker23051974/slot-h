<small>Previous articles:

- ➡️ [Section 10. Multi-project](/docs/10-multiproject)
- ➡️ [Section 11. Base Module](/docs/11-module-base)
- ➡️ [Section 12. Free Module](/docs/12-module-free)
</small>

# SLOT-H: Geo Module

The Geo module handles geographic data: countries, cities, regions, languages, currencies, and time zones. The demo version includes reduced functionality sufficient for basic scenarios. The full version includes all components for working with geographic data at any scale.

---

## Demo version composition

The demo version contains a minimal set for demonstrating multilingualism and project settings:

### Languages

Management of languages used in the system. The demo version includes basic languages (Russian, English, French, Spanish, Italian, German). Supports enabling and disabling languages for specific projects.

### Time zones

A list of available time zones with their offsets relative to UTC. Used for time settings in projects and for users.

### Demo tables

- `languages` - languages
- `t_languages` - language translations
- `timezones` - time zones

### Demo module structure
```text
Modules/Geo/
├── Bootstrap.php # Module loader
├── Crud.php # Access rights
├── Preloader.php # Preloading (authentication)
└── Models/
    ├── Language.php
    ├── TLanguage.php
    ├── Timezone.php
    └── DbTables/
        ├── Language.php
        ├── TLanguage.php
        └── Timezone.php
```

---

## Full module version

The full version of the Geo module includes all components for working with geographic data.

### Full module structure
```text
Modules/Geo/
├── Bootstrap.php # Module loader
├── Crud.php # Access rights
├── Preloader.php # Preloading (authentication)
├── Controllers/ # Controllers (admin section)
│   ├── Country.php # Country management
│   ├── Currency.php # Currency management
│   ├── CustomsZone.php # Customs zone management
│   └── Language.php # Language management
├── Models/ # Data models
│   └── DbTables/ # Table definitions
│       ├── City.php
│       ├── Country.php
│       ├── Currency.php
│       ├── CustomsZone.php
│       ├── Language.php
│       ├── State.php
│       ├── TCountry.php
│       ├── TCurrency.php
│       ├── TCustomsZone.php
│       ├── Timezone.php
│       ├── TLanguage.php
│       ├── TState.php
│       ├── TTimezone.php
│       └── Voice.php
├── Public/ # CSS, JS
│   ├── css/
│   │   └── Project_51/
│   │       ├── Country/
│   │       │   ├── edit.css
│   │       │   └── index.css
│   │       └── Language/
│   │           └── index.css
│   └── js/
│       └── Project_51/
│           ├── Country/
│           │   └── edit.js
│           ├── Currency/
│           │   └── edit.js
│           ├── CustomsZone/
│           │   └── edit.js
│           └── Language/
│               ├── edit.js
│               └── index.js
└── Views/ # Templates
    └── Project_51/
        ├── Country/
        │   ├── edit.phtml
        │   └── index.phtml
        ├── Currency/
        │   ├── edit.phtml
        │   └── index.phtml
        ├── CustomsZone/
        │   ├── edit.phtml
        │   └── index.phtml
        └── Language/
            ├── edit.phtml
            └── index.phtml
```
---

## Additional tables in the full version

- `city_*` - cities (sharded by country)
- `countries` - countries
- `counties` - counties (administrative units)
- `currency` - currencies
- `customs_zones` - customs zones
- `states` - regions (states, provinces, cantons)
- `t_countries` - country translations
- `t_currencies` - currency translations
- `t_customs_zones` - customs zone translations
- `t_timezones` - time zone translations
- `voices` - voice models
- `zips` - postal codes (worldwide)
- `country_v1v2` - mapping between two geo databases

---

## Two geo databases

The full version uses two geo databases:

1. **The first database** contains cities with names in the country's language (Russian, Chinese, English, etc.) with region associations.

2. **The second database** contains cities with English names, without region associations, but with geographic coordinates.

Switching between databases is done through project configuration.

Cities are stored in `city_*` tables, sharded by country. This allows working with millions of records without performance loss.

---

## Admin section

Geo module controllers are intended only for the admin section. They allow managing countries, currencies, customs zones, and languages through the admin interface.

Cities and regions do not have their own controllers in the full version, as they are not manually editable (millions of records). They are used as reference data for other modules.

---

## Multi-project

The Geo module, like any other system module, can work simultaneously on an unlimited number of projects. Each project can have its own language and currency settings, while geographic data remains shared across the entire system.

---

## Transition from demo to full version

The demo version contains only languages and time zones. The full version includes all geographic data.

Transitioning from the demo to the full version does not require code changes. Simply add the missing tables and DbTable classes. The system kernel already contains all the necessary logic for working with geographic data.

The Geo module demonstrates how a single module can be presented in a reduced form (for demonstration) and in full (for production), without changing the kernel architecture.

---

## What's next?

- ➡️ [Section 14. Creating a Module](/docs/14-module-custom)
- ➡️ [Section 15. Database Structure](/docs/15-database-diagram)
- ➡️ [Section 16. ORM Principles](/docs/16-orm-principles)