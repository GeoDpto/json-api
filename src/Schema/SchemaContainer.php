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

use Closure;
use Neomerx\JsonApi\Contract\Factory\FactoryInterface;
use Neomerx\JsonApi\Contract\Schema\SchemaContainerInterface;
use Neomerx\JsonApi\Contract\Schema\SchemaInterface;
use Neomerx\JsonApi\Exception\InvalidArgumentException;
use function Neomerx\JsonApi\I18n\format as _;

/**
 * @package Neomerx\JsonApi
 */
class SchemaContainer implements SchemaContainerInterface
{
    public const MSG_INVALID_MODEL_TYPE = 'Invalid model type.';
    public const MSG_INVALID_SCHEME = 'Schema for type `%s` must be non-empty string,' .
    ' callable or SchemaInterface instance.';
    public const MSG_TYPE_REUSE_FORBIDDEN = 'Type should not be used more than once to register a schema (`%s`).';

    private array $providerMapping = [];

    /**
     * @var SchemaInterface[]
     */
    private $createdProviders = [];

    private array $resType2JsonType = [];

    private FactoryInterface $factory;

    public function __construct(FactoryInterface $factory, iterable $schemas)
    {
        $this->factory = $factory;
        $this->registerCollection($schemas);
    }

    /**
     * Register provider for resource type.
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function register(string $type, object|string $schema): void
    {
        if (empty($type) === true || \class_exists($type) === false) {
            throw new InvalidArgumentException(_(static::MSG_INVALID_MODEL_TYPE));
        }

        $isOk = (
            (
                \is_string($schema) === true &&
                empty($schema) === false &&
                \class_exists($schema) === true &&
                \in_array(SchemaInterface::class, \class_implements($schema), true) === true
            ) ||
            \is_callable($schema) ||
            $schema instanceof SchemaInterface
        );
        if ($isOk === false) {
            throw new InvalidArgumentException(_(static::MSG_INVALID_SCHEME, $type));
        }

        if ($this->hasProviderMapping($type) === true) {
            throw new InvalidArgumentException(_(static::MSG_TYPE_REUSE_FORBIDDEN, $type));
        }

        if ($schema instanceof SchemaInterface) {
            $this->setProviderMapping($type, $schema::class);
            $this->setResourceToJsonTypeMapping($schema->getType(), $type);
            $this->setCreatedProvider($type, $schema);
        } else {
            $this->setProviderMapping($type, $schema);
        }
    }

    /**
     * Register providers for resource types.
     */
    public function registerCollection(iterable $schemas): void
    {
        foreach ($schemas as $type => $schema) {
            $this->register($type, $schema);
        }
    }

    public function getSchema(object $resourceObject): SchemaInterface
    {
        \assert($this->hasSchema($resourceObject));

        return $this->getSchemaByType($this->getResourceType($resourceObject));
    }

    public function hasSchema(object $resourceObject): bool
    {
        return $this->hasProviderMapping($this->getResourceType($resourceObject));
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    protected function getSchemaByType(string $type): SchemaInterface
    {
        if ($this->hasCreatedProvider($type) === true) {
            return $this->getCreatedProvider($type);
        }

        $classNameOrCallable = $this->getProviderMapping($type);
        if (\is_string($classNameOrCallable) === true) {
            $schema = $this->createSchemaFromClassName($classNameOrCallable);
        } else {
            \assert(\is_callable($classNameOrCallable) === true);
            $schema = $this->createSchemaFromCallable($classNameOrCallable);
        }
        $this->setCreatedProvider($type, $schema);

        /** @var SchemaInterface $schema */

        $this->setResourceToJsonTypeMapping($schema->getType(), $type);

        return $schema;
    }

    protected function hasProviderMapping(string $type): bool
    {
        return isset($this->providerMapping[$type]);
    }

    protected function getProviderMapping(string $type): mixed
    {
        return $this->providerMapping[$type];
    }

    protected function setProviderMapping(string $type, Closure|string $schema): void
    {
        $this->providerMapping[$type] = $schema;
    }

    protected function hasCreatedProvider(string $type): bool
    {
        return isset($this->createdProviders[$type]);
    }

    protected function getCreatedProvider(string $type): SchemaInterface
    {
        return $this->createdProviders[$type];
    }

    protected function setCreatedProvider(string $type, SchemaInterface $provider): void
    {
        $this->createdProviders[$type] = $provider;
    }

    protected function setResourceToJsonTypeMapping(string $resourceType, string $jsonType): void
    {
        $this->resType2JsonType[$resourceType] = $jsonType;
    }

    protected function getResourceType(object $resource): string
    {
        return $resource::class;
    }

    protected function createSchemaFromCallable(callable $callable): SchemaInterface
    {
        return \call_user_func($callable, $this->factory);
    }

    protected function createSchemaFromClassName(string $className): SchemaInterface
    {
        return new $className($this->factory);
    }
}
