<?php

namespace Krlove\EloquentModelGenerator;

class Config
{
    protected array $config;

    public function __construct(array $inputConfig, array $appConfig = [])
    {
        $inputConfig = $this->resolveKeys($inputConfig);

        if (count($appConfig) > 0) {
            $inputConfig = $this->merge($inputConfig, $appConfig);
        }

        $this->config = $this->merge($inputConfig, $this->getBaseConfig());
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->has($key) ? $this->config[$key] : $default;
    }

    public function has(string $key): bool
    {
        return isset($this->config[$key]);
    }

    protected function merge(array $high, array $low): array
    {
        foreach ($high as $key => $value)
        {
            if ($value !== null) {
                $low[$key] = $value;
            }
        }

        return $low;
    }

    protected function resolveKeys(array $array): array
    {
        $resolved = [];
        foreach ($array as $key => $value) {
            $resolvedKey = $this->resolveKey($key);
            $resolved[$resolvedKey] = $value;
        }

        return $resolved;
    }

    protected function resolveKey(string $key): string
    {
        return str_replace('-', '_', strtolower($key));
    }

    protected function getBaseConfig(): array
    {
        return require __DIR__ . '/Resources/config.php';
    }
}
