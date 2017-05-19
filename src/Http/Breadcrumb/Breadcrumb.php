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

namespace Antares\Modules\Search\Http;

use DaveJamesMiller\Breadcrumbs\Facade as Breadcrumbs;

class Breadcrumb
{

    /**
     * on search
     */
    public function onIndex()
    {
        Breadcrumbs::register('search', function($breadcrumbs) {
            $query = e(request()->get('search'));
            $breadcrumbs->push(trans('antares/search::messages.breadcrumb_search', ['query' => $query]), '#');
        });
        view()->share('breadcrumbs', Breadcrumbs::render('search'));
    }

}
