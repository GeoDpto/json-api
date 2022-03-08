<?php

declare(strict_types=1);

namespace Neomerx\JsonApi\Contract\Schema;

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

/**
 * @package Neomerx\JsonApi
 */
interface SchemaInterface
{
    /** @var int Relationship's data section */
    public const RELATIONSHIP_DATA = 0;

    /** @var int Relationship's links section */
    public const RELATIONSHIP_LINKS = self::RELATIONSHIP_DATA + 1;

    /** @var int Relationship's meta section */
    public const RELATIONSHIP_META = self::RELATIONSHIP_LINKS + 1;

    /** @var int If `self` link should be added in relationship */
    public const RELATIONSHIP_LINKS_SELF = self::RELATIONSHIP_META + 1;

    /** @var int If `related` link should be added in relationship */
    public const RELATIONSHIP_LINKS_RELATED = self::RELATIONSHIP_LINKS_SELF + 1;

    public function getType(): string;

    /**
     * Get resource identity. Newly created objects without ID may return `null` to exclude it from encoder output.
     *
     */
    public function getId(object $resource): ?string;

    /**
     * Get resource attributes.
     *
     */
    public function getAttributes(object $resource, ContextInterface $context): array;

    /**
     * Get resource relationship descriptions.
     *
     */
    public function getRelationships(object $resource, ContextInterface $context): array;

    /**
     * Get resource sub URL.
     */
    public function getSelfLink(object $resource): LinkInterface;

    /**
     * Get resource links.
     *
     * @see LinkInterface
     */
    public function getLinks(object $resource): iterable;

    /**
     * Get 'self' URL link to resource relationship.
     */
    public function getRelationshipSelfLink(object $resource, string $name): LinkInterface;

    /**
     * Get 'related' URL link to resource relationship.
     */
    public function getRelationshipRelatedLink(object $resource, string $name): LinkInterface;

    /**
     * If resource has meta when it is considered as a resource identifier (e.g. in a relationship).
     *
     */
    public function hasIdentifierMeta(object $resource): bool;

    /**
     * Get resource meta when it is considered as a resource identifier (e.g. in a relationship).
     */
    public function getIdentifierMeta(object $resource): mixed;

    /**
     * If resource has meta when it is considered as a resource (e.g. in a main data or included sections).
     */
    public function hasResourceMeta(object $resource): bool;

    /**
     * Get resource meta when it is considered as a resource (e.g. in a main data or included sections).
     */
    public function getResourceMeta(object $resource): mixed;

    /**
     * If `self` links should be added in relationships by default.
     */
    public function isAddSelfLinkInRelationshipByDefault(string $relationshipName): bool;

    /**
     * If `related` links should be added in relationships by default.
     */
    public function isAddRelatedLinkInRelationshipByDefault(string $relationshipName): bool;
}
