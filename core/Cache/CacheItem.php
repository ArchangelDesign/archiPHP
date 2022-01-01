<?php

namespace Archi\Cache;

use Psr\Cache\CacheItemInterface;

class CacheItem implements CacheItemInterface
{

    private string $key;
    private $value;
    private $isHit;
    private ?\DateTimeInterface $expiration;

    /**
     * CacheItem constructor.
     * @param $key
     * @param $value
     * @param $isHit
     * @param \DateTimeInterface|null $expiration
     */
    public function __construct(string $key, $value, $isHit = true, ?\DateTimeInterface $expiration = null)
    {
        $this->key = $key;
        $this->value = $value;
        $this->isHit = $isHit;
        $this->expiration = $expiration;
    }


    public function getKey()
    {
        return $this->key;
    }

    public function get()
    {
        return $this->value;
    }

    public function isHit()
    {
        return $this->isHit;
    }

    public function set($value)
    {
        $this->value = $value;
        return $this;
    }

    public function expiresAt($expiration)
    {
        $this->expiration = $expiration;
        return $this;
    }

    public function expiresAfter($time)
    {
        return $this;
    }

    public function getExpiration(): int
    {
        $diff = $this->expiration->diff(new \DateTime());
        return $diff->format('%r%s');
    }
}
