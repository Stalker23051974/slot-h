<small>Previous articles:

- ➡️ [Section 01. SLOT-H](/docs/01-introduction)
- ➡️ [Section 02. Requirements](/docs/02-requirements)
- ➡️ [Section 03. Quick Start](/docs/03-quick-start)
</small>

# SLOT-H: Ecosystem Philosophy

Convention over Configuration

This project is built around one idea: code should be clear, predictable, and minimal.

The code is written to speak for itself. Every file in this project should be understandable at first glance.

---

## Core Principles

### 1. Convention over Configuration

The system prioritizes structural uniformity over flexible configuration of each component.

A new module follows a familiar pattern: controllers, models, views - everything is in predictable locations. The developer doesn't need to figure out how to connect the module. Just follow the structure.

The system offers a consistent way of solving problems and sticks to it.

### 2. Minimalism in Code

Every class solves one specific problem.

- `DbTables` contain only the table schema definition
- `Models` contain only property declarations
- `Controllers` contain only request logic
- `Helpers` contain only utilities

All other logic lives in base classes. Code duplication is eliminated.

### 3. Predictability over Flexibility

The system is designed so that any developer, opening the project for the first time, understands where to look and how things work.

Understanding one module gives understanding of all modules. Because they follow the same principles.

### 4. Minimal External Dependencies

The system uses only what is truly necessary. Every external library is a potential risk: discontinued support, vulnerabilities, incompatibility with new PHP versions.

The system relies on its own solutions wherever it makes sense.

### 5. Code is Documentation

No separate documentation is written. Instead, the code is written to be self-documenting. Class names, method names, variable names - they all speak for themselves.

Annotations in the code aren't for IDEs. They're for people who will read this code a year from now.

### 6. Simplicity over Complexity

Complexity in code must be justified by the complexity of the task. The system favors simple solutions that work.

If a solution can be simpler - the system takes the simpler path.

### 7. Multi-project is Built into the Core

The system is designed from the ground up to support an unlimited number of projects. Each project is a separate site with its own configuration. Each project connects to its own data storage or uses an existing one on the server.

Every table contains a `project_id` field. Every config is stored in its own file. Every project runs as an isolated environment.

---

## Summary

The system is built on simple and clear principles:

- Convention over Configuration
- Understanding over Complexity
- Simplicity over Flexibility

---

## What's next?

- ➡️ [Section 05. Principles of Operation](/docs/05-principles)
- ➡️ [Section 06. Architecture](/docs/06-architecture)
- ➡️ [Section 07. Autoloader](/docs/07-autoloader)