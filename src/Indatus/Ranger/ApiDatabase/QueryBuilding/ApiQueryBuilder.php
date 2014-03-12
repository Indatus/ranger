<?php

/**
 * This file is part of Ranger
 *
 * @package Ranger
 * @author  Brian Webb <bwebb@indatus.com>
 * @author  Charles Griffin <cgriffin@indatus.com>
 * @license For the full copyright and license information, please view the LICENSE
 *          file that was distributed with this source code.
 *          
 */

namespace Indatus\Ranger\ApiDatabase\QueryBuilding;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * This interface defines how implementers will build to the query builder
 * 
 */
abstract class ApiQueryBuilder
{

    /**
     * QueryString parameters for <em>includes</em>, <em>joins</em>, and <em>left_joins</em> can be
     * provided in order to include additional data or join other tables. Both
     * should be passed in as an array (i.e. includes[0], includes[1], joins[0], etc.).
     * Includes are simply strings corresponding to the collection name. Joins should take
     * the format: <b>{join_table}:{table.some_field}={another_table.another_field}</b>
     * 
     * buildQueryFromParameters - based on the parameter type, the query builder object
     * will be modified to append additional goodness such as joins, leftJoins, and
     * eager loaded relationships.  Then the builder will be returned so that queries
     * can be executed on it.
     *
     * @param Illuminate\Database\Eloquent\Model | Illuminate\Database\Eloquent\Builder $builder
     * 
     * @return Illuminate\Database\Eloquent\Model | Illuminate\Database\Eloquent\Builder
     * 
     */
    abstract public function buildQueryFromParameters($builder, array $input);

    /**
     * checkBuilderType for now, this package will only work with eloquent
     * @param  Illuminate\Database\Eloquent\Model | Illuminate\Database\Eloquent\Builder $builder
     * @throws Exception
     * @codeCoverageIgnore
     */
    protected function checkBuilderType($builder)
    {
        //keeps clients from setting the builder to anything that isn't an Eloquent
        //Builder or an Model
        if(! $builder instanceof Builder && ! $builder instanceof Model ) {

            throw new Exception('builder must be instance of Illuminate\Database\Eloquent\Model or Illuminate\Database\Eloquent\Builder');

        }

    }

}