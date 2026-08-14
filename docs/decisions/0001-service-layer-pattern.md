# ADR-0001: Service Layer Pattern

**Status:** Accepted (2026-06-26)

## Context
Controllers were calling Eloquent models directly, mixing business logic with HTTP concerns. This made testing harder and violated Single Responsibility Principle.

## Decision
Introduce a Service layer between Controllers and Models:
- `BaseService` — abstract class with CRUD operations wrapped in DB transactions
- Domain services — `ProductService`, `OrderService`, `StoreService`, `UserService`, `CartService`
- Services handle business logic, events, and transactions
- Controllers handle HTTP concerns only (validation, response)

## Consequences
- Controllers become thinner and more testable
- Business logic is centralized and reusable
- Services can be injected and mocked in tests
- Slight increase in code volume (~5 new files)
