<small>Previous articles:

- ➡️ [Section 01. SLOT-H](/docs/01-introduction)
- ➡️ [Section 02. Requirements](/docs/02-requirements)
</small>

# SLOT-H: Quick Start

## A working instance in 10 minutes.

---

## 1. Requirements

PHP 7.0 and above (adaptation to version 8 works through a compatibility layer)  
MySQL 8.0+  
Apache or Nginx with mod_rewrite

---

## 2. Cloning

```bash
git clone https://github.com/your-username/slot-h.git
cd slot-h
```
##3. Creating tables
Run the SQL script from the repository that contains the full database structure and necessary initial data (inserts).

The script is located in Config/Schema/install.sql

Run the script via the console:

```bash
mysql -u root -p < Config/Schema/install.sql
```
Or via phpMyAdmin / any other client - import the file Config/Schema/install.sql

##4. Project configuration
In the projects table, replace the hardcoded value in the project_landing field with the actual address where the site will be accessible.

Examples:
127.0.0.1, 256.256.256.256:8155 or mysite.com

SQL query for replacement:

```sql
UPDATE projects SET project_landing = '127.0.0.1' WHERE project_id = 1;
```
##5. Creating a virtual host
Configure a virtual host on the address specified in project_landing. The port doesn't matter - the system works in any format.

Apache:

```apache
<VirtualHost *:80>
    DocumentRoot /var/www/slot-h
    ServerName 127.0.0.1
    <Directory /var/www/slot-h>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```
Nginx:

```nginx
server {
    listen 80;
    server_name 127.0.0.1;
    root /var/www/slot-h;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```
##6. Creating folders for logs and dumps
Create the folder Config/Logs/<site_address>

Address format:
- 127.0.0.1
- 256.256.256.256.8155 (colon replaced with a dot when using a port)
- mysite.com

```bash
mkdir -p Config/Logs/127.0.0.1
mkdir -p Config/Dump
```
##7. Permissions
Recursively assign the owner www-data:www-data to the folders:

```bash
sudo chown -R www-data:www-data Config/Logs
sudo chown -R www-data:www-data Config/Dump
```
##8. Database connection configuration
Open the file Config/Domens/cli.ini and enter the actual database access values.

The system uses two databases:
The base section - the main site database
The geo section - the shared geo database (countries, cities, languages, currencies)

Example cli.ini:

```ini
[base]
db_engine = "Mysql_Mysqli"
db_host = "localhost"
db_port = 3306
db_name = "slot_h_base"
db_login = "root"
db_password = "your_password"

[geo]
db_engine = "Mysql_Mysqli"
db_host = "localhost"
db_port = 3306
db_name = "slot_h_geo"
db_login = "root"
db_password = "your_password"
```
##9. Creating the project config
Copy the edited cli.ini file to a file named after the site address (same as the logs folder name):

For IP address:

```bash
cp Config/Domens/cli.ini Config/Domens/127.0.0.1.ini
```
For IP address with port:

```bash
cp Config/Domens/cli.ini Config/Domens/256.256.256.256.8155.ini
```
For domain:

```bash
cp Config/Domens/cli.ini Config/Domens/mysite.com.ini
```
##10. Generating the autoloader
Run the console script:

```bash
php Application/Tools/createAutoloader.php
```
##11. Launch
Open your browser at the address specified in project_landing:

http://<your-site>

Congratulations - the project is deployed!

##What's next?

- ➡️ [Section 04. Philosophy](/docs/04-philosophy)
- ➡️ [Section 05. Principles of Operation](/docs/05-principles)
- ➡️ [Section 06. Architecture](/docs/06-architecture)