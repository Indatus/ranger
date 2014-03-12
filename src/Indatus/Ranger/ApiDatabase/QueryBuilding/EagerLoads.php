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
 * Builds eager loaded relations on the Eloquent query builder
 * 
 */
class EagerLoads extends ApiQueryBuilder
{

    /**
     * We will 'eagerLoad' any of the related records with the response.
     * Should be in the format of:
     * 
     * array(
     *      0 => array('accounts'),
     *      1 => array('transactions')
     * )
     *
     * @return void
     * 
     */
    public function buildQueryFromParameters($builder, array $input)
    {
        $this->checkBuilderType($builder);

        $eagerLoads = $input['eagerLoads'];

        for ($i=0; $i < count($eagerLoads); $i++) {

            $builder = $builder->with($eagerLoads[$i]);
        }
        
        return $builder;
    }
}
