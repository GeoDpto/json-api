<?php

declare(strict_types=1);

namespace Neomerx\JsonApi\Http\Header;

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

use Neomerx\JsonApi\Contract\Http\Header\MediaTypeInterface;
use Neomerx\JsonApi\Exception\InvalidArgumentException;

/**
 * @package Neomerx\JsonApi
 */
class MediaType implements MediaTypeInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $subType;

    /**
     * @var string?
     */
    private $mediaType = null;

    /**
     * @var array<string,string>|null
     */
    private $parameters;

    /**
     * A list of parameter names for case-insensitive compare. Keys must be lower-cased.
     *
     * @var array
     */
    protected const PARAMETER_NAMES = [
        'charset' => true,
    ];

    /**
     * @param array<string,string>|null $parameters
     */
    public function __construct(string $type, string $subType, array $parameters = null)
    {
        $type = \trim($type);
        if (empty($type) === true) {
            throw new InvalidArgumentException('type');
        }

        $subType = \trim($subType);
        if (empty($subType) === true) {
            throw new InvalidArgumentException('subType');
        }

        $this->type       = $type;
        $this->subType    = $subType;
        $this->parameters = $parameters;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSubType(): string
    {
        return $this->subType;
    }

    public function getMediaType(): string
    {
        if ($this->mediaType === null) {
            $this->mediaType = $this->type . '/' . $this->getSubType();
        }

        return $this->mediaType;
    }

    public function getParameters(): ?array
    {
        return $this->parameters;
    }

    public function matchesTo(MediaTypeInterface $mediaType): bool
    {
        return
            $this->isTypeMatches($mediaType) &&
            $this->isSubTypeMatches($mediaType) &&
            $this->isMediaParametersMatch($mediaType);
    }

    public function equalsTo(MediaTypeInterface $mediaType): bool
    {
        return
            $this->isTypeEquals($mediaType) &&
            $this->isSubTypeEquals($mediaType) &&
            $this->isMediaParametersEqual($mediaType);
    }

    private function isTypeMatches(MediaTypeInterface $mediaType): bool
    {
        return $mediaType->getType() === '*' || $this->isTypeEquals($mediaType);
    }

    private function isTypeEquals(MediaTypeInterface $mediaType): bool
    {
        // Type, subtype and param name should be compared case-insensitive
        // https://tools.ietf.org/html/rfc7231#section-3.1.1.1
        return \strcasecmp($this->type, $mediaType->getType()) === 0;
    }

    private function isSubTypeMatches(MediaTypeInterface $mediaType): bool
    {
        return $mediaType->getSubType() === '*' || $this->isSubTypeEquals($mediaType);
    }

    private function isSubTypeEquals(MediaTypeInterface $mediaType): bool
    {
        // Type, subtype and param name should be compared case-insensitive
        // https://tools.ietf.org/html/rfc7231#section-3.1.1.1
        return \strcasecmp($this->getSubType(), $mediaType->getSubType()) === 0;
    }

    private function isMediaParametersMatch(MediaTypeInterface $mediaType): bool
    {
        if ($this->bothMediaTypeParamsEmpty($mediaType) === true) {
            return true;
        } elseif ($this->bothMediaTypeParamsNotEmptyAndEqualInSize($mediaType)) {
            // Type, subtype and param name should be compared case-insensitive
            // https://tools.ietf.org/html/rfc7231#section-3.1.1.1
            $ourParameters       = \array_change_key_case($this->parameters);
            $parametersToCompare = \array_change_key_case($mediaType->getParameters());

            // if at least one name are different they are not equal
            if (empty(\array_diff_key($ourParameters, $parametersToCompare)) === false) {
                return false;
            }

            // If we are here we have to compare values. Also some of the values should be compared case-insensitive
            // according to https://tools.ietf.org/html/rfc7231#section-3.1.1.1
            // > 'Parameter values might or might not be case-sensitive, depending on
            // the semantics of the parameter name.'
            foreach ($ourParameters as $name => $value) {
                if ($this->paramValuesMatch($name, $value, $parametersToCompare[$name]) === false) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    private function isMediaParametersEqual(MediaTypeInterface $mediaType): bool
    {
        if ($this->bothMediaTypeParamsEmpty($mediaType) === true) {
            return true;
        } elseif ($this->bothMediaTypeParamsNotEmptyAndEqualInSize($mediaType)) {
            // Type, subtype and param name should be compared case-insensitive
            // https://tools.ietf.org/html/rfc7231#section-3.1.1.1
            $ourParameters       = \array_change_key_case($this->parameters);
            $parametersToCompare = \array_change_key_case($mediaType->getParameters());

            // if at least one name are different they are not equal
            if (empty(\array_diff_key($ourParameters, $parametersToCompare)) === false) {
                return false;
            }

            // If we are here we have to compare values. Also some of the values should be compared case-insensitive
            // according to https://tools.ietf.org/html/rfc7231#section-3.1.1.1
            // > 'Parameter values might or might not be case-sensitive, depending on
            // the semantics of the parameter name.'
            foreach ($ourParameters as $name => $value) {
                if ($this->paramValuesEqual($name, $value, $parametersToCompare[$name]) === false) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    private function bothMediaTypeParamsEmpty(MediaTypeInterface $mediaType): bool
    {
        return $this->parameters === null && $mediaType->getParameters() === null;
    }

    private function bothMediaTypeParamsNotEmptyAndEqualInSize(MediaTypeInterface $mediaType): bool
    {
        $pr1 = $this->parameters;
        $pr2 = $mediaType->getParameters();

        return (empty($pr1) === false && empty($pr2) === false) && (\count($pr1) === \count($pr2));
    }

    private function isParamCaseInsensitive(string $name): bool
    {
        return isset(static::PARAMETER_NAMES[$name]);
    }

    private function paramValuesEqual(string $name, string $value, string $valueToCompare): bool
    {
        return $this->isParamCaseInsensitive($name) ?
            \strcasecmp($value, $valueToCompare) === 0 : $value === $valueToCompare;
    }

    private function paramValuesMatch(string $name, string $value, string $valueToCompare): bool
    {
        return $valueToCompare === '*' || $this->paramValuesEqual($name, $value, $valueToCompare);
    }
}
