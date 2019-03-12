# JobBuilder

A set of utility classes to help creating complex jobs for Tarantool JobQueue.


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
    ->withRecurrenceInterval(60)
    ->withTimeToExecute(5)
    ->withTimeToRun(300)
    ->withPriority(4)
    ->withDelay(60)
    ->withTube('foobar')
    ->putTo($queue);
```

```php
$createJobBuilders = static function () use ($ids) {
    foreach ($ids as $id) {
        yield JobBuilder::fromService(MyHandler::class, ['id' => $id]);
    }
};

(new JobEmitter())->emit($createJobBuilders(), $queue);
```

## Tests

```bash
vendor/bin/phpunit
```


## License

The library is released under the MIT License. See the bundled [LICENSE](LICENSE) file for details.
