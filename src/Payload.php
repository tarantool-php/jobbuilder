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

final class Payload
{
    private $jobReference;
    private $retryStrategy;
    private $maxRetries;
    private $recurrence;

    public function __construct($jobReference, string $retryStrategy, int $maxRetries, ?int $recurrence)
    {
        $this->jobReference = $jobReference;
        $this->retryStrategy = $retryStrategy;
        $this->maxRetries = $maxRetries;
        $this->recurrence = $recurrence;
    }

    public function getJobReference()
    {
        return $this->jobReference;
    }

    public function getRetryStrategy() : ?string
    {
        return $this->retryStrategy;
    }

    public function withRetryStrategy(string $retryStrategy) : self
    {
        $self = clone $this;
        $self->retryStrategy = $retryStrategy;

        return $self;
    }

    public function getMaxRetries() : ?int
    {
        return $this->maxRetries;
    }

    public function withMaxRetries(int $maxRetries) : self
    {
        $self = clone $this;
        $self->maxRetries = $maxRetries;

        return $self;
    }

    public function getRecurrence() : ?int
    {
        return $this->recurrence;
    }

    public function withRecurrence(int $recurrence) : self
    {
        $self = clone $this;
        $this->recurrence = $recurrence;

        return $self;
    }
}
