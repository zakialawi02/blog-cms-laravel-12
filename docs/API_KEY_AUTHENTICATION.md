# API Key Authentication

## Overview

All API endpoints in this CMS require an API Key for access. The API Key must be included in every request header.

## Obtaining an API Key

Contact the administrator to obtain your API key. The API key is configured in the server's environment variables.

## Usage

Include the `X-API-Key` header in all your API requests:

```http
X-API-Key: your-api-key-here
```

## Example Requests

### cURL

```bash
curl -X GET http://localhost:8000/api/v1/articles \
  -H "X-API-Key: your-api-key-here" \
  -H "Accept: application/json"
```

### JavaScript (Fetch API)

```javascript
fetch('http://localhost:8000/api/v1/articles', {
  method: 'GET',
  headers: {
    'X-API-Key': 'your-api-key-here',
    'Accept': 'application/json',
  }
})
.then(response => response.json())
.then(data => console.log(data));
```

### JavaScript (Axios)

```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'X-API-Key': 'your-api-key-here',
  }
});

// Get articles
const articles = await api.get('/v1/articles');

// Get users (requires additional auth)
const users = await api.get('/v1/users', {
  headers: {
    ...api.defaults.headers,
    'Authorization': 'Bearer ' + userToken
  }
});
```

### PHP (Guzzle)

```php
use GuzzleHttp\Client;

$client = new Client([
    'base_uri' => 'http://localhost:8000/api',
    'headers' => [
        'X-API-Key' => 'your-api-key-here',
        'Accept' => 'application/json',
    ],
]);

$response = $client->get('/v1/articles');
$articles = json_decode($response->getBody(), true);
```

### Python (Requests)

```python
import requests

headers = {
    'X-API-Key': 'your-api-key-here',
    'Accept': 'application/json',
}

response = requests.get('http://localhost:8000/api/v1/articles', headers=headers)
articles = response.json()
```

## Domain Restrictions

The API also validates that requests come from allowed domains. This is handled automatically by the browser through the `Origin` header.

### Allowed Domains

The following domains are configured by default:
- `http://localhost`
- `http://localhost:3000`
- `http://127.0.0.1`
- `http://127.0.0.1:3000`
- `https://yourfrontend.com` (configure in `.env`)

To add your frontend domain, contact the administrator to update the `ALLOWED_ORIGINS` environment variable.

## Error Responses

### Missing or Invalid API Key (401)

```json
{
  "success": false,
  "message": "Invalid or missing API Key",
  "hint": "Please include X-API-Key in your request headers",
  "example": "X-API-Key: your-api-key-here",
  "support": "Contact administrator for further assistance"
}
```

### Unauthorized Domain (403)

```json
{
  "success": false,
  "message": "Unauthorized domain",
  "hint": "Your domain is not in the allowed origins list",
  "your_domain": "https://evil.com",
  "support": "Contact administrator for further assistance"
}
```

## Combining with User Authentication

Some endpoints require both API Key and user authentication (Bearer token):

```javascript
const response = await fetch('http://localhost:8000/api/v1/users', {
  headers: {
    'X-API-Key': 'your-api-key-here',
    'Authorization': 'Bearer ' + userToken,
    'Accept': 'application/json',
  }
});
```

## Security Best Practices

1. **Never expose your API key in client-side code** - Use it on your backend server
2. **Use HTTPS in production** - API keys should only be transmitted over secure connections
3. **Rotate keys periodically** - Change API keys regularly
4. **Use environment-specific keys** - Different keys for development, staging, and production
5. **Monitor usage** - Track API key usage to detect unauthorized access

## Swagger/OpenAPI Documentation

This API includes Swagger documentation with API Key security defined. You can use the `apiKey` security scheme in your Swagger UI:

```yaml
security:
  - apiKey: []
  - bearerAuth: []  # For endpoints requiring user auth
```

## Need Help?

Contact the administrator if you:
- Need an API key
- Your domain is not allowed
- Experience any issues accessing the API
