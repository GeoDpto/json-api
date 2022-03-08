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
    /**
     * @var int|null|string
     */
    private $index;

    /**
     * @var iterable|null
     */
    private $links;

    /**
     * @var iterable|null
     */
    private $typeLinks;

    /**
     * @var null|string
     */
    private $status;

    /**
     * @var null|string
     */
    private $code;

    /**
     * @var null|string
     */
    private $title;

    /**
     * @var null|string
     */
    private $detail;

    /**
     * @var array|null
     */
    private $source;

    /**
     * @var bool
     */
    private $hasMeta;

    /**
     * @var mixed
     */
    private $meta;

    /**
     * @param int|null|string $idx
     * @param mixed           $meta
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     * @SuppressWarnings(PHPMD.IfStatementAssignment)
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $idx = null,
        LinkInterface $aboutLink = null,
        ?iterable $typeLinks = null,
        string $status = null,
        string $code = null,
        string $title = null,
        string $detail = null,
        array $source = null,
        bool $hasMeta = false,
        $meta = null
    ) {
        $this
            ->setId($idx)
            ->setLink(DocumentInterface::KEYWORD_ERRORS_ABOUT, $aboutLink)
            ->setTypeLinks($typeLinks)
            ->setStatus($status)
            ->setCode($code)
            ->setTitle($title)
            ->setDetail($detail)
            ->setSource($source);

        if (($this->hasMeta = $hasMeta) === true) {
            $this->setMeta($meta);
        }
    }

    public function getId()
    {
        return $this->index;
    }

    /**
     * @param int|null|string $index
     */
    public function setId($index): self
    {
        \assert($index === null || \is_int($index) === true || \is_string($index) === true);

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

    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param mixed|null $meta
     */
    public function setMeta($meta): self
    {
        $this->hasMeta = true;
        $this->meta    = $meta;

        return $this;
    }
}
