<?php

declare(strict_types=1);

namespace Neomerx\JsonApi\Factory;

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

use Neomerx\JsonApi\Contract\Encoder\EncoderInterface;
use Neomerx\JsonApi\Contract\Factory\FactoryInterface;
use Neomerx\JsonApi\Contract\Http\Header\AcceptMediaTypeInterface;
use Neomerx\JsonApi\Contract\Http\Header\MediaTypeInterface;
use Neomerx\JsonApi\Contract\Parser\EditableContextInterface;
use Neomerx\JsonApi\Contract\Parser\IdentifierInterface as ParserIdentifierInterface;
use Neomerx\JsonApi\Contract\Parser\ParserInterface;
use Neomerx\JsonApi\Contract\Parser\RelationshipDataInterface;
use Neomerx\JsonApi\Contract\Parser\RelationshipInterface;
use Neomerx\JsonApi\Contract\Parser\ResourceInterface;
use Neomerx\JsonApi\Contract\Representation\DocumentWriterInterface;
use Neomerx\JsonApi\Contract\Representation\ErrorWriterInterface;
use Neomerx\JsonApi\Contract\Representation\FieldSetFilterInterface;
use Neomerx\JsonApi\Contract\Schema\IdentifierInterface as SchemaIdentifierInterface;
use Neomerx\JsonApi\Contract\Schema\LinkInterface;
use Neomerx\JsonApi\Contract\Schema\PositionInterface;
use Neomerx\JsonApi\Contract\Schema\SchemaContainerInterface;
use Neomerx\JsonApi\Encoder\Encoder;
use Neomerx\JsonApi\Http\Header\AcceptMediaType;
use Neomerx\JsonApi\Http\Header\MediaType;
use Neomerx\JsonApi\Parser\IdentifierAndResource;
use Neomerx\JsonApi\Parser\Parser;
use Neomerx\JsonApi\Parser\RelationshipData\RelationshipDataIsCollection;
use Neomerx\JsonApi\Parser\RelationshipData\RelationshipDataIsIdentifier;
use Neomerx\JsonApi\Parser\RelationshipData\RelationshipDataIsNull;
use Neomerx\JsonApi\Parser\RelationshipData\RelationshipDataIsResource;
use Neomerx\JsonApi\Representation\DocumentWriter;
use Neomerx\JsonApi\Representation\ErrorWriter;
use Neomerx\JsonApi\Representation\FieldSetFilter;
use Neomerx\JsonApi\Schema\Link;
use Neomerx\JsonApi\Schema\SchemaContainer;

/**
 * @package Neomerx\JsonApi
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Factory implements FactoryInterface
{
    public function createEncoder(SchemaContainerInterface $container): EncoderInterface
    {
        return new Encoder($this, $container);
    }

    public function createSchemaContainer(iterable $schemas): SchemaContainerInterface
    {
        return new SchemaContainer($this, $schemas);
    }

    public function createPosition(
        int $level,
        string $path,
        ?string $parentType,
        ?string $parentRelationship,
    ): PositionInterface {
        return new class($level, $path, $parentType, $parentRelationship) implements PositionInterface {
            private int $level;
            private string $path;
            private ?string $parentType;
            private ?string $parentRelationship;

            public function __construct(int $level, string $path, ?string $parentType, ?string $parentRelationship)
            {
                $this->level              = $level;
                $this->path               = $path;
                $this->parentType         = $parentType;
                $this->parentRelationship = $parentRelationship;
            }

            public function getLevel(): int
            {
                return $this->level;
            }

            public function getPath(): string
            {
                return $this->path;
            }

            public function getParentType(): ?string
            {
                return $this->parentType;
            }

            public function getParentRelationship(): ?string
            {
                return $this->parentRelationship;
            }
        };
    }

    public function createParser(
        SchemaContainerInterface $container,
        EditableContextInterface $context
    ): ParserInterface {
        return new Parser($this, $container, $context);
    }

    public function createDocumentWriter(): DocumentWriterInterface
    {
        return new DocumentWriter();
    }

    public function createErrorWriter(): ErrorWriterInterface
    {
        return new ErrorWriter();
    }

    public function createFieldSetFilter(array $fieldSets): FieldSetFilterInterface
    {
        return new FieldSetFilter($fieldSets);
    }

    public function createParsedResource(
        EditableContextInterface $context,
        PositionInterface $position,
        SchemaContainerInterface $container,
        mixed $data,
    ): ResourceInterface {
        return new IdentifierAndResource($context, $position, $this, $container, $data);
    }

    public function createParsedIdentifier(
        PositionInterface $position,
        SchemaIdentifierInterface $identifier,
    ): ParserIdentifierInterface {
        return new class($position, $identifier) implements ParserIdentifierInterface {
            private PositionInterface $position;
            private SchemaIdentifierInterface $identifier;

            public function __construct(
                PositionInterface $position,
                SchemaIdentifierInterface $identifier,
            ) {
                $this->position   = $position;
                $this->identifier = $identifier;

                // for test coverage only
                \assert($this->getPosition() !== null);
            }

            public function getType(): string
            {
                return $this->identifier->getType();
            }

            public function getId(): ?string
            {
                return $this->identifier->getId();
            }

            public function hasIdentifierMeta(): bool
            {
                return $this->identifier->hasIdentifierMeta();
            }

            public function getIdentifierMeta(): mixed
            {
                return $this->identifier->getIdentifierMeta();
            }

            public function getPosition(): PositionInterface
            {
                return $this->position;
            }
        };
    }

    public function createLink(bool $isSubUrl, string $value, bool $hasMeta, $meta = null): LinkInterface
    {
        return new Link($isSubUrl, $value, $hasMeta, $meta);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function createRelationship(
        PositionInterface $position,
        bool $hasData,
        ?RelationshipDataInterface $data,
        bool $hasLinks,
        ?iterable $links,
        bool $hasMeta,
        mixed $meta,
    ): RelationshipInterface {
        return
            new class($position, $hasData, $data, $hasLinks, $links, $hasMeta, $meta) implements RelationshipInterface {
                private PositionInterface $position;
                private bool $hasData;
                private ?RelationshipDataInterface $data;
                private bool $hasLinks;
                private ?iterable $links;
                private bool $hasMeta;
                private mixed $meta;
                private bool $metaIsCallable;

                public function __construct(
                    PositionInterface $position,
                    bool $hasData,
                    ?RelationshipDataInterface $data,
                    bool $hasLinks,
                    ?iterable $links,
                    bool $hasMeta,
                    mixed $meta,
                ) {
                    \assert($position->getLevel() > ParserInterface::ROOT_LEVEL);
                    \assert(empty($position->getPath()) === false);
                    \assert(($hasData === false && $data === null) || ($hasData === true && $data !== null));
                    \assert(($hasLinks === false && $links === null) || ($hasLinks === true && $links !== null));

                    $this->position       = $position;
                    $this->hasData        = $hasData;
                    $this->data           = $data;
                    $this->hasLinks       = $hasLinks;
                    $this->links          = $links;
                    $this->hasMeta        = $hasMeta;
                    $this->meta           = $meta;
                    $this->metaIsCallable = \is_callable($meta);
                }

                public function getPosition(): PositionInterface
                {
                    return $this->position;
                }

                public function hasData(): bool
                {
                    return $this->hasData;
                }

                public function getData(): RelationshipDataInterface
                {
                    \assert($this->hasData());

                    return $this->data;
                }

                public function hasLinks(): bool
                {
                    return $this->hasLinks;
                }

                public function getLinks(): iterable
                {
                    \assert($this->hasLinks());

                    return $this->links;
                }

                public function hasMeta(): bool
                {
                    return $this->hasMeta;
                }

                public function getMeta()
                {
                    \assert($this->hasMeta());

                    if ($this->metaIsCallable === true) {
                        $this->meta           = \call_user_func($this->meta);
                        $this->metaIsCallable = false;
                    }

                    return $this->meta;
                }
            };
    }

    public function createRelationshipDataIsResource(
        SchemaContainerInterface $schemaContainer,
        EditableContextInterface $context,
        PositionInterface $position,
        object $resource,
    ): RelationshipDataInterface {
        return new RelationshipDataIsResource($this, $schemaContainer, $context, $position, $resource);
    }

    public function createRelationshipDataIsIdentifier(
        SchemaContainerInterface $schemaContainer,
        EditableContextInterface $context,
        PositionInterface $position,
        SchemaIdentifierInterface $identifier,
    ): RelationshipDataInterface {
        return new RelationshipDataIsIdentifier($this, $schemaContainer, $context, $position, $identifier);
    }

    public function createRelationshipDataIsCollection(
        SchemaContainerInterface $schemaContainer,
        EditableContextInterface $context,
        PositionInterface $position,
        iterable $resources,
    ): RelationshipDataInterface {
        return new RelationshipDataIsCollection($this, $schemaContainer, $context, $position, $resources);
    }

    public function createRelationshipDataIsNull(): RelationshipDataInterface
    {
        return new RelationshipDataIsNull();
    }

    public function createMediaType(string $type, string $subType, array $parameters = null): MediaTypeInterface
    {
        return new MediaType($type, $subType, $parameters);
    }

    public function createAcceptMediaType(
        int $position,
        string $type,
        string $subType,
        array $parameters = null,
        float $quality = 1.0,
    ): AcceptMediaTypeInterface {
        return new AcceptMediaType($position, $type, $subType, $parameters, $quality);
    }

    /**
     * @SuppressWarnings(PHPMD.UndefinedVariable) PHPMD currently has a glitch with `$position` in `setPosition`
     */
    public function createParserContext(array $fieldSets, array $includePaths): EditableContextInterface
    {
        return new class($fieldSets, $includePaths) implements EditableContextInterface {
            private array $fieldSets;
            private array $includePaths;
            private ?PositionInterface $position = null;

            public function __construct(array $fieldSets, array $includePaths)
            {
                $this->fieldSets    = $fieldSets;
                $this->includePaths = $includePaths;
            }

            public function getFieldSets(): array
            {
                return $this->fieldSets;
            }

            public function getIncludePaths(): array
            {
                return $this->includePaths;
            }

            public function getPosition(): PositionInterface
            {
                // parser's implementation should guarantee that position will always be initialized
                // before use in a schema.
                \assert($this->position !== null);

                return $this->position;
            }

            public function setPosition(PositionInterface $position): void
            {
                $this->position = $position;
            }
        };
    }
}
