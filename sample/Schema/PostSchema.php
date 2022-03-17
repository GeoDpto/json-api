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
use Neomerx\Samples\JsonApi\Model\Post;

/**
 * @package Neomerx\Samples\JsonApi
 */
class PostSchema extends BaseSchema
{

    public function getType(): string
    {
        return 'posts';
    }


    public function getId(object $resource): ?string
    {
        assert($resource instanceof Post);

        return (string) $resource->postId;
    }


    public function getAttributes(object $resource, ContextInterface $context): array
    {
        assert($resource instanceof Post);

        return [
            'title' => $resource->title,
            'body'  => $resource->body,
        ];
    }


    public function getRelationships(object $resource, ContextInterface $context): array
    {
        assert($resource instanceof Post);

        return [
            'author'   => [
                self::RELATIONSHIP_DATA          => $resource->author,
                self::RELATIONSHIP_LINKS_SELF    => false,
                self::RELATIONSHIP_LINKS_RELATED => false,
            ],
            'comments' => [
                self::RELATIONSHIP_DATA          => $resource->comments,
                self::RELATIONSHIP_LINKS_SELF    => false,
                self::RELATIONSHIP_LINKS_RELATED => false,
            ],
        ];
    }
}
