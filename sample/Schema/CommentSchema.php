<?php

declare(strict_types=1);

namespace Neomerx\Samples\JsonApi\Schema;

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
use Neomerx\JsonApi\Schema\BaseSchema;
use Neomerx\Samples\JsonApi\Model\Comment;

/**
 * @package Neomerx\Samples\JsonApi
 */
class CommentSchema extends BaseSchema
{

    public function getType(): string
    {
        return 'comments';
    }


    public function getId(object $comment): ?string
    {
        assert($comment instanceof Comment);

        return (string)$comment->commentId;
    }


    public function getAttributes(object $comment, ContextInterface $context): array
    {
        assert($comment instanceof Comment);

        return [
            'body' => $comment->body,
        ];
    }


    public function getRelationships(object $resource, ContextInterface $context): array
    {
        assert($resource instanceof Comment);

        return [
            'author' => [
                self::RELATIONSHIP_DATA          => $resource->author,
                self::RELATIONSHIP_LINKS_SELF    => false,
                self::RELATIONSHIP_LINKS_RELATED => false,
            ],
        ];
    }
}
