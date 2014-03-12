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

use Indatus\Ranger\ApiDatabase\ResponseHandler;
use Indatus\Ranger\ContentType\ContentRendererInterface;
use Indatus\Ranger\ApiDatabase\QueryExecution\QueryExecuter;

/**
 * The responsibility of this class is to handle the various tasks of handling http request.  This class delegates the responsibilities
 * of retreiving collections and instances depending on the type of request.
 */
abstract class HttpMethod
{
    const OK = 200;
    const NO_CONTENT = 204;
    const ENTITY_CREATED = 201;
    const VALIDATION_ERROR = 422;
    
    /**
     * 
     * @param Indatus\Ranger\ApiDatabase\QueryExecution\QueryExecuter $queryExecuter
     * @param Indatus\Ranger\ContentType\ContentRendererInterface     $contentType
     * @param Indatus\Ranger\ApiDatabase\ResponseHandler              $responseHandler
     * 
     */
    public function __construct(QueryExecuter $queryExecuter, 
                                ContentRendererInterface $contentType,
                                ResponseHandler $responseHandler)
    {
        $this->queryExecuter = $queryExecuter;

        $this->contentType = $contentType;

        $this->responseHandler = $responseHandler;

    }//end constructor


    /**
     * handleRequest - provides a way to handle a request
     * @return mixed json encoded string | Illuminate\Environment\View | Illuminate\Routing\Redirector
     */
    abstract public function handleRequest();

}
