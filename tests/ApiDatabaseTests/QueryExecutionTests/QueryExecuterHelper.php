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

trait QueryExecuterHelper
{
    protected function _nonNestedBuilderInstanceAssertions($primaryKey, $id, $builderModified = true, $instance)
    {   
        //if there are things like joins and eagerLoads then run this
        if($builderModified) {

            $this->builder->shouldReceive('where')->once()->with($primaryKey, '=', $id)->andReturn($this->builder);

            $this->builder->shouldReceive('first')->once()->andReturn($instance);

        } else {

            //else we want to use the model mock.
            $this->model->shouldReceive('where')->once()->with($primaryKey, '=', $id)->andReturn($this->model);

            $this->model->shouldReceive('first')->once()->andReturn($instance);
        }

        return $instance;
    }

    protected function _nestedBuilderInstanceAssertions($primaryKey, $id, $foreignKey, $parentId, $builderModified = true)
    {
        if($builderModified) {
            $this->builder->shouldReceive('where')->once()->with($primaryKey, '=', $id)->andReturn($this->builder);

            $this->builder->shouldReceive('where')->once()->with($foreignKey, '=', $parentId)->andReturn($this->builder);

            $this->builder->shouldReceive('first')->once()->andReturn($instance = m::mock('Illuminate\Database\Eloquent\Builder'));
        } else {

            $this->model->shouldReceive('where')->once()->with($primaryKey, '=', $id)->andReturn($this->model);

            $this->model->shouldReceive('where')->once()->with($foreignKey, '=', $parentId)->andReturn($this->model);

            $this->model->shouldReceive('first')->once()->andReturn($instance = m::mock('Illuminate\Database\Eloquent\Model'));

        }
        
        return $instance;
    }


    protected function _apiQueryBuilderAssertions(array $apiQueryBuilders, array $input)
    {

        foreach($apiQueryBuilders as $i => $apiQueryBuilder) {

            if($i == 0) {

                $eloquentObject = $this->model;

            } else {

                $eloquentObject = $this->builder;
            }

            if($apiQueryBuilder instanceof Indatus\Ranger\ApiDatabase\QueryBuilding\Joins) {

                $builderType = 'joinsBuilder';

            } elseif($apiQueryBuilder instanceof Indatus\Ranger\ApiDatabase\QueryBuilding\EagerLoads) {

                $builderType = 'eagerLoadBuilder';

            } elseif($apiQueryBuilder instanceof Indatus\Ranger\ApiDatabase\QueryBuilding\EagerLoads) {

                $builderType = 'searcherBuilder';

            }

            $this->{$builderType}
             ->shouldReceive('buildQueryFromParameters')
             ->once()
             ->with($eloquentObject, $input)
             ->andReturn($this->builder);

        }

    }

}