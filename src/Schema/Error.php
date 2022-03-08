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

use Neomerx\JsonApi\Contract\Schema\DocumentInterface;
use Neomerx\JsonApi\Contract\Schema\ErrorInterface;
use Neomerx\JsonApi\Contract\Schema\LinkInterface;

/**
 * @package Neomerx\JsonApi
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class Error implements ErrorInterface
{
    private ?string $index;
    private ?iterable $links = null;
    private ?iterable $typeLinks = null;
    private ?string $status = null;
    private ?string $code = null;
    private ?string $title = null;
    private ?string $detail = null;
    private array $source = [];
    private bool $hasMeta;
    private mixed $meta;

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     * @SuppressWarnings(PHPMD.IfStatementAssignment)
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ?string $idx = null,
        LinkInterface $aboutLink = null,
        ?iterable $typeLinks = null,
        string $status = null,
        string $code = null,
        string $title = null,
        string $detail = null,
        array $source = null,
        bool $hasMeta = false,
        mixed $meta = null,
    ) {
        $this
            ->setId($idx)
            ->setLink(DocumentInterface::KEYWORD_ERRORS_ABOUT, $aboutLink)
            ->setTypeLinks($typeLinks)
            ->setStatus($status)
            ->setCode($code)
            ->setTitle($title)
            ->setDetail($detail)
            ->setSource($source ?? []);

        if (($this->hasMeta = $hasMeta) === true) {
            $this->setMeta($meta);
        }
    }

    public function getId(): ?string
    {
        return $this->index;
    }

    public function setId(?string $index): self
    {
        $this->index = $index;

        return $this;
    }

    public function getLinks(): ?iterable
    {
        return $this->links;
    }

    public function getTypeLinks(): ?iterable
    {
        return $this->typeLinks;
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function setLink(string $name, ?LinkInterface $link): self
    {
        if ($link !== null) {
            $this->links[$name] = $link;
        } else {
            unset($this->links[$name]);
        }

        return $this;
    }

    public function setTypeLinks(?iterable $typeLinks): self
    {
        $this->typeLinks = $typeLinks;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(?string $detail): self
    {
        $this->detail = $detail;

        return $this;
    }

    public function getSource(): ?array
    {
        return $this->source;
    }

    public function setSource(?array $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function hasMeta(): bool
    {
        return $this->hasMeta;
    }

    public function getMeta(): mixed
    {
        return $this->meta;
    }

    public function setMeta(mixed $meta): self
    {
        $this->hasMeta = true;
        $this->meta    = $meta;

        return $this;
    }
}
