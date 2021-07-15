<?php

namespace Tarantool\JobQueue\JobBuilder;

final class RetryStrategies
{
    public const CONSTANT = 'constant';
    public const EXPONENTIAL = 'exponential';
    public const LINEAR = 'linear';

    private function __construct()
    {
    }
}
