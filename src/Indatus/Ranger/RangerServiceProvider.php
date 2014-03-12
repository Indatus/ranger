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

namespace Indatus\Ranger;

use Exception;
use Illuminate\Support\ServiceProvider;

use Indatus\Ranger\ApiBaseController;
use Indatus\Ranger\ApiDatabase\ResponseHandler;

use Indatus\Ranger\RequestDiagnostics\RequestContainer;

use Indatus\Ranger\ContentType\HtmlView\ViewManager;
use Indatus\Ranger\Factories\ContentTypeFactory;
use Indatus\Ranger\Factories\HttpMethodFactory;
use Indatus\Ranger\Factories\QueryExecuterFactory;
use Indatus\Ranger\Factories\QueryBuilderFactory;


/**
 * The service provider for the ranger.  Note, when using workbench use php artisan dump-autoload not composer dump-autoload
 * to load these classes
 */
class RangerServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('indatus/ranger');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		
		$this->registerViewManager();
		$this->registerResponseHandler();
		$this->registerHttpMethods();
	}

	protected function registerViewManager()
	{

		$this->app->bind('view_manager', function ($app)
		{
			return new ViewManager($app['view'], $app['redirect']);

		});

	}

	protected function registerResponseHandler()
	{
		$this->app->bind('response_handler', function ()
		{
			return new ResponseHandler;

		});

	}

	protected function registerApiBaseController()
	{

		$this->app->bind('api-base-controller', function($app, $params)
		{
			return new ApiBaseController;
		});
	}

	protected function registerHttpMethods()
	{
		$this->app->bind('request_container', function ($app, $params)
		{
			return new RequestContainer($params, $app['request'], $app['router']);

		});

		$this->app->bind('query_builders', function($app)
		{
			return with(new QueryBuilderFactory)->make($this->requestContainer->getInput(), $app);

		});


		$this->app->bind('query_executer', function($app, $params)
		{
			
			$this->requestContainer = $app->make('request_container', $params);

			return with(new QueryExecuterFactory)->make($this->requestContainer, $app);

		});

		$this->app->bind('content_type', function($app)
		{
			
			return with(new ContentTypeFactory)->make($this->requestContainer, $app['view_manager'], $app['config']);
		});

		$this->app->bind('http_method', function($app, $params)
        {
        	$request_type = ucfirst(strtolower($app['request']->server('REQUEST_METHOD')));
        	
        	return with(new HttpMethodFactory)->make($params, $app, $request_type);
		});
	}


	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('ranger');
	}

}