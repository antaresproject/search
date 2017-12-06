<?php

/**
 * Part of the Antares package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Search
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Modules\Search;

use Antares\Foundation\Support\Providers\ModuleServiceProvider;
use Antares\Modules\Search\Composer\SearchPlaceholder;
use Antares\Modules\Search\Decorator\RowDecorator;
use Antares\Modules\Search\Response\QueryResponse;
use Antares\Extension\FilesystemFinder;
use Antares\Html\HtmlServiceProvider;

class SearchServiceProvider extends ModuleServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * The application or extension namespace.
     *
     * @var string|null
     */
    protected $namespace = 'Antares\Modules\Search\Http\Controllers\Admin';

    /**
     * The application or extension group namespace.
     *
     * @var string|null
     */
    protected $routeGroup = 'antares/search';

    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'antares.ready: admin' => SearchPlaceholder::class
    ];

    /**
     * Register service provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();
        $this->app->singleton('antares-search-row-decorator', function($app) {
            return new RowDecorator([
                'view'      => 'antares/search::admin.partials._row',
                'variables' => []], $app->make(FilesystemFinder::class));
        });
        $sp = new HtmlServiceProvider($this->app);
        $sp->register();
        $this->app->singleton('antares-search-response', function($app) {
            return new QueryResponse($app->make('request'));
        });
    }

    /**
     * Boot servicer provider
     */
    public function boot()
    {
        parent::boot();
        if (!$this->app->make('antares.installed')) {
            return;
        }
        if (app()->bound('antares-search-row-decorator')) {
            $this->app->make('antares-search-response')->boot();
        }
        $this->loadFrontendRoutesFrom(__DIR__ . '/routes.php');
        $this->loadBackendRoutesFrom(__DIR__ . '/routes.php');
    }

}
