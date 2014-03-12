<?php

/**
 * 
 * This file is part of Ranger
 * 
 * @package  Ranger
 * @author   Brian Webb <bwebb@indatus.com>
 * @author   Charles Griffin <cgriffin@indatus.com>
 * @license  For the full copyright and license information, please view the LICENSE
 *           file that was distributed with this source code.
 */

namespace Indatus\Ranger\Factories;

use Illuminate\Container\Container;
use Indatus\Ranger\ApiDatabase\QueryExecution\InstanceQuery;
use Indatus\Ranger\ApiDatabase\QueryExecution\CollectionQuery;
use Indatus\Ranger\RequestDiagnostics\RequestContainer;

/**
 * Creates a query executer object.  The two types are instances and collections
 * 
 */
class QueryExecuterFactory
{
    public function make(RequestContainer $requestContainer, Container $app)
    {
        if($requestContainer->getId()) {

            return new InstanceQuery($app->make('query_builders'), $requestContainer);

        }

        return new CollectionQuery($app->make('query_builders'), $requestContainer);
    }
}