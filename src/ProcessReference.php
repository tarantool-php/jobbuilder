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

final class ProcessReference
{
    private $commandLine;

    public function __construct(string $commandLine)
    {
        $this->commandLine = $commandLine;
    }

    public function getCommandLine() : string
    {
        return $this->commandLine;
    }
}
