<?php

namespace Archi\Container\TestObjects;

/**
 * Class CannotBeAutowired
 * Autowire test. This class cannot be autowired
 * as the argumment type is builtin and does not have default value.
 */
class CannotBeAutowired
{
    public $s;

    public function __construct(string $s)
    {
        $this->s = $s;
    }
}
