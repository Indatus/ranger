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

namespace Indatus\Ranger\HttpRequestHandler;

use Indatus\Ranger\ApiDatabase\ValidationErrorException;
use Indatus\Ranger\HttpRequestHandler\HttpMethod;

/**
 * The responsibility of this class is to handle post request
 */
class HttpPost extends HttpMethod
{

    public function handleRequest()
    {
        try {

            $query_result = $this->queryExecuter->executeQuery('post');
            $result = $this->responseHandler->success($query_result, self::ENTITY_CREATED);
            return $this->contentType->render($result);

        } catch(ValidationErrorException $e) {

            return $this->responseHandler->error($e->getMessage(), self::VALIDATION_ERROR);

        }
        
    }

}