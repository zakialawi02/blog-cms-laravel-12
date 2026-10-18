<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        $this->forgetInheritedEnvironment();

        parent::setUp();

        $this->withHeaders([
            'X-API-Key' => config('app.api_key'),
        ]);
    }

    /**
     * Make the suite independent of the shell that runs it.
     *
     * Laravel's env() reads $_SERVER/$_ENV before .env, and neither PHPUnit's
     * <env> entries nor dotenv override a variable that is already present, so
     * any variable exported by the calling shell (including a whole production
     * .env, which is how this was found) silently wins over .env.testing. That
     * pointed the suite at the production database and switched reCAPTCHA on
     * inside tests. Dropping every variable declared in either env file lets
     * .env.testing be the single source of truth for a test run.
     */
    private function forgetInheritedEnvironment(): void
    {
        $root = dirname(__DIR__);
        $keys = array_unique(array_merge(
            $this->environmentKeys($root.'/.env'),
            $this->environmentKeys($root.'/.env.testing'),
        ));

        foreach ($keys as $key) {
            unset($_SERVER[$key], $_ENV[$key]);
            putenv($key);
        }

        $_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = 'testing';
        putenv('APP_ENV=testing');
    }

    /**
     * Variable names declared in an env file.
     *
     * @return array<int, string>
     */
    private function environmentKeys(string $path): array
    {
        if (!is_file($path)) {
            return [];
        }

        $keys = [];
        foreach (file($path, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
            if (preg_match('/^\s*(?:export\s+)?([A-Z0-9_]+)\s*=/', $line, $matches)) {
                $keys[] = $matches[1];
            }
        }

        return $keys;
    }
}
