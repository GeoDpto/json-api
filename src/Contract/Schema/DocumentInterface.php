<?php

declare(strict_types=1);

namespace Neomerx\JsonApi\Contract\Schema;

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

/**
 * @package Neomerx\JsonApi
 */
interface DocumentInterface
{
    public const KEYWORD_LINKS = 'links';
    public const KEYWORD_HREF = 'href';
    public const KEYWORD_RELATIONSHIPS = 'relationships';
    public const KEYWORD_SELF = 'self';
    public const KEYWORD_FIRST = 'first';
    public const KEYWORD_LAST = 'last';
    public const KEYWORD_NEXT = 'next';
    public const KEYWORD_PREV = 'prev';
    public const KEYWORD_RELATED = 'related';
    public const KEYWORD_TYPE = 'type';
    public const KEYWORD_ID = 'id';
    public const KEYWORD_ATTRIBUTES = 'attributes';
    public const KEYWORD_META = 'meta';
    public const KEYWORD_ALIASES = 'aliases';
    public const KEYWORD_PROFILE = 'profile';
    public const KEYWORD_DATA = 'data';
    public const KEYWORD_INCLUDED = 'included';
    public const KEYWORD_JSON_API = 'jsonapi';
    public const KEYWORD_VERSION = 'version';
    public const KEYWORD_ERRORS = 'errors';
    public const KEYWORD_ERRORS_ID = 'id';
    public const KEYWORD_ERRORS_TYPE = 'type';
    public const KEYWORD_ERRORS_STATUS = 'status';
    public const KEYWORD_ERRORS_CODE = 'code';
    public const KEYWORD_ERRORS_TITLE = 'title';
    public const KEYWORD_ERRORS_DETAIL = 'detail';
    public const KEYWORD_ERRORS_META = 'meta';
    public const KEYWORD_ERRORS_SOURCE = 'source';
    public const KEYWORD_ERRORS_ABOUT = 'about';
    public const PATH_SEPARATOR = '.';
}
