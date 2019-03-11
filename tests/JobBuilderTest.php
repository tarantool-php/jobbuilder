<?php

declare(strict_types=1);

/*
 * This file is part of the Tarantool JobBuilder package.
 *
 * (c) Eugene Leonovich <gen.work@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tarantool\JobQueue\JobBuilder\Tests;

use PHPUnit\Framework\TestCase;
use Tarantool\JobQueue\JobBuilder\JobBuilder;
use Tarantool\Queue\Options;

final class JobBuilderTest extends TestCase
{
    public function testBuild() : void
    {
        [$data, $taskOptions] = JobBuilder::fromPayload('foo')->build();

        self::assertSame(['payload' => 'foo'], $data);
        self::assertSame([], $taskOptions);
    }

    public function testBuildFull() : void
    {
        [$data, $taskOptions] = JobBuilder::fromService('service_foo', ['bar', 'baz'])
            ->withConstantBackoff()
            ->withMaxRetries(3)
            ->withTimeToExecute(5)
            ->withTimeToRun(300)
            ->withPriority(4)
            ->withDelay(60)
            ->withTube('foobar')
            ->build();

        self::assertSame([
            'payload' => [
                'service' => 'service_foo',
                'args' => ['bar', 'baz'],
            ],
            'retry_strategy' => 'constant',
            'retry_limit' => 3,
        ], $data);

        self::assertSame([
            Options::TTR => 5,
            Options::TTL => 300,
            Options::PRI => 4,
            Options::DELAY => 60,
            Options::UTUBE => 'foobar',
        ], $taskOptions);
    }

    public function testBuildServiceArgs() : void
    {
        [$data, ] = JobBuilder::fromService('service_foo', ['foo'])
            ->withServiceArg('bar')
            ->withServiceArg('baz', 'qux')
            ->build();

        self::assertSame([
            'payload' => [
                'service' => 'service_foo',
                'args' => ['foo', 'bar', 'qux' => 'baz'],
            ],
        ], $data);
    }

    public function testBuildWithDisabledRetries() : void
    {
        [$data, $taskOptions] = JobBuilder::fromPayload('foo')
            ->withDisabledRetries()
            ->build();

        self::assertSame([
            'payload' => 'foo',
            'retry_limit' => 0,
        ], $data);

        self::assertSame([], $taskOptions);
    }

    public function testBuildWithConstantRetryStrategy() : void
    {
        [$data, $taskOptions] = JobBuilder::fromPayload('foo')
            ->withConstantBackoff()
            ->build();

        self::assertSame([
            'payload' => 'foo',
            'retry_strategy' => 'constant',
        ], $data);

        self::assertSame([], $taskOptions);
    }

    public function testBuildWithExponentialRetryStrategy() : void
    {
        [$data, $taskOptions] = JobBuilder::fromPayload('foo')
            ->withExponentialBackoff()
            ->build();

        self::assertSame([
            'payload' => 'foo',
            'retry_strategy' => 'exponential',
        ], $data);

        self::assertSame([], $taskOptions);
    }

    public function testBuildWithLinearRetryStrategy() : void
    {
        [$data, $taskOptions] = JobBuilder::fromPayload('foo')
            ->withLinearBackoff()
            ->build();

        self::assertSame([
            'payload' => 'foo',
            'retry_strategy' => 'linear',
        ], $data);

        self::assertSame([], $taskOptions);
    }
}
