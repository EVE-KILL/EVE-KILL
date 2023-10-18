<?php

namespace EK\Http\Twig;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Twig
{
    protected Environment $twig;

    public function __construct()
    {
        $templateDirectory = isset($_SERVER['TWIG_TEMPLATE_DIR']) ? $_SERVER['TWIG_TEMPLATE_DIR'] : BASE_DIR . '/templates';
        if (!file_exists($templateDirectory)) {
            mkdir($templateDirectory, 0777, true);
        }

        $loader = new FilesystemLoader($templateDirectory);
        $this->twig = new Environment($loader, [
            'cache' => isset($_SERVER['TWIG_CACHE_DIR']) ? $_SERVER['TWIG_CACHE_DIR'] : BASE_DIR . '/resources/cache/twig',
            'debug' => isset($_SERVER['TWIG_DEBUG']) ? $_SERVER['TWIG_DEBUG'] === '1' : true,
            'auto_reload' => isset($_SERVER['TWIG_AUTO_RELOAD']) ? $_SERVER['TWIG_AUTO_RELOAD'] === '1' : true,
            'strict_variables' => isset($_SERVER['TWIG_STRICT_VARIABLES']) ? $_SERVER['TWIG_STRICT_VARIABLES'] === '1' : true,
            'optimizations' => isset($_SERVER['TWIG_OPTIMIZATIONS']) ? $_SERVER['TWIG_OPTIMIZATIONS'] === '1' : true
        ]);
    }

    public function render(string $templatePath, array $data = []): string
    {
        if (pathinfo($templatePath, PATHINFO_EXTENSION) !== 'twig') {
            throw new \RuntimeException('Error, twig templates need to end in .twig');
        }

        return $this->twig->render($templatePath, $data);
    }
}
