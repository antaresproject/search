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
use Illuminate\Routing\Router;

$router->group(['prefix' => 'search'], function (Router $router) {
    $router->get('/', 'IndexController@search');
    $router->match(['GET', 'POST'], '/{category?}', 'IndexController@index');
});
