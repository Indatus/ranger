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

use Mockery as m;
use  Indatus\Ranger\ApiDatabase\ResponseHandler;

/**
 * This set of tests are for testing all the core classes that extend the abstract ApiQueryBuilder
 * class.
 * 
 */
class ResponseHandlerTest extends PHPUnit_Framework_TestCase
{

    public function tearDown()
    {
        m::close();
    }

        public function success(array $data, $status_code = 200)
    {
        return array_merge($data, ['response_code' => $status_code]);
    }

    public function test_success_message()
    {
        $data = [

            'collection' => array(

                'total' => 2,
                'per_page' => 15,
                'current_page' => 1,
                'last_page' => 1,
                'from' => 1,
                'to' => 2,
                'data' => array(

                    0 => array(

                            'id' => 1,
                            'name' => 'Charles Griffin',
                            'email' => 'cgriffin@indatus.com',
                            'created_at' => '2014-03-08 18:02:17',
                            'updated_at' => '2014-03-08 18:02:17',
                    ),

                    1 => array(
                            'id' => 2,
                            'name' => 'Test User',
                            'email' => 'test_user@gmail.com',
                            'created_at' => '2014-03-08 18:02:17',
                            'updated_at' => '2014-03-08 18:02:17',
                    ),
                ),
            ),
        ];

        $data = with(new ResponseHandler)->success($data);

        $this->assertEquals($data, 
            ['collection' => array(

                'total' => 2,
                'per_page' => 15,
                'current_page' => 1,
                'last_page' => 1,
                'from' => 1,
                'to' => 2,
                'data' => array(

                    0 => array(

                            'id' => 1,
                            'name' => 'Charles Griffin',
                            'email' => 'cgriffin@indatus.com',
                            'created_at' => '2014-03-08 18:02:17',
                            'updated_at' => '2014-03-08 18:02:17',
                    ),

                    1 => array(
                            'id' => 2,
                            'name' => 'Test User',
                            'email' => 'test_user@gmail.com',
                            'created_at' => '2014-03-08 18:02:17',
                            'updated_at' => '2014-03-08 18:02:17',
                    ),
                ),

                
            ),
            'response_code' => 200,
            ]
        );

    }

    public function test_error_message()
    {

        $error = with(new ResponseHandler)->error();

        $this->assertEquals($error, ['error' => 'this entity does not exist', 'response_code' => 404]);

    }

    
  
}