<?php

/**
 * This file is part of Ranger
 * 
 * @package   Ranger
 * @author    Charles Griffin <cgriffin@indatus.com>
 * @author    Brian Webb <bwebb@indatus.com>
 * @license   For the full copyright and license information, please view the LICENSE
 *            file that was distributed with this source code.
 */

use Mockery as m;
use Indatus\Ranger\HttpRequestHandler\HttpGet;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class HttpGetTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->responseHandler = m::mock('Indatus\Ranger\ApiDatabase\ResponseHandler');
        $this->contentType = m::mock('Indatus\Ranger\ContentType\ContentRendererInterface');
        $this->queryExecuter = m::mock('Indatus\Ranger\ApiDatabase\QueryExecution\QueryExecuter');
        
    }


    public function tearDown()
    {
        m::close();
    }

    public function test_handle_successful_get_request()
    {
        //queryExecuter will return the toArray version of an Eloquent Model or Builder
        $this->queryExecuter->shouldReceive('executeQuery')->with('get')->andReturn(['Eloquent_object_toArray']);

        $this->responseHandler
             ->shouldReceive('success')
             ->once()
             ->with(['Eloquent_object_toArray'])
             ->andReturn(['Eloquent_object_toArray', 'response_code' => 200]);

        $this->contentType
             ->shouldReceive('render')
             ->once()
             ->with(['Eloquent_object_toArray', 'response_code' => 200])
             ->andReturn(json_encode(['Eloquent_object_toArray', 'response_code' => 200]));

        (new HttpGet($this->queryExecuter, $this->contentType, $this->responseHandler))->handleRequest();

    }


    public function test_handle_unsuccessful_get_request()
    {
        $this->responseHandler
             ->shouldReceive('error')
             ->once()
             ->andReturn(['Eloquent_object_toArray', 'response_code' => 404]);
        
        //queryExecuter will return the toArray version of an Eloquent Model or Builder
        $this->queryExecuter->shouldReceive('executeQuery')->with('get')->andThrow(new ModelNotFoundException);

        (new HttpGet($this->queryExecuter, $this->contentType, $this->responseHandler))->handleRequest();

    }

}