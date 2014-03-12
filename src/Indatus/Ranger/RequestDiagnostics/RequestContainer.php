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

namespace Indatus\Ranger\RequestDiagnostics;

use Illuminate\Http\Request;
use Illuminate\Routing\Router;

/**
 *
 * This class contains all the immutable properties that hold all
 * the information about the request that was just made.
 * 
 */

class RequestContainer
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
     * variable to hold the $this->input->all() or overwridden
     * value
     *
     * @var array
     */
    protected $request;

    protected $model;

    protected $controller;

    protected $action;

    protected $route;

    protected $id = null;

    /**
     * Variable set by subclasses to define the parent
     * association with a key / value array. Primarily
     * used for nested resources, the array would be
     * set as: array('parent_id' => $id)
     *
     * @type array
     */
    protected $parentAssociation = [];


    /**
     * Array of additional variables that should be
     * made available to the view
     * @var array
     */
    protected $additionalAssigns = [];


    public function __construct(array $params, Request $request, Router $router)
    {
        
        //this is an Eloquent model
        $this->model = $params['model'];

        $this->id = $params['id'];

        $this->parentData = $params['parent_data'];

        $this->additionalAssigns = $params['additional_assigns'];

        $this->belongsTo = $params['belongsTo'];

        $this->request = $request;

        $this->router = $router;

        $this->_setProperties();

    }


    private function _setProperties()
    {
        //add any route parameters into the assigns array for the view
        $routeParams = $this->router->getCurrentRoute()->parameters();

        $updatedRouteParams = array();

        foreach ($routeParams as $k => $v) {
            $updatedRouteParams["route_param_{$k}"] = $v;
        }

        if ( ! empty($this->parentData) ) {
            $this->parentData = $this->parentData;
        }

        if ( ! empty($this->additionalAssigns) ) {
            
            $this->additionalAssigns = array_merge($updatedRouteParams, $this->additionalAssigns);
        }


        list($controllerClass, $action) = explode('@', $this->router->currentRouteAction());
        $controllerClass = remove_namespace_from_class_name($controllerClass);

        $controller = snake_case(str_replace('Controller', '', $controllerClass));

        $this->route = "{$controller}.{$action}";
        
        $this->action = $action;
        $this->controller = $controller;

    }


    public function getRoute()
    {
        $route = implode('.', array_filter(array($this->belongsTo, $this->controller, $this->action)));
        
        return $route;
    }
    

    /**
     * getInput returns all the input
     * @return array
     */
    public function getInput()
    {
        return $this->request->all();
    }

    public function getController()
    {
        return $this->controller;
    }

    public function getModel()
    {

        return $this->model;

    }

    public function getId()
    {
        return $this->id;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getParentAssociation()
    {

        return $this->parentData;

    }

    public function getAdditionalAssigns()
    {

        return $this->additionalAssigns;

    }

    public function getContentType()
    {
        
        return $this->request->format();
    }

    public function getRequestMethod()
    {
        return strtolower($this->request->server('REQUEST_METHOD'));
    }

    public function getPrimaryKey()
    {
        return $this->model->getKeyName();
        
    }

    public function getUrl()
    {
        return $this->request->url();
    }

    public function getTable()
    {
        return $this->model->getTable();
    }

    public function getForeignKey()
    {
        return $this->model->getForeignKey();
    }
}
