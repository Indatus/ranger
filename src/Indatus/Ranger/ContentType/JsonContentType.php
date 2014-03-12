<?php

/**
 * This file contains the logic for rendering a json request
 *
 * @package Ranger
 * @author  Brian Webb <bwebb@indatus.com>
 * @author  Charles Griffin <cgriffin@indatus.com>
 */

namespace Indatus\Ranger\ContentType;

use Response;
use Indatus\Ranger\ContentType\ContentRendererInterface;

class JsonContentType implements ContentRendererInterface
{
    /**
     * Renders content in json serialized format
     * @return string
     * 
     */
    public function render(array $content)
    {
        
        return json_encode($content);
    }
}