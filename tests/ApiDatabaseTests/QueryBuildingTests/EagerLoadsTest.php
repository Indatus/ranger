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
use Indatus\Ranger\ApiDatabase\QueryBuilding\EagerLoads;

/**
 * This set of tests are for testing all the core classes that extend the abstract ApiQueryBuilder
 * class.
 * 
 */
class EagerLoadsTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->builder = m::mock('Illuminate\Database\Eloquent\Builder');
    }

    public function tearDown()
    {
        m::close();
    }

    public function test_eager_load_single_assocation_loads_relations()
    {
        $input = ['eagerLoads' => array(0 => 'accounts')];

        $this->builder->shouldReceive('with')->once()->with('accounts')->andReturn($this->builder);
        
        $builder = (new EagerLoads)->buildQueryFromParameters($this->builder, $input);

        $this->assertTrue($builder instanceof Illuminate\Database\Eloquent\Builder);
    }

    public function test_eager_load_multiple_assocations_loads_relations()
    {
        $input = ['eagerLoads' => array(0 => 'accounts', 1 => 'transactions')];

        $this->builder->shouldReceive('with')->once()->with('accounts')->andReturn($this->builder);
        $this->builder->shouldReceive('with')->once()->with('transactions')->andReturn($this->builder);
        
        $builder = (new EagerLoads)->buildQueryFromParameters($this->builder, $input);

        $this->assertTrue($builder instanceof Illuminate\Database\Eloquent\Builder);
    }

    /**
     * @expectedException Exception
     */
    public function test_eager_load_method_called_but_passed_a_builder_that_isnt_eloquent_friendly()
    {
        $input = ['eagerLoads' => array(0 => 'accounts')];

        $this->builder = new StdClass;
        
        $builder = (new EagerLoads)->buildQueryFromParameters($this->builder, $input);
    }
    
  
}