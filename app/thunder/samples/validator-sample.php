<?php

/**
 * This file is part of the ThunderPHP Framework.
 * 
 * @package ThunderPHP
 * @author Your Name <youremail@email.com>
 * @version 1.0.0
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * 
 */

namespace {NAMESPACE};
use \Core\Validator;

defined('ROOTPATH') or die("Direct script access denied");

/**
 * {CLASS_NAME} class
 */
class {CLASS_NAME} extends Validator
{

	protected function validate_phone(string $field, string $value, ?string $param, array $meta)
	{
		if(!preg_match("/[0-9]{10,12}/", $value))
			$this->errors[$field][] = empty($meta['error_message']) ? $meta['input_name']." must be a valid phone number":$meta['error_message'];
	}

	/* --add more validation functions here-- */
	
}