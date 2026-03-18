# Laravel Backend – Copilot Rules

## Architecture

Controller → Service (Interface) → Repository (Interface) → Model

- Controllers: thin, no business logic
- Services: business logic only
- Repositories: DB queries only
- Never access Model/Repository directly in Controller

---

## Request & Validation

- Always use FormRequest
- No manual validation in Controller

---

## DTO

- Use DTO between Controller → Service
- Services must NOT use Request
- Example: ProductFilterDTO

---

## Controller Rules

- Receive FormRequest
- Convert to DTO
- Call Service
- Return API Resource

---

## Service Layer

- Must use Interface
- Contains business logic
- Accept DTO only
- No HTTP dependency

---

## Repository Layer

- Must use Interface
- Only DB queries (Eloquent)
- No business logic

---

## Pagination

- Use `paginate()`
- DTO must have: `page`, `perPage`

---

## API Response

- Always use API Resource
- Standard response format

---

## OpenAPI

- Add annotations in Controllers

---

## Naming

- PSR-12
- Clear names:
  - ProductServiceInterface
  - ProductRepositoryInterface
  - ProductFilterDTO
  - ProductIndexRequest

---

## Strict Rules

1. Use FormRequest
2. Use DTO (Controller → Service)
3. Service depends on Interface
4. Thin Controllers
5. Repository = DB only
6. No business logic in Controller
7. No Request in Service
8. Use Dependency Injection
9. Follow PSR-12
