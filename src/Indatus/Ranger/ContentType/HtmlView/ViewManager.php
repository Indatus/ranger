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

namespace Indatus\Ranger\ContentType\HtmlView;

use InvalidArgumentException;
use Illuminate\View\Environment;
use Illuminate\Routing\Redirector;

/**
 *
 * This class manages views and redirections to routes with views.
 */
class ViewManager
{
    /**
     * Constant used for reference to the standard restful
     * controller's create action name.
     */
    const ACTION_SHOW  = 'show';

    /**
     * Constant used for reference to the standard restful
     * controller's edit action name.
     */
    const ACTION_EDIT    = 'edit';

    /**
     * 
     * @param Environment $view
     * @param Redirector  $redirect
     * @codeCoverageIgnore
     */
    public function __construct(Environment $view, Redirector $redirect)
    {
        $this->view = $view;
        $this->redirect = $redirect;
    }


    public function goToView($route, $parentAssociation, $additionalAssigns, $content)
    {
        $route_array = explode('.', $route);
        
        if(count($route_array) == 2) {

            $parent = $route_array[0];
            $action = $route_array[1];

            $controller = $parent;

        } elseif(count($route_array) == 3) {

            $parent = $route_array[0];
            $child = $route_array[1];
            $action = $route_array[2];

            $controller = $child;

        } else {

            throw new InvalidArgumentException("$route is not in correct format");
        }

        $assignStr = str_singular($controller);

        $assigns = array($assignStr => $content);

        if (! null_or_empty($parentAssociation)) {
            
            $assigns = array_merge($assigns, $parentAssociation);
        }

        if (! null_or_empty($additionalAssigns)) {

            $assigns = array_merge($assigns, $additionalAssigns);
        }
        
        return $this->view->make($controller.".".$action, $assigns);
    }

    /**
     * Function to handle redirection of regular and also nested resource
     * routes.
     *
     * @param  string   $controller The controller for the route
     * @param  string   $action     The action for the route
     * @param  integer  $id         The id for the route
     * @return Redirect
     */
    public function handleRedirect($route, $parentAssociation, $id = null, $responseCode)
    {
        $params = array();

        if ($id && null_or_empty($parentAssociation)) {
            
            $route = explode('.', $route)[0].'.'.self::ACTION_EDIT;

            $params = array($id);

        } elseif ($id && ! null_or_empty($parentAssociation)) {

            $parent = explode('.', $route)[0];
            $child = explode('.', $route)[1];

            $route = $parent.'.'.$child.'.'.self::ACTION_EDIT;

            $params = array_values(array_merge($parentAssociation, array($id)));

        } elseif (is_null($id) && ! null_or_empty($parentAssociation)) {
            $parent = explode('.', $route)[0];
            $child = explode('.', $route)[1];

            $route = $parent.'.'.$child.'.'.self::ACTION_SHOW;
            $params = array_values($parentAssociation);

        } elseif(is_null($id) && null_or_empty($parentAssociation)) {

            $route = explode('.', $route)[0].'.'.self::ACTION_SHOW;

            $params = array();

        } else {

            throw new InvalidArgumentException("incorrect format");
            
        }

        //handle error responses

        return $this->redirect->route($route, $params);

    }//end handleRedirect

}