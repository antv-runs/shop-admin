# GitHub Copilot Instructions for Laravel Backend

This project follows a strict layered architecture.
All generated code must follow these conventions.

## Architecture

Always follow this dependency flow:

Controller → Service (Interface) → Repository (Interface) → Model

Controllers must NEVER access repositories or models directly.

Services contain business logic.

Repositories handle database queries only.

Controllers must remain thin.

---

# Request Handling

Always use FormRequest for validation.

Example:

```
app/Http/Requests/ProductIndexRequest.php
```

Rules example:

- search: nullable|string
- category_id: nullable|integer|exists:categories,id
- page: nullable|integer|min:1
- per_page: nullable|integer|min:1|max:100

Controllers should never validate input manually.

---

# DTO Pattern

All data passed from Controller to Service must use DTO objects.

Services must NEVER depend on:

```
Illuminate\Http\Request
```

DTO example location:

```
app/DTO/Product/ProductFilterDTO.php
```

Example structure:

```php
class ProductFilterDTO
{
    public function __construct(
        public readonly ?string $search,
        public readonly ?int $categoryId,
        public readonly int $page,
        public readonly int $perPage
    ) {}

    public static function fromRequest(ProductIndexRequest $request): self
    {
        return new self(
            search: $request->input('search'),
            categoryId: $request->input('category_id'),
            page: $request->input('page', 1),
            perPage: $request->input('per_page', 15)
        );
    }
}
```

---

# Controllers

Controllers must:

- receive FormRequest
- convert Request → DTO
- call Service
- return API Resource

Controllers must NOT contain business logic.

Example:

```php
public function index(ProductIndexRequest $request)
{
    $filter = ProductFilterDTO::fromRequest($request);

    $products = $this->productService->getAllProducts($filter);

    return $this->success(
        ProductResource::collection($products),
        'Products retrieved successfully'
    );
}
```

---

# Service Layer

Services must:

- implement an interface
- contain business logic
- call repositories
- use DTO objects
- not depend on HTTP Request

Example:

```
app/Services/ProductService.php
app/Services/Interfaces/ProductServiceInterface.php
```

Example method:

```php
public function getAllProducts(ProductFilterDTO $filter);
```

---

# Repository Layer

Repositories must:

- implement an interface
- contain database queries only
- use Eloquent models
- not contain business logic

Example:

```
app/Repositories/ProductRepository.php
app/Repositories/Interfaces/ProductRepositoryInterface.php
```

---

# Pagination

All list endpoints must support pagination.

Use:

```
->paginate($perPage)
```

DTO should contain:

- page
- perPage

---

# API Responses

All responses must use:

- API Resource
- Standard response format

Example:

```
ProductResource::collection($products)
```

---

# OpenAPI

Controllers must include OpenAPI annotations.

Example:

```
@OA\Get(
    path="/api/products",
    summary="List products"
)
```

---

# Naming Conventions

Follow PSR-12.

Use clear naming:

- ProductServiceInterface
- ProductRepositoryInterface
- ProductFilterDTO
- ProductIndexRequest

---

# General Rules

Copilot must follow these rules when generating code:

1. Always use FormRequest for validation
2. Always use DTO between Controller and Service
3. Services must depend on interfaces
4. Controllers must stay thin
5. Repositories handle database queries only
6. No business logic in controllers
7. No Request objects inside Services
8. Use dependency injection
9. Follow PSR-12 PHP coding standards
