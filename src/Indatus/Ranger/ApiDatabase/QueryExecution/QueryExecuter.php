<?php

/**
 * This file is part of Ranger
 *
 * @package  Ranger
 * @author   Brian Webb <bwebb@indatus.com>
 * @author   Charles Griffin <cgriffin@indatus.com>
 */

namespace Indatus\Ranger\ApiDatabase\QueryExecution;

use Indatus\Ranger\ApiDatabase\QueryBuilding\ApiQueryBuilderInterface;
use Indatus\Ranger\RequestDiagnostics\RequestContainer;
use Illuminate\Database\Eloquent\Model;


/**
 * Super class for executing queries.  It builds the queries via delegation before
 * actually executing the query
 */

abstract class QueryExecuter
{

    /**
     * Holds an array of of objects that implement the ApiQueryBuilderInterface.
     * We will loop through these objects and call buildQueryFromParameters()
     * on each of the objects
     * 
     * ApiQueryBuilderInterface[] $apiQueryBuilderInterface
     * @var array
     */
    protected $apiQueryBuilders;


    /**
     * $builder - contains the eloquent builder instance to build queries on.
     * 
     * @var Illuminate\Database\Eloquent\Builder | Illuminate\Database\Eloquent\Model
     */
    protected $builder;


    /**
     * $requestContainer - contains all kinds of request diagnostics to make decisions on.
     * 
     * @var Indatus\Ranger\RequestDiagnostics\RequestContainer
     */
    protected $requestContainer;


    /**
     * @param array            $apiQueryBuilders
     * @param RequestContainer $requestContainer
     * @codeCoverageIgnore
     */
    public function __construct(array $apiQueryBuilders, RequestContainer $requestContainer)
    {
        $this->apiQueryBuilders = $apiQueryBuilders;

        $this->builder = $requestContainer->getModel();
        
        $this->requestContainer = $requestContainer;
    }


    /**
     * executeQuery executes either a collection or instance
     * based on the request
     *
     * @param string $requestType
     * @return array
     */
    public abstract function executeQuery($request_type);


    /**
     * _isNestedResource - looks at the parent association to determine if the api parameters
     * indicate a nested resource.
     *
     * These are both examples of nested resources
     * 
     * ie) http://example.com/users/1/accounts
     * ie) http://example.com/users/1/accounts/1
     * 
     * @return boolean
     * @codeCoverageIgnore
     */
    protected function isNestedResource()
    {
        $parent_association = $this->requestContainer->getParentAssociation();
        
        if(empty($parent_association) || is_null($parent_association)) {

            return false;
        }

        return true;
    }

}
