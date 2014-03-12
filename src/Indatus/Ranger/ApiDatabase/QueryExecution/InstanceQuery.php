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

use InvalidArgumentException;
use Indatus\Ranger\ApiDatabase\ValidationErrorException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Indatus\Ranger\ApiDatabase\QueryExecution\QueryExecuter;

/**
 * 
 * This class executes the queries on the builder instance returned by the class that
 * Implements the ApiQueryAssemblerInterface.  The instance being returned will be one
 * of the Laravel database eloquent classes (QueryBuilder or Model)
 *
 */
class InstanceQuery extends QueryExecuter
{
    const BAD_REQUEST = 400;

    public function executeQuery($request_type)
    {
        return $this->$request_type();
        
    }//end getBaseCollection

    /**
     * @return array
     * @codeCoverageIgnore
     */
    protected function get()
    {
        $instance = $this->getInstance();

        return array_fill_keys(['instance'], $instance->toArray());
  
    }

    /**
     * @return array
     * @codeCoverageIgnore
     */
    protected function delete()
    {
        $instance = $this->getInstance();
        $instance->delete();
        
        return ['message' => 'deletion successful'];

    }

    /**
     * @return array
     * @throws Indatus\Ranger\ApiDatabase\ValidationErrorException
     * 
     * @codeCoverageIgnore
     */
    protected function put()
    {
        $instance = $this->getInstance();
        $input = $this->requestContainer->getInput();
        
        //we want to filling the instance with deleted_at because it causes
        //problems using laravel's Eloquent ORM
        $instance->fill(array_except($input, ['_method', 'deleted_at']));
        
        if( ! $instance->save()) {

            throw new ValidationErrorException;
            
        }

        return array_fill_keys(['instance'], $instance->toArray());
    }

    /**
     * Function to return a resource instance.  The function will
     * handle wether or not the resource is nested and act appropriatley.
     * 
     * getInstance - The ApiQueryAssemblerInterface will build the query from
     * any api parameters that were passed in.  Eager loaded relations
     * joins, and left joins will be appended to the query builder
     * and returned by the buildQueryFromApiParams() method. The
     * returned value will then call the first() method which will return
     * an Eloquent model instance object.
     * 
     * @throws ModelNotFoundException If $instance is null 
     * @return array
     * @codeCoverageIgnore
     */
    protected function getInstance()
    {
        //Using polymorphism to loop through the apiQueryBuilder objects
        //ie) Joins, LeftJoins, etc. and call buildQueryFromParameters()
        //on each of those objects
        foreach($this->apiQueryBuilders as $queryBuilder) {
            
            $this->builder = $queryBuilder->buildQueryFromParameters($this->builder, $this->requestContainer->getInput());
        }

        $table = $this->requestContainer->getTable();

        //must run primary key after running handleApiParams because
        //any joins modifys the primaryKey to avoid ambiguity error
        $primaryKey = $table.'.'.$this->requestContainer->getPrimaryKey();

        $id = $this->requestContainer->getId();

        if($this->isNestedResource()) {

            $instance = $this->_buildNestedResource($table, $primaryKey, $id);

        } else {

            $instance = $this->builder->where($primaryKey, '=', $id)->first();

        }

        if(! $instance) {

            throw new ModelNotFoundException;
        }

        return $instance;
    }


    /**
     * _buildNestedResource - performs query on nested resource instance
     * @param string $table
     * @param int $primaryKey
     * @param int $id
     * @return Illuminate\Database\Eloquent\Model | Illuminate\Database\Eloquent\Builder
     * @codeCoverageIgnore
     */
    private function _buildNestedResource($table, $primaryKey, $id)
    {

        $parentAssociation = $this->requestContainer->getParentAssociation();

        $instance = $this->builder
                         ->where($primaryKey, '=' ,$id)
                         ->where($table.'.'.head(array_keys($parentAssociation)), '=', head($parentAssociation))
                         ->first();

        return $instance;
    }


    public function __call($method, $arguments)
    {
        return ['error' => "No such Http Request as a $method method", 'response_code' => self::BAD_REQUEST];

    }
}
