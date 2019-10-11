<?php declare(strict_types = 1);

namespace FZ\ControllerCache\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Http\Message\Request;
use Swoft\Http\Server\Contract\MiddlewareInterface;
use Swoft\Redis\Redis;

/**
 * Class CacheMiddleware
 *
 * @Bean()
 */
class CacheMiddleware implements MiddlewareInterface
{
    /**
     * Process an incoming server request.
     *
     * @param ServerRequestInterface|Request $request
     * @param RequestHandlerInterface        $handler
     *
     * @throws \Swoft\Exception\SwoftException
     * @throws \Swoft\Redis\Exception\RedisException
     * @throws \Swoft\Bean\Exception\ContainerException
     * @throws \ReflectionException
     * @inheritdoc
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->isGet()) {
            $cacheControl = $request->getHeaderLine('Cache-Control');
            $authentication = $request->getHeaderLine('Authentication');
            $uriPath = $request->getUriPath();
            $uriQuery = $request->getUriQuery();
            $uri = md5($uriPath.$uriQuery);
            $cacheKey = "{$uri}";

            if ($cacheControl !== 'no-cache' || $authentication === '') {

                $redisBean = Redis::connection('controller-cache.redis.pool');

                if ($redisBean->exists($cacheKey)) {
                    list($code, $body, $headers) = $redisBean->get($cacheKey);
                    $content = context()->getResponse()
                                        ->withStatus($code)
                                        ->withBody($body)
                                        ->withHeaders($headers);
                } else {
                    $content = $handler->handle($request);
                    $redisBean->set($cacheKey, [
                        $content->getStatusCode(),
                        $content->getBody(),
                        $content->getHeaders(),
                    ]);
                }

                return $content;
            }
        }

        return $handler->handle($request);
    }
}
