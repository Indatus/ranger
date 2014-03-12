<?php

/**
 * This file is part of Ranger
 * 
 * @package   Ranger
 * @author    Charles Griffin <cgriffin@indatus.com>
 * @author    Brian Webb <bwebb@indatus.com>
 * @license   For the full copyright and license information, please view the LICENSE
 *            file that was distributed with this source code.
 */

trait ContentFixture
{
    //This is a collection
    protected function getContentWithResponseCode()
    {
        return $content = [

            0 => array(

                0 => array(

                    'id' => 2,
                    'user_id' => 2,
                    'account_type_id' => 2,
                    'name' => 'PNC Bank',
                    'current_balance' => '100.00',
                    'opening_balance' => '1500.00',
                    'created_at' => '2014-02-24 03:12:10',
                    'updated_at' => '2014-02-24 03:12:10',
                    'transactions' => array(),
                )
            ),

            'response_code' => 200,
        ];

    }


    //This is an instance
    protected function getInstanceWithResponseCode()
    {
        return $content = [

            0 => array(

                'id' => 2,
                'user_id' => 2,
                'account_type_id' => 2,
                'name' => 'PNC Bank',
                'current_balance' => '100.00',
                'opening_balance' => '1500.00',
                'created_at' => '2014-02-24 03:12:10',
                'updated_at' => '2014-02-24 03:12:10',
                'transactions' => array(),
            ),

            'response_code' => 200,
        ];
    }


    //will test redirects
    protected function getContentFromPostRequest()
    {

        return array(

            0 => array(

                    'name' => 'Charles',
                    'username' => 'cgrif',
                    'email' => 'cgriffin@indatus.com',
                    'updated_at' => '2014-03-01 17:13:16',
                    'created_at' => '2014-03-01 17:13:16',
                    'id' => 3,
                ),

            'response_code' => 201,
        );

    }
}