<?php

declare(strict_types=1);

namespace Neomerx\JsonApi\Contract\Encoder;

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

use Neomerx\JsonApi\Contract\Schema\ErrorInterface;
use Neomerx\JsonApi\Contract\Schema\LinkInterface;
use Neomerx\JsonApi\Contract\Schema\LinkWithAliasesInterface;

/**
 * @package Neomerx\JsonApi
 */
interface EncoderInterface
{
    /** JSON API version implemented by the encoder */
    public const JSON_API_VERSION = '1.1';

    /** This prefix will be used for URL links while encoding. */
    public function withUrlPrefix(string $prefix): self;

    /**
     * Include specified paths to the output. Paths should be separated with a dot symbol.
     *
     * Format
     * [
     *     'relationship1',
     *     'relationship1.sub-relationship2',
     * ]
     *
     */
    public function withIncludedPaths(iterable $paths): self;

    /**
     * Limit fields in the output result.
     *
     * Format
     * [
     *     'type1' => ['attribute1', 'attribute2', 'relationship1', ...]
     *     'type2' => [] // no fields in output, only type and id.
     *
     *     // 'type3' is not on the list so all its attributes and relationships will be in output.
     * ]
     *
     */
    public function withFieldSets(array $fieldSets): self;

    /**
     * Set JSON encode options.
     *
     * @link http://php.net/manual/en/function.json-encode.php
     *
     */
    public function withEncodeOptions(int $options): self;

    /**
     * Set JSON encode depth.
     *
     * @link http://php.net/manual/en/function.json-encode.php
     *
     */
    public function withEncodeDepth(int $depth): self;

    /**
     * Add links that will be encoded with data. Links must be in `$name => $link, ...` format.
     *
     * @see LinkInterface
     *
     */
    public function withLinks(array $links): self;

    /**
     * Add profile links that will be encoded with data. Links must be in `$link1, $link2, ...` format.
     *
     * @see LinkWithAliasesInterface
     *
     */
    public function withProfile(iterable $links): self;

    /**
     * Add meta information that will be encoded with data. If 'null' meta will not appear in a document.
     */
    public function withMeta(mixed $meta): self;

    /**
     * If called JSON API version information will be added to a document.
     *
     * @see http://jsonapi.org/format/#document-jsonapi-object
     */
    public function withJsonApiVersion(string $version): self;

    /**
     * If called JSON API version meta will be added to a document.
     *
     * @see http://jsonapi.org/format/#document-jsonapi-object
     */
    public function withJsonApiMeta(mixed $meta): self;

    /**
     * Add 'self' Link to top-level document's 'links' section for relationship specified.
     *
     * @see http://jsonapi.org/format/#fetching-relationships
     */
    public function withRelationshipSelfLink(object $resource, string $relationshipName): self;

    /**
     * Add 'related' Link to top-level document's 'links' section for relationship specified.
     *
     * @see http://jsonapi.org/format/#fetching-relationships
     */
    public function withRelationshipRelatedLink(object $resource, string $relationshipName): self;

    /**
     * Reset encoder settings to defaults.
     */
    public function reset(): self;

    /**
     * Encode input as JSON API string.
     */
    public function encodeData(iterable|object|null $data): string;

    /**
     * Encode input as JSON API string with a list of resource identifiers.
     */
    public function encodeIdentifiers(iterable|null|object $data): string;

    /**
     * Encode error as JSON API string.
     */
    public function encodeError(ErrorInterface $error): string;

    /**
     * Encode errors as JSON API string.
     *
     * @see ErrorInterface
     */
    public function encodeErrors(iterable $errors): string;

    /**
     * Encode input meta as JSON API string.
     */
    public function encodeMeta(mixed $meta): string;
}
