# Changelog

All notable changes to this project will be documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).
This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [2.1.1] — 2026-05-05

### Fixed

- **CI matrix — `orchestra/testbench` version mismatch** — the workflow was passing
  `orchestra/testbench:^10.0` for all Laravel versions, but testbench `^10.x` requires
  Laravel 12, causing the Laravel 10/11 matrix legs to fail with an unresolvable conflict.
  The correct mapping is:

  | Laravel | testbench |
  |---|---|
  | 10 | `^8.0` |
  | 11 | `^9.0` |
  | 12 | `^10.0` |

  The workflow now uses an explicit `matrix.include` list with the correct `testbench`
  version per Laravel version. Laravel 10 legs are limited to PHP 8.2/8.3 (PHP 8.4 was
  not supported by Laravel 10). `composer.json` `require-dev` widened to
  `^8.0|^9.0|^10.0|^11.0`.

---



### Fixed

- **`DbaServiceProvider` tag mismatch** — handlers were tagged `'dba_handler'`; corrected to
  `'dba_ddd.command_handler'`, `'dba_ddd.query_handler'`, and `'dba_ddd.domain_event_subscriber'`
  to match the tags consumed by the buses in `DddSkeletonServiceProvider`. Previously all
  auto-discovered handlers were silently ignored at runtime.

- **`AggregateRoot::createNullableValueObject()` false-null bug** — `empty()` incorrectly treated
  `0`, `'0'`, and `false` as null. Replaced with a strict `=== null || === ''` check.

- **`FileRepository::toXml()` broken on PHP 8.4** — `xmlrpc_encode()` was removed in PHP 8.4.
  `FileRepository` has been fully rewritten using `DOMDocument` for XML serialisation and explicit
  `escape` parameters on `fputcsv`/`fgetcsv` (required since PHP 8.4).

- **`EloquentCriteria::raw()` and `QueryBuilderCriteria::raw()`** — `Stringable` values are now
  explicitly cast to `string` before being passed to the `Expression` constructor, fixing a
  PHPStan `argument.type` error.

### Removed

- `src/Shared/Domain/Security/SqlInjectionProtector.php` — had zero references anywhere in the
  codebase. Input sanitisation belongs at the controller / validation layer, not in the domain.

- `src/Shared/Helpers/ArrayHelper.php` — had zero references anywhere in the codebase.

- `improvements.md` — 8-line placeholder stub with no actionable content.

### Changed

- **`src/Shared/Infrastructure/Persistence/QueryBuilder/Expression.php`** — added
  `declare(strict_types=1)`; removed redundant constructor override (parent is sufficient).

- **`src/Shared/Infrastructure/Persistence/QueryBuilder/Method.php`** — added
  `declare(strict_types=1)`, declared `final`, converted to `readonly` constructor promotion.

- **README.md** — updated package structure tree, improved EventBus sync/async documentation with
  a comparison table, and added `DbaServiceProvider` auto-discovery as the recommended handler
  registration approach.

### Added

- **`src/Identity/User/Domain/UserName.php`** — `UserName` value object enforcing non-empty
  string (was missing from the generated Identity module).

- **`src/Identity/User/Domain/UserNotFoundDomainError.php`** — typed `NotFoundDomainError` for
  the `User` aggregate.

- **`src/Identity/User/Infrastructure/Persistence/UserModel.php`** — dedicated Eloquent model for
  the `users` table, extracted from the repository.

- **`src/Shared/Infrastructure/Bus/ReflectionHandlerMapper.php`** — shared trait used by both
  `LaravelCommandBus` and `LaravelQueryBus` to map handlers via reflection, eliminating
  duplicated code.

- **26 new test files** (140 total tests / 268 assertions — up from 59 / 142):

  | Layer | Tests added |
  |---|---|
  | Identity · Application | `CreateUserCommandHandlerTest`, `FindUserQueryHandlerTest`, `UpdateUserCommandHandlerTest`, `DeleteUserCommandHandlerTest`, `SearchUsersByCriteriaQueryHandlerTest`, `UserResponseTest`, `UsersResponseTest` |
  | Identity · Domain | `UserEmailTest`, `UserIdTest`, `UserNameTest`, `UserCreatedDomainEventTest`, `UserNotFoundDomainErrorTest` |
  | Shared · Domain | `DomainErrorTest`, `NotFoundDomainErrorTest`, `BadRequestDomainErrorTest`, `AssertTest`, `CollectionTest`, `UtilsTest`, `AggregateRootTest`, `DomainEventTest`, `FilterGroupTest`, `FilterOperatorTest` |
  | Shared · Domain · ValueObject | `DateTimeValueObjectTest`, `EmailValueObjectTest`, `UrlValueObjectTest`, `MoneyValueObjectTest` |
  | Shared · Infrastructure | `RamseyUuidGeneratorTest`, `LaravelQueueEventBusTest`, `FileRepositoryTest` (JSON / CSV / XML round-trips), `QueryBuilderCriteriaConverterTest`, `DomainEventJsonSerializerTest`, `RequestCriteriaBuilderTest`, `ApiControllerTest`, `MakeModuleCommandTest` |

---

## [2.0.0] — 2024 (see git log)

Major architecture upgrade. See commit `79196fb` for details.

---

## [1.x]

Initial release. See git history.
