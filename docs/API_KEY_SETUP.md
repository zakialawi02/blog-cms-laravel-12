# API Key Authentication Setup

## Overview

This middleware restricts API access to only requests that include a valid API key and come from allowed frontend domains. **Domain detection is automatic** from request headers and uses the **same configuration as CORS**.

## Configuration

### 1. Set up your `.env` file

#### Option A: Allow Specific Domains (Recommended for Production)

```env
API_KEY=your-secret-api-key-here
ALLOWED_ORIGINS=http://localhost,http://localhost:3000,http://127.0.0.1,https://yourfrontend.com
ALLOW_ALL_ORIGINS=false
```

#### Option B: Allow All Domains (Development Only!)

```env
API_KEY=your-secret-api-key-here
ALLOW_ALL_ORIGINS=true
```

**Important:**

- Generate a strong, random API key (e.g., `php -r "echo bin2hex(random_bytes(32));"`)
- `ALLOWED_ORIGINS` is shared with CORS configuration (no duplication!)
- List all allowed frontend domains (comma-separated)
- Keep the same API key in your frontend application
- After changing `.env`, run `php artisan config:cache` to apply changes
- ⚠️ **Never use `ALLOW_ALL_ORIGINS=true` in production!**

### 2. Frontend Integration

Include the API key header in your requests. **Domain is detected automatically!**

```javascript
// Example: Fetch API
fetch("http://localhost:8000/api/v1/articles", {
    headers: {
        "X-API-Key": "your-secret-api-key-here",
        Accept: "application/json",
    },
})
    .then((response) => response.json())
    .then((data) => console.log(data));
```

```javascript
// Example: Axios
import axios from "axios";

const api = axios.create({
    baseURL: "http://localhost:8000/api",
    headers: {
        "X-API-Key": "your-secret-api-key-here",
    },
});

// Use the API
const response = await api.get("/v1/articles");
```

## Protecting Routes

The API key middleware is **automatically applied to all API routes** (`/api/*`).

Every request to your API must include the `X-API-Key` header and come from an allowed origin.

```php
// All these routes are protected by API key middleware automatically
Route::api::get('/v1/articles', [ApiController::class, 'index']);
Route::api::post('/v1/users', [UserController::class, 'store']);
Route::api::resource('/v1/posts', PostController::class);
```

### Combine with Sanctum authentication

You can still add Sanctum middleware for user authentication:

```php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('posts', PostController::class);
});
```

## Response Codes

- **401 Unauthorized**: Invalid or missing API Key
- **403 Forbidden**: Domain not in allowed list (based on CORS `ALLOWED_ORIGINS`)

Example error response:

```json
{
    "success": false,
    "message": "Invalid or missing API Key"
}
```

## Security Best Practices

1. **Use HTTPS in production** - API keys should only be transmitted over secure connections
2. **Rotate keys periodically** - Change API keys regularly and update all frontend apps
3. **Use environment-specific keys** - Different keys for development, staging, and production
4. **Never expose keys in client-side code** - Use the API key on your frontend server, not in browser JavaScript
5. **Monitor usage** - Track API key usage to detect unauthorized access
6. **Configure CORS properly** - Keep `ALLOWED_ORIGINS` in sync with your actual frontend domains
7. **Never use ALLOW_ALL_ORIGINS=true in production** - Only for local development!

## Advanced CORS Patterns

You can use wildcard patterns in `config/cors.php` for subdomain matching:

```php
// In config/cors.php
'allowed_origins' => [
    'http://localhost',
    '*.yourdomain.com',  // Allows all subdomains
],
```

## Testing

Test the middleware with curl:

```bash
# Valid request (with Origin header to simulate browser)
curl -X GET http://localhost:8000/api/v1/articles \
  -H "X-API-Key: your-secret-api-key-here" \
  -H "Origin: http://localhost"

# Invalid API key (should return 401)
curl -X GET http://localhost:8000/api/v1/articles \
  -H "X-API-Key: wrong-key" \
  -H "Origin: http://localhost"

# Unauthorized domain (should return 403)
curl -X GET http://localhost:8000/api/v1/articles \
  -H "X-API-Key: your-secret-api-key-here" \
  -H "Origin: https://evil.com"

# Allow all domains (set ALLOW_ALL_ORIGINS=true, then test any domain)
curl -X GET http://localhost:8000/api/v1/articles \
  -H "X-API-Key: your-secret-api-key-here" \
  -H "Origin: https://any-domain.com"
```
