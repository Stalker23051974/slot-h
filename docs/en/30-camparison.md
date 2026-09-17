<small>Previous articles:

- ➡️ [Section 27. Queue Server Startup](/docs/27-queue-start)
- ➡️ [Section 28. Queue Server Tasks](/docs/28-queue-tasks)
- ➡️ [Section 29. Helpers](/docs/29-utils-helpers)
</small>

# SLOT-H: Demo vs Full Version

The demo version of the system is a minimally working set of components sufficient to demonstrate the architecture and principles of operation. The full version includes all modules and functions used in production projects.

---

## Key Differences

The system core in the demo version is identical to the full version. Differences only affect the set of modules and their content.

The demo version includes three modules: Base (reduced), Free (customized), and Geo (reduced). The full version includes all modules, including Main, Billing, as well as modules focused on integration solutions with 1C, AmoCRM, Bitrix, and several popular foreign CRMs.

---

### Base Module

The demo version of the Base module contains only the basic tables and minimal logic for working with users, roles, and queues.

The full version includes:
- extended user management system
- A/B testing
- SEO management
- and several other extensions

---

### Free Module

In the demo version, the Free module contains a minimal set of pages: home page, About, Docs, privacy policy, and terms of use. This module was created specifically for demonstration purposes.

---

### Geo Module

The demo version of the Geo module contains only tables with languages and time zones.

The full version includes a complete set of geographic data:
- countries
- regions
- cities (millions of records)
- currencies
- voice models
- customs zones

The full version implements controllers and views for managing all data in this module.

---

### Main Module

This module is absent in the demo version. It represents the resource management system: the administrative part. It includes systems for managing Base module data and data at the intersection of modules (relationships).

---

### Helpers

The demo version provides basic helpers: Curl, Date, File, Geo, Person.

The full version includes additional helpers for:
- push notifications
- voice
- charts
- PDF (xls/doc/rtf) generation
- messenger integration

The full version helpers implement interaction with third-party services:
- sending Email
- SMS
- maps (OpenStreet, GoogleMap, YandexMap)
- payment gateways PayPal, Stripe, Alfa-Bank

These capabilities are implemented via factory classes, with selection determined by the project's ini file.

---

### Queues

The demo version only includes the database dump task.

The full version includes all task types:
- email sending
- image processing and normalization
- automatic translation
- currency parsing
- weather parsing
- and others

---

### Security

The demo version implements basic protection: dynamic signature, routing-level access protection.

The full version includes additional mechanisms:
- device fingerprint based on parameters from CPU cores to graphics driver features, making forgery impossible
- two-factor authentication
- OAuth
- extended role and permission system
- detailed logging

---

### Administrative Part

The demo version has no administrative part. The full version includes a full-featured admin panel for managing users, roles, constants, translations, texts, FAQ, notifications, meta-data, short links, banners, invoices, and imports.

---

### Frontend

Both versions use the same JS wrapper over jQuery.

---

## Comparison Table

| Component | Demo Version | Full Version |
|-----------|--------------|--------------|
| Base Module | Basic | Extended |
| Free Module | Minimal | Full |
| Geo Module | Languages and time zones only | Countries, regions, cities, currencies |
| Main Module | Absent | Present |
| Billing Module | Absent | Present |
| Helpers | Basic | All |
| Queues | Dump only | All task types |
| Security | Basic | Extended |
| Administrative Part | Absent | Full-featured |
| Geo Data | Only reference data | Millions of records |
| Documentation | Complete | Complete |

---

## What Remains Unchanged

The system core is completely identical in both versions. All principles of operation: routing, ORM, autoloader, multi-project, parametrization - work the same way.

Transitioning from the demo version to the full version does not require code changes. Simply add the missing modules, tables, and configure the settings. This makes the demo version a good foundation for a quick start.

---

## What's next?

- ➡️ [Section 31. FAQ](/docs/31-faq)
- ➡️ [Section 32. Contributing](/docs/32-contributing)
- ➡️ [Section 33. License](/docs/33-license)