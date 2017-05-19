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

namespace Antares\Modules\Search\Composer;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Input;
use function view;

class SearchPlaceholder
{

    /**
     * @var Application 
     */
    protected $app;

    /**
     * constructing
     * 
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * up component search placeholder
     */
    public function handle()
    {
        if (!auth()->user()) {
            return false;
        }
        publish('search', 'scripts.resources');

        $search           = Input::get('search');
        $string           = serialize([
            'protection_string' => config('antares/search::protection_string'),
            'app_key'           => env('APP_KEY'),
            'time'              => time()]);
        $protectionString = Crypt::encrypt($string);
        $this->app->make('antares.widget')
                ->make('placeholder.global_search')
                ->add('search')
                ->value(view('antares/search::admin.partials._search', compact('search', 'protectionString')));
    }

}
