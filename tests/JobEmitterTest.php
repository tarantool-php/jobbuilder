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
use Tarantool\JobQueue\JobBuilder\JobEmitter;
use Tarantool\Queue\Queue;
use Tarantool\Queue\States;
use Tarantool\Queue\Task;

final class JobEmitterTest extends TestCase
{
    use PhpUnitCompat;

    public function testEmitNoJobs() : void
    {
        $queue = $this->createMock(Queue::class);
        $queue->expects(self::never())->method('put');

        $emitter = new JobEmitter();
        $tasks = $emitter->emit([], $queue);

        self::assertSame([], $tasks);
    }

    public function testEmitOneJob() : void
    {
        $builder = JobBuilder::fromPayload('task_data');
        $task = Task::fromTuple([42, States::READY, 'task_data']);

        $queue = $this->createMock(Queue::class);
        $queue->expects(self::once())->method('put')
            ->with(['payload' => 'task_data'], [])
            ->willReturn($task);

        $emitter = new JobEmitter();
        $tasks = $emitter->emit([$builder], $queue);

        self::assertSame($task, reset($tasks));
    }

    public function testEmitMultipleJobsArray() : void
    {
        $builder1 = JobBuilder::fromPayload('task_data1');
        $builder2 = JobBuilder::fromPayload('task_data2');

        $task1 = Task::fromTuple([42, States::READY, 'task_data1']);
        $task2 = Task::fromTuple([43, States::READY, 'task_data2']);

        $queue = $this->createMock(Queue::class);
        $queue->expects(self::once())->method('call')
            ->with('put_many', [
                [['payload' => 'task_data1'], []],
                [['payload' => 'task_data2'], []],
            ])
            ->willReturn([[
                [42, States::READY, 'task_data1'],
                [43, States::READY, 'task_data2'],
            ]]);

        $emitter = new JobEmitter();
        $tasks = $emitter->emit([$builder1, $builder2], $queue);

        self::assertEquals([$task1, $task2], $tasks);
    }

    public function testEmitMultipleJobsMap() : void
    {
        $builder1 = JobBuilder::fromPayload('task_data1');
        $builder2 = JobBuilder::fromPayload('task_data2');

        $task1 = Task::fromTuple([42, States::READY, 'task_data1']);
        $task2 = Task::fromTuple([43, States::READY, 'task_data2']);

        $queue = $this->createMock(Queue::class);
        $queue->expects(self::once())->method('call')
            ->with('put_many', [
                [['payload' => 'task_data1'], []],
                'key' => [['payload' => 'task_data2'], []],
            ])
            ->willReturn([[[
                [42, States::READY, 'task_data1'],
                'key' => [43, States::READY, 'task_data2'],
            ]]]);

        $emitter = new JobEmitter();
        $tasks = $emitter->emit([$builder1, 'key' => $builder2], $queue);

        self::assertEquals([$task1, 'key' => $task2], $tasks);
    }

    public function testThrowOnInvalidCallResult() : void
    {
        $builder1 = JobBuilder::fromPayload('task_data1');
        $builder2 = JobBuilder::fromPayload('task_data2');

        $queue = $this->createMock(Queue::class);
        $queue->expects(self::once())->method('call')
            ->willReturn(['invalid_response']);

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessageMatches('/Unable to parse call response as (map|array)\./');

        $emitter = new JobEmitter();
        $emitter->emit([$builder1, $builder2], $queue);
    }
}
