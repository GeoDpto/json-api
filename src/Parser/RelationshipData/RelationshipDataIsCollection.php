<?php

declare(strict_types=1);

namespace Neomerx\JsonApi\Parser\RelationshipData;

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
use Neomerx\JsonApi\Contract\Parser\IdentifierInterface as ParserIdentifierInterface;
use Neomerx\JsonApi\Contract\Parser\RelationshipDataInterface;
use Neomerx\JsonApi\Contract\Parser\ResourceInterface;
use Neomerx\JsonApi\Contract\Schema\IdentifierInterface as SchemaIdentifierInterface;
use Neomerx\JsonApi\Contract\Schema\PositionInterface;
use Neomerx\JsonApi\Contract\Schema\SchemaContainerInterface;
use Neomerx\JsonApi\Exception\LogicException;
use function Neomerx\JsonApi\I18n\format as _;

/**
 * @package Neomerx\JsonApi
 */
class RelationshipDataIsCollection extends BaseRelationshipData implements RelationshipDataInterface
{
    public const MSG_INVALID_OPERATION = 'Invalid operation.';

    private iterable $resources;
    private ?iterable $parsedResources = null;

    public function __construct(
        FactoryInterface $factory,
        SchemaContainerInterface $schemaContainer,
        EditableContextInterface $context,
        PositionInterface $position,
        iterable $resources,
    ) {
        parent::__construct($factory, $schemaContainer, $context, $position);

        $this->resources = $resources;
    }

    public function isCollection(): bool
    {
        return true;
    }

    public function isNull(): bool
    {
        return false;
    }

    public function isResource(): bool
    {
        return false;
    }

    public function isIdentifier(): bool
    {
        return false;
    }

    public function getIdentifier(): ParserIdentifierInterface
    {
        throw new LogicException(_(static::MSG_INVALID_OPERATION));
    }

    public function getIdentifiers(): iterable
    {
        return $this->getResources();
    }

    public function getResource(): ResourceInterface
    {
        throw new LogicException(_(static::MSG_INVALID_OPERATION));
    }

    public function getResources(): iterable
    {
        if ($this->parsedResources === null) {
            foreach ($this->resources as $resourceOrIdentifier) {
                $parsedResource          = $resourceOrIdentifier instanceof SchemaIdentifierInterface ?
                    $this->createParsedIdentifier($resourceOrIdentifier) :
                    $this->createParsedResource($resourceOrIdentifier);
                $this->parsedResources[] = $parsedResource;

                yield $parsedResource;
            }

            return;
        }

        yield from $this->parsedResources;
    }
}
