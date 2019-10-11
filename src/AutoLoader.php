<?php declare(strict_types = 1);

namespace FZ\ControllerCache;

use Redis;
use Swoft\Helper\ComposerJSON;
use Swoft\Redis\Pool;
use Swoft\Redis\RedisDb;
use Swoft\SwoftComponent;
use function dirname;

/**
 * class AutoLoader
 *
 * @since 2.0
 */
final class AutoLoader extends SwoftComponent
{
    /**
     * @return bool
     */
    public function enable(): bool
    {
        return true;
    }

    /**
     * Get namespace and dirs
     *
     * @return array
     */
    public function getPrefixDirs(): array
    {
        return [
            __NAMESPACE__ => __DIR__,
        ];
    }

    /**
     * Metadata information for the component
     *
     * @return array
     */
    public function metadata(): array
    {
        $jsonFile = dirname(__DIR__).'/composer.json';

        return ComposerJSON::open($jsonFile)->getMetadata();
    }

    /**
     * {@inheritDoc}
     */
    public function beans(): array
    {
        return [
            'controller-cache.redis'      => [
                'class'         => RedisDb::class,
                'host'          => '127.0.0.1',
                'port'          => 6379,
                'database'      => 1,
                'retryInterval' => 10,
                'readTimeout'   => 0,
                'timeout'       => 2,
                'option'        => [
                    'prefix'     => 'controller-cache:',
                    'serializer' => Redis::SERIALIZER_IGBINARY,
                ],
            ],
            'controller-cache.redis.pool' => [
                'class'       => Pool::class,
                'redisDb'     => bean('controller-cache.redis'),
                'minActive'   => 10,
                'maxActive'   => 20,
                'maxWait'     => 0,
                'maxWaitTime' => 0,
                'maxIdleTime' => 60,
            ],
        ];
    }
}
