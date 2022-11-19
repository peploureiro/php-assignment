<?php

declare(strict_types = 1);

namespace Tests\unit;

use PHPUnit\Framework\TestCase;
use Statistics\Service\Factory\StatisticsServiceFactory;
use Statistics\Service\StatisticsService;
use Statistics\Builder\ParamsBuilder;
use Statistics\Dto\StatisticsTo;
use SocialPost\Dto\SocialPostTo;
use DateTime;
use ArrayObject;
use Traversable;

/**
 * Class AveragePostNumberPerUserTest
 *
 * @package Tests\unit
 */
class AveragePostNumberPerUserTest extends TestCase
{
    private const JSON_DATA_PATH = "tests/data/social-posts-response.json";
    private const POST_CREATED_DATE_FORMAT = DateTime::ATOM;
    private const MONTH = "August, 2018";

    private StatisticsService $statisticsService;
    private Traversable $posts;

    protected function setUp() : void
    {
        $this->statisticsService = StatisticsServiceFactory::create();

        $json_mock_data = file_get_contents(self::JSON_DATA_PATH);
        $mock_data = json_decode($json_mock_data, true);
        $post_data = $mock_data['data']['posts'];
        $this->posts = $this->convertPosts($post_data);
    }

    protected function tearDown() : void
    {
        unset($this->statisticsService);
        unset($this->posts);
    }

    /**
     * @test
     */
    public function testDoCalculate(): void
    {
        $date = DateTime::createFromFormat('F, Y', self::MONTH);
        $params = ParamsBuilder::reportStatsParams($date);
        $stats_raw = $this->statisticsService->calculateStats($this->posts, $params);
        $stats = $stats_raw->getChildren();

        $this->assertEquals(1, $stats[3]->getValue());
    }

    /**
     *
     */
    private function convertPosts(array $post_data): Traversable
    {
        $posts = new ArrayObject();

        foreach ($post_data as $post_datum) {
            $post = (new SocialPostTo())
                ->setId($post_datum['id'] ?? null)
                ->setAuthorName($post_datum['from_name'] ?? null)
                ->setAuthorId($post_datum['from_id'] ?? null)
                ->setText($post_datum['message'] ?? null)
                ->setType($post_datum['type'] ?? null)
                ->setDate(DateTime::createFromFormat(self::POST_CREATED_DATE_FORMAT, $post_datum['created_time']));

            $posts[] = $post;
        }

        return new ArrayObject($posts);
    }
}
