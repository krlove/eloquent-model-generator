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
     * @var string
     */
    protected $defaultSchemaName;

    /**
     * @var string
     */
    protected $schemaName;

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

    public function getSchemaNameForQuery()
    {
        $schemaName = $this->getSchemaName();
        //Cannot add the default schema name set in the connection for query
        if ($schemaName === $this->defaultSchemaName) {
            $schemaName = "";
        }
        if($schemaName !== ""){
            $schemaName .= ".";
        }
        return $schemaName;
    }

    public function getSchemaName()
    {
        $schemaName = "";
        if (isset($this->schemaName) && $this->schemaName !== "") {
            $schemaName = $this->schemaName;
        } elseif (isset($this->defaultSchemaName) && $this->defaultSchemaName !== "") {
            $schemaName = $this->defaultSchemaName;
        }
        return $schemaName;
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
     * @param string     $key
     * @param mixed      $value
     */
    public function set($key, $value)
    {
        $this->config[$key] = $value;
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
     * @param string      $schemaName
     */
    public function setSchemaName(string $schemaName)
    {
        $this->schemaName = $schemaName;
    }

   /**
     * @param string      $defaultSchemaName
     */
    public function setDefaultSchemaName(string $defaultSchemaName)
    {
        $this->defaultSchemaName = $defaultSchemaName;
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
