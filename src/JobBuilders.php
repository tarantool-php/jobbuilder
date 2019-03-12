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

final class JobBuilders implements \IteratorAggregate
{
    private $definitions;

    public function __construct(iterable $definitions)
    {
        $this->definitions = $definitions;
    }

    public function getIterator() : \Iterator
    {
        foreach ($this->definitions as $definition) {
            yield self::parseDefinition($definition);
        }
    }

    public function apply(\Closure $callback) : \Iterator
    {
        foreach ($this->getIterator() as $jobBuilder) {
            yield $callback($jobBuilder);
        }
    }

    private static function parseDefinition($definition) : JobBuilder
    {
        if (\is_string($definition)) {
            $definition = JobBuilder::fromService($definition);
        }

        if ($definition instanceof JobBuilder) {
            return $definition;
        }

        throw new \UnexpectedValueException('Unable to parse definition.');
    }
}
