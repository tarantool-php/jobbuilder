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

        self::assertEquals([
            'payload' => [
                'service' => 'service_foo',
                'method' => 'qux',
                'args' => ['bar', 'baz'],
            ],
            'retry_strategy' => 'constant',
            'retry_limit' => 3,
            'recurrence' => 600,
        ], $data);

        self::assertSame([
            Options::TTL => 300,
            Options::TTR => 180,
            Options::PRI => 4,
            Options::DELAY => 60,
            Options::UTUBE => 'foobar',
        ], $taskOptions);
    }

    public function testBuildServiceArgs() : void
    {
        [$data] = JobBuilder::fromService('service_foo', ['foo'])
            ->withServiceArg('bar')
            ->withServiceArg('baz', 'qux')
            ->build();

        self::assertEquals([
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

        self::assertEquals([
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

        self::assertEquals([
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

        self::assertEquals([
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

        self::assertEquals([
            'payload' => 'foo',
            'retry_strategy' => 'linear',
        ], $data);

        self::assertSame([], $taskOptions);
    }
}
