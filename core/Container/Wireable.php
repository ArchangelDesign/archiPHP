<?php

namespace Archi\Container;

interface Wireable
{
    /**
     * Returns an ID by which entry in ArchiContainer can be located
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Returns an instance of the object that will be returned by ArchiContainer
     *
     * @param ArchiContainer $container
     * @return object
     */
    public function wire(ArchiContainer $container): object;
}
