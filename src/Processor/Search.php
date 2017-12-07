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

namespace Antares\Modules\Search\Processor;

use Antares\Foundation\Processor\Processor;
use Antares\Modules\Search\Http\Breadcrumb;

class Search extends Processor
{

    /**
     * Breadcrumb instance
     *
     * @var Breadcrumb 
     */
    protected $breadcrumb;

    /**
     * Constructor
     * 
     * @param Breadcrumb $breadcrumb
     */
    public function __construct(Breadcrumb $breadcrumb)
    {
        $this->breadcrumb = $breadcrumb;
    }

    /**
     * Default index action
     * 
     * @param array $input
     * @param String $category
     * @return \Illuminate\View\View
     */
    public function index(array $input = array(), $category = null)
    {
        $query = e(array_get($input, 'search.value', null));

        $this->breadcrumb->onIndex();
        $datatables = config('search.datatables');
        $tabs       = [];

        foreach ($datatables as $datatable) {

            if (!class_exists($datatable)) {
                continue;
            }
            $datatable                   = app($datatable);
            $row                         = $datatable->getQuickSearchRow();
            $category                    = array_get($row, 'category');
            $tabs[camel_case($category)] = [
                'title'     => $category,
                'datatable' => $datatable->render('antares/search::admin.partials._datatable')->render(),
            ];
        }
        return view('antares/search::admin.index.index', compact('tabs'));
    }

    /**
     * When search query is invalid
     * 
     * @param String $message
     * @return \Illuminate\View\View
     */
    public function error($message)
    {
        $this->breadcrumb->onIndex();
        swal('error', $message);
        return view('antares/search::admin.index.index', ['message' => $message]);
    }

}
