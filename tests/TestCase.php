<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia;

abstract class TestCase extends BaseTestCase
{
    /**
     * Re-register assertInertia with JSON_PRESERVE_ZERO_FRACTION so that float
     * props (e.g. today_sales_total = 100.0) survive json_encode/json_decode
     * as PHP floats, not ints, enabling assertSame(100.0, ...) to pass.
     */
    protected function setUp(): void
    {
        parent::setUp();

        TestResponse::macro('assertInertia', function (?callable $callback = null) {
            /** @var \Illuminate\Testing\TestResponse $this */
            $this->assertViewHas('page');

            $page = json_decode(
                json_encode($this->viewData('page'), JSON_PRESERVE_ZERO_FRACTION),
                true,
            );

            \PHPUnit\Framework\Assert::assertIsArray($page);

            $instance = AssertableInertia::fromArray($page['props'] ?? []);

            $ref = new \ReflectionObject($instance);
            foreach ([
                'component' => $page['component'] ?? null,
                'url' => $page['url'] ?? null,
                'version' => $page['version'] ?? null,
                'encryptHistory' => isset($page['encryptHistory']),
                'clearHistory' => isset($page['clearHistory']),
                'deferredProps' => $page['deferredProps'] ?? [],
                'flash' => $page['flash'] ?? [],
            ] as $prop => $value) {
                $p = $ref->getProperty($prop);
                $p->setAccessible(true);
                $p->setValue($instance, $value);
            }

            if ($callback !== null) {
                $callback($instance);
            }

            return $this;
        });
    }
}
