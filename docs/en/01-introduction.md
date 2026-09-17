# SLOT-H: Single Logical Operating Tool for Hosting

---

## What the hell is this?

SLOT-H is not a framework. If you came here looking for another "pipeline-service-container-injection" - you're in the wrong place.

This is a **combat-ready ecosystem** born from 25 years of pain, 15 years of continuous production, and hundreds of projects that needed to be maintained.

It does one thing and does it well: **allows a single codebase to run on multiple projects simultaneously**.

Write the code once. SLOT-H deploys it across dozens of sites. One database. One logic. One entry point.

No microservices. No containerization. No headaches that usually come with them.

---

## 100% Russian-built

This is not just words. This is a fact that sets SLOT-H apart from 99% of projects on GitHub.

**All SLOT-H code has been written from scratch in Russia. Since 2011.**

The repository contains:

- No third-party packages via Composer
- No libraries written outside Russia
- No commits from foreign developers
- No dependencies that need to be installed separately

**Exceptions:**

1. **jQuery 1.12.4** - the only third-party library used in the core. It's embedded and does not require separate installation.

2. **In the full version**, plugins built on top of jQuery (autocomplete, chosen, datetimepicker, etc.) may be used. They are optional and only added when needed for specific frontend tasks.

**No Composer. No vendor folders eating hundreds of megabytes. No "let's pull in another dependency because it's convenient".**

Every line of code in the SLOT-H core was written manually, deliberately, and with a clear understanding of why it exists. This is not "assembled from ready-made components." This is "designed, written, debugged, and maintained for 15 years."

**This is not patriotism. This is pragmatism.**

When you use someone else's library, you take on the responsibility to:

- Track its updates
- Find a replacement if the author stops maintaining it
- Dig through someone else's code when something breaks
- Drag in dependencies that pull in their own dependencies

SLOT-H doesn't have these problems. Everything in the core was written here, for specific tasks. If something breaks - it's fixed here. If something needs to be added - it's added without worrying about compatibility with a dozen external packages.

---

## Philosophy: "Convention Over Configuration"

This is where many people start to twitch because they're used to "right" and "wrong." Forget about that.

SLOT-H has a simple idea:

> **The core knows what's right. You just describe the logic.**

What does this mean in practice:

**1. You work in modules.** All your business logic lives in controllers, models, and views. You don't think about routing, autoloading, or CSRF protection - the core has already handled it. Because it's been doing it for 15 years and knows what it's doing.

**2. The core decides what's right.** You don't configure routes, you don't tweak .htaccess for every sneeze. You just put the file where it belongs and give it the right name. The core will find everything else.

**3. Conventions aren't boring - they're fast.** Want a new page? Create a method in the controller and a phtml file with the same name. That's it. No need to touch configs, no need to register routes. The system knows where to look. Build the habit, and you'll stop noticing how fast time flies.

**4. Write exactly as much code as needed - not a line more.** Don't over-engineer. SLOT-H does not encourage it.

---

## Where did this come from?

2011. The author is sitting on their tenth project, which completely repeats the ninth, but is written differently because they made one mistake in the ninth and a different one in the tenth. One bug - ten fixes. One update - ten hours of work. This isn't development - it's hard labor.

The first version of SLOT-H was a dumb set of scripts that at least unified the database. Since then, the system has grown like an overfed pet:

- **2015** - no longer scripts, but a full-fledged ecosystem
- **2020** - dozens of projects in production
- **2026** - 15 years of evolution

During this time, the system has survived:

- Migration from MySQL to support PostgreSQL and MongoDB (because clients love variety)
- ORM refactoring toward transparency (because magic is good only in books)
- Adaptation to PHP 8.x (because 7.x is no longer updated)
- Opening of the demo version (because hiding good code is a sin)

And thousands of other changes, big and small.

---

## What SLOT-H is not (and why that's good)

**It's not a framework.** A framework is like a Swiss Army knife: it seems to have everything, but to open a beer you need to read the manual. SLOT-H is like a simple screwdriver: always at hand, always works, and doesn't need instructions.

**It's not a study project.** The code you see feeds the author's family. For 15 years. This is not a portfolio toy. This is a battle-tested tool.

**It's not trying to be better than Laravel or Symfony.** It's just a different approach. It might suit you, or it might not. It worked for the author - and it's been feeding his family for 15 years.

---

## Who is this even for?

**Architects**  
Will see how to build multi-project systems without microservices and other trendy nonsense.

**Developers tired of magic**  
Will find pragmatic solutions that work for years. No hype. No rewriting every six months.

**Project owners**  
Will understand how to reduce maintenance costs without losing quality.

**Students and beginners**  
Will see real code, not textbook examples. Real production with 15 years of history.

---

## What's inside (briefly)

- **Modularity.** Controllers, models, views. Everything in its place.
- **Multi-project.** One codebase - dozens of projects. project_id rules the world.
- **Localization.** Automated translation via queues. Painless.
- **ORM without magic.** MySQL, PostgreSQL, MongoDB. Honest JOINs, clear WHEREs.
- **Security.** Dynamic form signature. Changes every 30 seconds. Can't be forged.
- **Runs anywhere.** Apache/Nginx, PHP 7.x-8.x, MySQL 8. Minimal requirements.

---

## License

**MIT.** Free. For any purpose.

- **Status:** production-ready, running in production
- **Age:** 15 years of continuous evolution
- **Author:** 25 years of experience and still not burned out

---

## What's next?

- ➡️ [Section 02. Server Requirements](/docs/02-requirements)
- ➡️ [Section 03. Quick Start](/docs/03-quick-start)
- ➡️ [Section 04. Philosophy](/docs/04-philosophy)