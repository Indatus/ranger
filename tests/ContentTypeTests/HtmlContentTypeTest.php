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
use Indatus\Ranger\ContentType\HtmlContentType;

class HtmlContentTypeTest extends PHPUnit_Framework_TestCase
{
    use ContentFixture;

    public function setUp()
    {
        $this->view = m::mock('Illuminate\View\Environment');
        $this->requestContainer = m::mock('Indatus\Ranger\RequestDiagnostics\RequestContainer');
        $this->viewManager = m::mock('Indatus\Ranger\ContentType\HtmlView\ViewManager');
        $this->redirect = m::mock('Illuminate\Routing\Redirector');
    }


    public function tearDown()
    {
        m::close();
    }


    //The content is based on the nested resource http://10.0.0.200/v1/users/2/accounts
    public function test_html_render_method_with_content_that_has_response_code_and_non_redirecting()
    {
        $additionalAssigns = ['route_param_users' => 2, 'test' => 'testing123'];
        $content = $this->getContentWithResponseCode();
        $this->requestContainer->shouldReceive('getRequestMethod')->once()->andReturn('get');
        $this->requestContainer->shouldReceive('getRoute')->once()->andReturn('users.accounts.index');
        $this->requestContainer->shouldReceive('getParentAssociation')->once()->andReturn(['user_id' => 2]);
        $this->requestContainer->shouldReceive('getAdditionalAssigns')->once()->andReturn($additionalAssigns);
        $this->requestContainer->shouldReceive('getController')->once()->andReturn('accounts');
        
        $this->viewManager
             ->shouldReceive('goToView')
             ->once()
             ->with('users.accounts.index', ['user_id' => 2], $additionalAssigns, $content[0])
             ->andReturn($this->view);

        $html = (new HtmlContentType($this->requestContainer, $this->viewManager))->render($content);

        $this->assertTrue($html instanceof Illuminate\View\Environment);

    }

    //post request to http://10.0.0.200/v1/users
    public function test_html_render_method_with_content_that_has_response_code_and_redirecting()
    {
        
        $content = $this->getContentFromPostRequest();

        $this->requestContainer->shouldReceive('getRequestMethod')->once()->andReturn('post');
        $this->requestContainer->shouldReceive('getRoute')->once()->andReturn('users.store');
        $this->requestContainer->shouldReceive('getId')->once()->andReturn(null);
        $this->requestContainer->shouldReceive('getParentAssociation')->once()->andReturn([]);
        
        $this->viewManager
             ->shouldReceive('handleRedirect')
             ->once()
             ->with('users.store', [], null, 201)
             ->andReturn($this->redirect);

        $html = (new HtmlContentType($this->requestContainer, $this->viewManager))->render($content);

        $this->assertTrue($html instanceof Illuminate\Routing\Redirector);

    }
}