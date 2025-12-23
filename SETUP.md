# PHP REST API (WAMP / Localhost Setup)

## Project Structure

This project contains a custom PHP REST API located under:

```
www/256-487-projects/256-php/api/
```

However, to expose the API cleanly at:

```
http://localhost/api
```

a lightweight wrapper folder is added directly under WAMP’s web root:

```
www/api/
```

This wrapper forwards all `/api/...` requests to the actual project API.

---

## Folder Layout

```
wamp64/www/
│
├── api/                          ← Public API endpoint (HTTP entry point)
│   ├── index.php                 ← Forwards requests to the real API
│   └── .htaccess                 ← Enables clean URLs (/api/users)
│
└── 256-487-projects/
    └── 256-php/
        └── api/
            ├── index.php         ← Real API router + controllers + logic
            ├── src/              ← Your utilities, classes, repository, etc.
            └── ...               ← Additional files
```

---

## Wrapper `/www/api` Setup

### `www/api/index.php`

```php
<?php
// Public wrapper entry point
// This forwards all API requests to the real API folder

require __DIR__ . '/../256-487-projects/256-php/api/index.php';
```

### `www/api/.htaccess`

```apache
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

RewriteRule ^ index.php [QSA,L]
```

This enables clean REST-style URLs like:

```
GET http://localhost/api/users
GET http://localhost/api/users/1
POST http://localhost/api/users
```

---

## Installing Dependencies

The project uses Composer to manage PHP dependencies (such as AWS SDK, Guzzle, etc.).

### Install Composer Packages

Navigate to the API directory and run:

```bash
cd 256-487-projects/256-php/api
composer install
```

This will:

-   Install all dependencies listed in `composer.json`
-   Create the `vendor/` directory with all required packages
-   Generate the autoloader for class loading

**Note**: The `vendor/` directory should not be committed to version control. It's listed in `.gitignore`.

---

## Running the API

### 1. Start WAMP

Launch WAMP and ensure the server icon is **green**.

### 2. Access the API

Open your browser and visit:

```
http://localhost/api
```

Example routes:

```
GET http://localhost/api/users
GET http://localhost/api/users/3
POST http://localhost/api/users
```

---

## How Routing Works

1. Browser hits `/api/...`
2. Apache serves `www/api/index.php` because of `.htaccess`
3. This index forwards the request to:

```
www/256-487-projects/256-php/api/index.php
```

4. The real router:
    - parses the request method
    - matches the URL path to Route attributes
    - executes the appropriate controller
    - returns JSON via `ApiResponse`

---

## Example Controller Route Definition

```php
#[Route('/users', 'GET')]
public function listUsers(): ApiResponse
{
    $users = array_map(fn(User $u) => $u->toArray(), $this->repository->getAll());
    return new ApiResponse(true, $users);
}

#[Route('/users/{id}', 'GET')]
public function getUser(string $id): ApiResponse
{
    $user = $this->repository->find($id);
    return $user
        ? new ApiResponse(true, $user->toArray())
        : new ApiResponse(false, null, 'User not found');
}
```

These respond to:

```
GET /api/users
GET /api/users/5
```

---

## Testing With cURL

```bash
curl http://localhost/api/users
```

```bash
curl http://localhost/api/users/1
```

```bash
curl -X POST http://localhost/api/users   -H "Content-Type: application/json"   -d '{"name": "Sezer"}'
```

---

## Notes

-   No `httpd.conf` edits are required.
-   The wrapper folder keeps the project portable across different PCs.
-   All routing logic resides in the real API folder.
-   `.htaccess` enables clean URLs and prevents exposing internal folders.

---

## Summary

This setup allows you to:

-   keep your real project inside a nested folder
-   expose a **clean and portable** endpoint at `/api`
-   avoid modifying Apache configs
-   support REST routing with clean URLs
