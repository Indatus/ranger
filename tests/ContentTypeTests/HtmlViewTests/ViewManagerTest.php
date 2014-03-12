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
use Indatus\Ranger\ContentType\HtmlView\ViewManager;

class ViewManagerTest extends PHPUnit_Framework_TestCase
{
    use ContentFixture;

    public function setUp()
    {
        $this->view = m::mock('Illuminate\View\Environment');
        $this->redirect = m::mock('Illuminate\Routing\Redirector');
    }


    public function tearDown()
    {
        m::close();
    }


    //http://example.com/v1/users
    public function test_no_parent_association_gets_proper_view_rendered()
    {
        $route = 'users.index';
        $parentAssociation = [];
        $additionalAssigns = [];

        //content from a get request
        $content = head($this->getContentWithResponseCode());

        $this->view->shouldReceive('make')->once()->with('users.index', ['user' => $content])->andReturn($this->view);

        (new ViewManager($this->view, $this->redirect))->goToView($route, $parentAssociation, $additionalAssigns, $content);

        $this->assertTrue($this->view instanceof Illuminate\View\Environment);
    }


    //http://example.com/v1/users/2/accounts
    public function test_with_parent_association_gets_proper_view_rendered()
    {
        $route = 'users.accounts.index';
        $parentAssociation = ['user_id' => 2];
        $additionalAssigns = [];

        //content from a get request
        $content = head($this->getContentWithResponseCode());

        $this->view->shouldReceive('make')->once()->with('accounts.index', ['account' => $content, 'user_id' => 2])->andReturn($this->view);

        (new ViewManager($this->view, $this->redirect))->goToView($route, $parentAssociation, $additionalAssigns, $content);

        $this->assertTrue($this->view instanceof Illuminate\View\Environment);
    }

    //http://example.com/v1/users/2/accounts/1 with additional assigns
    public function test_with_parent_association_and_additional_assigns_gets_proper_view_rendered()
    {
        $route = 'users.accounts.show';
        $parentAssociation = ['user_id' => 2];
        $additionalAssigns = ['test' => 'test message passed to the view'];

        //content from a get request
        $content = head($this->getInstanceWithResponseCode());

        $this->view->shouldReceive('make')
                   ->once()
                   ->with('accounts.show', ['account' => $content, 'user_id' => 2, 'test' => 'test message passed to the view'])
                   ->andReturn($this->view);

        (new ViewManager($this->view, $this->redirect))->goToView($route, $parentAssociation, $additionalAssigns, $content);

        $this->assertTrue($this->view instanceof Illuminate\View\Environment);
    }

    //http://example.com/v1/users/2/accounts POST request
    public function test_good_post_request_with_parent_association_with_redirects()
    {
        $route = 'users.accounts.store';
        $parentAssociation = ['user_id' => 2];

        $this->redirect->shouldReceive('route')
                   ->once()
                   ->with('users.accounts.show', [2])
                   ->andReturn($this->redirect);

        (new ViewManager($this->view, $this->redirect))->handleRedirect($route, $parentAssociation, null, 200);

        $this->assertTrue($this->redirect instanceof Illuminate\Routing\Redirector);
    }


    //http://example.com/v1/users POST request
    public function test_good_post_request_with_out_parent_association_with_redirects()
    {
        $route = 'users.accounts.store';
        $parentAssociation = [];

        $this->redirect->shouldReceive('route')
                   ->once()
                   ->with('users.show', [])
                   ->andReturn($this->redirect);

        (new ViewManager($this->view, $this->redirect))->handleRedirect($route, $parentAssociation, null, 200);

        $this->assertTrue($this->redirect instanceof Illuminate\Routing\Redirector);
    }


    //http://example.com/v1/users/1 PUT request
    public function test_good_put_request_with_out_parent_association_with_redirects()
    {
        $route = 'users.update';
        $parentAssociation = [];
        $id = 1;

        $this->redirect->shouldReceive('route')
                   ->once()
                   ->with('users.edit', [1])
                   ->andReturn($this->redirect);

        (new ViewManager($this->view, $this->redirect))->handleRedirect($route, $parentAssociation, $id, 200);

        $this->assertTrue($this->redirect instanceof Illuminate\Routing\Redirector);
    }

    //http://example.com/v1/users/2/accounts/1 PUT request
    public function test_good_put_request_with_parent_association_with_redirects()
    {
        $route = 'users.accounts.update';
        $parentAssociation = ['user_id' => 2];
        $id = 1;

        $this->redirect->shouldReceive('route')
                   ->once()
                   ->with('users.accounts.edit', [2, 1])
                   ->andReturn($this->redirect);

        (new ViewManager($this->view, $this->redirect))->handleRedirect($route, $parentAssociation, $id, 200);

        $this->assertTrue($this->redirect instanceof Illuminate\Routing\Redirector);
    }


}