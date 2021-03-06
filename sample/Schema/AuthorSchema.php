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
use Neomerx\Samples\JsonApi\Model\Author;

/**
 * @package Neomerx\Samples\JsonApi
 */
class AuthorSchema extends BaseSchema
{

    public function getType(): string
    {
        return 'people';
    }


    public function getId(object $resource): ?string
    {
        /** @var Author $resource */
        return (string) $resource->authorId;
    }


    public function getAttributes(object $resource, ContextInterface $context): array
    {
        /** @var Author $resource */
        return [
            'first_name' => $resource->firstName,
            'last_name'  => $resource->lastName,
        ];
    }


    public function getRelationships(object $resource, ContextInterface $context): array
    {
        return [];
    }
}
