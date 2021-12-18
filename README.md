Example repository to illustrate how to use [roach-php/laravel](https://github.com/roach-php/laravel) in a Laravel app.

Check `app/Spiders/FussballdatenSpider.php`  for an example spider that crawls yesterday’s football matches from 
[https://fussballdaten.de](https://fussballdaten.de) and imports new ones into a SQLite database.

Set the `LOG_CHANNEL` to `stdout` in your `.env` file to have Roach log to stdout during a spider’s run.

Run `php artisan scrape:fussballdaten` to run the spider.
