<small>Previous articles:

- ➡️ [Section 26. Localization Principles](/docs/26-localization-principle)
- ➡️ [Section 27. Queue Server Startup](/docs/27-queue-start)
- ➡️ [Section 28. Queue Server Tasks](/docs/28-queue-tasks)
</small>

# SLOT-H: Helpers (Curl, Date, File, Geo, Person)

In the system, helpers are a set of utilities for performing common operations. They contain no business logic and do not depend on modules, but are actively used by them to solve auxiliary tasks.

---

## Curl

The Curl helper provides a wrapper over cURL for making HTTP requests. Supports GET, POST, PUT, DELETE methods.

**Usage example:**

```php
$curl = new \Application\Helpers\Curl();
$result = $curl->setUrl('https://api.example.com/users')
    ->setData(['id' => 123])
    ->setHeader(['Authorization: Bearer token'])
    ->get();
```
The helper automatically processes the response, decodes JSON, and returns an array. On error, it returns an array with the curl_error key.

##Date
The Date helper provides methods for working with dates and times. Supports localization in different languages.

Main methods:

Date::dbDate() - formats date for database (Y-m-d)

Date::dbDateTime() - formats date and time for database (Y-m-d H:i:s)

Date::getHumanDate() - formats date for user display

Date::getTextDate() - text representation of date

Date::getStrToTime() - converts string to timestamp

Date::weekDays() - list of weekdays in the current language

Date::monthTitleReal() - list of months in the current language

The helper considers the user's language and returns localized day and month names.

##File
The File helper provides methods for working with files. Includes:

MIME type detection for various categories (images, documents, audio, video)

Human-readable file size formatting

Chunked file upload handling

Retrieving system log information

Class generation from data structure

Main methods:

File::getAllowedImageTypes() - list of allowed MIME types for images

File::getHumanSize() - converts bytes to readable format

File::getLogs() - retrieves list of logs with sizes

File::upload() - handles chunked upload

##Geo
The Geo helper provides methods for working with geographic data. The demo version implements basic functions for working with cities and regions.

Main methods:

Geo::getTown() - retrieves city information

Geo::getState() - retrieves region information

Geo::getCountries() - retrieves list of countries

In the full version, the helper is extended to work with geo-databases containing millions of records.

##Person
The Person helper provides methods for working with users and their sessions.

Main methods:

Person::quit() - terminates user session

Person::setAuth() - sets authentication

Person::getRoles() - retrieves role hierarchy

Person::clonePerson() - clones a user across projects

This helper is used for managing user sessions and authentication.

##General Principles
All helpers are static classes and do not require instantiation. They are called directly via the namespace.

Helpers do not depend on modules and can be used anywhere in the system. They do not store state and do not affect global data.

New helpers can be added to the Application/Helpers folder and will be available for use throughout the entire system.

##What's next?
- ➡️ [Section 30. Demo vs Full Version](/docs/30-camparison)
- ➡️ [Section 31. FAQ](/docs/31-faq)
- ➡️ [Section 32. Contributing](/docs/32-contributing)
