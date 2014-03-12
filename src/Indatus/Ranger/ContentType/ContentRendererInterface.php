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

namespace Indatus\Ranger\ContentType;

/**
 * Defines how interfaces will render api reponses
 * 
 */
interface ContentRendererInterface
{
    /**
     * render - renders api response in the format of the class
     * that implements this interface.
     *
     * @param array $content the data format of the api response should be an array. 
     * If using eloquent make sure to use the toArray() method before passing the
     * data to this method.
     * 
     * @return mixed
     * 
     */
    public function render(array $content);
}