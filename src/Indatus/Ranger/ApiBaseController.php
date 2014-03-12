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

namespace Indatus\Ranger;

use Illuminate\Support\Facades\App;
use Illuminate\Routing\Controller;
use Illuminate\Database\Eloquent\Model;

/**
 * Base controller class from which all other controllers within the application extend.
 * This controller contains default behavior for controller actions such that a controller
 * could just refer to the parent unless other custom functionality is needed.
 */
class ApiBaseController extends Controller
{

    /**
     * Variable set by subclasses to define the parent
     * that a nested resouce controller belongs to.
     * The variable should be set with the pluralized
     * lowercase name of the parent. i.e. 'products'
     *
     * @type string
     */
    protected $belongsTo = null;
    
    /**
     * Function to handle the default behavior for all restful actions, from the CRUD operations
     * to rendering the appropriate response based on the request format.
     *
     * This function essentially looks at what URL was called, and determines the action that should
     * be ran, then hands off the execution to a separate function specific to that action.
     *
     * @param  Model $model             The model class that the controller is handling
     * @param  int   $id                The ID of the particular record in question IF the action requires it
     * @param  array $parentData        The key / value data for identifying parent for nested resources
     * @param  array $additionalAssigns Array of additional data to be made available to the view
     * @return View | string  The rendered template, or JSON data
     */
    protected function handleAction(Model $model, $id = null, $parentData = [], $additionalAssigns = [])
    {
        
        $params = [ 'model' => $model, 
                    'id' => $id, 
                    'parent_data' => $parentData, 
                    'additional_assigns' => $additionalAssigns,
                    'belongsTo' => $this->belongsTo,
                    ];

        return App::make('http_method', $params)->handleRequest();
        
    }//end handleAction function
    
}