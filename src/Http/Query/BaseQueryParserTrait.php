<?php

declare(strict_types=1);

namespace Neomerx\JsonApi\Http\Query;

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

use Neomerx\JsonApi\Contract\Http\Query\BaseQueryParserInterface as P;
use Neomerx\JsonApi\Contract\Schema\ErrorInterface;
use Neomerx\JsonApi\Exception\JsonApiException;
use Neomerx\JsonApi\Schema\Error;

/**
 * @package Neomerx\JsonApi
 */
trait BaseQueryParserTrait
{
    protected function getIncludes(array $parameters, string $errorTitle): iterable
    {
        if (\array_key_exists(P::PARAM_INCLUDE, $parameters) === true) {
            $includes = $parameters[P::PARAM_INCLUDE];
            $paths    = $this->splitCommaSeparatedStringAndCheckNoEmpties(P::PARAM_INCLUDE, $includes, $errorTitle);
            foreach ($paths as $path) {
                yield $path => $this->splitStringAndCheckNoEmpties(P::PARAM_INCLUDE, $path, '.', $errorTitle);
            }
        }
    }

    protected function getIncludePaths(array $parameters, string $errorTitle): iterable
    {
        $aIncludes = $this->getIncludes($parameters, $errorTitle);
        foreach ($aIncludes as $path => $parsed) {
            \assert($parsed !== null);
            yield $path;
        }
    }

    protected function getFields(array $parameters, string $errorTitle): iterable
    {
        if (\array_key_exists(P::PARAM_FIELDS, $parameters) === true) {
            $fields = $parameters[P::PARAM_FIELDS];
            if (\is_array($fields) === false || empty($fields) === true) {
                throw new JsonApiException($this->createParameterError(P::PARAM_FIELDS, $errorTitle));
            }

            foreach ($fields as $type => $fieldList) {
                yield $type => $this->splitCommaSeparatedStringAndCheckNoEmpties($type, $fieldList, $errorTitle);
            }
        }
    }

    protected function getSorts(array $parameters, string $errorTitle): iterable
    {
        if (\array_key_exists(P::PARAM_SORT, $parameters) === true) {
            $sorts  = $parameters[P::PARAM_SORT];
            $values = $this->splitCommaSeparatedStringAndCheckNoEmpties(P::PARAM_SORT, $sorts, $errorTitle);
            foreach ($values as $orderAndField) {
                switch ($orderAndField[0]) {
                    case '-':
                        $isAsc = false;
                        $field = \mb_substr($orderAndField, 1);

                        break;
                    case '+':
                        $isAsc = true;
                        $field = \mb_substr($orderAndField, 1);

                        break;
                    default:
                        $isAsc = true;
                        $field = $orderAndField;

                        break;
                }

                yield $field => $isAsc;
            }
        }
    }

    protected function getProfileUrls(array $parameters, string $errorTitle): iterable
    {
        if (\array_key_exists(P::PARAM_PROFILE, $parameters) === true) {
            $encodedUrls = $parameters[P::PARAM_PROFILE];
            $decodedUrls = \urldecode($encodedUrls);
            yield from $this->splitSpaceSeparatedStringAndCheckNoEmpties(
                P::PARAM_PROFILE,
                $decodedUrls,
                $errorTitle
            );
        }
    }

    private function splitCommaSeparatedStringAndCheckNoEmpties(
        string $paramName,
        mixed $shouldBeString,
        string $errorTitle
    ): iterable {
        return $this->splitStringAndCheckNoEmpties($paramName, $shouldBeString, ',', $errorTitle);
    }

    private function splitSpaceSeparatedStringAndCheckNoEmpties(
        string $paramName,
        mixed $shouldBeString,
        string $errorTitle
    ): iterable {
        return $this->splitStringAndCheckNoEmpties($paramName, $shouldBeString, ' ', $errorTitle);
    }

    /**
     * @SuppressWarnings(PHPMD.IfStatementAssignment)
     */
    private function splitStringAndCheckNoEmpties(
        string $paramName,
        mixed $shouldBeString,
        string $separator,
        string $errorTitle
    ): iterable {
        if (\is_string($shouldBeString) === false || ($trimmed = \trim($shouldBeString)) === '') {
            throw new JsonApiException($this->createParameterError($paramName, $errorTitle));
        }

        foreach (\explode($separator, $trimmed) as $value) {
            $trimmedValue = \trim($value);
            if ($trimmedValue === '') {
                throw new JsonApiException($this->createParameterError($paramName, $errorTitle));
            }

            yield $trimmedValue;
        }
    }

    private function createParameterError(string $paramName, string $errorTitle): ErrorInterface
    {
        return new Error(title: $errorTitle, meta: [ErrorInterface::SOURCE_PARAMETER => $paramName]);
    }
}
