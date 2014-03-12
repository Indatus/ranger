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

namespace Indatus\Ranger\Factories;

use Indatus\Ranger\ContentType\HtmlView\ViewManager;
use Illuminate\Config\Repository;
use Indatus\Ranger\RequestDiagnostics\RequestContainer;

/**
 * Produces the various types of content types
 */
class ContentTypeFactory
{
    
    public function make(RequestContainer $requestContainer, ViewManager $viewManager, Repository $config)
    {

        $content_type_in_config = ucfirst($config->get('ranger::ranger.content_type.default'));

        $content_type = $content_type_in_config ?: ucfirst($requestContainer->getContentType());

        $class = 'Indatus\Ranger\ContentType\\'.$content_type. 'ContentType';

        //html content types need a few exta dependencies
        if($content_type == 'Html') {
            
            return new $class($requestContainer, $viewManager);
        }
        
        return new $class;
    }
}