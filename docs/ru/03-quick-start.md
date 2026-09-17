<small>Предыдущие статьи:

- ➡️ [Раздел 01. SOLT-H](/docs/01-introduction)
- ➡️ [Раздел 02. Быстрый старт](/docs/03-quick-start)
</small>
# SLOT-H: Быстрый старт
  
  #За 10 минут - работающий экземпляр.
  
  * * *
  
  ##1.  Требования
      
  
  PHP 7.0 и выше (адаптация к 8 версии работает через прослойку)  
  MySQL 8.0+  
  Apache или Nginx с mod\_rewrite
  
  * * *
  
  ##2.  Клонирование
      
  ```bash
  git clone [https://github.com/your-username/siteconstructor.git](https://github.com/your-username/siteconstructor.git)  
  cd siteconstructor
  ```
  * * *
  
  ##3.  Создание таблиц
      
  
  Запустить SQL-скрипт из репозитория, который содержит полную структуру базы данных и необходимые начальные данные (инсерты).
  
  Скрипт находится в папке Config/Schema/install.sql
  
  Выполнить скрипт через консоль:
  ```bash
  mysql -u root -p < Config/Schema/install.sql
  ```
  Или через phpMyAdmin / любой другой клиент - импортировать файл Config/Schema/install.sql
  
  * * *
  
  ##4.  Настройка проекта
      
  
  В таблице projects заменить хардкодное значение в поле project\_landing на реальный адрес, по которому будет доступен сайт.
  
  Примеры:  
  127.0.0.1  
  256.256.256.256:8155  
  [mysite.com](https://mysite.com/)
  
  SQL-запрос для замены:
  ```mysql
  UPDATE projects SET project_landing = '127.0.0.1' WHERE project_id = 1;
  ```
  
  * * *
  
  ##5.  Создание виртуального хоста
      
  
  Настроить виртуальный хост на адрес, указанный в project\_landing. Порт не имеет значения - система работает в любом формате.
  
  Apache:
  ```apache
  <VirtualHost \*:80>  
      DocumentRoot /var/www/siteconstructor  
      ServerName 127.0.0.1  
      <Directory /var/www/siteconstructor>  
          AllowOverride All  
          Require all granted  
      </Directory>  
  </VirtualHost>
  ```
  Nginx:
  ```nginx
  server {  
      listen 80;  
      server\_name 127.0.0.1;  
      root /var/www/siteconstructor;
  
      index index.php;
  
      location / {  
          try\_files 𝑢𝑟𝑖uriuri/ /index.php?$query\_string;  
      }
  
      location ~ .phpdocument\_root$fastcgi\_script\_name;  
      include fastcgi\_params;  
  }
  ```
  * * *
  
  ##6.  Создание папок для логов и дампов
      
  
  Создать папку Config/Logs/<адрес сайта>
  
  Формат адреса:  
  127.0.0.1  
  256.256.256.256.8155 (двоеточие заменяется на точку при использовании порта)  
  [mysite.com](https://mysite.com/)
  ```bash
  mkdir -p Config/Logs/127.0.0.1  
  mkdir -p Config/Dump
  ```
  * * *
  
  ##7.  Права доступа
      
  
  Рекурсивно назначить владельца www-data:www-data на папки:
  ```bash
  sudo chown -R www-data:www-data Config/Logs  
  sudo chown -R www-data:www-data Config/Dump
  ```
  * * *
  
  ##8.  Настройка подключения к базам данных
      
  
  Открыть файл Config/Domens/cli.ini и прописать актуальные значения доступа к базам данных.
  
  В системе используется две базы:  
  Секция base - основная база данных сайта  
  Секция geo - общая база с геоданными (страны, города, языки, валюты)
  
  Пример cli.ini:
  ```config
  [base]  
  db_engine = "Mysql_Mysqli"  
  db_host = "localhost"  
  db_port = 3306  
  db_name = "siteconstructor_base"  
  db_login = "root"  
  db_password = "your_password"
  
  [geo]  
  db_engine = "Mysql_Mysqli"  
  db_host = "localhost"  
  db_port = 3306  
  db_name = "siteconstructor_geo"  
  db_login = "root"  
  db_password = "your_password"
  ```
  * * *
  
  ##9.  Создание конфига проекта
      
  
  Скопировать отредактированный файл cli.ini в файл с именем, соответствующим адресу сайта (как называлась папка логов):
  
  Для IP-адреса:
  ```bash
  cp Config/Domens/cli.ini Config/Domens/127.0.0.1.ini
  ```
  Для IP-адреса с портом:
  ```bash
  cp Config/Domens/cli.ini Config/Domens/256.256.256.256.8155.ini
  ```
  Для домена:
  ```bash
  cp Config/Domens/cli.ini Config/Domens/mysite.com.ini
  ```
  * * *
  
  ##10.  Генерация автолоадера
      
  
  Запустить консольный скрипт:
  ```bash
  php Application/Tools/createAutoloader.php
  ```
  * * *
  
  ##11.  Запуск
      
  
  Открыть браузер по адресу, указанному в project\_landing.
  
  http://<ваш сайт>
  
  Поздравляю - проект развёрнут!
---

## Что дальше?

- ➡️ [Раздел 04. Философия](/docs/04-philosophy)
- ➡️ [Раздел 05. Принципы работы](/docs/05-principles)
- ➡️ [Раздел 06. Архитектура](/docs/06-architecture)
