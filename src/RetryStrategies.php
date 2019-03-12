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

final class RetryStrategies
{
    public const CONSTANT = 'constant';
    public const EXPONENTIAL = 'exponential';
    public const LINEAR = 'linear';

    private function __construct()
    {
    }
}
