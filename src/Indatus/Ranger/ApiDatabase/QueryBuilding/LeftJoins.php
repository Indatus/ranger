<?php

/**
 * This file is part of Ranger
 *
 * @package Ranger
 * @author  Brian Webb <bwebb@indatus.com>
 * @author  Charles Griffin <cgriffin@indatus.com>
 * @license For the full copyright and license information, please view the LICENSE
 *          file that was distributed with this source code.
 */

namespace Indatus\Ranger\ApiDatabase\QueryBuilding;

use Indatus\Ranger\ApiDatabase\QueryBuilding\ApiQueryBuilder;


/**
 * Builds left joins on the Eloquent query builder
 * 
 */
class LeftJoins extends ApiQueryBuilder
{

    /**
     * We will 'join' any of the related records with the response.
     * the join parameter array values should be a string in the format:
     * {join_table}:{join_table.id_field}={another_table.another_id_field}
     */
    public function buildQueryFromParameters($builder, array $input)
    {
        $this->checkBuilderType($builder);
        
        $leftJoins = $input['leftJoins'];

        for ($i=0; $i < count($leftJoins); $i++) {

            list($table, $conditions) = array_map('trim', explode(':', $leftJoins[$i]));
            list($col1, $col2) = array_map('trim', explode('=', $conditions));

            $builder = $builder->leftJoin($table, $col1, '=', $col2);

        }

        return $builder;
        
    }

}