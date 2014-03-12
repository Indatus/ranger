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

use Mockery as m;
use Indatus\Ranger\ContentType\JsonContentType;


class JsonContentTypeTest extends PHPUnit_Framework_TestCase
{

    public function tearDown()
    {
        m::close();
    }

    public function test_json_render_method()
    {
        $array = ['user' => 'info'];

        $json_array = (new JsonContentType)->render($array);

        $this->assertEquals('{"user":"info"}', $json_array);
    }
}