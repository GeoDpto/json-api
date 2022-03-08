<?php

declare(strict_types=1);

namespace Neomerx\JsonApi\Encoder;

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

use Neomerx\JsonApi\Contract\Encoder\EncoderInterface;
use Neomerx\JsonApi\Contract\Factory\FactoryInterface;
use Neomerx\JsonApi\Contract\Schema\LinkInterface;
use Neomerx\JsonApi\Contract\Schema\SchemaContainerInterface;
use Traversable;

/**
 * @package Neomerx\JsonApi
 */
trait EncoderPropertiesTrait
{
    private SchemaContainerInterface $container;
    private FactoryInterface $factory;
    private string $urlPrefix;
    private array $includePaths;
    private array $fieldSets;
    private int $encodeOptions;
    private int $encodeDepth;
    private ?iterable $links = null;
    private ?iterable $profile = null;
    private bool $hasMeta;
    private mixed $meta;
    private ?string $jsonApiVersion = null;
    private mixed $jsonApiMeta;
    private bool $hasJsonApiMeta;

    /**
     * Reset to initial state.
     */
    public function reset(
        string $urlPrefix = Encoder::DEFAULT_URL_PREFIX,
        iterable $includePaths = Encoder::DEFAULT_INCLUDE_PATHS,
        array $fieldSets = Encoder::DEFAULT_FIELD_SET_FILTERS,
        int $encodeOptions = Encoder::DEFAULT_JSON_ENCODE_OPTIONS,
        int $encodeDepth = Encoder::DEFAULT_JSON_ENCODE_DEPTH
    ): static {
        $this->links          = null;
        $this->profile        = null;
        $this->hasMeta        = false;
        $this->meta           = null;
        $this->jsonApiVersion = null;
        $this->jsonApiMeta    = null;
        $this->hasJsonApiMeta = false;

        $this
            ->withUrlPrefix($urlPrefix)
            ->withIncludedPaths($includePaths)
            ->withFieldSets($fieldSets)
            ->withEncodeOptions($encodeOptions)
            ->withEncodeDepth($encodeDepth);

        return $this;
    }

    protected function getSchemaContainer(): SchemaContainerInterface
    {
        return $this->container;
    }

    public function setContainer(SchemaContainerInterface $container): self
    {
        $this->container = $container;

        return $this;
    }

    protected function getFactory(): FactoryInterface
    {
        return $this->factory;
    }

    public function setFactory(FactoryInterface $factory): self
    {
        $this->factory = $factory;

        return $this;
    }

    public function withUrlPrefix(string $prefix): static
    {
        $this->urlPrefix = $prefix;

        return $this;
    }

    protected function getUrlPrefix(): string
    {
        return $this->urlPrefix;
    }

    public function withIncludedPaths(iterable $paths): static
    {
        $paths = $this->iterableToArray($paths);

        \assert(
            \call_user_func(
                function (array $paths): bool {
                    $pathsOk = true;
                    foreach ($paths as $path) {
                        $pathsOk = $pathsOk === true && \is_string($path) === true && empty($path) === false;
                    }

                    return $pathsOk;
                },
                $paths
            )
        );

        $this->includePaths = $paths;

        return $this;
    }

    protected function getIncludePaths(): array
    {
        return $this->includePaths;
    }

    public function withFieldSets(array $fieldSets): static
    {
        $this->fieldSets = $fieldSets;

        return $this;
    }

    protected function getFieldSets(): array
    {
        return $this->fieldSets;
    }

    public function withEncodeOptions(int $options): static
    {
        $this->encodeOptions = $options;

        return $this;
    }

    protected function getEncodeOptions(): int
    {
        return $this->encodeOptions;
    }

    public function withEncodeDepth(int $depth): static
    {
        \assert($depth > 0);

        $this->encodeDepth = $depth;

        return $this;
    }

    protected function getEncodeDepth(): int
    {
        return $this->encodeDepth;
    }

    public function withLinks(iterable $links): static
    {
        $this->links = $this->hasLinks() === false ?
            $links :
            $this->links = \array_merge(
                $this->iterableToArray($this->getLinks()),
                $this->iterableToArray($links)
            );

        return $this;
    }

    protected function hasLinks(): bool
    {
        return $this->links !== null;
    }

    protected function getLinks(): iterable
    {
        return $this->links;
    }

    public function withProfile(iterable $links): static
    {
        $this->profile = $links;

        return $this;
    }

    protected function hasProfile(): bool
    {
        return $this->profile !== null;
    }

    protected function getProfile(): iterable
    {
        return $this->profile;
    }

    public function withMeta(mixed $meta): static
    {
        $this->meta    = $meta;
        $this->hasMeta = true;

        return $this;
    }

    protected function hasMeta(): bool
    {
        return $this->hasMeta;
    }

    public function getMeta(): mixed
    {
        return $this->meta;
    }

    public function withJsonApiVersion(string $version): static
    {
        $this->jsonApiVersion = $version;

        return $this;
    }

    protected function hasJsonApiVersion(): bool
    {
        return $this->jsonApiVersion !== null;
    }

    protected function getJsonApiVersion(): string
    {
        return $this->jsonApiVersion;
    }

    public function withJsonApiMeta(mixed $meta): static
    {
        $this->jsonApiMeta    = $meta;
        $this->hasJsonApiMeta = true;

        return $this;
    }

    protected function hasJsonApiMeta(): bool
    {
        return $this->hasJsonApiMeta;
    }

    protected function getJsonApiMeta(): mixed
    {
        return $this->jsonApiMeta;
    }

    public function withRelationshipSelfLink(object $resource, string $relationshipName): static
    {
        $link = $this
            ->getSchemaContainer()->getSchema($resource)
            ->getRelationshipSelfLink($resource, $relationshipName);

        return $this->withLinks([
            LinkInterface::SELF => $link,
        ]);
    }

    public function withRelationshipRelatedLink(object $resource, string $relationshipName): static
    {
        $link = $this
            ->getSchemaContainer()->getSchema($resource)
            ->getRelationshipRelatedLink($resource, $relationshipName);

        return $this->withLinks([
            LinkInterface::RELATED => $link,
        ]);
    }

    private function iterableToArray(array|Traversable $value): array
    {
        return \is_array($value) === true ? $value : \iterator_to_array($value);
    }
}
