<?php

declare(strict_types=1);

namespace Neomerx\JsonApi\Schema;

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

use Neomerx\JsonApi\Contract\Schema\IdentifierInterface;

/**
 * @package Neomerx\JsonApi
 */
class Identifier implements IdentifierInterface
{
    private String $index;
    private string $type;
    private bool $hasMeta;
    private mixed $meta;

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function __construct(string $index, string $type, bool $hasMeta = false, mixed $meta = null)
    {
        $this->setId($index);
        $this->setType($type);

        $this->hasMeta = $hasMeta;
        $this->meta    = $meta;
    }

    public function getId(): string
    {
        return $this->index;
    }

    public function setId(string $index): self
    {
        $this->index = $index;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function hasIdentifierMeta(): bool
    {
        return $this->hasMeta;
    }

    public function getIdentifierMeta(): mixed
    {
        return $this->meta;
    }

    public function setIdentifierMeta(mixed $meta): self
    {
        $this->meta    = $meta;
        $this->hasMeta = true;

        return $this;
    }
}
