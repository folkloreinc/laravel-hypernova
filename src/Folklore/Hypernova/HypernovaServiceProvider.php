<?php

namespace Folklore\Hypernova;

use Blade;
use Illuminate\Support\ServiceProvider;
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
        $this->bootBlade();

        $this->bootPublishes();
    }

    public function bootBlade()
    {
        app('blade')->directive('hypernova', function ($expression) {
            return "<?php echo app('hypernova')->addJob{$expression}; ?>";
        });
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
            $host = config('hypernova.host');
            $port = config('hypernova.port');
            $renderer = new Renderer($host.':'.$port);
            return new Hypernova($app, $renderer);
        });
    }
}
