# PHP REST API (WAMP / Localhost Setup)

## Project Structure

This project contains a custom PHP REST API located under:

```
www/256-487-projects/256-php/api/
```

The API is exposed at:

```
http://localhost/api
```

using a centralized router at the web root that handles both API and frontend requests.

---

## Folder Layout

```
wamp64/www/
│
├── index.php                     ← Centralized router (handles all requests)
├── .htaccess                     ← Clean URLs + security rules
│
└── 256-487-projects/
    └── 256-php/
        ├── api/
        │   ├── index.php         ← API router + controllers + logic
        │   ├── restUtil.php      ← REST utilities & base classes
        │   └── controllers/      ← API controllers
        │
        └── frontend/
            ├── index.php         ← Frontend home page
            ├── login.php         ← Login page
            └── admin.php         ← Admin dashboard
```

---

## Centralized Router Setup

### `www/index.php`

```php
<?php
// D:\Documents\wamp64\www\index.php - Centralized Router

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');

// API Routes - anything starting with 'api/'
if (strpos($uri, 'api/') === 0 || $uri === 'api') {
    // Block dotfiles in API paths (security)
    if (preg_match('/(^|\/)\./i', $uri)) {
        http_response_code(403);
        die('Forbidden');
    }

    // Fix SCRIPT_NAME so API routing works correctly
    $_SERVER['SCRIPT_NAME'] = '/api/index.php';

    require __DIR__ . '/256-487-projects/256-php/api/index.php';
    exit;
}

// Frontend Routes
$frontendDir = __DIR__ . '/256-487-projects/256-php/frontend';

// If empty, load index.php
if (empty($uri)) {
    require $frontendDir . '/index.php';
    exit;
}

// Check if the requested file exists in frontend directory
$requestedFile = $frontendDir . '/' . $uri;

if (file_exists($requestedFile) && is_file($requestedFile)) {
    $ext = pathinfo($requestedFile, PATHINFO_EXTENSION);

    // Handle PHP files
    if ($ext === 'php') {
        require $requestedFile;
        exit;
    }

    // Handle static assets (CSS, JS, images, etc.)
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
    ];

    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
        readfile($requestedFile);
        exit;
    }
}

// If file doesn't exist, load index.php (404 or home page)
require $frontendDir . '/index.php';
```

### `www/.htaccess`

```apache
RewriteEngine On

# Allow Let's Encrypt challenges
RewriteRule ^\.well-known/ - [L]

# Block all dotfiles (includes .env)
RewriteRule (^|/)\. - [F]

# Route all requests to index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

This enables clean REST-style URLs like:

```
GET http://localhost/api/users
GET http://localhost/api/users/1
POST http://localhost/api/users
GET http://localhost/login.php
GET http://localhost/admin.php
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

### 2. Access the API and Frontend

Open your browser and visit:

**API endpoints:**

```
http://localhost/api
http://localhost/api/users
http://localhost/api/users/3
```

**Frontend pages:**

```
http://localhost/
http://localhost/login.php
http://localhost/admin.php
```

---

## How Routing Works

1. Browser hits any URL (e.g., `/api/users` or `/login.php`)
2. Apache's `.htaccess` rewrites all requests to `www/index.php`
3. The centralized router:
    - Checks if the path starts with `api/`
        - If yes: sets `SCRIPT_NAME` and forwards to `256-487-projects/256-php/api/index.php`
        - If no: routes to frontend files in `256-487-projects/256-php/frontend/`
    - Serves PHP files, static assets (CSS, JS, images), or 404 pages
4. The API router:
    - Parses the request method
    - Matches the URL path to Route attributes
    - Executes the appropriate controller
    - Returns JSON via `ApiResponse`

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
-   Single centralized router handles both API and frontend requests.
-   All routing logic resides in the root `index.php`.
-   `.htaccess` enables clean URLs, blocks dotfiles, and secures `.env` files.
-   Static assets (CSS, JS, images) are served directly by the router.

---

## Summary

This setup allows you to:

-   Keep your real project inside a nested folder
-   Expose a **clean and portable** endpoint at `/api` and `/`
-   Avoid modifying Apache configs
-   Support REST routing with clean URLs
-   Centralize all routing logic in one place
-   Serve both API and frontend from a single entry point
