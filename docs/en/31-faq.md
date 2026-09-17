<small>Previous articles:

- ➡️ [Section 28. Queue Server Tasks](/docs/28-queue-tasks)
- ➡️ [Section 29. Helpers](/docs/29-utils-helpers)
- ➡️ [Section 30. Demo vs Full Version](/docs/30-comparison)
</small>

# SLOT-H: Frequently Asked Questions

---

### 1. What is this system?

It's a micro-framework for building multi-project ecosystems. It allows running an unlimited number of sites on a single core, with a unified user base and flexible configuration for each project.

---

### 2. Is this a CMS or a framework?

Neither in pure form. It's an ecosystem that combines features of a framework (core, routing, ORM) and a ready-made CMS (Base, Free, Geo modules, admin panel). You can use it as a foundation for your projects or as a ready-to-go website builder.

---

### 3. Which databases are supported?

The demo version uses only MySQL. The full version supports PostgreSQL and MongoDB. Switching between them is done via configuration, without code changes.

---

### 4. Do I need to pay for using it?

The demo version is distributed free of charge with open source code. The full version is commercial and includes additional modules, geo-databases, and technical support.

---

### 5. Can I use the demo version in production?

Technically yes, but we do not recommend it. The demo version is intended for evaluation, testing, and development. For production projects, use the full version.

---

### 6. How do I add my own module?

Create a folder in `Modules` with the module name, add `Bootstrap`, `Crud`, and `Preloader`. Detailed description is in the article "Creating a Module".

---

### 7. How does multi-project work?

Each project is a separate record in the `projects` table, a separate config in the `Config/Domens` folder, and a separate log folder. More details in the article "Multi-project".

---

### 8. Where are files stored?

Uploaded files are stored in the `Storage` folder. Logs are in `Config/Logs`. Database dumps are in `Config/Dump`.

---

### 9. How do I start the queue?

Add a task to cron:

```bash
php Application/Tools/cli.php -stage=<project_name> -module=Base -controller=Cli -action=demon
```
More details in the article "Queue Server Startup".

###10. How do I update the autoloader?
Run:

```bash
php Application/Tools/createAutoloader.php
```
This is required after adding, removing, or moving any class.

###11. Is there API documentation?
The demo version includes basic API controllers. Full API documentation is included in the full version.

###12. What access rights are available in the demo version?
The demo version implements basic protection at the routing level (checking module/controller/action existence, brutus protection). Extended role and permission systems are available in the full version.

###13. Can I use my own layout?
Yes. All templates are located in Views/Project_<project_number> folders. You can modify them or create your own. I prefer working directly, but you can also integrate template engines. More details in the article "Frontend Structure".

###14. What's up with the JS?
You can use any JS of your choice - from vanilla to React. I use my own set of functions I'm accustomed to, which makes the code almost obfuscated and minimalistic. It's a matter of choice. More details in the article "JS Library Choice".

###15. What if I find a bug?
Create an Issue on GitHub describing the problem. For urgent questions, use Email alex.nt1974@gmail.com.

###16. How do I transition from demo to full version?
Contact the developer. The transition does not require code changes - just add the missing modules and configure the settings.

###What's next?
- ➡️ [Section 32. Contributing](/docs/32-contributing)
- ➡️ [Section 33. License](/docs/33-license)
