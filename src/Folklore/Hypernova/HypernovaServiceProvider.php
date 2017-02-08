<?php

namespace Folklore\Hypernova;

use Illuminate\Support\ServiceProvider;
use Folklore\Hypernova\Contracts\Renderer as RendererContract;
use WF\Hypernova\Renderer;

class HypernovaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootPublishes();

        $this->bootBlade();
    }

    protected function bootPublishes()
    {
        // Config file path
        $configFile = __DIR__ . '/../../config/hypernova.php';

        // Merge files
        $this->mergeConfigFrom($configFile, 'hypernova');

        // Publish
        $this->publishes([
            $configFile => config_path('hypernova.php')
        ], 'config');
    }

    protected function bootBlade()
    {
        if ($this->app->bound('blade.compiler')) {
            $this->app['blade.compiler']
                ->directive('hypernova', function ($expression) {
                    return "<?php ".
                        "\$uuid = app('hypernova')->addJob({$expression});".
                        "echo app('hypernova')->renderPlaceholder(\$uuid);?>";
                });
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerHypernova();

        $this->registerRenderer();
    }

    protected function registerHypernova()
    {
        $this->app->singleton('hypernova', function ($app) {
            return new Hypernova($app);
        });
    }

    protected function registerRenderer()
    {
        $this->app->bind(RendererContract::class, function ($app) {
            $host = $app['config']['hypernova.host'];
            $port = $app['config']['hypernova.port'];
            return new Renderer($host.':'.$port);
        });
    }
}
