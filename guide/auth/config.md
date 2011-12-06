# Configuration

The default config file is located in `MODPATH/auth/config/auth.php`. You should copy this file to `APPPATH/config/auth.php` and make changes there, in keeping with the [cascading filesystem](../kohana/files).

[Config merging](../kohana/config#config-merging) allows the default config settings to apply if you don't overwrite them in your application configuration file.

Name | Type | Default | Description
-----|------|---------|------------
driver | `string` | file | The name of the auth driver to use.
hash_method | `string` | sha256 | The hashing function to use.
hash_key | `string` | NULL | The key to use when hashing the password.
lifetime | `int` | 1209600 | The time (in seconds) that the user session is valid without activity.
session_type | `string` | [Session::$default] | The type of session to use to store the auth user.
session_key | `string` | auth_user | The name of the session variable to save the user.
