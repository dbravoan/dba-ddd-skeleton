<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\BoundedContextExample\Article\Application;

use Dba\DddSkeleton\BoundedContextExample\Article\Application\Find\FindArticleQuery;
use Dba\DddSkeleton\BoundedContextExample\Article\Application\Find\FindArticleQueryHandler;
use Dba\DddSkeleton\BoundedContextExample\Article\Application\Response\ArticleResponse;
use Dba\DddSkeleton\BoundedContextExample\Article\Domain\Article;
use Dba\DddSkeleton\BoundedContextExample\Article\Domain\ArticleId;
use Dba\DddSkeleton\BoundedContextExample\Article\Domain\ArticleName;
use Dba\DddSkeleton\BoundedContextExample\Article\Domain\ArticleRepository;
use PHPUnit\Framework\TestCase;

final class FindArticleQueryHandlerTest extends TestCase
{
    public function testItReturnsArticleResponseWhenFound(): void
    {
        $id = '123';
        $name = 'Test Article';
        $price = 10.5;
        $stock = 5;

        $article = Article::create(
            new ArticleId($id),
            new ArticleName($name),
            $price,
            $stock
        );

        $repository = $this->createMock(ArticleRepository::class);
        $repository->method('search')->willReturn($article);

        $handler = new FindArticleQueryHandler($repository);
        $query = new FindArticleQuery($id);

        $response = $handler($query);

        $this->assertInstanceOf(ArticleResponse::class, $response);
        $this->assertEquals([
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'stock' => $stock,
        ], $response->toArray());
    }

    public function testItReturnsNullWhenNotFound(): void
    {
        $repository = $this->createMock(ArticleRepository::class);
        $repository->method('search')->willReturn(null);

        $handler = new FindArticleQueryHandler($repository);
        $query = new FindArticleQuery('999');

        $response = $handler($query);

        $this->assertNull($response);
    }
}
