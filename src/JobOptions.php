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

final class JobOptions
{
    public const PAYLOAD = 'payload';
    public const PAYLOAD_SERVICE_ID = 'service';
    public const PAYLOAD_SERVICE_METHOD = 'method';
    public const PAYLOAD_SERVICE_ARGS = 'args';
    public const RETRY_LIMIT = 'retry_limit';
    public const RETRY_ATTEMPT = 'retry_attempt';
    public const RETRY_STRATEGY = 'retry_strategy';
    public const RECURRENCE = 'recurrence';

    private function __construct()
    {
    }
}
