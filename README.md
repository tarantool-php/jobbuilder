# JobBuilder

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
    ->build();
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
