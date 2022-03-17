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
use Neomerx\Tests\JsonApi\Data\Model\Comment;
use function assert;

/**
 * @package Neomerx\Tests\JsonApi
 */
class CommentSchema extends DevSchema
{
    public function getType(): string
    {
        return 'comments';
    }

    public function getId(object $resource): ?string
    {
        assert($resource instanceof Comment);

        $index = $resource->{Comment::ATTRIBUTE_ID};

        return $index === null ? $index : (string)$index;
    }

    public function getAttributes(object $resource, ContextInterface $context): array
    {
        assert($resource instanceof Comment);

        return [
            Comment::ATTRIBUTE_BODY => $resource->{Comment::ATTRIBUTE_BODY},
        ];
    }

    public function getRelationships(object $resource, ContextInterface $context): array
    {
        assert($resource instanceof Comment);

        // NOTE: The `fixing` thing is for testing purposes only. Not for production.
        return $this->fixDescriptions(
            $resource,
            [
                Comment::LINK_AUTHOR => [self::RELATIONSHIP_DATA => $resource->{Comment::LINK_AUTHOR}],
            ]
        );
    }
}
