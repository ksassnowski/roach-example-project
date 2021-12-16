<?php

namespace App\Spiders\Processors;

use App\Models\FootballMatch;
use RoachPHP\ItemPipeline\ItemInterface;
use RoachPHP\ItemPipeline\ItemProcessor;

class SaveMatchToDatabaseProcessor extends ItemProcessor
{
    public function processItem(ItemInterface $item): ItemInterface
    {
        FootballMatch::create([
            'external_id' => $item->get('id'),
            'home_team' => $item->get('home_team'),
            'away_team' => $item->get('away_team'),
            'home_score' => $item->get('home_score'),
            'away_score' => $item->get('away_score'),
            'played_at' => $item->get('date'),
        ]);

        return $item;
    }
}
