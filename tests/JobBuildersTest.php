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
use Tarantool\JobQueue\JobBuilder\JobBuilders;
use Tarantool\JobQueue\JobBuilder\JobOptions;

final class JobBuildersTest extends TestCase
{
    public function testIterateServiceNameDefinitions() : void
    {
        $definitions = [
            'task_data_1',
            'task_data_2',
        ];

        $builders = new JobBuilders($definitions);

        $expected = [];
        foreach ($builders as $builder) {
            $expected[] = $builder->build()[0][JobOptions::PAYLOAD][JobOptions::PAYLOAD_SERVICE_ID] ?? null;
        }

        self::assertSame($expected, $definitions);
    }

    public function testIterateJobBuilderDefinitions() : void
    {
        $definitions = [
            JobBuilder::fromService('task_data_1'),
            JobBuilder::fromService('task_data_2'),
        ];

        $builders = new JobBuilders($definitions);

        $expected = [];
        foreach ($builders as $builder) {
            $expected[] = $builder;
        }

        self::assertSame($expected, $definitions);
    }

    public function testIterateInvalidDefinitions() : void
    {
        $builders = new JobBuilders([
            'task_data_1',
            new \stdClass(),
        ]);

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unable to parse definition.');

        foreach ($builders as $builder) {
            $builder->build();
        }
    }

    public function testApply() : void
    {
        $builders = new JobBuilders([
            'task_data_1',
            'task_data_2',
        ]);

        $builders = $builders->apply(static function (JobBuilder $builder) : JobBuilder {
            $taskData = $builder->build()[0];
            $serviceName = $taskData[JobOptions::PAYLOAD][JobOptions::PAYLOAD_SERVICE_ID];

            return JobBuilder::fromService(strtoupper($serviceName));
        });

        $expected = [];
        foreach ($builders as $builder) {
            $expected[] = $builder->build()[0][JobOptions::PAYLOAD][JobOptions::PAYLOAD_SERVICE_ID] ?? null;
        }

        self::assertSame($expected, [
            'TASK_DATA_1',
            'TASK_DATA_2',
        ]);
    }

    public function testConstructorIteratorArgument() : void
    {
        $generator = static function () {
            yield from ['task_data_1', 'task_data_2'];
        };

        $builders = new JobBuilders($generator());

        $expected = [];
        foreach ($builders as $builder) {
            $expected[] = $builder->build()[0][JobOptions::PAYLOAD][JobOptions::PAYLOAD_SERVICE_ID] ?? null;
        }

        self::assertSame($expected, [
            'task_data_1',
            'task_data_2',
        ]);
    }
}
