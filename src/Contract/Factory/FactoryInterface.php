<?php

declare(strict_types=1);

namespace Neomerx\JsonApi\Contract\Factory;

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

/**
 * @package Neomerx\JsonApi
 */
interface FactoryInterface
{
    public function createEncoder(SchemaContainerInterface $container): EncoderInterface;

    public function createSchemaContainer(iterable $schemas): SchemaContainerInterface;

    public function createParser(
        SchemaContainerInterface $container,
        EditableContextInterface $context
    ): ParserInterface;

    public function createPosition(
        int $level,
        string $path,
        ?string $parentType,
        ?string $parentRelationship
    ): PositionInterface;

    public function createDocumentWriter(): DocumentWriterInterface;

    public function createErrorWriter(): ErrorWriterInterface;

    /**
     * Create filter for attributes and relationships.
     */
    public function createFieldSetFilter(array $fieldSets): FieldSetFilterInterface;

    /**
     * Create parsed resource over raw resource data.
     *
     * @param mixed $data
     *
     */
    public function createParsedResource(
        EditableContextInterface $context,
        PositionInterface $position,
        SchemaContainerInterface $container,
        $data
    ): ResourceInterface;

    /**
     * Create parsed identifier over raw resource identifier.
     *
     */
    public function createParsedIdentifier(
        PositionInterface $position,
        SchemaIdentifierInterface $identifier
    ): ParserIdentifierInterface;

    /**
     * Create link.
     *
     * @param bool   $isSubUrl If value is either full URL or sub-URL.
     * @param string $value    Either full URL or sub-URL.
     * @param bool   $hasMeta  If links has meta information.
     * @param null   $meta     Value for meta.
     *
     */
    public function createLink(bool $isSubUrl, string $value, bool $hasMeta, $meta = null): LinkInterface;

    /**
     * Create parsed relationship.
     *
     * @param mixed $meta
     *
     */
    public function createRelationship(
        PositionInterface $position,
        bool $hasData,
        ?RelationshipDataInterface $data,
        bool $hasLinks,
        ?iterable $links,
        bool $hasMeta,
        $meta
    ): RelationshipInterface;

    /**
     * Create relationship that represents resource.
     *
     * @param mixed $resource
     *
     */
    public function createRelationshipDataIsResource(
        SchemaContainerInterface $schemaContainer,
        EditableContextInterface $context,
        PositionInterface $position,
        $resource
    ): RelationshipDataInterface;

    /**
     * Create relationship that represents identifier.
     */
    public function createRelationshipDataIsIdentifier(
        SchemaContainerInterface $schemaContainer,
        EditableContextInterface $context,
        PositionInterface $position,
        SchemaIdentifierInterface $identifier
    ): RelationshipDataInterface;

    /**
     * Create relationship that represents collection.
     */
    public function createRelationshipDataIsCollection(
        SchemaContainerInterface $schemaContainer,
        EditableContextInterface $context,
        PositionInterface $position,
        iterable $resources
    ): RelationshipDataInterface;

    /**
     * Create relationship that represents `null`.
     *
     */
    public function createRelationshipDataIsNull(): RelationshipDataInterface;

    /**
     * Create media type.
     *
     * @param array<string,string>|null $parameters
     *
     */
    public function createMediaType(string $type, string $subType, array $parameters = null): MediaTypeInterface;

    /**
     * Create media type for Accept HTTP header.
     *
     * @param array<string,string>|null $parameters
     *
     */
    public function createAcceptMediaType(
        int $position,
        string $type,
        string $subType,
        array $parameters = null,
        float $quality = 1.0
    ): AcceptMediaTypeInterface;

    public function createParserContext(array $fieldSets, array $includePaths): EditableContextInterface;
}
