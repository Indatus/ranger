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
use Illuminate\Support\Facades\Config;
use Indatus\Ranger\ApiDatabase\ValidationErrorException;
use Indatus\Ranger\ApiDatabase\QueryExecution\CollectionQuery;

class CollectionQueryTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->requestContainer = m::mock('Indatus\Ranger\RequestDiagnostics\RequestContainer');
        $this->eagerLoadBuilder = m::mock('Indatus\Ranger\ApiDatabase\QueryBuilding\EagerLoads');
        $this->joinsBuilder = m::mock('Indatus\Ranger\ApiDatabase\QueryBuilding\Joins');
        $this->builder = m::mock('Illuminate\Database\Eloquent\Builder');
        $this->model = m::mock('Illuminate\Database\Eloquent\Model');
        $this->requestContainer->shouldReceive('getModel')->once()->andReturn($this->model);
    }


    public function tearDown()
    {
        m::close();
    }


    public function test_get_collection_on_a_non_nested_paginated_resource_get_request()
    {
        $apiQueryBuilders = [$this->joinsBuilder, $this->eagerLoadBuilder];
        
        $input = array(
                        'joins'      => [0 => 'transactions:users.id=transactions.user_id'],
                        'eagerLoads' => [0 => 'accounts'],
                        
                    );

        Config::shouldReceive('get')->once()->with('ranger::ranger.pagination.per_page')->andReturn(25);

        $this->requestContainer->shouldReceive('getInput')->twice()->andReturn($input);
        
        $this->joinsBuilder
             ->shouldReceive('buildQueryFromParameters')
             ->once()
             ->with($this->model, $input)
             ->andReturn($this->builder);

        $this->eagerLoadBuilder
             ->shouldReceive('buildQueryFromParameters')
             ->once()
             ->with($this->builder, $input)
             ->andReturn($this->builder);

        $this->requestContainer->shouldReceive('getParentAssociation')->once()->andReturn(null);

        //$this->builder->shouldReceive('get')->once()->andReturn($collection = m::mock('Illuminate\Database\Eloquent\Collection'));
        $this->builder->shouldReceive('paginate')->once()->andReturn($paginator = m::mock('Illuminate\Pagination\Paginator'));
        $paginator->shouldReceive('toArray')->once()->andReturn([]);

        (new CollectionQuery($apiQueryBuilders, $this->requestContainer))->executeQuery('get');

    }

    public function test_get_collection_on_a_nested_non_paginated_resource_get_request()
    {
        $apiQueryBuilders = [$this->joinsBuilder];
        
        $input = array('joins' => [0 => 'accounts:users.id=accounts.user_id']);

        Config::shouldReceive('get')->once()->with('ranger::ranger.pagination.per_page')->andReturn(null);

        $this->requestContainer->shouldReceive('getInput')->once()->andReturn($input);
        
        $this->joinsBuilder
             ->shouldReceive('buildQueryFromParameters')
             ->once()
             ->with($this->model, $input)
             ->andReturn($this->builder);

        
        $this->requestContainer->shouldReceive('getParentAssociation')->twice()->andReturn(['user_id' => 1]);

        $this->requestContainer
             ->shouldReceive('getTable')
             ->once()
             ->andReturn('accounts');

        $this->builder->shouldReceive('where')->once()->with('accounts.user_id', '=', 1)->andReturn($this->builder);
        $this->builder->shouldReceive('get')->once()->andReturn($collection = m::mock('Illuminate\Database\Eloquent\Builder'));
        
        $collection->shouldReceive('toArray')->once()->andReturn([]);
        
        (new CollectionQuery($apiQueryBuilders, $this->requestContainer))->executeQuery('get');

    }

    public function test_post_request_saves_properly()
    {
        $apiQueryBuilders = [];

        $input = array(
                        'name' => 'Charles',
                        'username' => 'cgriffin',
                        'email' => 'cgriffin@indatus.com',
                        'password' => 'password'
                    );

        $this->requestContainer->shouldReceive('getInput')->once()->andReturn($input);

        $this->model->shouldReceive('newInstance')->once()->with($input)->andReturn($this->model);

        $this->model->shouldReceive('save')->once()->andReturn(true);

        $this->model->shouldReceive('toArray')->once()->andReturn([]);

        (new CollectionQuery($apiQueryBuilders, $this->requestContainer))->executeQuery('post');
    }

    /**
     * @expectedException Indatus\Ranger\ApiDatabase\ValidationErrorException
     */
    public function test_post_request_with_validation_error_throws_exception()
    {
        $apiQueryBuilders = [];

        $input = array(
                        'name' => 'Charles',
                        'username' => 'cgriffin',
                        'email' => 'cgriffinindatus.com',
                        'password' => 'password'
                    );

        $this->requestContainer->shouldReceive('getInput')->once()->andReturn($input);

        $this->model->shouldReceive('newInstance')->once()->with($input)->andReturn($this->model);

        $this->model->shouldReceive('save')->once()->andReturn(false);

        (new CollectionQuery($apiQueryBuilders, $this->requestContainer))->executeQuery('post');
    }

    public function test_execute_query_throws_400_error_when_using_bogus_http_type()
    {
        $apiQueryBuilders = [];
        $error_array = (new CollectionQuery($apiQueryBuilders, $this->requestContainer))->executeQuery('bogus_method');
        
        $this->assertEquals($error_array, ['error' => "No such Http Request as a bogus_method method", 'response_code' => 400]);

    }

}
