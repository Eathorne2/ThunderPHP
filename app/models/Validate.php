<?php

/**
 * This file is part of the ThunderPHP Framework.
 * It contains the Validate class which validates user input with provided rules.
 *
 * @package ThunderPHP
 * @version 2.1.0
 * @author Eathorne Choongo
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace Core;

/**
 * Validate user input against a defined set of rules.
 *
 * Features:
 * - Built-in validation rules
 * - Hook-based custom rule extension
 * - Static rule extension
 * - Database uniqueness validation
 * - Scoped uniqueness with where and whereNot support
 * - File and image validation
 *
 * Rule examples:
 * - 'required'
 * - 'email'
 * - 'min:3'
 * - 'unique:users'
 * - ['rule' => 'unique', 'param' => ['table' => 'users', 'where' => ['deleted' => 0]]]
 */
class Validate extends Database
{
	/**
	 * Raw input data being validated.
	 *
	 * @var array
	 */
	protected array $data = [];

	/**
	 * Validation rules keyed by field name.
	 *
	 * @var array
	 */
	protected array $rules = [];

	/**
	 * Custom rule handlers loaded from hooks and static extensions.
	 *
	 * @var array
	 */
	protected array $customRules = [];

	/**
	 * Collected validation errors keyed by field name.
	 *
	 * @var array
	 */
	public array $errors = [];

	/**
	 * Default primary key name used by unique validation
	 * when ignoring the current record during updates.
	 *
	 * @var string
	 */
	public string $primaryKey = 'id';

	/**
	 * Statically registered rule extensions.
	 *
	 * @var array
	 */
	protected static array $extensions = [];

	/**
	 * Create a validator instance.
	 *
	 * @param array $data Data to validate.
	 */
	public function __construct(array $data = [])
	{
		$this->data = $data;
		$this->customRules = $this->loadCustomRules();
	}

	/**
	 * Replace the data being validated.
	 *
	 * @param array $data New validation data.
	 * @return self
	 */
	public function setData(array $data): self
	{
		$this->data = $data;
		return $this;
	}

	/**
	 * Get the current validation data.
	 *
	 * @return array
	 */
	public function getData(): array
	{
		return $this->data;
	}

	/**
	 * Set validation rules.
	 *
	 * @param array $rules Validation rules.
	 * @return self
	 */
	public function setRules(array $rules): self
	{
		$this->rules = $rules;
		return $this;
	}

	/**
	 * Get currently assigned validation rules.
	 *
	 * @return array
	 */
	public function getRules(): array
	{
		return $this->rules;
	}

	/**
	 * Run validation.
	 *
	 * If data is provided, it replaces the current data before validation.
	 *
	 * @param array|null $data Optional data override.
	 * @return bool True when validation passes, false otherwise.
	 */
	public function validate(?array $data = null): bool
	{
		if($data !== null)
			$this->data = $data;

		$this->errors = [];

		foreach($this->rules as $field => $rules)
		{
			$input_name = ucfirst(str_replace('_', ' ', $field));

			if(isset($rules['name']) || (isset($rules[0]) && is_string($rules[0]) && isset($rules[1]) && is_array($rules[1])))
			{
				$input_name = $rules['name'] ?? $rules[0];
				$rules = $rules['rules'] ?? $rules[1];
			}

			foreach($rules as $rule)
			{
				$error_message = '';
				$param = null;

				if(is_array($rule))
				{
					$error_message = $rule['error_message'] ?? ($rule[1] ?? '');
					$param = $rule['param'] ?? null;
					$rule = $rule['rule'] ?? ($rule[0] ?? '');
				}

				[$ruleName, $stringParam] = array_pad(explode(':', (string)$rule, 2), 2, null);

				if($param === null)
					$param = $stringParam;

				$value = $this->data[$field] ?? null;

				if(is_string($value))
					$value = trim($value);

				$meta = [
					'input_name' => $input_name,
					'error_message' => $error_message,
					'field' => $field,
				];

				$this->runRule($ruleName, $field, $value, $param, $meta);
			}
		}

		return empty($this->errors);
	}

	/**
	 * Check whether validation has passed.
	 *
	 * Note: call validate() first.
	 *
	 * @return bool
	 */
	public function passes(): bool
	{
		return empty($this->errors);
	}

	/**
	 * Check whether validation has failed.
	 *
	 * Note: call validate() first.
	 *
	 * @return bool
	 */
	public function fails(): bool
	{
		return !empty($this->errors);
	}

	/**
	 * Alias for fails check.
	 *
	 * @return bool
	 */
	public function has_errors(): bool
	{
		return !empty($this->errors);
	}

	/**
	 * Return all collected errors.
	 *
	 * @return array
	 */
	public function getErrors(): array
	{
		return $this->errors;
	}

	/**
	 * Return the first error for a field.
	 *
	 * @param string $field Field name.
	 * @param string $default Default value when no error exists.
	 * @return string
	 */
	public function firstError(string $field, string $default = ''): string
	{
		return $this->errors[$field][0] ?? $default;
	}

	/**
	 * Add an error message to a field.
	 *
	 * @param string $field Field name.
	 * @param string $message Error message.
	 * @return void
	 */
	public function addError(string $field, string $message): void
	{
		$this->errors[$field][] = $message;
	}

	/**
	 * Determine whether a value is considered empty by the validator.
	 *
	 * Empty means null or empty string.
	 * Numeric zero and string '0' are NOT treated as empty.
	 *
	 * @param mixed $value Value to inspect.
	 * @return bool
	 */
	public function isEmpty(mixed $value): bool
	{
		return $value === null || $value === '';
	}

	/**
	 * Determine whether a field has a given rule assigned.
	 *
	 * @param string $field Field name.
	 * @param string $ruleName Rule name to check.
	 * @return bool
	 */
	public function hasRule(string $field, string $ruleName): bool
	{
		if(empty($this->rules[$field]) || !is_array($this->rules[$field]))
			return false;

		$rules = $this->rules[$field];
		$rules = $rules['rules'] ?? $rules;

		foreach($rules as $rule)
		{
			if(is_array($rule))
				$rule = $rule['rule'] ?? ($rule[0] ?? '');

			[$name] = array_pad(explode(':', (string)$rule, 2), 2, null);

			if($name === $ruleName)
				return true;
		}

		return false;
	}

	/**
	 * Resolve and execute a validation rule.
	 *
	 * Rule resolution order:
	 * 1. Built-in validate_<rule> method
	 * 2. Custom callable from registered extensions or hooks
	 * 3. Custom object with validate() method
	 * 4. Unknown rule error
	 *
	 * @param string $rule Rule name.
	 * @param string $field Field name.
	 * @param mixed $value Field value.
	 * @param mixed $param Rule parameter.
	 * @param array $meta Extra metadata.
	 * @return void
	 */
	protected function runRule(string $rule, string $field, mixed $value, mixed $param, array $meta): void
	{
		$method = 'validate_'.$rule;

		if(method_exists($this, $method))
		{
			$this->$method($field, $value, $param, $meta);
			return;
		}

		if(isset($this->customRules[$rule]))
		{
			$handler = $this->customRules[$rule];

			if(is_callable($handler))
			{
				$handler($field, $value, $param, $meta, $this);
				return;
			}

			if(is_object($handler) && method_exists($handler, 'validate'))
			{
				$handler->validate($field, $value, $param, $meta, $this);
				return;
			}
		}

		$this->addError($field, "Unknown validation rule: ".$rule);
	}

	/**
	 * Load custom rules from static extensions and framework hooks.
	 *
	 * Hook used:
	 * - validation_rules
	 *
	 * @return array
	 */
	protected function loadCustomRules(): array
	{
		$rules = self::$extensions;

		if(function_exists('apply_filter'))
			$rules = apply_filter('validation_rules', $rules);

		return is_array($rules) ? $rules : [];
	}

	/**
	 * Register a custom validation rule globally.
	 *
	 * Accepted handlers:
	 * - callable
	 * - object with validate() method
	 *
	 * @param string $name Rule name.
	 * @param callable|object $callback Rule handler.
	 * @return void
	 */
	public static function extend(string $name, callable|object $callback): void
	{
		self::$extensions[$name] = $callback;
	}

	/**
	 * Return either the custom rule message or the provided default.
	 *
	 * @param array $meta Rule metadata.
	 * @param string $default Default message.
	 * @return string
	 */
	protected function message(array $meta, string $default): string
	{
		return !empty($meta['error_message']) ? $meta['error_message'] : $default;
	}

	/**
	 * Validate whether a database identifier is safe to interpolate.
	 *
	 * Used for table names and column names in unique validation.
	 *
	 * @param string $name Identifier.
	 * @return bool
	 */
	protected function identifierIsSafe(string $name): bool
	{
		return (bool)preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name);
	}

	/**
	 * Get the string length of a value using mb_strlen when available.
	 *
	 * @param mixed $value Value to measure.
	 * @return int
	 */
	protected function getLength(mixed $value): int
	{
		$value = (string)$value;
		return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
	}

	/**
	 * Validate that a field is required.
	 *
	 * @param string $field Field name.
	 * @param mixed $value Field value.
	 * @param mixed $param Unused parameter.
	 * @param array $meta Rule metadata.
	 * @return void
	 */
	protected function validate_required(string $field, mixed $value, mixed $param, array $meta): void
	{
		if($this->isEmpty($value))
			$this->addError($field, $this->message($meta, $meta['input_name']." is required"));
	}

	/**
	 * Nullable marker rule.
	 *
	 * This rule currently acts as a semantic marker and does not add errors.
	 *
	 * @param string $field Field name.
	 * @param mixed $value Field value.
	 * @param mixed $param Unused parameter.
	 * @param array $meta Rule metadata.
	 * @return void
	 */
	protected function validate_nullable(string $field, mixed $value, mixed $param, array $meta): void
	{
	}

	/**
	 * Validate that a field contains a valid email address.
	 *
	 * @param string $field Field name.
	 * @param mixed $value Field value.
	 * @param mixed $param Unused parameter.
	 * @param array $meta Rule metadata.
	 * @return void
	 */
	protected function validate_email(string $field, mixed $value, mixed $param, array $meta): void
	{
		if($this->isEmpty($value)) return;

		if(!filter_var($value, FILTER_VALIDATE_EMAIL))
			$this->addError($field, $this->message($meta, $meta['input_name']." is not valid"));
	}

	/**
	 * Validate that a field contains a valid URL.
	 *
	 * @param string $field Field name.
	 * @param mixed $value Field value.
	 * @param mixed $param Unused parameter.
	 * @param array $meta Rule metadata.
	 * @return void
	 */
	protected function validate_url(string $field, mixed $value, mixed $param, array $meta): void
	{
		if($this->isEmpty($value)) return;

		if(!filter_var($value, FILTER_VALIDATE_URL))
			$this->addError($field, $this->message($meta, $meta['input_name']." is not a valid URL"));
	}

	/**
	 * Validate that a field contains a boolean-like value.
	 *
	 * Accepted values include:
	 * true, false, 1, 0, '1', '0', 'true', 'false', 'yes', 'no', 'on', 'off'
	 *
	 * @param string $field Field name.
	 * @param mixed $value Field value.
	 * @param mixed $param Unused parameter.
	 * @param array $meta Rule metadata.
	 * @return void
	 */
	protected function validate_boolean(string $field, mixed $value, mixed $param, array $meta): void
	{
		if($this->isEmpty($value)) return;

		$valid = [true, false, 0, 1, '0', '1', 'true', 'false', 'on', 'off', 'yes', 'no'];

		if(!in_array($value, $valid, true))
			$this->addError($field, $this->message($meta, $meta['input_name']." must be true or false"));
	}

	/**
	 * Validate that a field is numeric.
	 *
	 * @param string $field Field name.
	 * @param mixed $value Field value.
	 * @param mixed $param Unused parameter.
	 * @param array $meta Rule metadata.
	 * @return void
	 */
	protected function validate_numeric(string $field, mixed $value, mixed $param, array $meta): void
	{
		if($this->isEmpty($value)) return;

		if(!is_numeric($value))
			$this->addError($field, $this->message($meta, $meta['input_name']." should be numeric"));
	}

	/**
	 * Validate that a field is an integer.
	 *
	 * @param string $field Field name.
	 * @param mixed $value Field value.
	 * @param mixed $param Unused parameter.
	 * @param array $meta Rule metadata.
	 * @return void
	 */
	protected function validate_integer(string $field, mixed $value, mixed $param, array $meta): void
	{
		if($this->isEmpty($value)) return;

		if(filter_var($value, FILTER_VALIDATE_INT) === false)
			$this->addError($field, $this->message($meta, $meta['input_name']." should be a whole number"));
	}

	/**
	 * Validate minimum string length.
	 *
	 * @param string $field Field name.
	 * @param mixed $value Field value.
	 * @param mixed $param Minimum length.
	 * @param array $meta Rule metadata.
	 * @return void
	 */
	protected function validate_min(string $field, mixed $value, mixed $param, array $meta): void
	{
		if($this->isEmpty($value)) return;

		if($this->getLength($value) < (int)$param)
			$this->addError($field, $this->message($meta, $meta['input_name']." must be at least ".$param." characters long"));
	}

	/**
	 * Validate maximum string length.
	 *
	 * @param string $field Field name.
	 * @param mixed $value Field value.
	 * @param mixed $param Maximum length.
	 * @param array $meta Rule metadata.
	 * @return void
	 */
	protected function validate_max(string $field, mixed $value, mixed $param, array $meta): void
	{
		if($this->isEmpty($value)) return;

		if($this->getLength($value) > (int)$param)
			$this->addError($field, $this->message($meta, $meta['input_name']." must not exceed ".$param." characters"));
	}

	/**
	 * Validate minimum numeric value.
	 *
	 * @param string $field Field name.
	 * @param mixed $value Field value.
	 * @param mixed $param Minimum numeric value.
	 * @param array $meta Rule metadata.
	 * @return void
	 */
	protected function validate_min_value(string $field, mixed $value, mixed $param, array $meta): void
	{
		if($this->isEmpty($value)) return;

		if(!is_numeric($value) || $value < $param)
			$this->addError($field, $this->message($meta, $meta['input_name']." must be at least ".$param));
	}

	/**
	 * Validate maximum numeric value.
	 *
	 * @param string $field Field name.
	 * @param mixed $value Field value.
	 * @param mixed $param Maximum numeric value.
	 * @param array $meta Rule metadata.
	 * @return void
	 */
	protected function validate_max_value(string $field, mixed $value, mixed $param, array $meta): void
	{
		if($this->isEmpty($value)) return;

		if(!is_numeric($value) || $value > $param)
			$this->addError($field, $this->message($meta, $meta['input_name']." must not exceed ".$param));
	}

	/**
	 * Validate that a field is a valid date string.
	 *
	 * @param string $field Field name.
	 * @param mixed $value Field value.
	 * @param mixed $param Unused parameter.
	 * @param array $meta Rule metadata.
	 * @return void
	 */
	protected function validate_date(string $field, mixed $value, mixed $param, array $meta): void
	{
		if($this->isEmpty($value)) return;

		if(strtotime((string)$value) === false)
			$this->addError($field, $this->message($meta, $meta['input_name']." is not valid"));
	}

	/**
	 * Validate that a field matches another field.
	 *
	 * @param string $field Field name.
	 * @param mixed $value Field value.
	 * @param mixed $param Other field name.
	 * @param array $meta Rule metadata.
	 * @return void
	 */
	protected function validate_match(string $field, mixed $value, mixed $param, array $meta): void
	{
		$other = $this->data[$param] ?? null;

		if($value !== $other)
			$this->addError($field, $this->message($meta, $meta['input_name']." should match ".ucfirst(str_replace('_', ' ', (string)$param))));
	}

	/**
	 * Validate alphabetic text and spaces only.
	 *
	 * @param string $field Field name.
	 * @param mixed $value Field value.
	 * @param mixed $param Unused parameter.
	 * @param array $meta Rule metadata.
	 * @return void
	 */
	protected function validate_alpha(string $field, mixed $value, mixed $param, array $meta): void
	{
		if($this->isEmpty($value)) return;

		if(!preg_match('/^[a-zA-Z ]+$/', (string)$value))
			$this->addError($field, $this->message($meta, $meta['input_name']." should only contain letters"));
	}

	/**
	 * Validate alphanumeric text and spaces only.
	 *
	 * @param string $field Field name.
	 * @param mixed $value Field value.
	 * @param mixed $param Unused parameter.
	 * @param array $meta Rule metadata.
	 * @return void
	 */
	protected function validate_alpha_numeric(string $field, mixed $value, mixed $param, array $meta): void
	{
		if($this->isEmpty($value)) return;

		if(!preg_match('/^[a-zA-Z0-9 ]+$/', (string)$value))
			$this->addError($field, $this->message($meta, $meta['input_name']." should only contain letters & numbers"));
	}

	/**
	 * Validate that a field contains no spaces.
	 *
	 * @param string $field Field name.
	 * @param mixed $value Field value.
	 * @param mixed $param Unused parameter.
	 * @param array $meta Rule metadata.
	 * @return void
	 */
	protected function validate_no_space(string $field, mixed $value, mixed $param, array $meta): void
	{
		if($this->isEmpty($value)) return;

		if(preg_match('/\s+/', (string)$value))
			$this->addError($field, $this->message($meta, $meta['input_name']." should not contain spaces"));
	}

	/**
	 * Validate that a field is within an allowed set.
	 *
	 * Param may be:
	 * - comma separated string
	 * - array of allowed values
	 *
	 * @param string $field Field name.
	 * @param mixed $value Field value.
	 * @param mixed $param Allowed values.
	 * @param array $meta Rule metadata.
	 * @return void
	 */
	protected function validate_in(string $field, mixed $value, mixed $param, array $meta): void
	{
		if($this->isEmpty($value)) return;

		$allowed = is_array($param) ? $param : array_map('trim', explode(',', (string)$param));

		if(!in_array((string)$value, array_map('strval', $allowed), true))
			$this->addError($field, $this->message($meta, $meta['input_name']." contains an invalid value"));
	}

	/**
	 * Validate that a field is not within a blocked set.
	 *
	 * Param may be:
	 * - comma separated string
	 * - array of blocked values
	 *
	 * @param string $field Field name.
	 * @param mixed $value Field value.
	 * @param mixed $param Blocked values.
	 * @param array $meta Rule metadata.
	 * @return void
	 */
	protected function validate_not_in(string $field, mixed $value, mixed $param, array $meta): void
	{
		if($this->isEmpty($value)) return;

		$blocked = is_array($param) ? $param : array_map('trim', explode(',', (string)$param));

		if(in_array((string)$value, array_map('strval', $blocked), true))
			$this->addError($field, $this->message($meta, $meta['input_name']." contains a disallowed value"));
	}

	/**
	 * Validate that a field matches a regular expression.
	 *
	 * Param should be a complete regex pattern, for example:
	 * /^(?:\+260|0)(95|96|97)\d{7}$/
	 *
	 * @param string $field Field name.
	 * @param mixed $value Field value.
	 * @param mixed $param Regex pattern.
	 * @param array $meta Rule metadata.
	 * @return void
	 */
	protected function validate_regex(string $field, mixed $value, mixed $param, array $meta): void
	{
		if($this->isEmpty($value)) return;

		if(empty($param))
		{
			$this->addError($field, $this->message($meta, $meta['input_name']." format is not valid"));
			return;
		}

		$result = @preg_match((string)$param, (string)$value);

		if($result !== 1)
			$this->addError($field, $this->message($meta, $meta['input_name']." format is not valid"));
	}

	/**
	 * Validate that a field is required when another field equals a given value.
	 *
	 * Param format:
	 * other_field,expected_value
	 *
	 * Example:
	 * required_if:account_type,business
	 *
	 * @param string $field Field name.
	 * @param mixed $value Field value.
	 * @param mixed $param Required-if parameter.
	 * @param array $meta Rule metadata.
	 * @return void
	 */
	protected function validate_required_if(string $field, mixed $value, mixed $param, array $meta): void
	{
		if(empty($param)) return;

		$parts = explode(',', (string)$param, 2);
		$otherField = $parts[0] ?? '';
		$expectedValue = $parts[1] ?? '';

		$otherValue = $this->data[$otherField] ?? null;

		if((string)$otherValue === $expectedValue && $this->isEmpty($value))
			$this->addError($field, $this->message($meta, $meta['input_name']." is required"));
	}

	/**
	 * Validate that an uploaded file exists and uploaded successfully.
	 *
	 * This rule reads from $_FILES using the field name.
	 *
	 * @param string $field Field name.
	 * @param mixed $value Field value.
	 * @param mixed $param Unused parameter.
	 * @param array $meta Rule metadata.
	 * @return void
	 */
	protected function validate_file(string $field, mixed $value, mixed $param, array $meta): void
	{
		if(empty($_FILES[$field])) return;

		$file = $_FILES[$field];

		if(!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) return;

		if($file['error'] !== UPLOAD_ERR_OK)
			$this->addError($field, $this->message($meta, $meta['input_name']." upload failed"));
	}

	/**
	 * Validate that an uploaded file is an image.
	 *
	 * Allowed MIME types:
	 * - image/jpeg
	 * - image/png
	 * - image/gif
	 * - image/webp
	 *
	 * @param string $field Field name.
	 * @param mixed $value Field value.
	 * @param mixed $param Unused parameter.
	 * @param array $meta Rule metadata.
	 * @return void
	 */
	protected function validate_image(string $field, mixed $value, mixed $param, array $meta): void
	{
		if(empty($_FILES[$field])) return;

		$file = $_FILES[$field];

		if(!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) return;

		if($file['error'] !== UPLOAD_ERR_OK)
		{
			$this->addError($field, $this->message($meta, $meta['input_name']." upload failed"));
			return;
		}

		$mime = mime_content_type($file['tmp_name']);
		$allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

		if(!in_array($mime, $allowed, true))
			$this->addError($field, $this->message($meta, $meta['input_name']." must be an image"));
	}

	/**
	 * Validate database uniqueness.
	 *
	 * Supported param formats:
	 *
	 * String form:
	 * - 'users'
	 *
	 * Array form:
	 * [
	 *   'table' => 'users',
	 *   'column' => 'email',
	 *   'primary_key' => 'id',
	 *   'ignore_value' => 10,
	 *   'ignore_from_data' => 'id',
	 *   'where' => ['deleted' => 0],
	 *   'whereNot' => ['status' => 'archived']
	 * ]
	 *
	 * @param string $field Field name.
	 * @param mixed $value Field value.
	 * @param mixed $param Unique rule configuration.
	 * @param array $meta Rule metadata.
	 * @return void
	 */
	protected function validate_unique(string $field, mixed $value, mixed $param, array $meta): void
	{
		if($this->isEmpty($value)) return;

		$config = $this->normalizeUniqueConfig($field, $param);

		if(empty($config['table']))
		{
			$this->addError($field, "Unique validation requires a table name");
			return;
		}

		$table = $config['table'];
		$column = $config['column'];
		$primaryKey = $config['primary_key'];
		$ignoreValue = $config['ignore_value'];
		$where = $config['where'];
		$whereNot = $config['whereNot'];

		if(
			!$this->identifierIsSafe($table) ||
			!$this->identifierIsSafe($column) ||
			!$this->identifierIsSafe($primaryKey)
		){
			$this->addError($field, "Unsafe identifier used in unique validation");
			return;
		}

		$sql = "select count(*) as total from {$table} where {$column} = :unique_value";
		$params = ['unique_value' => $value];
		$counter = 0;

		if($ignoreValue !== null && $ignoreValue !== '')
		{
			$sql .= " and {$primaryKey} != :ignore_value";
			$params['ignore_value'] = $ignoreValue;
		}

		foreach($where as $whereColumn => $whereValue)
		{
			if(!$this->identifierIsSafe($whereColumn))
			{
				$this->addError($field, "Unsafe where column used in unique validation");
				return;
			}

			if($whereValue === null)
			{
				$sql .= " and {$whereColumn} is null";
			}
			else
			{
				$key = 'w_'.$counter++;
				$sql .= " and {$whereColumn} = :{$key}";
				$params[$key] = $whereValue;
			}
		}

		foreach($whereNot as $whereColumn => $whereValue)
		{
			if(!$this->identifierIsSafe($whereColumn))
			{
				$this->addError($field, "Unsafe whereNot column used in unique validation");
				return;
			}

			if($whereValue === null)
			{
				$sql .= " and {$whereColumn} is not null";
			}
			else
			{
				$key = 'wn_'.$counter++;
				$sql .= " and {$whereColumn} != :{$key}";
				$params[$key] = $whereValue;
			}
		}

		$sql .= " limit 1";

		$row = $this->get_row($sql, $params);

		if($row && !empty($row->total))
			$this->addError($field, $this->message($meta, $meta['input_name']." must be unique"));
	}

	/**
	 * Normalize unique validation configuration into a predictable array.
	 *
	 * Returned keys:
	 * - table
	 * - column
	 * - primary_key
	 * - ignore_value
	 * - where
	 * - whereNot
	 *
	 * @param string $field Current field name.
	 * @param mixed $param Raw unique rule parameter.
	 * @return array
	 */
	protected function normalizeUniqueConfig(string $field, mixed $param): array
	{
		$config = [
			'table' => '',
			'column' => $field,
			'primary_key' => $this->primaryKey,
			'ignore_value' => $this->data[$this->primaryKey] ?? null,
			'where' => [],
			'whereNot' => [],
		];

		if(is_string($param))
		{
			$config['table'] = $param;
			return $config;
		}

		if(is_array($param))
		{
			$config['table'] = $param['table'] ?? '';
			$config['column'] = $param['column'] ?? $field;
			$config['primary_key'] = $param['primary_key'] ?? $this->primaryKey;
			$config['where'] = isset($param['where']) && is_array($param['where']) ? $param['where'] : [];
			$config['whereNot'] = isset($param['whereNot']) && is_array($param['whereNot']) ? $param['whereNot'] : [];

			if(array_key_exists('ignore_value', $param))
			{
				$config['ignore_value'] = $param['ignore_value'];
			}
			elseif(!empty($param['ignore_from_data']))
			{
				$dataKey = (string)$param['ignore_from_data'];
				$config['ignore_value'] = $this->data[$dataKey] ?? null;
			}
		}

		return $config;
	}
}