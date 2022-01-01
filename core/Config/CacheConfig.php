<?php

namespace Archi\Config;

use Archi\Environment\Env;

class CacheConfig
{
    private ?string $driver;
    private ?string $host;
    private ?string $username;
    private ?string $password;

    /**
     * CacheConfig constructor.
     * @param string|null $driver
     * @param string|null $host
     * @param string|null $username
     * @param string|null $password
     */
    public function __construct(
        ?string $driver = null,
        ?string $host = null,
        ?string $username = null,
        ?string $password = null
    ) {
        $this->driver = $this->getOrEnv($driver, 'CACHE_DRIVER');
        $this->host = $this->getOrEnv($host, 'CACHE_HOST');
        $this->username = $this->getOrEnv($username, 'CACHE_USERNAME');
        $this->password = $this->getOrEnv($password, 'CACHE_PASSWORD');
    }

    private function getOrEnv(?string $driver, string $envKey)
    {
        if (!is_null($driver)) {
            return $driver;
        }

        return Env::get($envKey);
    }

    /**
     * @return string|null
     */
    public function getDriver(): ?string
    {
        return $this->driver;
    }

    /**
     * @return string|null
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }
}
