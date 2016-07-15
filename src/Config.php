<?php

namespace Krlove\EloquentModelGenerator;

/**
 * Class Config
 * @package Krlove\EloquentModelGenerator
 */
class Config
{
    /**
     * @var array
     */
    protected $config;

    /**
     * Config constructor.
     * @param array $inputConfig
     * @param array|null $appConfig
     */
    public function __construct($inputConfig, $appConfig = null)
    {
        $inputConfig = $this->resolveKeys($inputConfig);

        if ($appConfig !== null && is_array($appConfig)) {
            $inputConfig = $this->merge($inputConfig, $appConfig);
        }

        $this->config = $this->merge($inputConfig, $this->getBaseConfig());
    }

    /**
     * @param string     $key
     * @param mixed|null $default
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->config[$key] : $default;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->config[$key]);
    }

    /**
     * @param array $high
     * @param array $low
     * @return array
     */
    protected function merge(array $high, array $low)
    {
        foreach ($high as $key => $value)
        {
            if ($value !== null) {
                $low[$key] = $value;
            }
        }

        return $low;
    }

    /**
     * @param array $array
     * @return array
     */
    protected function resolveKeys(array $array)
    {
        $resolved = [];
        foreach ($array as $key => $value) {
            $resolvedKey = $this->resolveKey($key);
            $resolved[$resolvedKey] = $value;
        }

        return $resolved;
    }

    /**
     * @param string $key
     * @return mixed
     */
    protected function resolveKey($key)
    {
        return str_replace('-', '_', strtolower($key));
    }

    /**
     * @return array
     */
    protected function getBaseConfig()
    {
        return require __DIR__ . '/Resources/config.php';
    }
}
