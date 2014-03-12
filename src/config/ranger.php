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

return array(

    /*
    |--------------------------------------------------------------------------
    | Default Application Pagination Settings
    |--------------------------------------------------------------------------
    |
    | This option controls the default settings that are used for pagination
    | throughout the custom views of the application.
    | 
    | Set to null if you don't want paginated results
    |
    |
    */
    'pagination' => array(

        'per_page' => null,
    ),

    /*
    |--------------------------------------------------------------------------
    | Default Application Content Type Settings
    |--------------------------------------------------------------------------
    |
    |  By default, content is returned in json format.  You can also use html
    |  and use html to return views and redirects where appropriate.
    |
    |  If you want the content type to be determined by the request header,
    |  just add null for this option
    |
    | ie) json, html, or null
    |
    */
    'content_type' => array(

        //other options are html and null
        'default' => 'json',
    ),

    /*
    |--------------------------------------------------------------------------
    | Default controller search parameter names
    |--------------------------------------------------------------------------
    |
    | This set of options controlls the default settings that are used for index
    | search parameters for a controller.
    */
    'std_search' => array(

        'search_property_key'         => 'property',
        'search_operator_key'         => 'operator',
        'search_value_key'            => 'value',
    ),

);
