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
use Indatus\Ranger\ApiDatabase\QueryBuilding\Searcher;

/**
 * This set of tests are for testing all the core classes that extend the abstract ApiQueryBuilder
 * class.
 * 
 */
class SearcherTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->builder = m::mock('Illuminate\Database\Eloquent\Builder');
        $this->_constructSearcher();
    }


    public function tearDown()
    {
        m::close();
    }


    public function test_searching_with_like_operator_key()
    {
        $input = ['searchParams' => array(
                                            'property' => 'name', 
                                            'operator' => 'like',
                                            'value' => '%Charl%'
                                        )];

        $this->builder->shouldReceive('where')->once()->with('name', 'like', '%Charl%')->andReturn($this->builder);
        
        $builder = (new Searcher($this->config))->buildQueryFromParameters($this->builder, $input);

        $this->assertTrue($builder instanceof Illuminate\Database\Eloquent\Builder);
    }


    /**
     * @expectedException Exception
     */
    public function test_exception_gets_thrown_if_builder_is_wrong_type()
    {
    
        $input = ['searchParams' => array(
                                            'property' => 'name', 
                                            'operator' => 'like',
                                            'value' => '%Charl%'
                                        )];

        //pass in a bogus class
        $this->builder = new StdClass;
        
        $builder = (new Searcher($this->config))->buildQueryFromParameters($this->builder, $input);
    
    }


    private function _constructSearcher()
    {
        $this->config = m::mock('Illuminate\Config\Repository');

        $this->config->shouldReceive('get')->once()->with('ranger::ranger.std_search.search_property_key')->andReturn('property');
        $this->config->shouldReceive('get')->once()->with('ranger::ranger.std_search.search_operator_key')->andReturn('operator');
        $this->config->shouldReceive('get')->once()->with('ranger::ranger.std_search.search_value_key')->andReturn('value');
    }
    
}