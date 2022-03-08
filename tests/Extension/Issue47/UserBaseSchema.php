<?php declare(strict_types=1);

namespace Neomerx\Tests\JsonApi\Extension\Issue47;

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

/**
 * @package Neomerx\Tests\JsonApi
 */
class UserBaseSchema extends BaseSchema
{

    public function getType(): string
    {
        return 'users';
    }


    public function getId(object $resource): ?string
    {
        assert($resource instanceof User);

        return $resource->identity;
    }


    public function getAttributes(object $resource, ContextInterface $context): array
    {
        assert($resource instanceof User);

        return [
            'username' => $resource->name,
            'private'  => $resource->contactDetails,
        ];
    }


    public function getRelationships(object $resource, ContextInterface $context): array
    {
        return [];
    }
}
