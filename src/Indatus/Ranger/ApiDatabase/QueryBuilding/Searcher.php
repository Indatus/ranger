<?php

/**
 * This file is part of Ranger
 *
 * @package Ranger
 * @author  Brian Webb <bwebb@indatus.com>
 * @author  Charles Griffin <cgriffin@indatus.com>
 */

namespace Indatus\Ranger\ApiDatabase\QueryBuilding;

use Illuminate\Config\Repository;
use Indatus\Ranger\ApiDatabase\QueryBuilding\ApiQueryBuilder;

/**
 * The responsibility of this class is to perform searching and filtering from the api 
 * query parameters.  This class doesn't run queries, however, this class does prep a
 * query builder so that it can execute a query in the CollectionHandler class.
 * 
 */
class Searcher extends ApiQueryBuilder
{


    /**
     * Default value for the index action 'search' input sub-parameter key
     * within the 'search' array that identifies the property being searched on.
     *
     * This value is popuplated from an app/config/ranger.php in the
     * constructor of this class.
     *
     * @type string
     * @see Searcher::SEARCH_PARAM
     */
    protected $SEARCH_PROPERTY_KEY;


    /**
     * Default value for the index action 'search' input sub-parameter key
     * within the 'search' array that identifies the type of comparison operator
     * to use for the property in question (i.e. <, >, LIKE, =, etc.)
     *
     * This value is popuplated from an app/config/ranger.php in the
     * constructor of this class.
     *
     * @type string
     * @see Searcher::SEARCH_PARAM
     */
    protected $SEARCH_OPERATOR_KEY;


    /**
     * Default value for the index action 'search' input sub-parameter key
     * within the 'search' array that identifies the value to search
     * for the property in question
     *
     * This value is popuplated from an app/config/ranger.php in the
     * constructor of this class.
     *
     * @type string
     * @see Searcher::SEARCH_PARAM
     */
    protected $SEARCH_VALUE_KEY;


    public function __construct(Repository $config)
    {

        $this->config = $config;
        
        $this->SEARCH_PROPERTY_KEY         = $this->config->get('ranger::ranger.std_search.search_property_key');
        $this->SEARCH_OPERATOR_KEY         = $this->config->get('ranger::ranger.std_search.search_operator_key');
        $this->SEARCH_VALUE_KEY            = $this->config->get('ranger::ranger.std_search.search_value_key');
        
    }


    /**
     * Search parameters in the format of:
     * 
     * ie) http://10.0.0.200/v1/users?searchParams[0][property]=name&searchParams[1][operator]=like&searchParams[2][value]=%Ch%
     *
     * The above example will look at the users table for a property called name.  Then it will find anything in the name
     * field that's like %Ch%.  So if the Charles is a user it will return the user Charles.
     *
     * @param Indatus\Ranger\ApiDatabase\UriProcessor\Interfaces\ApiQueryDataInterface $apiQueryDataInterface
     * 
     * @return void
     *
     */
    public function buildQueryFromParameters($builder, array $input)
    {
        
        $this->checkBuilderType($builder);

        $searchParams = $input['searchParams'];
        
        //initial query custom condition
        $builder = $builder->where(
            $searchParams[$this->SEARCH_PROPERTY_KEY],
            $searchParams[$this->SEARCH_OPERATOR_KEY],
            $searchParams[$this->SEARCH_VALUE_KEY]
        );

        return $builder;

    }

}