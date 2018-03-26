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
        $directive = function ($expression) {
            if (substr($expression, 0, 1) !== '(' || substr($expression, -1) !== ')') {
                $expression = '('.$expression.')';
            }
            return "<?php echo \$app['hypernova']->pushJob{$expression}; ?>";
        };
        if ($this->app->bound('blade.compiler')) {
            $this->app['blade.compiler']
                ->directive('hypernova', $directive);
        } else {
            $this->app->resolving('blade.compiler', function ($compiler, $app) use ($directive) {
                $compiler->directive('hypernova', $directive);
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
            $host = $app['config']['hypernova.endpoint'];
            return new Renderer($host);
        });
    }
}
