<?php

namespace App\Spiders;

use App\Spiders\Processors\SaveMatchToDatabaseProcessor;
use App\Spiders\Processors\ValidateMatchDataProcessor;
use App\Spiders\SpiderMiddleware\CheckMatchAlreadyExistsMiddleware;
use Carbon\Carbon;
use Generator;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Downloader\Middleware\UserAgentMiddleware;
use RoachPHP\Extensions\LoggerExtension;
use RoachPHP\Extensions\StatsCollectorExtension;
use RoachPHP\Http\Request;
use RoachPHP\Http\Response;
use RoachPHP\ResponseProcessing\ParseResult;
use RoachPHP\Spider\BasicSpider;
use Symfony\Component\DomCrawler\Crawler;
use Throwable;

class FussballdatenSpider extends BasicSpider
{
    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
        [UserAgentMiddleware::class, ['userAgent' => 'Mozilla/5.0 (compatible; RoachPHP/0.1.0)']],
    ];

    public array $spiderMiddleware = [
        CheckMatchAlreadyExistsMiddleware::class,
    ];

    public array $itemProcessors = [
        ValidateMatchDataProcessor::class,
        SaveMatchToDatabaseProcessor::class,
    ];

    public array $extensions = [
        LoggerExtension::class,
        StatsCollectorExtension::class,
    ];

    public int $concurrency = 2;

    public int $requestDelay = 2;

    private Carbon $date;

    public function __construct()
    {
        parent::__construct();

        $this->date = now()->subDays();
    }

    public function parse(Response $response): Generator
    {
        $links = $response->filter('.spiele-row a.ergebnis')
            ->each(function (Crawler $crawler) {
                return [
                    'id' => $crawler->attr('id'),
                    'uri' => $crawler->link()->getUri(),
                ];
            });

        foreach ($links as $link) {
            $request = new Request($link['uri'], [$this, 'parseMatchPage']);

            yield ParseResult::fromValue($request->withMeta('id', $link['id']));
        }
    }

    public function parseMatchPage(Response $response): Generator
    {
        $homeTeam = $response->filter('.box-spiel-verein.home span.verein-name')->text('');
        $awayTeam = $response->filter('.box-spiel-verein.away span.verein-name')->text('');
        $score = explode(':', $response->filter('.box-spiel-ergebnis b')->text(''));
        $date = $this->parseMatchTime(
            $response->filter('.ergebnis-info > span > span:last-child')->text('')
        );

        yield $this->item([
            'id' => $response->getRequest()->getMeta('id'),
            'home_team' => $homeTeam,
            'away_team' => $awayTeam,
            'home_score' => $score[0] ?? null,
            'away_score' => $score[1] ?? null,
            'date' => $date,
        ]);
    }

    protected function getStartUrls(): array
    {
        $yesterday = $this->date->format('Y/m/d');

        return [
            'https://fussballdaten.de/kalender/' . $yesterday . '?whid=alle',
        ];
    }

    private function parseMatchTime(string $text): Carbon
    {
        $date = $this->date->clone();

        $time = trim(strtok($text, 'Uhr'));

        try {
            return $date->setTimeFrom($time);
        } catch (Throwable) {
            return $date;
        }
    }
}
