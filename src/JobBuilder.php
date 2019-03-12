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

class JobBuilder
{
    private $payload;
    private $jobOptions = [];
    private $taskOptions = [];

    public static function fromService(string $serviceId, array $serviceArgs = []) : self
    {
        $self = new self();
        $self->payload[JobOptions::PAYLOAD_SERVICE_ID] = $serviceId;
        $self->payload[JobOptions::PAYLOAD_SERVICE_ARGS] = $serviceArgs;

        return $self;
    }

    public static function fromPayload($payload) : self
    {
        $self = new self();
        $self->payload = $payload;

        return $self;
    }

    public function withServiceArg($value, $key = null) : self
    {
        $new = clone $this;

        (null === $key)
            ? $new->payload[JobOptions::PAYLOAD_SERVICE_ARGS][] = $value
            : $new->payload[JobOptions::PAYLOAD_SERVICE_ARGS][$key] = $value;

        return $new;
    }

    public function withConstantBackoff() : self
    {
        $new = clone $this;
        $new->jobOptions[JobOptions::RETRY_STRATEGY] = RetryStrategies::CONSTANT;

        return $new;
    }

    public function withExponentialBackoff() : self
    {
        $new = clone $this;
        $new->jobOptions[JobOptions::RETRY_STRATEGY] = RetryStrategies::EXPONENTIAL;

        return $new;
    }

    public function withLinearBackoff() : self
    {
        $new = clone $this;
        $new->jobOptions[JobOptions::RETRY_STRATEGY] = RetryStrategies::LINEAR;

        return $new;
    }

    public function withMaxRetries(int $maxRetries) : self
    {
        $new = clone $this;
        $new->jobOptions[JobOptions::RETRY_LIMIT] = $maxRetries;

        return $new;
    }

    public function withDisabledRetries() : self
    {
        $new = clone $this;
        $new->jobOptions[JobOptions::RETRY_LIMIT] = 0;

        return $new;
    }

    public function withRecurrenceInterval(int $interval) : self
    {
        $new = clone $this;
        $new->jobOptions[JobOptions::RECURRENCE] = $interval;

        return $new;
    }

    public function withDisabledRecurrence() : self
    {
        $new = clone $this;
        unset($new->jobOptions[JobOptions::RECURRENCE]);

        return $new;
    }

    public function withTimeToRun(int $ttl) : self
    {
        $new = clone $this;
        $new->taskOptions[Options::TTL] = $ttl;

        return $new;
    }

    public function withTimeToExecute(int $ttr) : self
    {
        $new = clone $this;
        $new->taskOptions[Options::TTR] = $ttr;

        return $new;
    }

    public function withPriority(int $priority) : self
    {
        $new = clone $this;
        $new->taskOptions[Options::PRI] = $priority;

        return $new;
    }

    public function withDelay(int $delay) : self
    {
        $new = clone $this;
        $new->taskOptions[Options::DELAY] = $delay;

        return $new;
    }

    public function withTube(string $tube) : self
    {
        $new = clone $this;
        $new->taskOptions[Options::UTUBE] = $tube;

        return $new;
    }

    public function build() : array
    {
        return [
            \array_merge([JobOptions::PAYLOAD => $this->payload], $this->jobOptions),
            $this->taskOptions,
        ];
    }

    public function putTo(Queue $queue) : Task
    {
        return $queue->put(...$this->build());
    }
}
