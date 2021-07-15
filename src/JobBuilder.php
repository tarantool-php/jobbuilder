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

use Tarantool\Queue\Options;
use Tarantool\Queue\Queue;
use Tarantool\Queue\Task;

final class JobBuilder
{
    private $reference;
    private $retryStrategy;
    private $maxRetries;
    private $repeatInterval;
    private $taskOptions = [];

    private function __construct($reference)
    {
        $this->reference = $reference;
    }

    public static function fromService(string $id, array $arguments = []) : self
    {
        return new self(new ServiceReference($id, $arguments));
    }

    public static function fromCommandLine(string $commandLine) : self
    {
        return new self(new ProcessReference($commandLine));
    }

    public function retryConstantly() : self
    {
        $this->retryStrategy = RetryStrategies::CONSTANT;

        return $this;
    }

    public function retryExponentially() : self
    {
        $this->retryStrategy = RetryStrategies::EXPONENTIAL;

        return $this;
    }

    public function retryLinearly() : self
    {
        $this->retryStrategy = RetryStrategies::LINEAR;

        return $this;
    }

    public function limitRetries(int $maxRetries) : self
    {
        $this->maxRetries = $maxRetries;

        return $this;
    }

    public function disableRetries() : self
    {
        $this->maxRetries = 0;

        return $this;
    }

    public function repeat(int $interval) : self
    {
        $this->repeatInterval = $interval;

        return $this;
    }

    public function limitLifeTime(int $ttl) : self
    {
        $this->taskOptions[Options::TTL] = $ttl;

        return $this;
    }

    public function limitExecutionTime(int $ttr) : self
    {
        $this->taskOptions[Options::TTR] = $ttr;

        return $this;
    }

    public function prioritize(int $priority) : self
    {
        $this->taskOptions[Options::PRI] = $priority;

        return $this;
    }

    public function delay(int $delay) : self
    {
        $this->taskOptions[Options::DELAY] = $delay;

        return $this;
    }

    public function tube(string $tube) : self
    {
        $this->taskOptions[Options::UTUBE] = $tube;

        return $this;
    }

    public function build() : array
    {
        return [
            new Payload(
                $this->reference,
                $this->retryStrategy,
                $this->maxRetries,
                $this->repeatInterval
            ),
            $this->taskOptions,
        ];
    }

    public function putTo(Queue $queue) : Task
    {
        return $queue->put(...$this->build());
    }
}
