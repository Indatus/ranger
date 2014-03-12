<?php

/**
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

/**
 * Creates either a put, post, delete, or get http_method
 * 
 */
class HttpMethodFactory
{

    public function make(array $params, Container $app, $request_type)
    {
        
        $class = 'Indatus\Ranger\HttpRequestHandler\Http'.$request_type;

        return new $class($app->make('query_executer', $params), 
                          $app->make('content_type', $params),
                          $app->make('response_handler'));
    }

    
}