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

use Closure;
use Neomerx\JsonApi\Contract\Http\Header\AcceptMediaTypeInterface;
use Neomerx\JsonApi\Exception\InvalidArgumentException;

/**
 * @package Neomerx\JsonApi
 */
class AcceptMediaType extends MediaType implements AcceptMediaTypeInterface
{
    /**
     * @var float [0..1]
     */
    private $quality;

    /**
     * @var int
     */
    private $position;

    /**
     * @param array<string,string>|null $parameters
     */
    public function __construct(
        int $position,
        string $type,
        string $subType,
        array $parameters = null,
        float $quality = 1.0
    ) {
        parent::__construct($type, $subType, $parameters);

        if ($position < 0) {
            throw new InvalidArgumentException('position');
        }

        if ($quality < 0 || $quality > 1) {
            throw new InvalidArgumentException('quality');
        }

        // rfc2616: 3 digits are meaningful (#3.9 Quality Values)
        $quality = \floor($quality * 1000) / 1000;

        $this->position = $position;
        $this->quality  = $quality;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getQuality(): float
    {
        return $this->quality;
    }

    public static function getCompare(): Closure
    {
        return function (AcceptMediaTypeInterface $lhs, AcceptMediaTypeInterface $rhs) {
            $qualityCompare = self::compareQuality($lhs->getQuality(), $rhs->getQuality());
            if ($qualityCompare !== 0) {
                return $qualityCompare;
            }

            $typeCompare = self::compareStrings($lhs->getType(), $rhs->getType());
            if ($typeCompare !== 0) {
                return $typeCompare;
            }

            $subTypeCompare = self::compareStrings($lhs->getSubType(), $rhs->getSubType());
            if ($subTypeCompare !== 0) {
                return $subTypeCompare;
            }

            $parametersCompare = self::compareParameters($lhs->getParameters(), $rhs->getParameters());
            if ($parametersCompare !== 0) {
                return $parametersCompare;
            }

            return ($lhs->getPosition() - $rhs->getPosition());
        };
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    private static function compareQuality(float $lhs, float $rhs): int
    {
        $qualityDiff = $lhs - $rhs;

        // rfc2616: 3 digits are meaningful (#3.9 Quality Values)
        if (\abs($qualityDiff) < 0.001) {
            return 0;
        } else {
            return $lhs > $rhs ? -1 : 1;
        }
    }

    private static function compareStrings(string $lhs, string $rhs): int
    {
        return ($rhs !== '*' ? 1 : 0) - ($lhs !== '*' ? 1 : 0);
    }

    private static function compareParameters(?array $lhs, ?array $rhs): int
    {
        return (empty($lhs) !== false ? 1 : 0) - (empty($rhs) !== false ? 1 : 0);
    }
}
