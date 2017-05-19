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

namespace Antares\Modules\Search\Decorator;

use Exception;

class RowDecorator
{

    /**
     * Data container
     *
     * @var array
     */
    protected $datatable;

    /**
     * constructing
     * 
     * @param array $config
     * @throws Exception
     */
    public function __construct(array $config)
    {
        if (!isset($config['view'])) {
            throw new Exception('Default row view cannot be empty.');
        }
        $this->view      = array_get($config, 'view');
        $this->variables = array_get($config, 'variables', []);
    }

    /**
     * Data setter
     * 
     * @param array $data
     * @return $this
     */
    public function setDatatable($datatable)
    {
        $this->datatable = $datatable;
        return $this;
    }

    /**
     * decorated row getter
     * 
     * @return String
     */
    public function getRows()
    {
        $data = $this->datatable->setPerPage(3)->ajax()->original;
        $rows = [];
        foreach ($data['data'] as $row) {
            $decorated = array_merge($this->datatable->getQuickSearchRow($row), ['total' => $data['recordsFiltered']]);
            $rows[]    = $decorated;
        }
        return $rows;
    }

}
