<?php declare(strict_types=1);

namespace Neomerx\Tests\JsonApi\Schema;

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

use Neomerx\JsonApi\Contract\Schema\SchemaInterface;
use Neomerx\JsonApi\Exception\InvalidArgumentException;
use Neomerx\JsonApi\Exception\LogicException;
use Neomerx\JsonApi\Schema\SchemaContainer;
use Neomerx\Tests\JsonApi\BaseTestCase;
use Neomerx\Tests\JsonApi\Data\Model\Author;
use Neomerx\Tests\JsonApi\Data\Model\Comment;
use Neomerx\Tests\JsonApi\Data\Model\Post;
use Neomerx\Tests\JsonApi\Data\Schema\AuthorSchema;
use Neomerx\Tests\JsonApi\Data\Schema\CommentSchema;
use Neomerx\Tests\JsonApi\Data\Schema\PostSchema;

/**
 * @package Neomerx\Tests\JsonApi
 */
class SchemaContainerTest extends BaseTestCase
{
    /**
     * Test register and get schema.
     */
    public function testRegisterAndGet(): void
    {
        $factory       = $this->createFactory();
        $commentSchema = new CommentSchema($factory);
        $postSchema    = new PostSchema($factory);
        $container     = $factory->createSchemaContainer([
            Author::class  => AuthorSchema::class,
            Comment::class => $commentSchema,
            Post::class    => function () use ($postSchema): SchemaInterface {
                return $postSchema;
            },
        ]);

        $author  = $this->createAuthor();
        $comment = $this->createComment();
        $post    = $this->createPost();

        self::assertTrue($container->hasSchema($author));
        self::assertNotNull($container->getSchema($author));
        self::assertTrue($container->hasSchema($comment));
        self::assertSame($commentSchema, $container->getSchema($comment));
        self::assertTrue($container->hasSchema($post));
        self::assertSame($postSchema, $container->getSchema($post));
    }

    /**
     * Test invalid model class.
     */
    public function testInvalidModelClass(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $notExistingClass = self::class . 'xxx';

        $this->createFactory()->createSchemaContainer([$notExistingClass => AuthorSchema::class]);
    }

    /**
     * Test invalid schema class.
     */
    public function testInvalidSchemaClass(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $notSchemaClass = self::class;

        $this->createFactory()->createSchemaContainer([Author::class => $notSchemaClass]);
    }

    /**
     * Test model cannot have more than one schema.
     */
    public function testModelCannotHaveTwoSchemas(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $container = $this->createFactory()->createSchemaContainer([Author::class  => AuthorSchema::class]);

        assert($container instanceof SchemaContainer);

        $container->register(Author::class, CommentSchema::class);
    }

    /**
     * Test default schema do not provide identifier meta.
     */
    public function testDefaultSchemaDoNotProvideIdentifierMeta(): void
    {
        $this->expectException(LogicException::class);

        $schema = new CommentSchema($this->createFactory());

        $schema->getIdentifierMeta($this->createComment());
    }

    /**
     * Test default schema do not provide resource meta.
     */
    public function testDefaultSchemaDoNotProvideResourceMeta(): void
    {
        $this->expectException(LogicException::class);

        $schema = new CommentSchema($this->createFactory());

        $schema->getResourceMeta($this->createComment());
    }

    /**
     * @return Author
     */
    private function createAuthor(): Author
    {
        return Author::instance(1, 'FirstName', 'LastName');
    }

    /**
     * @return Comment
     */
    private function createComment(): Comment
    {
        return Comment::instance(321, 'Comment body');
    }

    /**
     * @return Post
     */
    private function createPost(): Post
    {
        return Post::instance(321, 'Post Title', 'Post body');
    }
}
