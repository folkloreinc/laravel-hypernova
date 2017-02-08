<?php

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Folklore\Image\Exception\FormatException;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('view.paths', [
            __DIR__.'/views/'
        ]);
        $app['config']->set('hypernova.host', 'localhost');
        $app['config']->set('hypernova.port', 3030);
    }

    protected function getPackageProviders($app)
    {
        return [
            \Folklore\Hypernova\HypernovaServiceProvider::class
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Hypernova' => \Folklore\Hypernova\Support\Facades\Hypernova::class
        ];
    }
}
