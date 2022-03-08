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
use Neomerx\JsonApi\Contract\Parser\IdentifierInterface;
use Neomerx\JsonApi\Contract\Parser\RelationshipDataInterface;
use Neomerx\JsonApi\Contract\Parser\ResourceInterface;
use Neomerx\JsonApi\Contract\Schema\PositionInterface;
use Neomerx\JsonApi\Contract\Schema\SchemaContainerInterface;
use Neomerx\JsonApi\Exception\LogicException;
use function Neomerx\JsonApi\I18n\format as _;

/**
 * @package Neomerx\JsonApi
 */
class RelationshipDataIsResource extends BaseRelationshipData implements RelationshipDataInterface
{
    /** @var string */
    public const MSG_INVALID_OPERATION = 'Invalid operation.';

    /**
     * @var mixed
     */
    private $resource;

    /**
     * @var null|ResourceInterface
     */
    private $parsedResource = null;

    /**
     * @param mixed $resource
     */
    public function __construct(
        FactoryInterface $factory,
        SchemaContainerInterface $schemaContainer,
        EditableContextInterface $context,
        PositionInterface $position,
        $resource
    ) {
        parent::__construct($factory, $schemaContainer, $context, $position);

        $this->resource = $resource;
    }

    public function isCollection(): bool
    {
        return false;
    }

    public function isNull(): bool
    {
        return false;
    }

    public function isResource(): bool
    {
        return true;
    }

    public function isIdentifier(): bool
    {
        return false;
    }

    public function getIdentifier(): IdentifierInterface
    {
        throw new LogicException(_(static::MSG_INVALID_OPERATION));
    }

    public function getIdentifiers(): iterable
    {
        throw new LogicException(_(static::MSG_INVALID_OPERATION));
    }

    public function getResource(): ResourceInterface
    {
        if ($this->parsedResource === null) {
            $this->parsedResource = $this->createParsedResource($this->resource);
        }

        return $this->parsedResource;
    }

    public function getResources(): iterable
    {
        throw new LogicException(_(static::MSG_INVALID_OPERATION));
    }
}
