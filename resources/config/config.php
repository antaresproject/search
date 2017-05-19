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



return [
    /**
     * default boost (priority)
     */
    'boost'             => 0.5,
    /**
     * cache name
     */
    'cache'             => 'anatares-search-models-cache',
    /**
     * protection string to validate search requests
     */
    'protection_string' => 'anatares search',
    /**
     * scripts available in search component
     */
    'scripts'           => [
        'resources' => [
            'search-js' => 'js/search.js'
        ]
    ],
    /**
     * search result row definition
     */
    'row'               => [
        'view'      => 'antares/search::admin.partials._row',
        'variables' => []
    ],
];

