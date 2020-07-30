<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('web_profiler', ['toolbar' => false]);

    $containerConfigurator->extension('web_profiler', ['intercept_redirects' => false]);

    $containerConfigurator->extension('framework', [
        'profiler' => [
            'collect' => false,
        ],
    ]);
};
