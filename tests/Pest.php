<?php

use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

// Each Feature test file declares its own `uses(TestCase::class)`
// to avoid double-binding conflicts.

// Unit tests use PHPUnit's base TestCase directly.

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to
| your project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/*
|--------------------------------------------------------------------------
| Passport Client Setup Helper
|--------------------------------------------------------------------------
|
| Call in beforeEach() of any test file that uses Passport::actingAs().
| Runs Artisan::call() to create a personal access client in Passport v12's
| schema. No static flag — RefreshDatabase wipes tables between test
| classes, so we must create fresh each time. Multiple calls are harmless
| (Passport v12 has no uniqueness constraint on personal access clients).
|
*/
function setupPassport(): void
{
    try {
        Artisan::call('passport:client', [
            '--personal' => true,
            '--name' => 'Test',
            '--no-interaction' => true,
        ]);
    } catch (\Exception $e) {
        // Client may already exist or table not ready yet; silently continue
    }
}
