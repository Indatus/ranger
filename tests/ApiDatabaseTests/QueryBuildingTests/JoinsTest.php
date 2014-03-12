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
use Indatus\Ranger\ApiDatabase\QueryBuilding\Joins;

/**
 * This set of tests are for testing all the core classes that extend the abstract ApiQueryBuilder
 * class.
 * 
 */
class JoinsTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->builder = m::mock('Illuminate\Database\Eloquent\Builder');
    }

    public function tearDown()
    {
        m::close();
    }

    public function test_single_join()
    {
        $input = ['joins' => array(0 => 'accounts:users.id=accounts.user_id')];

        $this->builder->shouldReceive('join')->once()->with('accounts', 'users.id', '=', 'accounts.user_id')->andReturn($this->builder);
        
        $builder = (new Joins)->buildQueryFromParameters($this->builder, $input);

        $this->assertTrue($builder instanceof Illuminate\Database\Eloquent\Builder);
    }

    public function test_mutliple_joins()
    {
        $input = ['joins' => array(0 => 'accounts:users.id=accounts.user_id',
                                   1 => 'transactions:accounts.id=transactions.account_id')];

        $this->builder->shouldReceive('join')->once()->with('accounts', 'users.id', '=', 'accounts.user_id')->andReturn($this->builder);
        $this->builder->shouldReceive('join')->once()->with('transactions', 'accounts.id', '=', 'transactions.account_id')->andReturn($this->builder);

        $builder = (new Joins)->buildQueryFromParameters($this->builder, $input);

        $this->assertTrue($builder instanceof Illuminate\Database\Eloquent\Builder);
    }

    /**
     * @expectedException Exception
     */
    public function test_join_load_method_called_but_passed_a_builder_that_isnt_eloquent_friendly()
    {
        $input = ['joins' => array(0 => 'accounts:users.id=accounts.user_id')];

        //pass in a bogus class
        $this->builder = new StdClass;
        
        $builder = (new Joins)->buildQueryFromParameters($this->builder, $input);
    }
    
}