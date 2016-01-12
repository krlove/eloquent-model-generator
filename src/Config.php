<?php

namespace Krlove\Generator;

use Krlove\Generator\Exception\ConfigException;

/**
 * Class Config
 * @package Krlove\Generator
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
     */
    public function __construct(array $inputConfig)
    {
        $inputConfig = $this->resolveKeys($inputConfig);

        if (array_key_exists('config', $inputConfig)) {
            if (isset($inputConfig['config']) && file_exists($inputConfig['config'])) {
                $fileConfig = require $inputConfig['config'];

                $userConfig = $this->merge($inputConfig, $fileConfig);
            } else {
                $userConfig = $inputConfig;
            }

            unset($userConfig['config']);
        } else {
            $userConfig = $inputConfig;
        }

        $baseConfig = $this->getBaseConfig();
        $this->validateKeys($userConfig, array_keys($baseConfig));

        // todo validate required options

        $this->config = $this->merge($userConfig, $baseConfig);
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
        return array_key_exists($key, $this->config);
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
        return str_replace('-', '_', $key);
    }

    /**
     * @return array
     */
    protected function getBaseConfig()
    {
        return require __DIR__ . '/Resources/config.php';
    }

    /**
     * @param array $array
     * @param array $allowedKeys
     * @throws ConfigException
     */
    protected function validateKeys(array $array, array $allowedKeys)
    {
        foreach ($array as $key => $value) {
            if (!in_array($key, $allowedKeys)) {
                throw new ConfigException(sprintf('Key `%s` is not allowed', $key));
            }
        }
    }
}
