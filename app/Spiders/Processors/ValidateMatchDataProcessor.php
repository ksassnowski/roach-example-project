<?php

namespace App\Spiders\Processors;

use Log;
use RoachPHP\ItemPipeline\ItemInterface;
use RoachPHP\ItemPipeline\ItemProcessor;

class ValidateMatchDataProcessor extends ItemProcessor
{
    public function processItem(ItemInterface $item): ItemInterface
    {
        $matchId = $item->get('id');
        $date = $item->get('date');

        if ($date === null) {
            return $item->drop("No match date for match $matchId");
        }

        if ($item->get('home_score') === null || $item->get('away_score') === null) {
            return $item->drop('No score');
        }

        return $item;
    }
}
