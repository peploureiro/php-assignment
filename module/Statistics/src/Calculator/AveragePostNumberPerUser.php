<?php

declare(strict_types = 1);

namespace Statistics\Calculator;

use SocialPost\Dto\SocialPostTo;
use Statistics\Dto\StatisticsTo;

class AveragePostNumberPerUser extends AbstractCalculator
{
    protected const UNITS = 'posts';

    /**
     * @var array
     */
    private array $totals = [];

    /**
     * @param SocialPostTo $postTo
     */
    protected function doAccumulate(SocialPostTo $postTo): void
    {
        $post_author_name = $postTo->getAuthorName();

        if (in_array($post_author_name, array_keys($this->totals))) {
            $this->totals[$post_author_name]++;
        } else {
            $this->totals[$post_author_name] = 1;
        }
    }

    /**
     * @return StatisticsTo
     */
    protected function doCalculate(): StatisticsTo
    {
        $value = (!empty($this->totals)) ? array_sum($this->totals) / count($this->totals) : 0;

        return (new StatisticsTo())->setValue(round($value,2));
    }
}
