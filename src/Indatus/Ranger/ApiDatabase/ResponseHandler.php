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

namespace Indatus\Ranger\ApiDatabase;

/**
 * Handles success and error messages
 */
class ResponseHandler
{
    public function success(array $data, $status_code = 200)
    {
        return array_merge($data, ['response_code' => $status_code]);
    }

    public function error($error_message = 'this entity does not exist', $status_code = 404)
    {
        return ['error' => $error_message, 'response_code' => $status_code];
    }

}