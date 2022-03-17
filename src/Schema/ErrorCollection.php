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

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Neomerx\JsonApi\Contract\Schema\DocumentInterface;
use Neomerx\JsonApi\Contract\Schema\ErrorInterface;
use Neomerx\JsonApi\Contract\Schema\LinkInterface;
use Serializable;

/**
 * @package Neomerx\JsonApi
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
class ErrorCollection implements IteratorAggregate, ArrayAccess, Serializable, Countable
{
    private array $items = [];

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    public function count(): int
    {
        return \count($this->items);
    }

    public function serialize(): string
    {
        return \serialize($this->items);
    }

    public function unserialize(string $data): void
    {
        $this->items = \unserialize($data);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet(mixed  $offset): ErrorInterface
    {
        return $this->items[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $offset === null ? $this->add($value) : $this->items[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }

    /**
     * @return ErrorInterface[]
     */
    public function getArrayCopy(): array
    {
        return $this->items;
    }

    public function add(ErrorInterface $error): self
    {
        $this->items[] = $error;

        return $this;
    }

    public function addDataError(
        string $title,
        ?string $detail = null,
        ?string $status = null,
        ?string $idx = null,
        LinkInterface $aboutLink = null,
        ?iterable $typeLinks = null,
        string $code = null,
        bool $hasMeta = false,
        mixed $meta = null,
    ): self {
        $pointer = $this->getPathToData();

        return $this->addResourceError(
            $title,
            $pointer,
            $detail,
            $status,
            $idx,
            $aboutLink,
            $typeLinks,
            $code,
            $hasMeta,
            $meta
        );
    }

    public function addDataTypeError(
        string $title,
        ?string $detail = null,
        ?string $status = null,
        ?string $idx = null,
        LinkInterface $aboutLink = null,
        ?iterable $typeLinks = null,
        string $code = null,
        bool $hasMeta = false,
        mixed $meta = null
    ): self {
        $pointer = $this->getPathToType();

        return $this->addResourceError(
            $title,
            $pointer,
            $detail,
            $status,
            $idx,
            $aboutLink,
            $typeLinks,
            $code,
            $hasMeta,
            $meta
        );
    }

    public function addDataIdError(
        string $title,
        ?string $detail = null,
        ?string $status = null,
        ?string $idx = null,
        LinkInterface $aboutLink = null,
        ?iterable $typeLinks = null,
        string $code = null,
        bool $hasMeta = false,
        mixed $meta = null
    ): self {
        $pointer = $this->getPathToId();

        return $this->addResourceError(
            $title,
            $pointer,
            $detail,
            $status,
            $idx,
            $aboutLink,
            $typeLinks,
            $code,
            $hasMeta,
            $meta
        );
    }

    public function addAttributesError(
        string $title,
        ?string $detail = null,
        ?string $status = null,
        ?string $idx = null,
        LinkInterface $aboutLink = null,
        ?iterable $typeLinks = null,
        string $code = null,
        bool $hasMeta = false,
        mixed $meta = null
    ): self {
        $pointer = $this->getPathToAttributes();

        return $this->addResourceError(
            $title,
            $pointer,
            $detail,
            $status,
            $idx,
            $aboutLink,
            $typeLinks,
            $code,
            $hasMeta,
            $meta
        );
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function addDataAttributeError(
        string $name,
        string $title,
        ?string $detail = null,
        ?string $status = null,
        ?string $idx = null,
        ?LinkInterface $aboutLink = null,
        ?iterable $typeLinks = null,
        string $code = null,
        bool $hasMeta = false,
        mixed $meta = null,
    ): self {
        $pointer = $this->getPathToAttribute($name);

        return $this->addResourceError(
            $title,
            $pointer,
            $detail,
            $status,
            $idx,
            $aboutLink,
            $typeLinks,
            $code,
            $hasMeta,
            $meta
        );
    }

    public function addRelationshipsError(
        string $title,
        ?string $detail = null,
        ?string $status = null,
        ?string $idx = null,
        ?LinkInterface $aboutLink = null,
        ?iterable $typeLinks = null,
        string $code = null,
        bool $hasMeta = false,
        $meta = null,
    ): self {
        $pointer = $this->getPathToRelationships();

        return $this->addResourceError(
            $title,
            $pointer,
            $detail,
            $status,
            $idx,
            $aboutLink,
            $typeLinks,
            $code,
            $hasMeta,
            $meta
        );
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function addRelationshipError(
        string $name,
        string $title,
        ?string $detail = null,
        ?string $status = null,
        ?string $idx = null,
        ?LinkInterface $aboutLink = null,
        ?iterable $typeLinks = null,
        string $code = null,
        bool $hasMeta = false,
        mixed $meta = null
    ): self {
        $pointer = $this->getPathToRelationship($name);

        return $this->addResourceError(
            $title,
            $pointer,
            $detail,
            $status,
            $idx,
            $aboutLink,
            $typeLinks,
            $code,
            $hasMeta,
            $meta
        );
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function addRelationshipTypeError(
        string $name,
        string $title,
        ?string $detail = null,
        ?string $status = null,
        ?string $idx = null,
        ?LinkInterface $aboutLink = null,
        ?iterable $typeLinks = null,
        string $code = null,
        bool $hasMeta = false,
        mixed $meta = null,
    ): self {
        $pointer = $this->getPathToRelationshipType($name);

        return $this->addResourceError(
            $title,
            $pointer,
            $detail,
            $status,
            $idx,
            $aboutLink,
            $typeLinks,
            $code,
            $hasMeta,
            $meta
        );
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function addRelationshipIdError(
        string $name,
        string $title,
        ?string $detail = null,
        ?string $status = null,
        ?string $idx = null,
        ?LinkInterface $aboutLink = null,
        ?iterable $typeLinks = null,
        string $code = null,
        bool $hasMeta = false,
        mixed $meta = null,
    ): self {
        $pointer = $this->getPathToRelationshipId($name);

        return $this->addResourceError(
            $title,
            $pointer,
            $detail,
            $status,
            $idx,
            $aboutLink,
            $typeLinks,
            $code,
            $hasMeta,
            $meta
        );
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function addQueryParameterError(
        string $name,
        string $title,
        string $detail = null,
        string $status = null,
        ?string $idx = null,
        ?LinkInterface $aboutLink = null,
        ?iterable $typeLinks = null,
        ?string $code = null,
        bool $hasMeta = false,
        mixed $meta = null,
    ): self {
        $source = [ErrorInterface::SOURCE_PARAMETER => $name];
        $error  = new Error($idx, $aboutLink, $typeLinks, $status, $code, $title, $detail, $source, $hasMeta, $meta);

        $this->add($error);

        return $this;
    }

    /** @noinspection PhpTooManyParametersInspection
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    protected function addResourceError(
        string $title,
        string $pointer,
        ?string $detail = null,
        ?string $status = null,
        ?string $idx = null,
        ?LinkInterface $aboutLink = null,
        ?iterable $typeLinks = null,
        string $code = null,
        bool $hasMeta = false,
        mixed $meta = null,
    ): self {
        $source = [ErrorInterface::SOURCE_POINTER => $pointer];
        $error  = new Error($idx, $aboutLink, $typeLinks, $status, $code, $title, $detail, $source, $hasMeta, $meta);

        $this->add($error);

        return $this;
    }

    protected function getPathToData(): string
    {
        return '/' . DocumentInterface::KEYWORD_DATA;
    }

    protected function getPathToType(): string
    {
        return $this->getPathToData() . '/' . DocumentInterface::KEYWORD_TYPE;
    }

    protected function getPathToId(): string
    {
        return $this->getPathToData() . '/' . DocumentInterface::KEYWORD_ID;
    }

    protected function getPathToAttributes(): string
    {
        return $this->getPathToData() . '/' . DocumentInterface::KEYWORD_ATTRIBUTES;
    }

    protected function getPathToAttribute(string $name): string
    {
        return $this->getPathToData() . '/' . DocumentInterface::KEYWORD_ATTRIBUTES . '/' . $name;
    }

    protected function getPathToRelationships(): string
    {
        return $this->getPathToData() . '/' . DocumentInterface::KEYWORD_RELATIONSHIPS;
    }

    protected function getPathToRelationship(string $name): string
    {
        return $this->getPathToRelationships() . '/' . $name;
    }

    protected function getPathToRelationshipType(string $name): string
    {
        return $this->getPathToRelationship($name) . '/' .
            DocumentInterface::KEYWORD_DATA . '/' . DocumentInterface::KEYWORD_TYPE;
    }

    protected function getPathToRelationshipId(string $name): string
    {
        return $this->getPathToRelationship($name) . '/' .
            DocumentInterface::KEYWORD_DATA . '/' . DocumentInterface::KEYWORD_ID;
    }
}
