<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;

class ApiDocsController extends Controller
{
    public function schema(): JsonResponse
    {
        $baseUrl = URL::to('/');

        $spec = [
            'openapi' => '3.0.3',
            'info' => [
                'title' => 'Blog CMS Laravel 12 API',
                'version' => '1.0.0',
                'description' => 'RESTful endpoints for articles, categories, tags, pages, comments, menus, newsletter, and authentication.',
            ],
            'servers' => [
                ['url' => $baseUrl . '/api'],
            ],
            'tags' => [
                ['name' => 'Auth', 'description' => 'Authentication and session management'],
                ['name' => 'Articles', 'description' => 'CRUD for blog articles'],
                ['name' => 'Categories', 'description' => 'CRUD for article categories'],
                ['name' => 'Tags', 'description' => 'CRUD for article tags'],
                ['name' => 'Pages', 'description' => 'CRUD for static pages'],
                ['name' => 'Comments', 'description' => 'Manage article comments'],
                ['name' => 'Menus', 'description' => 'Public menu configuration'],
                ['name' => 'Settings', 'description' => 'Public site configuration'],
                ['name' => 'Newsletter', 'description' => 'Email subscription management'],
                ['name' => 'Users', 'description' => 'User profile and admin CRUD'],
            ],
            'paths' => [
                '/auth/login' => [
                    'post' => [
                        'tags' => ['Auth'],
                        'summary' => 'Login and retrieve token',
                        'requestBody' => [
                            'required' => true,
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'required' => ['username', 'password'],
                                        'properties' => [
                                            'username' => ['type' => 'string'],
                                            'password' => ['type' => 'string'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            '200' => ['description' => 'Authenticated'],
                            '401' => ['description' => 'Invalid credentials'],
                        ],
                    ],
                ],
                '/auth/register' => [
                    'post' => [
                        'tags' => ['Auth'],
                        'summary' => 'Register a new user',
                        'requestBody' => [
                            'required' => true,
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'required' => ['name', 'username', 'email', 'password', 'password_confirmation'],
                                        'properties' => [
                                            'name' => ['type' => 'string'],
                                            'username' => ['type' => 'string'],
                                            'email' => ['type' => 'string', 'format' => 'email'],
                                            'password' => ['type' => 'string', 'format' => 'password'],
                                            'password_confirmation' => ['type' => 'string', 'format' => 'password'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            '201' => ['description' => 'User registered'],
                        ],
                    ],
                ],
                '/auth/logout' => [
                    'post' => [
                        'tags' => ['Auth'],
                        'summary' => 'Logout current session',
                        'security' => [['BearerAuth' => []]],
                        'responses' => [
                            '200' => ['description' => 'Logged out'],
                        ],
                    ],
                ],
                '/v1/articles' => [
                    'get' => [
                        'tags' => ['Articles'],
                        'summary' => 'List articles',
                        'parameters' => [
                            ['name' => 'status', 'in' => 'query', 'schema' => ['type' => 'string']],
                            ['name' => 'category', 'in' => 'query', 'schema' => ['type' => 'string']],
                            ['name' => 'tag', 'in' => 'query', 'schema' => ['type' => 'string']],
                            ['name' => 'search', 'in' => 'query', 'schema' => ['type' => 'string']],
                            ['name' => 'per_page', 'in' => 'query', 'schema' => ['type' => 'integer']],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Paginated list of articles',
                                'content' => [
                                    'application/json' => [
                                        'schema' => ['$ref' => '#/components/schemas/PaginatedArticles'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'post' => [
                        'tags' => ['Articles'],
                        'summary' => 'Create article',
                        'security' => [['BearerAuth' => []]],
                        'requestBody' => [
                            'required' => true,
                            'content' => [
                                'application/json' => [
                                    'schema' => ['$ref' => '#/components/schemas/ArticleInput'],
                                ],
                            ],
                        ],
                        'responses' => [
                            '201' => ['description' => 'Article created'],
                            '422' => ['description' => 'Validation error'],
                        ],
                    ],
                ],
                '/v1/articles/{slug}' => [
                    'get' => [
                        'tags' => ['Articles'],
                        'summary' => 'Show article',
                        'parameters' => [
                            ['name' => 'slug', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']],
                        ],
                        'responses' => [
                            '200' => ['description' => 'Article detail'],
                            '404' => ['description' => 'Not found'],
                        ],
                    ],
                    'put' => [
                        'tags' => ['Articles'],
                        'summary' => 'Update article',
                        'security' => [['BearerAuth' => []]],
                        'parameters' => [
                            ['name' => 'slug', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']],
                        ],
                        'requestBody' => [
                            'content' => [
                                'application/json' => [
                                    'schema' => ['$ref' => '#/components/schemas/ArticleInput'],
                                ],
                            ],
                        ],
                        'responses' => [
                            '200' => ['description' => 'Article updated'],
                        ],
                    ],
                    'delete' => [
                        'tags' => ['Articles'],
                        'summary' => 'Delete article',
                        'security' => [['BearerAuth' => []]],
                        'parameters' => [
                            ['name' => 'slug', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']],
                        ],
                        'responses' => [
                            '200' => ['description' => 'Article deleted'],
                        ],
                    ],
                ],
                '/v1/categories' => [
                    'get' => [
                        'tags' => ['Categories'],
                        'summary' => 'List categories',
                        'responses' => ['200' => ['description' => 'OK']],
                    ],
                    'post' => [
                        'tags' => ['Categories'],
                        'summary' => 'Create category',
                        'security' => [['BearerAuth' => []]],
                        'requestBody' => [
                            'content' => [
                                'application/json' => [
                                    'schema' => ['$ref' => '#/components/schemas/CategoryInput'],
                                ],
                            ],
                        ],
                        'responses' => ['201' => ['description' => 'Created']],
                    ],
                ],
                '/v1/categories/{slug}' => [
                    'get' => [
                        'tags' => ['Categories'],
                        'summary' => 'Show category',
                        'parameters' => [['name' => 'slug', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']]],
                        'responses' => ['200' => ['description' => 'OK']],
                    ],
                    'put' => [
                        'tags' => ['Categories'],
                        'summary' => 'Update category',
                        'security' => [['BearerAuth' => []]],
                        'parameters' => [['name' => 'slug', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']]],
                        'requestBody' => [
                            'content' => [
                                'application/json' => [
                                    'schema' => ['$ref' => '#/components/schemas/CategoryInput'],
                                ],
                            ],
                        ],
                        'responses' => ['200' => ['description' => 'Updated']],
                    ],
                    'delete' => [
                        'tags' => ['Categories'],
                        'summary' => 'Delete category',
                        'security' => [['BearerAuth' => []]],
                        'parameters' => [['name' => 'slug', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']]],
                        'responses' => ['200' => ['description' => 'Deleted']],
                    ],
                ],
                '/v1/tags' => [
                    'get' => [
                        'tags' => ['Tags'],
                        'summary' => 'List tags',
                        'responses' => ['200' => ['description' => 'OK']],
                    ],
                    'post' => [
                        'tags' => ['Tags'],
                        'summary' => 'Create tag',
                        'security' => [['BearerAuth' => []]],
                        'requestBody' => [
                            'content' => [
                                'application/json' => [
                                    'schema' => ['$ref' => '#/components/schemas/TagInput'],
                                ],
                            ],
                        ],
                        'responses' => ['201' => ['description' => 'Created']],
                    ],
                ],
                '/v1/tags/{slug}' => [
                    'get' => [
                        'tags' => ['Tags'],
                        'summary' => 'Show tag',
                        'parameters' => [['name' => 'slug', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']]],
                        'responses' => ['200' => ['description' => 'OK']],
                    ],
                    'put' => [
                        'tags' => ['Tags'],
                        'summary' => 'Update tag',
                        'security' => [['BearerAuth' => []]],
                        'parameters' => [['name' => 'slug', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']]],
                        'requestBody' => [
                            'content' => [
                                'application/json' => [
                                    'schema' => ['$ref' => '#/components/schemas/TagInput'],
                                ],
                            ],
                        ],
                        'responses' => ['200' => ['description' => 'Updated']],
                    ],
                    'delete' => [
                        'tags' => ['Tags'],
                        'summary' => 'Delete tag',
                        'security' => [['BearerAuth' => []]],
                        'parameters' => [['name' => 'slug', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']]],
                        'responses' => ['200' => ['description' => 'Deleted']],
                    ],
                ],
                '/v1/pages' => [
                    'get' => [
                        'tags' => ['Pages'],
                        'summary' => 'List pages',
                        'responses' => ['200' => ['description' => 'OK']],
                    ],
                    'post' => [
                        'tags' => ['Pages'],
                        'summary' => 'Create page',
                        'security' => [['BearerAuth' => []]],
                        'requestBody' => [
                            'content' => [
                                'application/json' => [
                                    'schema' => ['$ref' => '#/components/schemas/PageInput'],
                                ],
                            ],
                        ],
                        'responses' => ['201' => ['description' => 'Created']],
                    ],
                ],
                '/v1/pages/{slug}' => [
                    'get' => [
                        'tags' => ['Pages'],
                        'summary' => 'Show page',
                        'parameters' => [['name' => 'slug', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']]],
                        'responses' => ['200' => ['description' => 'OK']],
                    ],
                    'put' => [
                        'tags' => ['Pages'],
                        'summary' => 'Update page',
                        'security' => [['BearerAuth' => []]],
                        'parameters' => [['name' => 'slug', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']]],
                        'requestBody' => [
                            'content' => [
                                'application/json' => [
                                    'schema' => ['$ref' => '#/components/schemas/PageInput'],
                                ],
                            ],
                        ],
                        'responses' => ['200' => ['description' => 'Updated']],
                    ],
                    'delete' => [
                        'tags' => ['Pages'],
                        'summary' => 'Delete page',
                        'security' => [['BearerAuth' => []]],
                        'parameters' => [['name' => 'slug', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']]],
                        'responses' => ['200' => ['description' => 'Deleted']],
                    ],
                ],
                '/v1/articles/{slug}/comments' => [
                    'get' => [
                        'tags' => ['Comments'],
                        'summary' => 'List comments for article',
                        'parameters' => [['name' => 'slug', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']]],
                        'responses' => ['200' => ['description' => 'OK']],
                    ],
                    'post' => [
                        'tags' => ['Comments'],
                        'summary' => 'Create comment for article',
                        'security' => [['BearerAuth' => []]],
                        'parameters' => [['name' => 'slug', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']]],
                        'requestBody' => [
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'required' => ['comment'],
                                        'properties' => [
                                            'comment' => ['type' => 'string'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'responses' => ['201' => ['description' => 'Created']],
                    ],
                ],
                '/v1/comments/{id}' => [
                    'delete' => [
                        'tags' => ['Comments'],
                        'summary' => 'Delete comment',
                        'security' => [['BearerAuth' => []]],
                        'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                        'responses' => ['200' => ['description' => 'Deleted']],
                    ],
                ],
                '/v1/menus' => [
                    'get' => [
                        'tags' => ['Menus'],
                        'summary' => 'List menus with items',
                        'responses' => ['200' => ['description' => 'OK']],
                    ],
                ],
                '/v1/settings' => [
                    'get' => [
                        'tags' => ['Settings'],
                        'summary' => 'Get web settings',
                        'responses' => ['200' => ['description' => 'OK']],
                    ],
                ],
                '/v1/newsletter' => [
                    'get' => [
                        'tags' => ['Newsletter'],
                        'summary' => 'List subscribers',
                        'security' => [['BearerAuth' => []]],
                        'responses' => ['200' => ['description' => 'OK']],
                    ],
                    'post' => [
                        'tags' => ['Newsletter'],
                        'summary' => 'Subscribe email',
                        'requestBody' => [
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'required' => ['email'],
                                        'properties' => [
                                            'email' => ['type' => 'string', 'format' => 'email'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'responses' => ['201' => ['description' => 'Created']],
                    ],
                ],
                '/v1/newsletter/{id}' => [
                    'delete' => [
                        'tags' => ['Newsletter'],
                        'summary' => 'Remove subscriber',
                        'security' => [['BearerAuth' => []]],
                        'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                        'responses' => ['200' => ['description' => 'Deleted']],
                    ],
                ],
                '/v1/user' => [
                    'get' => [
                        'tags' => ['Users'],
                        'summary' => 'Authenticated user profile',
                        'security' => [['BearerAuth' => []]],
                        'responses' => ['200' => ['description' => 'OK']],
                    ],
                    'patch' => [
                        'tags' => ['Users'],
                        'summary' => 'Update own profile',
                        'security' => [['BearerAuth' => []]],
                        'responses' => ['200' => ['description' => 'Updated']],
                    ],
                ],
                '/v1/users' => [
                    'get' => [
                        'tags' => ['Users'],
                        'summary' => 'List users (admin only)',
                        'security' => [['BearerAuth' => []]],
                        'responses' => ['200' => ['description' => 'OK']],
                    ],
                    'post' => [
                        'tags' => ['Users'],
                        'summary' => 'Create user',
                        'security' => [['BearerAuth' => []]],
                        'responses' => ['201' => ['description' => 'Created']],
                    ],
                ],
                '/v1/users/{id}' => [
                    'get' => [
                        'tags' => ['Users'],
                        'summary' => 'Show user',
                        'security' => [['BearerAuth' => []]],
                        'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']]],
                        'responses' => ['200' => ['description' => 'OK']],
                    ],
                    'put' => [
                        'tags' => ['Users'],
                        'summary' => 'Update user',
                        'security' => [['BearerAuth' => []]],
                        'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']]],
                        'responses' => ['200' => ['description' => 'Updated']],
                    ],
                    'delete' => [
                        'tags' => ['Users'],
                        'summary' => 'Delete user',
                        'security' => [['BearerAuth' => []]],
                        'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']]],
                        'responses' => ['200' => ['description' => 'Deleted']],
                    ],
                ],
            ],
            'components' => [
                'securitySchemes' => [
                    'BearerAuth' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                        'bearerFormat' => 'Token',
                    ],
                ],
                'schemas' => [
                    'Article' => [
                        'type' => 'object',
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'content' => ['type' => 'string'],
                            'excerpt' => ['type' => 'string'],
                            'status' => ['type' => 'string'],
                            'published_at' => ['type' => 'string', 'format' => 'date-time'],
                        ],
                    ],
                    'ArticleInput' => [
                        'type' => 'object',
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'category_id' => ['type' => 'integer'],
                            'status' => ['type' => 'string', 'enum' => ['draft', 'published', 'pending']],
                            'content' => ['type' => 'string'],
                            'excerpt' => ['type' => 'string'],
                            'tags' => [
                                'type' => 'array',
                                'items' => ['type' => 'string'],
                            ],
                        ],
                        'required' => ['title'],
                    ],
                    'CategoryInput' => [
                        'type' => 'object',
                        'properties' => [
                            'name' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'description' => ['type' => 'string'],
                        ],
                        'required' => ['name'],
                    ],
                    'TagInput' => [
                        'type' => 'object',
                        'properties' => [
                            'tag_name' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                        ],
                        'required' => ['tag_name'],
                    ],
                    'PageInput' => [
                        'type' => 'object',
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'content' => ['type' => 'string'],
                            'description' => ['type' => 'string'],
                            'isFullWidth' => ['type' => 'boolean'],
                        ],
                        'required' => ['title'],
                    ],
                    'PaginatedArticles' => [
                        'type' => 'object',
                        'properties' => [
                            'current_page' => ['type' => 'integer'],
                            'data' => [
                                'type' => 'array',
                                'items' => ['$ref' => '#/components/schemas/Article'],
                            ],
                            'last_page' => ['type' => 'integer'],
                            'per_page' => ['type' => 'integer'],
                            'total' => ['type' => 'integer'],
                        ],
                    ],
                ],
            ],
        ];

        return response()->json($spec);
    }
}
