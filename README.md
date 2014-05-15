# Ranger

[<img src="https://s3-us-west-2.amazonaws.com/oss-avatars/ranger.png"/>](http://indatus.com/company/careers)

The Api Base Controller is a Laravel package that allows you to get your api up and running fast.  The process of creating an api can be very tedious, especially when you consider how validation, errors, and response codes will be handled.  Instead of this tedium, install ranger, extend the ApiBaseController (see [examples](#examples) below), and that's it.  If you feel that you want this more tailored to your needs, Ranger follows the Open/Closed principle so extending the core components is really easy.

Ranger allows for:

**Searching**<br />
**Eager Loading**<br />
**Joins**<br />
**Left Joins**<br />

Ranger also has support for both Nested and Non-nested resources.  It handles Json and HTML content types but can easily be extended for other content types as well. 

*****

<!--
<img height="300" src="https://s3.amazonaws.com/uploads.hipchat.com/64994/458588/F6THwLZW7VsJCP1/RangerReadMe.png"><br />
-->

<a name="top" />
## README Contents
    
* [Installation](#installation)
    * [Configure in Laravel](#config-laravel)
    
* [Configuration Options](#config)
    
* [Security](#security)
    
* [Examples](#examples)
    * [Non-nested resource Controller](#non-nested-resource-example)
    * [Nested resource Controller](#nested-resource-example)
    * [Nested Resource Routes](#nested-resource-routes)
    * [GET Collection Example](#collection-example)
    * [GET Instance Example](#instance-example)
    * [POST Request Example](#post-example)
    * [PUT Request Example](#put-example)
    * [DELETE Request Example](#delete-example)
    * [Eager load example](#eager-load-example)
    * [Join example](#join-example)
    * [Search example](#search-example)

* [How to Solve Basic Problems](#faq)

<a name="installation" />
## Installation
Install With Composer

You can install the library via Composer by adding the following line to the require block of your composer.json file: 

````
"indatus/ranger": "dev-master"
````

Next run `composer install`

[Back To Top](#top)

<a name="config-laravel" />
## Configure in Laravel

Currently Ranger works **Only** with the Laravel framework.  However, we do have plans to make this package framework agnostic.  

To publish this config to **app/config/packages/indatus/ranger** folder, you need to run the following:

````
php artisan config:publish indatus/ranger
````

The final step is to add the service provider. Open `app/config/app.php`, and add a new item to the providers array.

  `'Indatus\Ranger\RangerServiceProvider'`

That's all.  Now you can start using Ranger.  Checkout the [**Examples**](#examples) below.

****
[Back To Top](#top)

<a name="config" />
## Configuration Options

**Ranger Settings (ranger.php)**

Ranger comes with a few configuration options. You can set the content type of the results in your api.  You can also select whether you want paginated results or return the entire collection.  If you want pagination, you can set the per_page value.

Setting | Default | Description
--- | --- | ---
`pagination.per_page` | `null` | On collections, you get to decide whether you want the results paginated.  A null value will return all of the results<br /><br />Supported Options: `any integer` will return a paginated result ie) 25 for this config setting will return a paginated collection of 25.
`content_type.default` | `json` | Set the content type of the data.  By default, it's json, but you can set this to html.<br /><br /> If you would like the header to dictate what the content type to be, just set this option to null and make sure the client embeds the content type in the header ie) 'Accept' = 'application/json'<br /><br />Supported Options: `html`

**NOTE:** The std_search options in this config are for future versions.  Change them at your own risk, but I recommend leaving them alone for now.  As we expand the searching functionality a bit, these values will be more configurable in future releases.


**All of this functionality can be achieved by just extending the ApiBaseController class.**

I'm assuming that you have taken the appropriate steps in setting up Eloquent models along with migrations.


The examples in this documentation are directly from the example app, in the repo above.  Two entities are created User and Account.

[Back To Top](#top)
<a name="examples" />
## Examples

<a name="non-nested-resource-example" />
## Non-Nested Resource Example
**Here's an example of a non-nested resource**

```php
<?php

class UsersController extends Indatus\Ranger\ApiBaseController {

    /**
     * Illuminate\Database\Eloquent
     *
     * @var User
     */
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return Illuminate\View\Environment | Json string
     */
    public function index()
    {
        return $this->handleAction($this->user);
    }

    /**
     * @return Illuminate\Routing\Redirector | Json string
     */
    public function store()
    {
        return $this->handleAction($this->user);
    }

    /**
     * @param  $id - id of the model instance
     * @return Illuminate\View\Environment | Json string
     */
    public function show($id)
    {
        return $this->handleAction($this->user, $id);
    }

    /**
     * @param  int  $id
     * @return Illuminate\Routing\Redirector | Json string
     */
    public function update($id)
    {
        return $this->handleAction($this->user, $id);
    }

    /**
     *
     * @param  int  $id
     * @return Illuminate\Routing\Redirector | Json string
     */
    public function destroy($id)
    {
        return $this->handleAction($this->user, $id);
    }
}
```
<br />
<br />
[Back To Top](#top)<br /><br />
<a name="non-nested-resource-routes" />
**Next, let's have a look at the routes file in app/routes.php**

```php
<?php

//It's always a good idea to prefix your api
Route::group(['prefix' => 'v1'], function() {

    Route::resource('users', 'UsersController');
});
```
<br />
<br />
**Throughout this readme, I will assume the url of your code will be http://www.example.com**<br /><br />
<a name="collection-example" />
http://www.example.com/v1/users GET Request will return a collection of all users in json format ie):

````
{
    "collection": [
        {
            "id": "1",
            "name": "Charles Griffin",
            "email": "cgriffin@indatus.com",
            "created_at": "2014-03-04 02:25:03",
            "updated_at": "2014-03-04 02:25:03"
        },
        {
            "id": "2",
            "name": "Test User",
            "email": "test_user@gmail.com",
            "created_at": "2014-03-04 02:25:03",
            "updated_at": "2014-03-04 02:25:03"
        }
    ],
    "response_code": 200
}
````
<br />
<br />
[Back To Top](#top)<br /><br />
<a name="instance-example" />
http://www.example.com/v1/users/1 GET request will return a single user instance in json format ie):

````
{
    "instance": {
        "id": "1",
        "name": "Charles Griffin",
        "email": "cgriffin@indatus.com",
        "created_at": "2014-03-04 02:25:03",
        "updated_at": "2014-03-04 02:25:03"
    },
    "response_code": 200
}
````
<br />
<br />
[Back To Top](#top)<br /><br />
<a name="post-example" />
http://www.example.com/v1/users POST request will add a user to the database and return:

````
{
    "instance": {
        "name": "Another User",
        "email": "user@example.com",
        "updated_at": "2014-03-04 02:56:59",
        "created_at": "2014-03-04 02:56:59",
        "id": 4
    },
    "response_code": 201
}
````
<br />
<br />
[Back To Top](#top)<br /><br />
<a name="delete-example" />
http://www.example.com/v1/users/1 DELETE request will delete a single user instance and return the following:

````
{
    "delete_message": ['successful deletion'],
    "response_code": 204
}
````
<br />
<br />
[Back To Top](#top)<br /><br />
<a name="put-example" />
http://www.example.com/v1/users/1 PUT request will update a single user instance and return the following:

````
{
    "instance": {
        "id": "1",
        "name": "Charles Updated",
        "email": "charles_updated@indatus.com",
        "created_at": "2014-03-04 02:58:43",
        "updated_at": "2014-03-04 03:15:33"
    },
    "response_code": 200
}
````
<br />
<br />
[Back To Top](#top)<br /><br />
<a name="eager-load-example" />
http://www.example.com/v1/users?eagerLoads[0]=accounts GET request will return all users along with their accounts:


````
{
    "collection": [
        {
            "id": "1",
            "name": "Charles Griffin",
            "email": "cgriffin@indatus.com",
            "created_at": "2014-03-04 03:22:57",
            "updated_at": "2014-03-04 03:22:57",
            "accounts": [
                {
                    "id": "1",
                    "user_id": "1",
                    "name": "Us Bank",
                    "current_balance": "1500.00",
                    "created_at": "2014-03-04 03:22:57",
                    "updated_at": "2014-03-04 03:22:57"
                }
            ]
        },
        {
            "id": "2",
            "name": "Test User",
            "email": "test_user@gmail.com",
            "created_at": "2014-03-04 03:22:57",
            "updated_at": "2014-03-04 03:22:57",
            "accounts": [
                {
                    "id": "2",
                    "user_id": "2",
                    "name": "PNC Bank",
                    "current_balance": "100.00",
                    "created_at": "2014-03-04 03:22:57",
                    "updated_at": "2014-03-04 03:22:57"
                }
            ]
        }
    ],
    "response_code": 200
}
````
<br />
<br />
[Back To Top](#top)<br /><br />
<a name="join-example" />
http://www.example.com/v1/users?joins[0]=accounts:users.id=accounts.user_id GET request will return all users along with their accounts joined:

````
{
    "collection": [
        {
            "id": "1",
            "name": "Us Bank",
            "email": "cgriffin@indatus.com",
            "created_at": "2014-03-04 03:22:57",
            "updated_at": "2014-03-04 03:22:57",
            "user_id": "1",
            "current_balance": "1500.00"
        },
        {
            "id": "2",
            "name": "PNC Bank",
            "email": "test_user@gmail.com",
            "created_at": "2014-03-04 03:22:57",
            "updated_at": "2014-03-04 03:22:57",
            "user_id": "2",
            "current_balance": "100.00"
        }
    ],
    "response_code": 200
}
````
<br />
<br />

[Back To Top](#top)

<a name="search-example" />
SEARCHING: http://www.example.com/v1/users?searchParams[property]=name&searchParams[operator]=like&searchParams[value]=%Ch%

````
{
    "collection": [
        {
            "id": "1",
            "name": "Charles Griffin",
            "email": "cgriffin@indatus.com",
            "created_at": "2014-03-04 03:22:57",
            "updated_at": "2014-03-04 03:22:57"
        }
    ],
    "response_code": 200
}
````
<br />

****
[Back To Top](#top)

<a name="nested-resource-example" />        
## Nested Resource Example
**Here's an example of a nested resource controller**

```php
<?php

class AccountsController extends Indatus\Ranger\ApiBaseController {

    //because it's a nested resource we must have this
    protected $belongsTo = 'users';

    /**
     * account Repository
     *
     * @var account
     */
    protected $account;

    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    /**
     * Display all accounts for a specific user.
     *
     * @param  int  $user_id The user id
     * @return View or JSON response based on the request
     */
    public function index($user_id)
    {
        $passToView = ['test' => 'this is a test message'];
        return $this->handleAction($this->account, null, compact('user_id'), $passToView);
    }

    /**
     * Show the form for creating a new account.
     *
     * @param  int  $user_id The user id
     * @return View for creating a new account
     */
    public function create($user_id)
    {
        return $this->handleAction($this->account, null, compact('user_id'));
    }

    /**
     * Store a newly created account.
     *
     * @param  int      $user_id The user id
     * @return Redirect or JSON response based on the request format
     */
    public function store($user_id)
    {
        return $this->handleAction($this->account, null, compact('user_id'));
    }

    /**
     * Display the specified account.
     *
     * @param  int  $user_id The user id
     * @param  int  $id         The account id
     * @return View or JSON response based on the request format
     */
    public function show($user_id, $id)
    {
        return $this->handleAction($this->account, $id, compact('user_id'));
    }

    /**
     * Show the form for editing the specified account.
     *
     * @param  int  $user_id The user id
     * @param  int  $id         The account id
     * @return View for updating a account
     */
    public function edit($user_id, $id)
    {
        // if using html, you can pass additional parameters to the view
        $passToView = ['test' => 'this is a test message'];
        return $this->handleAction($this->account, $id, compact('user_id'), $passToView);
    }

    /**
     * Update the specified account.
     *
     * @param  int      $user_id The user id
     * @param  int      $id         The account id
     * @return Redirect or JSON response based on the request format
     */
    public function update($user_id, $id)
    {
        return $this->handleAction($this->account, $id, compact('user_id'));
    }

    /**
     * Remove the specified account from storage.
     *
     * @param  int      $user_id The user id
     * @param  int      $id         The account id
     * @return Redirect or JSON response based on the request format
     */
    public function destroy($user_id, $id)
    {
        return $this->handleAction($this->account, $id, compact('user_id'));
    }

}
```
<br />
<br />
[Back To Top](#top)<br /><br />
<a name="nested-resource-routes" />
**Next, let's have a look at the routes file in app/routes.php.** Nested resources are a little different

```php
<?php

//It's always a good idea to prefix your api
Route::group(['prefix' => 'v1'], function() {

    Route::resource('users.accounts', 'AccountsController');
});
```
<br />
<br />

Now you will be able to access the data in the same manner as the non nested resource above.  The only difference is the urls will be the following:

```
GET
http://example.com/v1/users/1/accounts
returns all accounts for the user with id of 1

GET
http://example.com/v1/users/1/accounts/1
returns account with id of 1 for the user with id of one.  If the user doesn't own that account, then ModelNotFoundException (404 error) is thrown

GET (with eager loads)
http://www.example.com/v1/users/1/accounts?eagerLoads[0]=transactions
returns all accounts for the given user and eager loads that account's transactions

GET (with joins)
http://www.example.com/v1/users/1/accounts?joins[0]=transactions:accounts.id=transactions.account_id
returns all accounts for the given user and join that account's transactions

GET (with Left Joins)
http://www.example.com/v1/users/1/accounts?leftJoins[0]=transactions:accounts.id=transactions.account_id
returns all accounts for the given user and left join that account's transactions

DELETE
http://www.example.com/v1/users/1/accounts/1
Deletes the account if it belongs to the user, otherwise, 404 error gets returned

PUT
http://www.example.com/v1/users/1/accounts/1
updates the account if it belongs to the user otherwise 404 error gets returned

POST
http://www.example.com/v1/users/1/accounts
Adds an account for the given user

```
<br />
<br />

****
[Back To Top](#top)

<a name="security" />
## Security

SECURITY: Out of the box, we do not offer authentication to perform these operations.  **Authentication should be up to the developer to implement** because it's so specific to the application.  We feel, that we needed to make a statement about security because without it, anyone will have access to data on your api.

Keep in mind, this package takes away a lot of the pain points in developing an api, and you would have to write your own authentication on top of reinventing the api wheel. 

We are working on a sample app that will show you basic authentication.
****
<a name="faq" />
## How to Solve Basic Problems

## Problem: 
You are getting an InvalidInputException even though you sure your http request is working.

##Solution

If you are trying to hit one of the api endpoints described above ie) example.com/api/users and get an InvalidInputException, this is most likely due to how you have your apache or nginx config setup.  

If you are using apache, make sure that your .htaccess file looks like the following:

```
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews
    </IfModule>

    RewriteEngine On

    # Redirect Trailing Slashes...
    RewriteRule ^(.*)/$ /$1 [L,R=301]

    # Handle Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

If you are using ngnix please refer to the following:

```
http://phawk.co.uk/blog/laravel-4-nginx-config/
```

****
