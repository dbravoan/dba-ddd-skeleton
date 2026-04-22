# Development Guide: DBA DDD Skeleton (IA Agent Instructions)

This document defines the architecture, standards, and conventions for the project. All AI agents MUST adhere to these rules to maintain system integrity and quality.

## 1. Project Overview
A Laravel skeleton for implementing **Domain-Driven Design (DDD)** and **Hexagonal Architecture**. The primary goal is a complete decoupling of business logic from the framework.

### Mandatory Technical Pillars:
- **PHP 8.2+**: Extensive use of `readonly classes`.
- **PHPStan Level 9**: Zero tolerance for type errors. No `mixed` types allowed without prior validation.
- **Immutability**: All DTOs (Commands/Queries) and Value Objects MUST be `readonly`.
- **Strict Typing**: Every parameter, property, and return type MUST have a native type or precise generic PHPDoc.

---

## 2. Directory Structure and Layers

Each module follows this structure (e.g., `src/Context/Module`):

1.  **Domain**: Pure PHP classes.
    - `Entity/Aggregate`: Business logic and event recording (`record`).
    - `ValueObject`: Immutable types (must extend `StringValueObject`, `Uuid`, etc.).
    - `Event`: Domain events (must be `readonly`).
    - `Repository`: Persistence interfaces.
2.  **Application**: Use cases.
    - `Create/Update/Find...`: Commands, Queries, and their Handlers.
    - `Response`: DTOs for query outputs.
    - **Rule**: Handlers orchestrate the domain; they do not contain business logic.
3.  **Infrastructure**: Technical implementations.
    - `Persistence`: Actual repositories (Eloquent, QueryBuilder, File).
    - `Controller`: Laravel controllers (must extend `ApiController`).

---

## 3. Coding Conventions and Patterns

### Value Objects
- MUST be `readonly`.
- MUST validate state within the constructor.
- MUST use `@phpstan-consistent-constructor`.

### Message Bus (CQRS)
- **Commands**: State-changing actions. Return `void`.
- **Queries**: Data fetching. Return a `Response` object.
- **Handlers**: MUST be `final readonly` and use the `__invoke` method.

### Criteria Pattern
- Use the `Criteria` object for complex querying.
- Domain-to-Infrastructure field mapping happens in the repository via the `$toEloquentFields` array.

---

## 4. Code Generation Instructions

When creating new features, the agent MUST:

1.  **Prioritize the Generator**: Always use `php artisan dba:make:module` to bootstrap the module structure.
2.  **Use --application-service**: By convention, separate logic from the Handler into an Application Service (using the `--application-service` flag).
3.  **Type Validation**: 
    - When handling `mixed` inputs (e.g., from a Request), use `is_string()`, `is_array()`, or `is_scalar()` before processing.
    - Avoid direct casts like `(string) $mixed` if PHPStan Level 9 might flag them.
4.  **Domain Events**: Ensure the repository calls `$this->publishEvents($aggregate)` after persistence.

---

## 5. Verification Protocol (Mandatory)

Before completing any task, the agent MUST successfully run:

1.  **Tests**: `composer test` (PHPUnit).
2.  **Style**: `composer lint` (Laravel Pint).
3.  **Static Analysis**: `composer analyze` (PHPStan Level 9). **This step is non-negotiable.**

---

## 6. Acknowledgments
This project follows design patterns promoted by **CodelyTV** (Hexagonal Architecture, CQRS, Clean Code). Maintain the technical elegance and simplicity characteristic of their training.

---

**Any deviation from these rules is considered an architectural bug.**
