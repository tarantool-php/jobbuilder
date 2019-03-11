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
        $self->payload['service'] = $serviceId;
        $self->payload['args'] = $serviceArgs;

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
            ? $new->payload['args'][] = $value
            : $new->payload['args'][$key] = $value;

        return $new;
    }

    public function withConstantBackoff() : self
    {
        $new = clone $this;
        $new->jobOptions['retry_strategy'] = 'constant';

        return $new;
    }

    public function withExponentialBackoff() : self
    {
        $new = clone $this;
        $new->jobOptions['retry_strategy'] = 'exponential';

        return $new;
    }

    public function withLinearBackoff() : self
    {
        $new = clone $this;
        $new->jobOptions['retry_strategy'] = 'linear';

        return $new;
    }

    public function withMaxRetries(int $maxRetries) : self
    {
        $new = clone $this;
        $new->jobOptions['retry_limit'] = $maxRetries;

        return $new;
    }

    public function withDisabledRetries() : self
    {
        $new = clone $this;
        $new->jobOptions['retry_limit'] = 0;

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
            \array_merge(['payload' => $this->payload], $this->jobOptions),
            $this->taskOptions,
        ];
    }

    public function putTo(Queue $queue) : Task
    {
        return $queue->put(...$this->build());
    }
}
