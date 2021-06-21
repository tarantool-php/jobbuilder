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

namespace Tarantool\JobQueue\JobBuilder;

use Tarantool\Queue\Queue;
use Tarantool\Queue\Task;

final class JobEmitter
{
    public function emit(iterable $jobBuilders, Queue $queue) : array
    {
        $jobs = [];
        foreach ($jobBuilders as $key => $jobBuilder) {
            $jobs[$key] = $jobBuilder->build();
        }

        if (!$count = \count($jobs)) {
            return [];
        }

        if (1 === $count) {
            return [$queue->put(...\reset($jobs))];
        }

        $response = $queue->call('put_many', $jobs);
        $isMap = \array_values($jobs) !== $jobs;

        return self::parseCallResponse($response, $isMap);
    }

    private static function parseCallResponse(array $response, bool $isMapResponse) : array
    {
        $tuples = $isMapResponse
            ? self::getTuplesFromMapResponse($response)
            : self::getTuplesFromArrayResponse($response);

        $tasks = [];
        foreach ($tuples as $key => $tuple) {
            $tasks[$key] = Task::fromTuple($tuple);
        }

        return $tasks;
    }

    private static function getTuplesFromMapResponse(array $response) : array
    {
        if (!empty($response[0][0]) && \is_array($response[0][0])) {
            return $response[0][0];
        }

        throw new \UnexpectedValueException('Unable to parse call response as map.');
    }

    private static function getTuplesFromArrayResponse(array $response) : array
    {
        if (!empty($response[0]) && \is_array($response[0])) {
            return $response[0];
        }

        throw new \UnexpectedValueException('Unable to parse call response as array.');
    }
}
