<?php

declare(strict_types=1);

namespace Neomerx\JsonApi\Contract\Http;

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

/**
 * @package Neomerx\JsonApi
 */
interface ResponsesInterface
{
    public const HTTP_OK = 200;
    public const HTTP_CREATED = 201;
    public const HTTP_BAD_REQUEST = 400;

    /**
     * Get response with regular JSON API Document in body.
     *
     * @param array<object>|object $data Resource or resources to encode.
     */
    public function getContentResponse(array|object $data, int $statusCode = self::HTTP_OK, array $headers = []): mixed;

    /**
     * Get response for newly created resource with HTTP code 201 (adds 'location' header).
     */
    public function getCreatedResponse(object $resource, string $url, array $headers = []): mixed;

    /**
     * Get response with HTTP code only.
     */
    public function getCodeResponse(int $statusCode, array $headers = []): mixed;

    /**
     * Get response with meta information only.
     */
    public function getMetaResponse(array|object $meta, int $statusCode = self::HTTP_OK, array $headers = []): mixed;

    /**
     * Get response with only resource identifiers.
     */
    public function getIdentifiersResponse(
        array|object $data,
        int $statusCode = self::HTTP_OK,
        array $headers = []
    ): mixed;

    public function getErrorResponse(
        ErrorInterface|iterable $errors,
        int $statusCode = self::HTTP_BAD_REQUEST,
        array $headers = []
    ): string|object;
}
