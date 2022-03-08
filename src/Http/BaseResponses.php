<?php

declare(strict_types=1);

namespace Neomerx\JsonApi\Http;

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
use Neomerx\JsonApi\Contract\Http\Header\HeaderParametersParserInterface;
use Neomerx\JsonApi\Contract\Http\Header\MediaTypeInterface;
use Neomerx\JsonApi\Contract\Http\ResponsesInterface;
use Neomerx\JsonApi\Contract\Schema\ErrorInterface;

/**
 * @package Neomerx\JsonApi
 */
abstract class BaseResponses implements ResponsesInterface
{
    /** Header name that contains format of input data from client */
    public const HEADER_CONTENT_TYPE = HeaderParametersParserInterface::HEADER_CONTENT_TYPE;

    /** Header name that location of newly created resource */
    public const HEADER_LOCATION = 'Location';

    abstract protected function createResponse(?string $content, int $statusCode, array $headers): mixed;

    abstract protected function getEncoder(): EncoderInterface;

    abstract protected function getMediaType(): MediaTypeInterface;

    public function getContentResponse(array|object $data, int $statusCode = self::HTTP_OK, array $headers = []): mixed
    {
        return $this->createJsonApiResponse($this->getEncoder()->encodeData($data), $statusCode, $headers);
    }

    public function getCreatedResponse(object $resource, string $url, array $headers = []): mixed
    {
        return $this->createJsonApiResponse(
            $this->getEncoder()->encodeData($resource),
            self::HTTP_CREATED,
            \array_merge($headers, [self::HEADER_LOCATION => $url]),
        );
    }

    public function getCodeResponse(int $statusCode, array $headers = []): mixed
    {
        return $this->createJsonApiResponse(null, $statusCode, $headers, false);
    }

    public function getMetaResponse(array|object $meta, int $statusCode = self::HTTP_OK, array $headers = []): mixed
    {
        $content = $this->getEncoder()->encodeMeta($meta);

        return $this->createJsonApiResponse($content, $statusCode, $headers);
    }

    public function getIdentifiersResponse(
        array|object $data,
        int $statusCode = self::HTTP_OK,
        array $headers = [],
    ): mixed {
        return $this->createJsonApiResponse($this->getEncoder()->encodeIdentifiers($data), $statusCode, $headers);
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function getErrorResponse(
        ErrorInterface|iterable $errors,
        int $statusCode = self::HTTP_BAD_REQUEST,
        array $headers = [],
    ): string|object {
        if ($errors instanceof ErrorInterface) {
            return $this->createJsonApiResponse($this->getEncoder()->encodeError($errors), $statusCode, $headers);
        }

        return $this->createJsonApiResponse($this->getEncoder()->encodeErrors($errors), $statusCode, $headers);
    }

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    protected function createJsonApiResponse(
        ?string $content,
        int $statusCode,
        array $headers = [],
        bool $addContentType = true,
    ): mixed {
        if ($addContentType === true) {
            $headers[self::HEADER_CONTENT_TYPE] = $this->getMediaType()->getMediaType();
        }

        try {
            return $this->createResponse($content, $statusCode, $headers);
        } catch (\Throwable $exception) {
            echo $exception->getMessage();

            throw $exception;
        }
    }
}
