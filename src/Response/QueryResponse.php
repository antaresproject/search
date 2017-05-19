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

namespace Antares\Modules\Search\Response;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ReflectionClass;
use Exception;

class QueryResponse
{

    /**
     * Request instance
     *
     * @var Request
     */
    protected $request;

    /**
     * constructing
     * 
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * validates submitted data from search form
     * 
     * @return boolean
     */
    protected function isValid()
    {
        if (!$this->request->ajax()) {
            return false;
        }
        $search = $this->request->input('search');
        if (!is_string($search) or strlen($this->request->input('search')) <= 0) {
            return false;
        }
        $token = $this->request->header('search-protection');
        if (!$this->validateToken($token)) {
            return false;
        }
        return true;
    }

    /**
     * validates protection token
     * 
     * @param String $token
     * @return boolean
     */
    private function validateToken($token = null)
    {

        if (is_null($token)) {
            return false;
        }
        $decrypted = Crypt::decrypt($token);
        $args      = unserialize($decrypted);
        if (!isset($args['protection_string']) or $args['protection_string'] !== config('antares/search::protection_string')) {
            return false;
        }
        if (!isset($args['app_key']) or $args['app_key'] !== env('APP_KEY')) {
            return false;
        }
        return true;
    }

    /**
     * Boots search query in lucene indexes
     * 
     * @return boolean
     */
    public function boot()
    {
        if (!$this->isValid()) {
            return false;
        }
        $serviceProvider = new \Antares\Customfields\CustomFieldsServiceProvider(app());
        $serviceProvider->register();
        $serviceProvider->boot();
        $query           = e($this->request->input('search'));
        $cacheKey        = 'search_' . snake_case($query);
        $formated        = [];
        try {
            //$formated = Cache::remember($cacheKey, 5, function() use($query) {
            $datatables = config('search.datatables', []);
            foreach ($datatables as $classname) {
                $datatable = $this->getDatatableInstance($classname);
                if (!$datatable) {
                    continue;
                }
                request()->merge(['inline_search' => ['value' => $query, 'regex' => false]]);
                $formated = array_merge($formated, app('antares-search-row-decorator')->setDatatable($datatable)->getRows());
            }

            if (empty($formated)) {
                $formated[] = [
                    'content'  => '<div class="type--datarow"><div class="datarow__left"><span>No results found...</span></div></div>',
                    'url'      => '#',
                    'category' => '',
                    'total'    => 0
                ];
            }

            $jsonResponse = new JsonResponse($formated, 200);
        } catch (Exception $e) {
            $jsonResponse = new JsonResponse(['message' => $e->getMessage()], 500);
        }
        $jsonResponse->send();
        return exit();
    }

    /**
     * Gets instance of datatable
     * 
     * @param String $classname
     * @return boolean
     */
    protected function getDatatableInstance($classname)
    {
        if (!class_exists($classname)) {
            return false;
        }
        $datatable  = app($classname);
        $reflection = new ReflectionClass($datatable);
        if (($filename   = $reflection->getFileName()) && !str_contains($filename, 'core')) {
            if (!app('antares.extension')->getActiveExtensionByPath($filename)) {
                return false;
            }
        }
        return $datatable;
    }

}
