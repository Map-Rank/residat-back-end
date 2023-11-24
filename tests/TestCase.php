<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Set up your test environment to handle test without exceptions nor depreciation handling.
     */
    public function setUp() : void{

        parent::setUp();

        $this->withoutExceptionHandling();
        $this->withoutDeprecationHandling();
    }
}
