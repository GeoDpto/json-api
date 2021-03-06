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

use IteratorAggregate;
use Neomerx\JsonApi\Contract\Factory\FactoryInterface;
use Neomerx\JsonApi\Contract\Parser\EditableContextInterface;
use Neomerx\JsonApi\Contract\Parser\RelationshipDataInterface;
use Neomerx\JsonApi\Contract\Schema\IdentifierInterface;
use Neomerx\JsonApi\Contract\Schema\PositionInterface;
use Neomerx\JsonApi\Contract\Schema\SchemaContainerInterface;
use Neomerx\JsonApi\Contract\Schema\SchemaInterface;
use Neomerx\JsonApi\Exception\InvalidArgumentException;
use Neomerx\JsonApi\Parser\IdentifierAndResource;
use Traversable;
use function Neomerx\JsonApi\I18n\format as _;

/**
 * @package Neomerx\JsonApi
 */
trait ParseRelationshipDataTrait
{
    /**
     * @return array [has data, parsed data, next position]
     */
    private function parseRelationshipData(
        FactoryInterface $factory,
        SchemaContainerInterface $container,
        EditableContextInterface $context,
        string $parentType,
        string $name,
        array $description,
        int $nextLevel,
        string $nextPathPrefix,
    ): array {
        $hasData = \array_key_exists(SchemaInterface::RELATIONSHIP_DATA, $description);
        // either no data or data should be array/object/null
        \assert(
            $hasData === false ||
            (
                \is_array($data = $description[SchemaInterface::RELATIONSHIP_DATA]) === true ||
                \is_object($data) === true ||
                $data === null
            )
        );

        $nextPosition = $factory->createPosition(
            $nextLevel,
            $nextPathPrefix . $name,
            $parentType,
            $name
        );

        $relationshipData = $hasData === true ? $this->parseData(
            $factory,
            $container,
            $context,
            $nextPosition,
            $description[SchemaInterface::RELATIONSHIP_DATA]
        ) : null;

        return [$hasData, $relationshipData, $nextPosition];
    }

    private function parseData(
        FactoryInterface $factory,
        SchemaContainerInterface $container,
        EditableContextInterface $context,
        PositionInterface $position,
        mixed $data,
    ): RelationshipDataInterface {
        // support if data is callable (e.g. a closure used to postpone actual data reading)
        if (\is_callable($data) === true) {
            $data = \call_user_func($data);
        }

        if (\is_object($data) && $container->hasSchema($data)) {
            return $factory->createRelationshipDataIsResource($container, $context, $position, $data);
        } elseif ($data instanceof IdentifierInterface) {
            return $factory->createRelationshipDataIsIdentifier($container, $context, $position, $data);
        } elseif (\is_array($data) === true) {
            return $factory->createRelationshipDataIsCollection($container, $context, $position, $data);
        } elseif ($data instanceof Traversable) {
            return $factory->createRelationshipDataIsCollection(
                $container,
                $context,
                $position,
                $data instanceof IteratorAggregate ? $data->getIterator() : $data
            );
        } elseif ($data === null) {
            return $factory->createRelationshipDataIsNull();
        }

        throw new InvalidArgumentException(
            _(IdentifierAndResource::MSG_NO_SCHEMA_FOUND, $data::class, $position->getPath())
        );
    }
}
