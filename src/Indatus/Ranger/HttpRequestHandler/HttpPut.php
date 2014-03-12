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

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Indatus\Ranger\HttpRequestHandler\HttpMethod;

/**
 * The responsibility of this class is to handle put request for instances
 */
class HttpPut extends HttpMethod
{

    public function handleRequest()
    {

        try {

            $query_result = $this->queryExecuter->executeQuery('put');
            $result = $this->responseHandler->success($query_result, self::OK);
            return $this->contentType->render($result);

        } catch(ModelNotFoundException $e) {

            $this->responseHandler->error($e->getMessage());
        }
        
    }
}
