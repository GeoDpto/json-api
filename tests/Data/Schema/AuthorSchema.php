<?php declare(strict_types=1);

namespace Neomerx\Tests\JsonApi\Data\Schema;

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

use Neomerx\JsonApi\Contract\Schema\ContextInterface;
use Neomerx\JsonApi\Contract\Schema\LinkInterface;
use Neomerx\Tests\JsonApi\Data\Model\Author;
use function assert;
use function property_exists;

/**
 * @package Neomerx\Tests\JsonApi
 */
class AuthorSchema extends DevSchema
{
    public function getType(): string
    {
        return 'people';
    }

    public function getId(object $resource): ?string
    {
        assert($resource instanceof Author);

        $index = $resource->{Author::ATTRIBUTE_ID};

        return $index === null ? $index : (string)$index;
    }

    public function getAttributes(object $resource, ContextInterface $context): array
    {
        assert($resource instanceof Author);

        return [
            Author::ATTRIBUTE_FIRST_NAME => $resource->{Author::ATTRIBUTE_FIRST_NAME},
            Author::ATTRIBUTE_LAST_NAME  => $resource->{Author::ATTRIBUTE_LAST_NAME},
        ];
    }

    public function getRelationships(object $resource, ContextInterface $context): array
    {
        assert($resource instanceof Author);

        // add test coverage for context param
        assert($context->getPosition() !== null);
        assert($context->getFieldSets() !== null);
        assert($context->getIncludePaths() !== null);

        // test and cover with test that factory could be used from a Schema.
        assert($this->getFactory()->createLink(true, 'test-example', false) !== null);

        if (property_exists($resource, Author::LINK_COMMENTS) === true) {
            $description = [self::RELATIONSHIP_DATA => $resource->{Author::LINK_COMMENTS}];
        } else {
            $selfLink    = $this->getRelationshipSelfLink($resource, Author::LINK_COMMENTS);
            $description = [self::RELATIONSHIP_LINKS => [LinkInterface::SELF => $selfLink]];
        }

        // NOTE: The `fixing` thing is for testing purposes only. Not for production.
        return $this->fixDescriptions(
            $resource,
            [
                Author::LINK_COMMENTS => $description,
            ]
        );
    }

    public function hasIdentifierMeta($resource): bool
    {
        assert($resource instanceof Author);

        return parent::hasIdentifierMeta($resource) || property_exists($resource, Author::IDENTIFIER_META);
    }

    public function getIdentifierMeta(object $resource): mixed
    {
        assert($resource instanceof Author);

        return $resource->{Author::IDENTIFIER_META};
    }

    public function hasResourceMeta(object $resource): bool
    {
        assert($resource instanceof Author);

        return parent::hasResourceMeta($resource) || property_exists($resource, Author::RESOURCE_META);
    }

    public function getResourceMeta(object $resource): mixed
    {
        assert($resource instanceof Author);

        return $resource->{Author::RESOURCE_META};
    }
}
