<?php

/**
 * This file is part of Ranger
 *
 * @package Ranger
 * @author  Brian Webb <bwebb@indatus.com>
 * @author  Charles Griffin <cgriffin@indatus.com>
 */

namespace Indatus\Ranger\ContentType;

use Exception;
use Indatus\Ranger\ContentType\HtmlView\ViewManager;
use Indatus\Ranger\RequestDiagnostics\RequestContainer;
use Indatus\Ranger\ContentType\ContentRendererInterface;

/**
 * Renders content in html format
 * 
 */

class HtmlContentType implements ContentRendererInterface
{

    /**
     * @param RequestContainer $requestContainer
     * @param ViewManager      $viewManager
     * @codeCoverageIgnore
     */
    public function __construct(RequestContainer $requestContainer, ViewManager $viewManager)
    {
        $this->requestContainer = $requestContainer;

        $this->viewManager = $viewManager;
    }

    public function render(array $content)
    {

        $responseCode = return_keys_value_if_exists('response_code', $content);

        if(is_null($responseCode)) {

            throw new Exception('there needs to be a response code in the content');
        }

        $requestMethod = strtolower($this->requestContainer->getRequestMethod());
        
        //only post, put, and delete request handle redirects
        if($requestMethod == 'put' || $requestMethod == 'post' || $requestMethod == 'delete') {
            
            return $this->doRedirect($responseCode);
        }

        return $this->showView($content);
            
    }

    /**
     * [doRedirect description]
     * @param  [type] $responseCode
     * @return Redirector
     * @codeCoverageIgnore
     */
    private function doRedirect($responseCode)
    {
        $id = $this->requestContainer->getId();
        $route = $this->requestContainer->getRoute();
        $parentAssociation = $this->requestContainer->getParentAssociation();

        return $this->viewManager->handleRedirect($route, $parentAssociation, $id, $responseCode);

    }

    /**
     * [showView description]
     * @param  array  $content
     * @return View
     * @codeCoverageIgnore
     */
    private function showView(array $content)
    {
        $route = $this->requestContainer->getRoute();
        $controller = $this->requestContainer->getController();
        $additionalAssigns = $this->requestContainer->getAdditionalAssigns();
        $parentAssociation = $this->requestContainer->getParentAssociation();

        return $this->viewManager->goToView($route, $parentAssociation, $additionalAssigns, head($content));
    }
}