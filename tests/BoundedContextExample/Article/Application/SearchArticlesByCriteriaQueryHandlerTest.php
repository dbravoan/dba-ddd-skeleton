<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\BoundedContextExample\Article\Application;

use Dba\DddSkeleton\BoundedContextExample\Article\Application\Response\ArticleResponse;
use Dba\DddSkeleton\BoundedContextExample\Article\Application\Response\ArticlesResponse;
use Dba\DddSkeleton\BoundedContextExample\Article\Application\SearchByCriteria\ArticlesByCriteriaSearcher;
use Dba\DddSkeleton\BoundedContextExample\Article\Application\SearchByCriteria\SearchArticlesByCriteriaQuery;
use Dba\DddSkeleton\BoundedContextExample\Article\Application\SearchByCriteria\SearchArticlesByCriteriaQueryHandler;
use Dba\DddSkeleton\BoundedContextExample\Article\Domain\Article;
use Dba\DddSkeleton\BoundedContextExample\Article\Domain\ArticleId;
use Dba\DddSkeleton\BoundedContextExample\Article\Domain\ArticleName;
use PHPUnit\Framework\TestCase;

final class SearchArticlesByCriteriaQueryHandlerTest extends TestCase
{
    public function testItReturnsArticlesResponse(): void
    {
        $article = Article::create(
            new ArticleId('123'),
            new ArticleName('Test Article'),
            10.5,
            5
        );

        $searcher = $this->createMock(ArticlesByCriteriaSearcher::class);
        $searcher->method('search')->willReturn([$article]);

        $handler = new SearchArticlesByCriteriaQueryHandler($searcher);
        $query = new SearchArticlesByCriteriaQuery([], 'name', 'asc', 10, 0);

        $response = $handler($query);

        $this->assertInstanceOf(ArticlesResponse::class, $response);
        $this->assertCount(1, $response->articles());
        $this->assertInstanceOf(ArticleResponse::class, $response->articles()[0]);
        $this->assertEquals([
            [
                'id' => '123',
                'name' => 'Test Article',
                'price' => 10.5,
                'stock' => 5,
            ]
        ], $response->toArray());
    }
}
