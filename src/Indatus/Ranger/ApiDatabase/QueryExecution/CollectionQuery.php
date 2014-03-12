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

namespace Indatus\Ranger\ApiDatabase\QueryExecution;

use Illuminate\Support\Facades\Config;
use Indatus\Ranger\ApiDatabase\ValidationErrorException;
use Indatus\Ranger\ApiDatabase\QueryExecution\QueryExecuter;

/**
 * TODO - add the ability to make pagination optional
 * 
 * This class executes the queries on the builder collection returned by the class that
 * Implements the ApiQueryAssemblerInterface.  The collection being returned will be one
 * of the Laravel database eloquent classes (QueryBuilder or Model)
 * 
 */

class CollectionQuery extends QueryExecuter
{

    const BAD_REQUEST = 400;


    public function executeQuery($request_type)
    {
        
        if($request_type == 'get') {

            $per_page = Config::get('ranger::ranger.pagination.per_page');

            return $this->get($per_page);

        } else if($request_type == 'post') {

            return $this->post();

        } else {

            return ['error' => "No such Http Request as a $request_type method", 'response_code' => self::BAD_REQUEST];

        }

    }//end getBaseCollection

    /**
     * runs a query on the get request results
     * @param  int $per_page - pagination setting
     * @return array formatted results
     * @codeCoverageIgnore
     * 
     */
    protected function get($per_page)
    {

        $collection_array = $per_page ? $this->getCollection()->paginate($per_page)->toArray() : $this->getCollection()->get()->toArray();

        return array_fill_keys(['collection'], $collection_array);

    }


    /**
     * In collection because I want to add an instance to a collection
     * This could go to the InstanceQuery class instead
     * @codeCoverageIgnore
     */
    protected function post()
    {
        
        $input = $this->requestContainer->getInput();

        $instance = $this->builder->newInstance($input);
        
        if(! $instance->save()) {

            throw new ValidationErrorException;

        }

        return array_fill_keys(['instance'], $instance->toArray());
    }

    /**
     * getCollection - first this method loops through an array of objects that are
     * sub-classes of the Indatus\Ranger\ApiDatabase\QueryBuilding\ApiQueryBuilder
     * and runs the buildQueryFromParameters() method on each of them.
     * 
     * Then if the collection is called from a nested resource, it will do some
     * additional formatting.  Else, it will just return the builder.
     * 
     * 
     * @return Illuminate\Database\Eloquent\Model | Illuminate\Database\Eloquent\Builder
     * @codeCoverageIgnore
     */
    protected function getCollection()
    {
        //Using polymorphism to loop through the apiQueryBuilder objects
        //ie) Joins, LeftJoins, etc. and call buildQueryFromParameters()
        //on each of those objects
        foreach($this->apiQueryBuilders as $queryBuilder) {

            $this->builder = $queryBuilder->buildQueryFromParameters($this->builder, $this->requestContainer->getInput());
        }
        
        //think about moving nested Resource in the apiQueryBuilders array of objects
        if( ! $this->isNestedResource()) {
            
            return $this->builder;
        }

        return $this->_buildNestedResource();
    }


    /**
     * _buildNestedResource handles nested resource by formatting it
     * @return Illuminate\Database\Eloquent\Model | Illuminate\Database\Eloquent\Builder
     * @codeCoverageIgnore
     */
    private function _buildNestedResource()
    {

        $table = $this->requestContainer->getTable();

        $parentAssociation = $this->requestContainer->getParentAssociation();

        $this->builder = $this->builder
                              ->where($table.'.'.head(array_keys($parentAssociation)), '=', head($parentAssociation));

        return $this->builder;
    }

}