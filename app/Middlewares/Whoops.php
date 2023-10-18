<?php

namespace EK\Middlewares;

use Whoops\Run;
use Whoops\Handler\PlainTextHandler;
use Nyholm\Psr7\Factory\Psr17Factory;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\XmlResponseHandler;
use Whoops\Handler\JsonResponseHandler;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;

class Whoops implements MiddlewareInterface
{
    public function __construct(
        private Psr17Factory $responseFactory
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (HttpNotFoundException|HttpMethodNotAllowedException $e) {
            return $handler->handle($request);
        } catch (\Throwable $e) {
            // Handle the exception with Whoops
            $response = $this->responseFactory->createResponse(500);

            $acceptHeaders = explode(',', $request->getHeader('accept')[0] ?? '');
            $response->getBody()->write($this->renderWhoops($e, $acceptHeaders));

            return $response;
        }
    }

    private function renderWhoops(\Throwable $e, array $acceptHeaders = ['application/json']): string
    {
        $whoops = new Run();
        $whoops->allowQuit(false);
        $whoops->writeToOutput(false);

        /** @var \Whoops\Handler\PrettyPageHandler|\Whoops\Handler\JsonResponseHandler|\Whoops\Handler\XmlResponseHandler|\Whoops\Handler\PlainTextHandler $handler */
        $handler = null;

        foreach ($acceptHeaders as $acceptHeader) {
            $handler = match ($acceptHeader) {
                'application/json' => new JsonResponseHandler(),
                'application/xml', 'text/xml' => new XmlResponseHandler(),
                'text/plain', 'text/css', 'text/javascript' => new PlainTextHandler(),
                default => new PrettyPageHandler()
            };
        }

        if ($handler instanceof PrettyPageHandler) {
            $handler->handleUnconditionally(true);
            $handler->setEditor('vscode');
        }

        $whoops->prependHandler($handler);
        return $whoops->handleException($e);
    }
}
