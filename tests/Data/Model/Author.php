<?php declare(strict_types=1);

namespace Neomerx\Tests\JsonApi\Data\Model;

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

use stdClass;

/**
 * @package Neomerx\Tests\JsonApi
 */
class Author extends stdClass
{
    public const ATTRIBUTE_ID = 'author_id';
    public const ATTRIBUTE_FIRST_NAME = 'first_name';
    public const ATTRIBUTE_LAST_NAME = 'last_name';
    public const LINK_COMMENTS = 'comments';
    public const IDENTIFIER_META = 'identifier_meta';
    public const RESOURCE_META = 'resource_meta';

    public static function instance(int $identity, string $firstName, string $lastName, array $comments = null): self
    {
        $author = new self();

        $author->{self::ATTRIBUTE_ID}         = $identity;
        $author->{self::ATTRIBUTE_FIRST_NAME} = $firstName;
        $author->{self::ATTRIBUTE_LAST_NAME}  = $lastName;

        $comments === null ?: $author->{self::LINK_COMMENTS} = $comments;

        return $author;
    }

    public function setIdentifierMeta(mixed $meta): self
    {
        $this->{static::IDENTIFIER_META} = $meta;

        return $this;
    }

    public function setResourceMeta(mixed $meta): self
    {
        $this->{static::RESOURCE_META} = $meta;

        return $this;
    }
}
