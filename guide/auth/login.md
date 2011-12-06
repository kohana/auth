# Log in and out

The auth module provides methods to help you log users in and out of your application.

## Log in

The [Auth::login] method with handle the login.

~~~
// Handled from a form with inputs of names email / password
$post = $this->request->post();
$success = Auth::instance()->login($post['email'], $post['password']);

if ($success)
{
	// Login successful, send to app
}
else
{
	// Login failed, send back to form with error message
}
~~~

## Logged in User

To find the logged in use within you app you will call [Auth::get_user].

~~~
$user = Auth::instance()->get_user();

// Check for a user (NULL if not user is found)
if ($user !== null)
{
	 // User is found, continue on
}
else
{
	// User was not found, redirect to the login form
}
~~~

## Log out

The [Auth::logout] method will take care of logging out a user.

	Auth::instance()->logout();
