<?php declare(strict_types=1);

namespace Neomerx\Tests\JsonApi\Extension\Issue81;

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
use Neomerx\Tests\JsonApi\Data\Model\Author;
use Neomerx\Tests\JsonApi\Data\Model\AuthorIdentity;
use Neomerx\Tests\JsonApi\Data\Model\Comment;
use Neomerx\Tests\JsonApi\Data\Schema\CommentSchema as ParentSchema;

/**
 * @package Neomerx\Tests\JsonApi
 */
class CommentSchema extends ParentSchema
{
    public function getRelationships(object $resource, ContextInterface $context): array
    {
        assert($resource instanceof Comment);

        // emulate situation when we have only ID in relationship (e.g. user ID) and know type.
        $author   = $resource->{Comment::LINK_AUTHOR};
        $authorId = (string)$author->{Author::ATTRIBUTE_ID};

        $authorIdentity = new AuthorIdentity($authorId);

        $hasMeta = property_exists($author, Author::IDENTIFIER_META);
        if ($hasMeta === true) {
            $authorIdentity->setIdentifierMeta($author->{Author::IDENTIFIER_META});
        }

        return $this->fixDescriptions(
            $resource,
            [
                Comment::LINK_AUTHOR => [self::RELATIONSHIP_DATA => $authorIdentity],
            ]
        );
    }
}
