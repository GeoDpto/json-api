<?php

declare(strict_types=1);

namespace Neomerx\JsonApi\Contract\Parser;

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
 * @package Neomerx\JsonApi
 */
interface ResourceInterface extends IdentifierInterface
{
    public function getAttributes(): iterable;

    public function getRelationships(): iterable;

    public function hasLinks(): bool;

    public function getLinks(): iterable;

    public function hasResourceMeta(): bool;

    public function getResourceMeta();
}
