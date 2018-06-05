[![StyleCI](https://styleci.io/repos/119122531/shield?branch=master)](https://styleci.io/repos/119122531)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/aad0fe4a-4ddc-4357-807e-71a2c931375f/big.png)](https://insight.sensiolabs.com/projects/aad0fe4a-4ddc-4357-807e-71a2c931375f)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sectheater/laravel-jarvis.svg?style=flat-square)](https://packagist.org/packages/sectheater/laravel-jarvis)

<!-- [![Total Downloads](https://img.shields.io/packagist/dt/sectheater/laravel-jarvis.svg?style=flat-square)](https://packagist.org/packages/sectheater/laravel-jarvis) -->

<p align="center"> Made with ❤️ by  SecTheater Foundation:  http://www.sectheater.org</p>
<p align="center">Package Documentation : http://www.sectheater.org/documentation
</p>

> the Jarvis CheatSheet will be availble soon 


<hr>

## Jarvis provides you the following :
### 1. Authentication System
Login, Register, Reminders,Security Questions for reseting passwords and Activations.
### 2. Comment System & Reply System
### 3. Like/Dislike System
You could always like,dislike anything such as comments,replies and posts. Also you could reset this like so the user's like or dislike is removed
### 4. Upgrading & Downgrading Users
### 5. Approving Anything
You could approve anything just by providing the model and the ID of the record you wish to approve
### 6.Tag
Assign Tags to whatever on your application
> You can Assign one tag or multiple tags within one text input and you can set the separator dynamically

### 7. Authorizing Users & Managing Roles.

## Installation Steps

### 1. Require the Package

After creating your new Laravel application you can include the Jarvis package with the following command: 

```bash
composer require sectheater/laravel-jarvis:dev-master
```

### 2. Add the DB Credentials & APP_URL

Next make sure to create a new database and add your database credentials to your .env file:

```
DB_HOST=localhost
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```

You will also want to update your website URL inside of the `APP_URL` variable inside the .env file:

```
APP_URL=http://localhost:8000
```

### 3. Getting Your Environment Ready.

#### Just Run The following command.


```bash
	php artisan sectheater:install
```

##### Command Interpretation

1- It sets up your config file by registering whatever you confirm within the survey, in case you use *Everything* option that command supplies, the default configuration will be used. If you ever want to change anything , go to your config/jarvis.php and set whatever you need.
2- It will provide you a survey so the package sets up which features have to be enabled.
3- It also sets up the authentication routes.
> Jarvis doesn't depend on models at all, Everything runs through the repositories to provide you the best quality.
![Installation Preview](http://sectheater.org/assets/images/doc/installation.png)
### 4. Sample Usage
##### 4.1 Registering A User 
Whenever you try to register a user, just supply Jarivs with the data and the slug of the role which you want to assign to this user
```bash
	Jarvis::registerWithRole($data,'user')
```
#### 4.2 Login A User

Logging a user is also an easy thing to do, just pass the data the user tries to attempt with , It can be username/email and password or whatever. Then if you want to remember the user , pass the second argument with true.
```bash
	Jarvis::registerWithRole($data,true)
```

#### 4.3 Checking For Roles.
<b> Let's assume I want to check whether the current logged in user has any permissions to create a post or not. It's worth mentioning that whenever you pass many elements to check on and the user has any of them, The method will return you a boolean value </b>
```bash
	Jarvis::User()->hasAnyRole(['create-post']) 
```
We are just checking here if the user has either creating permission under the user/moderator permissions.
```bash
	Jarvis::User()->hasAnyRole(['create-tag','moderator.create-tag']) 
/*
first permission determines what the role the user has and then check if he has this permission ,
second one checks if the user has moderator.create-tag and this should exist 
in users table permissions column,
as this doesn't exist in default roles table ( unless you add it )
*/
```

Sometimes, you want to check if a specific user has all of the roles , not just any of them.
Well That's also included.
```bash
	Jarvis::User()->hasAllRole(['create-post','edit-post'])
```
If you want to check on only one permission, you can pass that to the HasAnyRole within an array, Or just do it like the following.
```bash
	Jarvis::User()->hasRole('view-post')
```
If you want to check whether the user has any of the post permissions or whatever, you can do it like the following .
```bash
  // you can use the helper also
  jarvis()->user()->hasRole('*-post');
```
<b> There is much more details within the <a href="http://www.sectheater.org/documentation">documentation</a>.</b>

### Recently Added Features

#### Recent Changes

- Jarvis Middleware is removed.

- Policies & Gates are set automatically depending on the database roles.
- Existing relationships Dynamically set depending on existing models either through properties or config file.
- Observers watch models that has been bounded to the config file dynamically.
- package_version function is added ( checks the version of any package passed to it )
- model_exists function is now checking for the user models
- jarvis_model_exists function only checks for Jarvis's models only.
- recent method is added to all of Repositories ( you can fetch with it recent stuff under condition and,or approved stuff )
- Repository Interface updated
- Basic Repository Is updated, now you can pass to the delete and update methods either an identifier to find or the instance that should be updated/deleted.
- make:facade command is added
- make:response command is added
- make:repository is added
- package_path function is added
- Relationships are synchronized with the related models dynamically.
- Roles Trait is updated.

#### Extra Commands are added such as :
 - sectheater:register-authorization // registers the policies and gates and publishing them , depending on your database   roles.
 - make:observer
 - sectheater:seed-db , seeding your database with the RolesSeeder and call for register-authorization ( optional)

#### Fixes :

- Routes in blades are fixed.
- Jarvis routes method is fixed, removed from web file and It will be added in your Route Service Provider
- app.blade.php is copied during running sectheater:auth if it doesn't exist
- RegisterWithRole method is fixed.

