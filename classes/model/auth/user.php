<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Auth_User extends ORM {

	// Relationships
	protected $has_many = array('user_tokens');
	protected $has_and_belongs_to_many = array('roles');

	protected $rules = array
	(
		'username'			=> array
		(
			'not_empty'		=> NULL,
			'min_length'		=> array(4),
			'max_length'		=> array(32),
			'regex'			=> array('![a-zA-Z0-9_.]!u'),
		),
		'password'			=> array
		(
			'not_empty'		=> NULL,
			'min_length'		=> array(5),
			'max_length'		=> array(42),
		),
		'password_confirm'	=> array
		(
			'matches'		=> array('password'),
		),
		'email'				=> array
		(
			'not_empty'		=> NULL,
			'min_length'		=> array(4),
			'max_length'		=> array(127),
			'validate::email'	=> NULL,
		),
	);

	// Columns to ignore
	protected $ignored_columns = array('password_confirm');

	public function __set($key, $value)
	{
		if ($key === 'password')
		{
			// Use Auth to hash the password
			$value = Auth::instance()->hash_password($value);
		}

		parent::__set($key, $value);
	}

	/**
	 * Validates and optionally saves a new user record from an array.
	 *
	 * @param  array    values to check
	 * @param  boolean  save the record when validation succeeds
	 * @param  array    errors
	 * @return boolean
	 */
	public function validate(array & $array, $save = FALSE)
	{
		$array = Validate::factory($array)
			->filter(TRUE, 'trim')
			->rules('email', $this->rules['email'])
			->callback('email', array($this, 'email_available'))
			->rules('username', $this->rules['username'])
			->callback('username', array($this, 'username_available'))
			->rules('password', $this->rules['password'])
			->rules('password_confirm', $this->rules['password_confirm']);

		return parent::validate($array, $save);
	}

	/**
	 * Validates login information from an array, and optionally redirects
	 * after a successful login.
	 *
	 * @param  array    values to check
	 * @param  string   URI or URL to redirect to
	 * @return boolean
	 */
	public function login(array & $array, $redirect = FALSE)
	{
		$array = Validate::factory($array)
			->filter(TRUE, 'trim')
			->rules('username', $this->rules['username'])
			->rules('password', $this->rules['password']);

		// Login starts out invalid
		$status = FALSE;

		if ($array->check())
		{
			// Attempt to load the user
			$this->find($array['username']);

			if ($this->loaded AND Auth::instance()->login($this, $array['password']))
			{
				if (is_string($redirect))
				{
					// Redirect after a successful login
					url::redirect($redirect);
				}

				// Login is successful
				$status = TRUE;
			}
			else
			{
				$array->error('username', 'invalid');
			}
		}

		return $status;
	}

	/**
	 * Validates an array for a matching password and password_confirm field.
	 *
	 * @param  array    values to check
	 * @param  string   save the user if
	 * @return boolean
	 */
	public function change_password(array & $array, $save = FALSE)
	{
		$array = Validate::factory($array)
			->filter(TRUE, 'trim')
			->rules('password', $this->rules['password'])
			->rules('password_confirm', $this->rules['password_confirm']);

		if ($status = $array->check())
		{
			// Change the password
			$this->password = $array['password'];

			if ($save !== FALSE AND $status = $this->save())
			{
				if (is_string($save))
				{
					// Redirect to the success page
					url::redirect($save);
				}
			}
		}

		return $status;
	}

	/**
	 * Does the reverse of unique_key_exists() by triggering error if username exists
	 * Validation Rule
	 *
	 * @param    Validate  $array   validate object
	 * @param    string    $field   field name
	 * @param    array     $errors  current validation errors
	 * @return   array
	 */
	public function username_available(Validate $array, $field)
	{
		if ($this->unique_key_exists($array[$field])) {
			$array->error($field, 'username_available', array($array[$field]));
		}
	}

	/**
	 * Does the reverse of unique_key_exists() by triggering error if email exists
	 * Validation Rule
	 *
	 * @param    Validate  $array   validate object
	 * @param    string    $field   field name
	 * @param    array     $errors  current validation errors
	 * @return   array
	 */
	public function email_available(Validate $array, $field)
	{
		if ($this->unique_key_exists($array[$field])) {
			$array->error($field, 'email_available', array($array[$field]));
		}
	}

	/**
	 * Tests if a unique key value exists in the database
	 *
	 * @param   mixed        value  the value to test
	 * @return  boolean
	 */
	public function unique_key_exists($value)
	{
		return (bool) DB::select(array('COUNT("*")', 'total_count '))
						->from($this->db->table_prefix().$this->table_name)
						->where($this->unique_key($value), '=', $value)
						->execute($this->db)
						->get('total_count');
	}

	/**
	 * Allows a model to be loaded by username or email address.
	 */
	public function unique_key($id)
	{
		if ( ! empty($id) AND is_string($id) AND ! ctype_digit($id))
		{
			return validate::email($id) ? 'email' : 'username';
		}

		return parent::unique_key($id);
	}

} // End Auth User Model