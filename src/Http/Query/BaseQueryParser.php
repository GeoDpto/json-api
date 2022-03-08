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

use Neomerx\JsonApi\Contract\Http\Query\BaseQueryParserInterface;

/**
 * @package Neomerx\JsonApi
 */
class BaseQueryParser implements BaseQueryParserInterface
{
    use BaseQueryParserTrait {
        BaseQueryParserTrait::getFields as getFieldsImpl;
        BaseQueryParserTrait::getIncludes as getIncludesImpl;
        BaseQueryParserTrait::getIncludePaths as getIncludePathsImpl;
        BaseQueryParserTrait::getSorts as getSortsImpl;
        BaseQueryParserTrait::getProfileUrls as getProfileUrlsImpl;
    }

    public const MSG_ERR_INVALID_PARAMETER = 'Invalid Parameter.';

    private array $parameters;

    private array $messages = [];

    public function __construct(array $parameters = [], array $messages = [])
    {
        $this->setParameters($parameters)->setMessages($messages);
    }

    public function getFields(): iterable
    {
        return $this->getFieldsImpl($this->getParameters(), $this->getMessage(static::MSG_ERR_INVALID_PARAMETER));
    }

    public function getIncludes(): iterable
    {
        return $this->getIncludesImpl($this->getParameters(), $this->getMessage(static::MSG_ERR_INVALID_PARAMETER));
    }

    public function getIncludePaths(): iterable
    {
        return $this->getIncludePathsImpl($this->getParameters(), $this->getMessage(static::MSG_ERR_INVALID_PARAMETER));
    }

    public function getSorts(): iterable
    {
        return $this->getSortsImpl($this->getParameters(), $this->getMessage(static::MSG_ERR_INVALID_PARAMETER));
    }

    public function getProfileUrls(): iterable
    {
        return $this->getProfileUrlsImpl($this->getParameters(), $this->getMessage(static::MSG_ERR_INVALID_PARAMETER));
    }

    protected function setParameters(array $parameters): self
    {
        $this->parameters = $parameters;

        return $this;
    }

    protected function getParameters(): array
    {
        return $this->parameters;
    }

    protected function setMessages(?array $messages): self
    {
        $this->messages = $messages;

        return $this;
    }

    protected function getMessage(string $message): string
    {
        return $this->messages[$message] ?? $message;
    }
}
