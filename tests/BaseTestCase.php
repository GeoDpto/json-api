<?php declare(strict_types=1);

namespace Neomerx\Tests\JsonApi;

/**
 * Copyright 2015-2020 info@neomerx.com
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use Mockery;
use Neomerx\JsonApi\Contracts\Factories\FactoryInterface;
use Neomerx\JsonApi\Factories\Factory;
use PHPUnit\Framework\TestCase;

/**
 * @package Neomerx\JsonApi
 */
abstract class BaseTestCase extends TestCase
{
    /**
     * Tear down test.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    /**
     * @return FactoryInterface
     */
    protected function createFactory(): FactoryInterface
    {
        return new Factory();
    }
}
