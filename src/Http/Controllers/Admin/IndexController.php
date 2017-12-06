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

namespace Antares\Modules\Search\Http\Controllers\Admin;

use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Modules\Search\Processor\Search as Processor;
use Illuminate\Support\Facades\Input;

class IndexController extends AdminController
{

    /**
     * Processor instance
     *
     * @var Processor 
     */
    protected $processor = null;

    /**
     * implments instance of controller
     * 
     * @param Processor $processor
     */
    public function __construct(Processor $processor)
    {
        parent::__construct();
        $this->processor = $processor;
    }

    /**
     * route acl access controlling
     */
    public function setupMiddleware()
    {
        $this->middleware('antares.auth');
        $this->middleware('antares.can:antares/search::search', ['only' => ['index'],]);
    }

    /**
     * Default action
     * 
     * @param String $category
     * @return \Illuminate\View\View
     */
    public function index($category = null)
    {
        return $this->processor->index(Input::all(), $category);
    }

    /**
     * Quick search action
     * 
     * @return \Illuminate\View\View
     */
    public function search()
    {
        return event('quick-search');
    }

}
