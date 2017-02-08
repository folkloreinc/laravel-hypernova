<?php

namespace Folklore\Hypernova;

use Blade;
use Illuminate\Support\ServiceProvider;
use WF\Hypernova\Renderer;
use Folklore\Hypernova\Contracts\Renderer as RendererContract;

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

    public function bootBlade()
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

    public function bootPublishes()
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

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('hypernova', function ($app) {
            return new Hypernova($app);
        });

        $this->app->bind(RendererContract::class, function ($app) {
            $host = $app['config']['hypernova.host'];
            $port = $app['config']['hypernova.port'];
            return new Renderer($host.':'.$port);
        });
    }
}
