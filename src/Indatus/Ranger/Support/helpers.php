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

/**
 * Returns false if the value is null or empty.
 * 
 */
if ( ! function_exists('null_or_empty')) {

    /**
     * Checks if the value is null or empty.  If it meets that criteria then return true.
     * Otherwise return false.
     */
    function null_or_empty($value) {

        if(! empty($value) && ! is_null($value)) {
            return false;
        }

        return true;
    }
}

if ( ! function_exists('pp')) {

    /**
     * The pp function makes pretty array formats easy to run just do pp($array)
     *
     * @param array   $x   Array to be printed
     * @param boolean $die If true die will be called
     * 
     * @return void
     */
    function pp($x, $die=true)
    {
        echo "<pre>".print_r($x, true)."</pre>";
        if ($die) die;
    }
}

if ( ! function_exists('remove_namespace_from_class_name')) {
    /**
     * There are instances where we want to get table names and attributes directly
     * from the model name. Or load routes from controller class names. The namespace
     * gets in the way in these cases, so this helper just strips off the namespace.
     *
     * @param Model $model The eloquent model is just passed by reference
     * 
     * @return string The class name with namespaces stripped off
     */
    function remove_namespace_from_class_name($model)
    {
        $model_name = explode("\\", $model);

        return end($model_name);
    }
}

/**
 *  return_key_value_if_exists - checks if the array key exists, if it does return 
 *  the value otherwise return null;
 * 
 */
if(! function_exists('return_keys_value_if_exists'))
{
    function return_keys_value_if_exists($key, array $array_to_search)
    {
        if( array_key_exists($key, $array_to_search) ) {

            return $array_to_search[$key];

        } 

        return null;
       
    }
}