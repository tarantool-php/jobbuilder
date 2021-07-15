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

final class ServiceReference
{
    private $id;
    private $arguments;

    public function __construct(string $id, array $arguments = [])
    {
        $this->id = $id;
        $this->arguments = $arguments;
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function getArguments() : array
    {
        return $this->arguments;
    }

    public function getArgument(string $name)
    {
        if (array_key_exists($name, $this->arguments)) {
            return $this->arguments[$name];
        }

        throw new \OutOfBoundsException(sprintf('The argument "%s" doesn\'t exist', $name));
    }

    public function tryGetArgument(string $name, $default = null)
    {
        if (array_key_exists($name, $this->arguments)) {
            return $this->arguments[$name];
        }

        return $default;
    }
}
