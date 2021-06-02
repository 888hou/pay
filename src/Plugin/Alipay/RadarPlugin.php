<?php

declare(strict_types=1);

namespace Yansongda\Pay\Plugin\Alipay;

use Closure;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Yansongda\Pay\Contract\PluginInterface;
use Yansongda\Pay\Pay;
use Yansongda\Pay\Provider\Alipay;
use Yansongda\Pay\Rocket;

class RadarPlugin implements PluginInterface
{
    /**
     * @throws \Yansongda\Pay\Exception\ServiceNotFoundException
     * @throws \Yansongda\Pay\Exception\ContainerException
     * @throws \Yansongda\Pay\Exception\ContainerDependencyException
     */
    public function assembly(Rocket $rocket, Closure $next): Rocket
    {
        $rocket->setRadar($this->getRequest($rocket));

        return $next($rocket);
    }

    /**
     * @throws \Yansongda\Pay\Exception\ContainerDependencyException
     * @throws \Yansongda\Pay\Exception\ContainerException
     * @throws \Yansongda\Pay\Exception\ServiceNotFoundException
     */
    protected function getRequest(Rocket $rocket): RequestInterface
    {
        return new Request(
            $this->getMethod($rocket),
            $this->getUrl($rocket),
            $this->getHeaders(),
            $this->getBody($rocket),
        );
    }

    protected function getMethod(Rocket $rocket): string
    {
        return $rocket->getParams()['_method'] ?? 'POST';
    }

    /**
     * @throws \Yansongda\Pay\Exception\ContainerDependencyException
     * @throws \Yansongda\Pay\Exception\ContainerException
     * @throws \Yansongda\Pay\Exception\ServiceNotFoundException
     */
    protected function getUrl(Rocket $rocket): string
    {
        $config = get_alipay_config($rocket->getParams());

        return Alipay::URL[$config->get('mode', Pay::MODE_NORMAL)];
    }

    protected function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }

    protected function getBody(Rocket $rocket): string
    {
        return $rocket->getPayload()->toJson();
    }
}