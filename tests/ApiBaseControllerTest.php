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

use Illuminate\Support\Facades\App;
use Mockery as m;
use Indatus\Ranger\ApiBaseController;

/**
 * This set of tests are for testing all the core classes that extend the abstract ApiQueryBuilder
 * class.
 * 
 */
class ApiBaseControllerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
    	$this->user = m::mock('Illuminate\Database\Eloquent\Model');
    	$this->http_method = m::mock('Indatus\Ranger\HttpRequestHandler\HttpMethod');
    }

    public function tearDown()
    {
        m::close();
    }

    public function test_handle_action()
    {
    	$params = [ 'model' => $this->user, 
                    'id' => null, 
                    'parent_data' => [], 
                    'additional_assigns' => [],
                    'belongsTo' => null,
                    ];

    	App::shouldReceive('make')->once()->with('http_method', $params)->andReturn($this->http_method);
    	$this->http_method->shouldReceive('handleRequest')->andReturn('json string');

    	//since api base controller is protected, we have to use reflection magic
    	$reflector = new ReflectionClass(new ApiBaseController);
    	$handleAction = $reflector->getMethod('handleAction');
    	$handleAction->setAccessible(true);
        $handleAction->invoke(new ApiBaseController, $this->user);
    	
    }
    
}