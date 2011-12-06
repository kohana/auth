# File Driver

The [Auth::File] driver is the default driver when you activate the auth module.

Below are additional configuration options that can be set for this driver.

Name | Type | Default | Description
-----|------|---------|-------------
users | `array` | array() | A user => password (hashed) array of all the users in your application

## Forcing Login

[Auth_File::force_login] allows you to force a user login without a password.

~~~
// Force the admin user to be logged in
Auth::instance()->force_login('admin');
$user = Auth::instance()->get_user(); // Returns the user with the admin username.
~~~

## Roles

The file driver does not have built in support for roles. If roles are important for your application you should look into a different driver or [develop](driver/develop) your own.