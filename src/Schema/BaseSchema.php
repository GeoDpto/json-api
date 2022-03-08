<?php

declare(strict_types=1);

namespace Neomerx\JsonApi\Schema;

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

use Neomerx\JsonApi\Contract\Factory\FactoryInterface;
use Neomerx\JsonApi\Contract\Schema\DocumentInterface;
use Neomerx\JsonApi\Contract\Schema\LinkInterface;
use Neomerx\JsonApi\Contract\Schema\SchemaInterface;
use Neomerx\JsonApi\Exception\LogicException;

/**
 * @package Neomerx\JsonApi
 */
abstract class BaseSchema implements SchemaInterface
{
    private FactoryInterface $factory;
    private ?string $subUrl = null;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function getSelfLink($resource): LinkInterface
    {
        return $this->factory->createLink(true, $this->getSelfSubUrl($resource), false);
    }

    public function getLinks(object $resource): iterable
    {
        return [
            LinkInterface::SELF => $this->getSelfLink($resource),
        ];
    }

    public function getRelationshipSelfLink(object $resource, string $name): LinkInterface
    {
        // Feel free to override this method to change default URL or add meta

        $url = $this->getSelfSubUrl($resource) . '/' . DocumentInterface::KEYWORD_RELATIONSHIPS . '/' . $name;

        return $this->factory->createLink(true, $url, false);
    }

    public function getRelationshipRelatedLink(object $resource, string $name): LinkInterface
    {
        // Feel free to override this method to change default URL or add meta

        $url = $this->getSelfSubUrl($resource) . '/' . $name;

        return $this->factory->createLink(true, $url, false);
    }

    public function hasIdentifierMeta(object $resource): bool
    {
        return false;
    }

    public function getIdentifierMeta(object $resource): mixed
    {
        // default schema does not provide any meta
        throw new LogicException();
    }

    public function hasResourceMeta(object $resource): bool
    {
        return false;
    }

    public function getResourceMeta(object $resource): mixed
    {
        // default schema does not provide any meta
        throw new LogicException();
    }

    public function isAddSelfLinkInRelationshipByDefault(string $relationshipName): bool
    {
        return true;
    }

    public function isAddRelatedLinkInRelationshipByDefault(string $relationshipName): bool
    {
        return true;
    }

    protected function getFactory(): FactoryInterface
    {
        return $this->factory;
    }

    protected function getResourcesSubUrl(): string
    {
        if ($this->subUrl === null) {
            $this->subUrl = '/' . $this->getType();
        }

        return $this->subUrl;
    }

    protected function getSelfSubUrl(object $resource): string
    {
        return $this->getResourcesSubUrl() . '/' . $this->getId($resource);
    }
}
