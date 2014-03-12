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
use Indatus\Ranger\ApiDatabase\ValidationErrorException;
use Indatus\Ranger\ApiDatabase\QueryExecution\InstanceQuery;

class InstanceQueryTest extends PHPUnit_Framework_TestCase
{
    use QueryExecuterHelper;

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


    public function test_retreive_instance_from_get_request_non_nested_resource()
    {
        $apiQueryBuilders = [$this->joinsBuilder, $this->eagerLoadBuilder];
        
        $input = array(
                        'joins'      => [0 => 'transactions:users.id=transactions.user_id'],
                        'eagerLoads' => [0 => 'accounts'],
                    );

        $this->requestContainer->shouldReceive('getParentAssociation')->once()->andReturn(null);
        $this->requestContainer->shouldReceive('getInput')->twice()->andReturn($input);
        $this->requestContainer->shouldReceive('getTable')->once()->andReturn('users');
        $this->requestContainer->shouldReceive('getPrimaryKey')->once()->andReturn('id');
        $this->requestContainer->shouldReceive('getId')->once()->andReturn(1);
        
        $this->_apiQueryBuilderAssertions($apiQueryBuilders, $input);

        $instance_mock = m::mock('Illuminate\Database\Eloquent\Builder');
        $instance = $this->_nonNestedBuilderInstanceAssertions('users.id', 1, true, $instance_mock);

        $instance->shouldReceive('toArray')->once()->andReturn([]);

        (new InstanceQuery($apiQueryBuilders, $this->requestContainer))->executeQuery('get');

        $this->assertTrue($instance instanceof Illuminate\Database\Eloquent\Builder);

    }


    public function test_retreive_instance_from_get_request_nested_resource()
    {
        $apiQueryBuilders = ['join' => $this->joinsBuilder];
        
        $input = array('joins' => [0 => 'accounts:users.id=accounts.user_id']);

        $this->requestContainer->shouldReceive('getParentAssociation')->twice()->andReturn(['user_id' => 2]);
        $this->requestContainer->shouldReceive('getInput')->once()->andReturn($input);
        $this->requestContainer->shouldReceive('getTable')->once()->andReturn('accounts');
        $this->requestContainer->shouldReceive('getPrimaryKey')->once()->andReturn('id');
        $this->requestContainer->shouldReceive('getId')->once()->andReturn(1);
        
        $this->_apiQueryBuilderAssertions($apiQueryBuilders, $input);
        
        $instance = $this->_nestedBuilderInstanceAssertions('accounts.id', 1, 'accounts.user_id', 2);

        $instance->shouldReceive('toArray')->once()->andReturn([]);

        (new InstanceQuery($apiQueryBuilders, $this->requestContainer))->executeQuery('get');

        $this->assertTrue($instance instanceof Illuminate\Database\Eloquent\Builder);

    }

    public function test_delete_on_non_nested_instance()
    {
        $apiQueryBuilders = [];

        $this->requestContainer->shouldReceive('getParentAssociation')->once()->andReturn(null);
        
        $this->requestContainer->shouldReceive('getTable')->once()->andReturn('accounts');
        $this->requestContainer->shouldReceive('getPrimaryKey')->once()->andReturn('id');
        $this->requestContainer->shouldReceive('getId')->once()->andReturn(1);

        $instance_mock =  m::mock('Illuminate\Database\Eloquent\Builder');
        $instance = $this->_nonNestedBuilderInstanceAssertions('accounts.id', 1, false, $instance_mock);

        $instance->shouldReceive('delete')->once()->andReturn(true);

        (new InstanceQuery($apiQueryBuilders, $this->requestContainer))->executeQuery('delete');

        $this->assertTrue($instance instanceof Illuminate\Database\Eloquent\Builder);
    }

    public function test_delete_on_nested_instance()
    {
        $apiQueryBuilders = [];

        $this->requestContainer->shouldReceive('getParentAssociation')->twice()->andReturn(['user_id' => 2]);
        
        $this->requestContainer->shouldReceive('getTable')->once()->andReturn('accounts');
        $this->requestContainer->shouldReceive('getPrimaryKey')->once()->andReturn('id');
        $this->requestContainer->shouldReceive('getId')->once()->andReturn(1);

        $instance = $this->_nestedBuilderInstanceAssertions('accounts.id', 1, 'accounts.user_id', 2, false);

        $instance->shouldReceive('delete')->once()->andReturn(true);

        (new InstanceQuery($apiQueryBuilders, $this->requestContainer))->executeQuery('delete');

        $this->assertTrue($instance instanceof Illuminate\Database\Eloquent\Model);

    }

    
    public function test_put_on_non_nested_instance()
    {
        $apiQueryBuilders = [];
        
        //updated information
        $input = array(
                'name' => 'Charles',
                'username' => 'cgriffin',
                'email' => 'cgriffin@indatus.com',
                'password' => 'password'
            );

        $this->requestContainer->shouldReceive('getInput')->once()->andReturn($input);
        $this->requestContainer->shouldReceive('getTable')->once()->andReturn('users');
        $this->requestContainer->shouldReceive('getPrimaryKey')->once()->andReturn('id');
        $this->requestContainer->shouldReceive('getId')->once()->andReturn(1);
        $this->requestContainer->shouldReceive('getParentAssociation')->once()->andReturn(null);

        $instance_mock = m::mock('Illuminate\Database\Eloquent\Builder');
        $instance = $this->_nonNestedBuilderInstanceAssertions('users.id', 1, false, $instance_mock);

        $instance->shouldReceive('fill')->once()->with($input);
        $instance->shouldReceive('save')->once()->andReturn(true);

        $instance->shouldReceive('toArray')->once()->andReturn([]);

        (new InstanceQuery($apiQueryBuilders, $this->requestContainer))->executeQuery('put');

        $this->assertTrue($instance instanceof Illuminate\Database\Eloquent\Builder);

    }

    public function test_put_on_nested_instance()
    {
        $apiQueryBuilders = [];
        
        //updated information
        $input = array(
                'name' => 'Charles',
                'username' => 'cgriffin',
                'email' => 'cgriffin@indatus.com',
                'password' => 'password'
            );

        $this->requestContainer->shouldReceive('getParentAssociation')->twice()->andReturn(['user_id' => 2]);
        $this->requestContainer->shouldReceive('getInput')->once()->andReturn($input);
        $this->requestContainer->shouldReceive('getTable')->once()->andReturn('accounts');
        $this->requestContainer->shouldReceive('getPrimaryKey')->once()->andReturn('id');
        $this->requestContainer->shouldReceive('getId')->once()->andReturn(1);

        $instance = $this->_nestedBuilderInstanceAssertions('accounts.id', 1, 'accounts.user_id', 2, false);

        $instance->shouldReceive('fill')->once()->with($input);
        $instance->shouldReceive('save')->once()->andReturn(true);

        $instance->shouldReceive('toArray')->once()->andReturn([]);

        (new InstanceQuery($apiQueryBuilders, $this->requestContainer))->executeQuery('put');

        $this->assertTrue($instance instanceof Illuminate\Database\Eloquent\Model);

    }

    /**
     * @expectedException Indatus\Ranger\ApiDatabase\ValidationErrorException
     */
    public function test_failing_put_request_throws_validation_exception()
    {
        $apiQueryBuilders = [];
        
        //updated information
        $input = array(
                'name' => 'Charles',
                'username' => 'cgriffin',
                'email' => 'cgriffinindatus.com',
                'password' => 'password'
            );

        $this->requestContainer->shouldReceive('getInput')->once()->andReturn($input);
        $this->requestContainer->shouldReceive('getTable')->once()->andReturn('users');
        $this->requestContainer->shouldReceive('getPrimaryKey')->once()->andReturn('id');
        $this->requestContainer->shouldReceive('getId')->once()->andReturn(1);
        $this->requestContainer->shouldReceive('getParentAssociation')->once()->andReturn(null);

        $instance_mock = m::mock('Illuminate\Database\Eloquent\Builder');
        $instance = $this->_nonNestedBuilderInstanceAssertions('users.id', 1, false, $instance_mock);

        $instance->shouldReceive('fill')->once()->with($input);
        $instance->shouldReceive('save')->once()->andReturn(false);
        (new InstanceQuery($apiQueryBuilders, $this->requestContainer))->executeQuery('put');

    }

    /**
     * @expectedException Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function test_null_instance_throws_model_not_found_exception()
    {
        $apiQueryBuilders = [];
        
        $this->requestContainer->shouldReceive('getTable')->once()->andReturn('users');
        $this->requestContainer->shouldReceive('getPrimaryKey')->once()->andReturn('id');
        $this->requestContainer->shouldReceive('getId')->once()->andReturn(1);
        $this->requestContainer->shouldReceive('getParentAssociation')->once()->andReturn(null);

        $instance = $this->_nonNestedBuilderInstanceAssertions('users.id', 1, false, null);

        (new InstanceQuery($apiQueryBuilders, $this->requestContainer))->executeQuery('put');
    }

    public function test_call_magic_method_throws_400_error_when_using_bogus_http_type()
    {
        $apiQueryBuilders = [];
        $error_array = (new InstanceQuery($apiQueryBuilders, $this->requestContainer))->executeQuery('bogus_method');
        
        $this->assertEquals($error_array, ['error' => "No such Http Request as a bogus_method method", 'response_code' => 400]);

    }
}
