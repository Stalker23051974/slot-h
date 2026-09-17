<small>Previous articles:

- ➡️ [Section 01. SLOT-H](/docs/01-introduction)
</small>

# SLOT-H: What you need to run it

---

## Introduction: Don't panic, nothing complicated here

SLOT-H is not a monster that requires a dedicated cluster and a DevOps team. It's a pragmatic tool that runs on what you already have.

If your hosting can handle WordPress - it can handle SLOT-H. If you deploy projects on VPS - no problem at all. If you have a local development server - it will run in five minutes.

**Spoiler:** the requirements are minimal. All you need is a web server, PHP, and MySQL. Everything else is optional.

---

## Minimum Requirements

- **Web server:** Apache 2.x or Nginx. Either one. Configuration is trivial.

- **PHP:** 7.4.x or 8.x.x. Both branches are supported. The current core version runs on PHP 8.x.

- **Database:** MySQL 8.0+ (for demo). 5.7 will work too, but version 8 is faster and more convenient. For the full version - PostgreSQL, MongoDB.

- **Resources:** 2 vCPU, 4 GB RAM. The server currently running the core has these specs. No performance drops.

---

## How much space does the core take?

**Base package - 4 MB.**

Yes, you heard that right. Four megabytes.

That's without vendor folders, without node_modules, without Composer, without hundreds of tiny files. Everything needed to run is already included. All static files, all icons, all libraries.

4 MB.

**Important:** The demo version does not include the `Storage/` folder. It's not needed because the demo does not include:

- Image storage
- File storage
- Report generation

Everything required for the demo is already inside the core.

---

## Which PHP modules are required

Here's the full list of modules running on the server where SLOT-H has been running for years. In practice, you'll need a subset, but better to have them all - they won't hurt.

**Installed and running:**

`calendar, Core, ctype, curl, date, exif, FFI, fileinfo, filter, ftp, gd, gettext, gmagick, hash, iconv, intl, json, libxml, mbstring, mysqli, mysqlnd, openssl, pcntl, pcre, PDO, pdo_mysql, Phar, posix, psr, random, readline, Reflection, session, shmop, sockets, sodium, SPL, standard, sysvmsg, sysvsem, sysvshm, tokenizer, uploadprogress, xdebug, Zend OPcache, zlib`

**What is actually required for the demo:**

- `mysqli` - for MySQL access
- `pdo_mysql` - alternative MySQL driver
- `curl` - for HTTP requests and APIs
- `gd` - for image processing
- `mbstring` - for multibyte strings
- `json` - JSON support
- `session` - user sessions
- `openssl` - encryption and security
- `intl` - internationalization
- `zlib` - compression

**Optional:**

- `gmagick` - advanced image processing (if available - good, if not - GD will do)

**Full version additionally requires:**

- `pdo_pgsql` - for PostgreSQL
- `mongodb` - for MongoDB
- `redis` - for Redis caching
- `memcached` - for Memcached caching

---

## Which MySQL version

**MySQL 8.0+.** If you have 5.7 - it will work too, but we recommend version 8. It's faster, more convenient, and supports modern features.

If you use **MariaDB** - it will work if you have version 10.3+.

---

## Web server configuration

Minimal Apache configuration for a single project:

```apache
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/your_project_path
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>

<Directory /var/www/your_project_path>
    RewriteEngine on
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```
For Nginx - similar. The main rule: all requests must go through index.php, unless the file exists physically.

The project root contains .htaccess which already does everything needed:

```apache
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule .*? index.php
```
##Git and deployment
The core is deployed via Git from the repository:

```bash
git clone https://github.com/yourname/slot-h.git
```
No Composer. No npm install. No vendor folders.

Clone it - and it runs.

##What if I'm using shared hosting?
If you don't have a VPS but a regular shared hosting with a control panel - it works too.

The only mandatory condition:

Access to run console scripts.

Without this, SLOT-H will not start.

Why? Because after deployment you need to run createAutoloader.php. This is a console script that builds the class dependency graph. Without it, the autoloader won't be generated, and the system won't start.

If you have SSH access - it's simple:

```bash
php Application/Tools/createAutoloader.php
```
If SSH is not available - the hosting control panel should have a terminal emulator. Many panels (cPanel, ISPmanager, etc.) provide this feature.

If you have neither SSH nor a terminal emulator - SLOT-H will not run on that hosting. This is the only hard limitation.

**Everything else doesn't matter:**

- You can upload files via FTP

- Through the file manager

- Or via Git from the panel

The main thing - **run createAutoloader once**.

##Summary
Minimum setup to start:

**PHP 7.4 / 8.x** + modules: mysqli, curl, gd, mbstring, json, session, openssl

**MySQL 8.0**

**Apache / Nginx**

**Git** (for deployment)

**Console access** (SSH or terminal emulator)

**2 vCPU, 4 GB RAM** - more than enough

**4 MB disk space** - base package

Everything else is optional and added as needed.

**Spoiler**: if you have something that can run WordPress - SLOT-H will run without issues, with one condition: you have console access.

## What's next?
- ➡️ [Section 03. Quick Start](/docs/03-quick-start)
- ➡️ [Section 04. Philosophy](/docs/04-philosophy)
- ➡️ [Section 05. Principles of Operation](/docs/05-principles)
