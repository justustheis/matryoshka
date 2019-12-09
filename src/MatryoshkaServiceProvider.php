<?php

namespace JustusTheis\Matryoshka;

use Blade;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class MatryoshkaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @param  Kernel  $kernel
     */
    public function boot(Kernel $kernel)
    {
        if ($this->app->isLocal()) {
            $kernel->pushMiddleware('JustusTheis\Matryoshka\FlushViews');
        }

        Blade::directive('cache', function ($expression) {
            $version = explode('.', $this->app::VERSION);
            // Starting with laravel 5.3 the parens are not included in the expression string.
            if ($version[1] > 2) {
                return "<?php if (! app('JustusTheis\Matryoshka\BladeDirective')->setUp({$expression})) : ?>";
            }

            return "<?php if (! app('JustusTheis\Matryoshka\BladeDirective')->setUp{$expression}) : ?>";
        });

        Blade::directive('endcache', function () {
            return "<?php endif; echo app('JustusTheis\Matryoshka\BladeDirective')->tearDown() ?>";
        });
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton(BladeDirective::class);
    }
}

