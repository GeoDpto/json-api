<?php declare(strict_types=1);

namespace Neomerx\Tests\JsonApi\Extension\Issue91;

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

/**
 * @package Neomerx\Tests\JsonApi
 */
class Category
{
    public ?int $index = null;
    public ?string $description = null;
    public ?Category $parent = null;

    public function __construct(int $index, string $description, Category $parent = null)
    {
        $this->index       = $index;
        $this->description = $description;
        $this->parent      = $parent;
    }
}
