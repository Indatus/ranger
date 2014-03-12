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

use Request;
use Illuminate\Container\Container;
use Indatus\Ranger\Factories\InvalidInputException;
use Indatus\Ranger\ApiDatabase\QueryBuilding\EagerLoads;
use Indatus\Ranger\ApiDatabase\QueryBuilding\Joins;
use Indatus\Ranger\ApiDatabase\QueryBuilding\LeftJoins;
use Indatus\Ranger\ApiDatabase\QueryBuilding\Searcher;

/**
 * Creates the different type of query builder objects
 * 
 */
class QueryBuilderFactory
{

    /**
     * Assembles the query builder objects based on the inputs
     * @param  array  $input
     * @return array of ApiQueryBuilder objects
     */
    public function make(array $input, Container $app)
    {
        $inputs = array_keys($input);
            
            $array_of_builder_types = [];
            
            //don't want put or post request to enter this
            if($this->_notEmptyNotPutOrNotPost($inputs)) {

                foreach($inputs as $input) {

                    if($input == 'eagerLoads') {

                        $array_of_builder_types[] = new EagerLoads;

                    } elseif($input == 'joins') {

                        $array_of_builder_types[] = new Joins;

                    } elseif($input == 'leftJoins') {

                        $array_of_builder_types[] = new LeftJoins;

                    } elseif($input == 'searchParams') {

                        $array_of_builder_types[] = new Searcher($app['config']);

                    } else {

                        throw new InvalidInputException;
                    }
                }

            }

        return $array_of_builder_types;

    }

    private function _notEmptyNotPutOrNotPost($inputs)
    {
        if( ! empty($inputs) && Request::server('REQUEST_METHOD') != 'PUT' && Request::server('REQUEST_METHOD') != 'POST') {

            return true;
        }

        return false;

    }
    
}