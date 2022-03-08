<?php

declare(strict_types=1);

namespace Neomerx\JsonApi\Contract\Schema;

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
interface BaseLinkInterface
{
    public const SELF = DocumentInterface::KEYWORD_SELF;
    public const RELATED = DocumentInterface::KEYWORD_RELATED;
    public const FIRST = DocumentInterface::KEYWORD_FIRST;
    public const LAST = DocumentInterface::KEYWORD_LAST;
    public const NEXT = DocumentInterface::KEYWORD_NEXT;
    public const PREV = DocumentInterface::KEYWORD_PREV;
    public const ABOUT = 'about';

    /**
     * If `string` or `array` representation should be used.
     */
    public function canBeShownAsString(): bool;

    public function getStringRepresentation(string $prefix): string;

    public function getArrayRepresentation(string $prefix): array;
}
