<?php

declare(strict_types=1);

namespace Neomerx\JsonApi\Parser;

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

use Neomerx\JsonApi\Contract\Factory\FactoryInterface;
use Neomerx\JsonApi\Contract\Parser\EditableContextInterface;
use Neomerx\JsonApi\Contract\Parser\ParserInterface;
use Neomerx\JsonApi\Contract\Parser\ResourceInterface;
use Neomerx\JsonApi\Contract\Schema\LinkInterface;
use Neomerx\JsonApi\Contract\Schema\PositionInterface;
use Neomerx\JsonApi\Contract\Schema\SchemaContainerInterface;
use Neomerx\JsonApi\Contract\Schema\SchemaInterface;
use Neomerx\JsonApi\Parser\RelationshipData\ParseRelationshipDataTrait;
use Neomerx\JsonApi\Parser\RelationshipData\ParseRelationshipLinksTrait;

/**
 * @package Neomerx\JsonApi
 */
class IdentifierAndResource implements ResourceInterface
{
    use ParseRelationshipDataTrait, ParseRelationshipLinksTrait;

    public const MSG_NO_SCHEMA_FOUND = 'No Schema found for resource `%s` at path `%s`.';
    public const MSG_INVALID_OPERATION = 'Invalid operation.';

    private EditableContextInterface $context;
    private PositionInterface $position;
    private FactoryInterface $factory;
    private SchemaContainerInterface $schemaContainer;
    private SchemaInterface $schema;
    private mixed $data;
    private ?string $index;
    private string $type;
    private array $links = [];
    private array $relationshipsCache = [];

    public function __construct(
        EditableContextInterface $context,
        PositionInterface $position,
        FactoryInterface $factory,
        SchemaContainerInterface $container,
        mixed $data,
    ) {
        \assert($position->getLevel() >= ParserInterface::ROOT_LEVEL);

        $schema = $container->getSchema($data);

        $this->context         = $context;
        $this->position        = $position;
        $this->factory         = $factory;
        $this->schemaContainer = $container;
        $this->schema          = $schema;
        $this->data            = $data;
        $this->index           = $schema->getId($data);
        $this->type            = $schema->getType();
    }

    public function getPosition(): PositionInterface
    {
        return $this->position;
    }

    public function getId(): ?string
    {
        return $this->index;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function hasIdentifierMeta(): bool
    {
        return $this->schema->hasIdentifierMeta($this->data);
    }

    public function getIdentifierMeta(): mixed
    {
        return $this->schema->getIdentifierMeta($this->data);
    }

    public function getAttributes(): iterable
    {
        $this->getContext()->setPosition($this->getPosition());

        return $this->schema->getAttributes($this->data, $this->getContext());
    }

    /**
     * @SuppressWarnings(PHPMD.UndefinedVariable) PHPMD currently do not support `list` in `[]` syntax
     */
    public function getRelationships(): iterable
    {
        if (\count($this->relationshipsCache) > 0) {
            yield from $this->relationshipsCache;

            return;
        }

        $this->relationshipsCache = [];

        $currentPath    = $this->position->getPath();
        $nextLevel      = $this->position->getLevel() + 1;
        $nextPathPrefix = empty($currentPath) === true ? '' : $currentPath . PositionInterface::PATH_SEPARATOR;
        $this->getContext()->setPosition($this->getPosition());
        foreach ($this->schema->getRelationships($this->data, $this->getContext()) as $name => $description) {
            \assert($this->assertRelationshipNameAndDescription($name, $description) === true);

            [$hasData, $relationshipData, $nextPosition] = $this->parseRelationshipData(
                $this->factory,
                $this->schemaContainer,
                $this->getContext(),
                $this->type,
                $name,
                $description,
                $nextLevel,
                $nextPathPrefix
            );

            [$hasLinks, $links] =
                $this->parseRelationshipLinks($this->schema, $this->data, $name, $description);

            $hasMeta = \array_key_exists(SchemaInterface::RELATIONSHIP_META, $description);
            $meta    = $hasMeta === true ? $description[SchemaInterface::RELATIONSHIP_META] : null;

            \assert(
                $hasData || $hasMeta || $hasLinks,
                "Relationship `$name` for type `" . $this->getType() .
                '` MUST contain at least one of the following: links, data or meta.'
            );

            $relationship = $this->factory->createRelationship(
                $nextPosition,
                $hasData,
                $relationshipData,
                $hasLinks,
                $links,
                $hasMeta,
                $meta
            );

            $this->relationshipsCache[$name] = $relationship;

            yield $name => $relationship;
        }
    }

    public function hasLinks(): bool
    {
        $this->cacheLinks();

        return empty($this->links) === false;
    }

    public function getLinks(): iterable
    {
        $this->cacheLinks();

        return $this->links;
    }

    public function hasResourceMeta(): bool
    {
        return $this->schema->hasResourceMeta($this->data);
    }

    public function getResourceMeta()
    {
        return $this->schema->getResourceMeta($this->data);
    }

    protected function getContext(): EditableContextInterface
    {
        return $this->context;
    }

    /**
     * Read and parse links from schema.
     */
    private function cacheLinks(): void
    {
        if (\count($this->links) === 0) {
            $this->links = [];
            foreach ($this->schema->getLinks($this->data) as $name => $link) {
                \assert(\is_string($name) === true && empty($name) === false);
                \assert($link instanceof LinkInterface);
                $this->links[$name] = $link;
            }
        }
    }

    private function assertRelationshipNameAndDescription(string $name, array $description): bool
    {
        \assert(
            \is_string($name) === true && empty($name) === false,
            "Relationship names for type `" . $this->getType() . '` should be non-empty strings.'
        );
        \assert(
            \is_array($description) === true && empty($description) === false,
            "Relationship `$name` for type `" . $this->getType() . '` should be a non-empty array.'
        );

        return true;
    }
}
