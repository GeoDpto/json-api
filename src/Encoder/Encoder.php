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
use Neomerx\JsonApi\Contract\Parser\DocumentDataInterface;
use Neomerx\JsonApi\Contract\Parser\IdentifierInterface;
use Neomerx\JsonApi\Contract\Parser\ParserInterface;
use Neomerx\JsonApi\Contract\Parser\ResourceInterface;
use Neomerx\JsonApi\Contract\Representation\BaseWriterInterface;
use Neomerx\JsonApi\Contract\Representation\DocumentWriterInterface;
use Neomerx\JsonApi\Contract\Representation\ErrorWriterInterface;
use Neomerx\JsonApi\Contract\Schema\ErrorInterface;
use Neomerx\JsonApi\Contract\Schema\SchemaContainerInterface;
use Neomerx\JsonApi\Exception\InvalidArgumentException;
use Neomerx\JsonApi\Factory\Factory;

/**
 * @package Neomerx\JsonApi
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Encoder implements EncoderInterface
{
    use EncoderPropertiesTrait;

    public const DEFAULT_URL_PREFIX = '';
    public const DEFAULT_INCLUDE_PATHS = [];
    public const DEFAULT_FIELD_SET_FILTERS = [];

    /**
     * Default encode options.
     *
     * @link http://php.net/manual/en/function.json-encode.php
     */
    public const DEFAULT_JSON_ENCODE_OPTIONS = 0;

    /**
     * Default encode depth.
     *
     * @link http://php.net/manual/en/function.json-encode.php
     */
    public const DEFAULT_JSON_ENCODE_DEPTH = 512;

    public function __construct(
        FactoryInterface $factory,
        SchemaContainerInterface $container,
    ) {
        $this->setFactory($factory)->setContainer($container)->reset();
    }

    /**
     * Create encoder instance.
     *
     * @param array $schemas Schema providers.
     */
    public static function instance(array $schemas = []): EncoderInterface
    {
        $factory   = static::createFactory();
        $container = $factory->createSchemaContainer($schemas);

        return $factory->createEncoder($container);
    }

    public function encodeData(iterable|null|object $data): string
    {
        return $this->encodeToJson($this->encodeDataToArray($data));
    }

    public function encodeIdentifiers(iterable|null|object $data): string
    {
        return $this->encodeToJson($this->encodeIdentifiersToArray($data));
    }

    public function encodeError(ErrorInterface $error): string
    {
        return $this->encodeToJson($this->encodeErrorToArray($error));
    }

    public function encodeErrors(iterable $errors): string
    {
        return $this->encodeToJson($this->encodeErrorsToArray($errors));
    }

    public function encodeMeta(mixed $meta): string
    {
        return $this->encodeToJson($this->encodeMetaToArray($meta));
    }

    protected static function createFactory(): FactoryInterface
    {
        return new Factory();
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function encodeDataToArray(iterable|null|object $data): array
    {
        if (\is_array($data) === false && \is_object($data) === false && $data !== null) {
            throw new InvalidArgumentException();
        }

        $context = $this->getFactory()->createParserContext($this->getFieldSets(), $this->getIncludePaths());
        $parser  = $this->getFactory()->createParser($this->getSchemaContainer(), $context);
        $writer  = $this->createDocumentWriter();
        $filter  = $this->getFactory()->createFieldSetFilter($this->getFieldSets());

        // write header
        $this->writeHeader($writer);

        // write body
        foreach ($parser->parse($data, $this->getIncludePaths()) as $item) {
            if ($item instanceof ResourceInterface) {
                if ($item->getPosition()->getLevel() > ParserInterface::ROOT_LEVEL) {
                    if ($filter->shouldOutputRelationship($item->getPosition()) === true) {
                        $writer->addResourceToIncluded($item, $filter);
                    }
                } else {
                    $writer->addResourceToData($item, $filter);
                }
            } elseif ($item instanceof IdentifierInterface) {
                \assert($item->getPosition()->getLevel() <= ParserInterface::ROOT_LEVEL);
                $writer->addIdentifierToData($item);
            } else {
                \assert($item instanceof DocumentDataInterface);
                \assert($item->getPosition()->getLevel() === 0);
                if ($item->isCollection() === true) {
                    $writer->setDataAsArray();
                } elseif ($item->isNull() === true) {
                    $writer->setNullToData();
                }
            }
        }

        // write footer
        $this->writeFooter($writer);

        return $writer->getDocument();
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    protected function encodeIdentifiersToArray(iterable|null|object $data): array
    {
        $context = $this->getFactory()->createParserContext($this->getFieldSets(), $this->getIncludePaths());
        $parser  = $this->getFactory()->createParser($this->getSchemaContainer(), $context);
        $writer  = $this->createDocumentWriter();
        $filter  = $this->getFactory()->createFieldSetFilter($this->getFieldSets());

        // write header
        $this->writeHeader($writer);

        // write body
        $includePaths   = $this->getIncludePaths();
        $expectIncluded = empty($includePaths) === false;

        // https://github.com/neomerx/json-api/issues/218
        //
        // if we expect included resources we have to include top level resources in `included` as well
        // Spec:
        //
        // GET /articles/1/relationships/comments?include=comments.author HTTP/1.1
        // Accept: application/vnd.api+json
        //
        // In this case, the primary data would be a collection of resource identifier objects that
        // represent linkage to comments for an article, while the full comments and comment authors
        // would be returned as included data.

        foreach ($parser->parse($data, $includePaths) as $item) {
            if ($item instanceof ResourceInterface) {
                if ($item->getPosition()->getLevel() > ParserInterface::ROOT_LEVEL) {
                    \assert($expectIncluded === true);
                    if ($filter->shouldOutputRelationship($item->getPosition()) === true) {
                        $writer->addResourceToIncluded($item, $filter);
                    }
                } else {
                    $writer->addIdentifierToData($item);
                    if ($expectIncluded === true) {
                        $writer->addResourceToIncluded($item, $filter);
                    }
                }
            } elseif ($item instanceof IdentifierInterface) {
                \assert($item->getPosition()->getLevel() <= ParserInterface::ROOT_LEVEL);
                $writer->addIdentifierToData($item);
            } else {
                \assert($item instanceof DocumentDataInterface);
                \assert($item->getPosition()->getLevel() === 0);
                if ($item->isCollection() === true) {
                    $writer->setDataAsArray();
                } elseif ($item->isNull() === true) {
                    $writer->setNullToData();
                }
            }
        }

        // write footer
        $this->writeFooter($writer);

        return $writer->getDocument();
    }

    protected function encodeErrorToArray(ErrorInterface $error): array
    {
        $writer = $this->createErrorWriter();

        // write header
        $this->writeHeader($writer);

        // write body
        $writer->addError($error);

        // write footer
        $this->writeFooter($writer);

        return $writer->getDocument();
    }

    protected function encodeErrorsToArray(iterable $errors): array
    {
        $writer = $this->createErrorWriter();

        // write header
        $this->writeHeader($writer);

        // write body
        foreach ($errors as $error) {
            \assert($error instanceof ErrorInterface);
            $writer->addError($error);
        }

        // write footer
        $this->writeFooter($writer);

        // encode to json
        return $writer->getDocument();
    }

    protected function encodeMetaToArray(mixed $meta): array
    {
        $this->withMeta($meta);

        $writer = $this->getFactory()->createDocumentWriter();

        $writer->setUrlPrefix($this->getUrlPrefix());

        // write header
        $this->writeHeader($writer);

        // write footer
        $this->writeFooter($writer);

        // encode to json
        return $writer->getDocument();
    }

    protected function writeHeader(BaseWriterInterface $writer): void
    {
        if ($this->hasMeta()) {
            $writer->setMeta($this->getMeta());
        }

        if ($this->hasJsonApiVersion()) {
            $writer->setJsonApiVersion($this->getJsonApiVersion());
        }

        if ($this->hasJsonApiMeta()) {
            $writer->setJsonApiMeta($this->getJsonApiMeta());
        }

        if ($this->hasLinks()) {
            $writer->setLinks($this->getLinks());
        }

        if ($this->hasProfile()) {
            $writer->setProfile($this->getProfile());
        }
    }

    /**
     * @SuppressWarnings(PHPMD)
     */
    protected function writeFooter(BaseWriterInterface $writer): void
    {
    }

    protected function encodeToJson(array $document): string
    {
        return \json_encode($document, $this->getEncodeOptions(), $this->getEncodeDepth());
    }

    private function createDocumentWriter(): DocumentWriterInterface
    {
        $writer = $this->getFactory()->createDocumentWriter();
        $writer->setUrlPrefix($this->getUrlPrefix());

        return $writer;
    }

    private function createErrorWriter(): ErrorWriterInterface
    {
        $writer = $this->getFactory()->createErrorWriter();
        $writer->setUrlPrefix($this->getUrlPrefix());

        return $writer;
    }
}
