# JobBuilder

[![Quality Assurance](https://github.com/tarantool-php/jobbuilder/workflows/QA/badge.svg)](https://github.com/tarantool-php/jobbuilder/actions?query=workflow%3AQA)
[![Telegram](https://img.shields.io/badge/Telegram-join%20chat-blue.svg)](https://t.me/tarantool_php)

A set of utility classes to help creating complex jobs for [Tarantool JobQueue](https://github.com/tarantool-php/jobqueue).


## Installation

The recommended way to create a new application is through [Composer](http://getcomposer.org):

```sh
composer require tarantool/jobbuilder
```


## Usage

```php
use Tarantool\JobQueue\JobBuilder\JobBuilder;

...

$task = JobBuilder::fromService('service_foo', ['bar', 'baz'])
    ->withServiceMethod('qux')
    ->withConstantBackoff()
    ->withMaxRetries(3)
    ->withRecurrenceIntervalSeconds(600)
    ->withTimeToLiveSeconds(300)
    ->withTimeToRunSeconds(180)
    ->withPriority(4)
    ->withDelaySeconds(60)
    ->withTube('foobar')
    ->putTo($queue);
```

```php
use App\Job\MyJob\MyJobHandler;
use Tarantool\JobQueue\JobBuilder\JobBuilder;
use Tarantool\JobQueue\JobBuilder\JobEmitter;

...

$jobBuilders = (static function () use ($ids) {
    foreach ($ids as $id) {
        yield JobBuilder::fromService(MyJobHandler::class, ['id' => $id]);
    }
})();

(new JobEmitter())->emit($jobBuilders, $queue);
```

## Tests

```bash
vendor/bin/phpunit
```


## License

The library is released under the MIT License. See the bundled [LICENSE](LICENSE) file for details.
