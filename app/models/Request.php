<?php

/**
 * This file is part of the ThunderPHP Framework.
 * Contains the Request class that Handles page POST & GET requests.
 * 
 * @package ThunderPHP
 * @version 1.0.0
 * @author Eathorne Choongo <eathorne2012@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * 
 */

namespace Core;

defined('ROOTPATH') or die("Direct script access denied");

/**
 * Request class to handle URL requests in one place
 */
class Request
{
	protected $upload_max_size    = 20; // MB
	protected $upload_folder      = 'uploads';
	protected $upload_errors      = [];
	protected $upload_error_code  = 0;
	protected $file_prefix        = '';
	protected $upload_file_types  = [
		'image/jpeg',
		'image/png',
		'image/webp',
		'image/gif',
	];
	protected $upload_extensions  = [
		'jpg',
		'jpeg',
		'png',
		'webp',
		'gif',
	];
	protected $verify_images      = false;
	protected $image_min_width    = 0;
	protected $image_min_height   = 0;
	protected $image_max_width    = 0;
	protected $image_max_height   = 0;
	protected $field_rules        = [];
	protected $filename_callback  = null;

	/**
	 * Get the current HTTP request method.
	 */
	public function method(): string
	{
		return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
	}

	/**
	 * Determine if the current request is a POST request.
	 */
	public function posted(): bool
	{
		return $this->method() === 'POST';
	}

	/**
	 * Determine if the current request is a GET request.
	 */
	public function is_get(): bool
	{
		return $this->method() === 'GET';
	}

	/**
	 * Determine if the current request is an AJAX request.
	 */
	public function is_ajax(): bool
	{
		return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
	}

	/**
	 * Retrieve a single POST value or all POST data.
	 */
	public function post(string $key = '', mixed $default = ''): mixed
	{
		if($key === '')
			return $_POST;

		return array_key_exists($key, $_POST) ? $_POST[$key] : $default;
	}

	/**
	 * Retrieve a single POST value with a default fallback.
	 */
	public function input(string $key, mixed $default = ''): mixed
	{
		return $this->post($key, $default);
	}

	/**
	 * Retrieve a single GET value or all GET data.
	 */
	public function get(string $key = '', mixed $default = ''): mixed
	{
		if($key === '')
			return $_GET;

		return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
	}

	/**
	 * Retrieve a single FILES value or all uploaded files.
	 */
	public function files(string $key = ''): mixed
	{
		if($key === '')
			return $_FILES;

		return array_key_exists($key, $_FILES) ? $_FILES[$key] : [];
	}

	/**
	 * Retrieve a REQUEST value or all request data.
	 */
	public function all(string $key = '', mixed $default = ''): mixed
	{
		if($key === '')
			return $_REQUEST;

		return array_key_exists($key, $_REQUEST) ? $_REQUEST[$key] : $default;
	}

	/**
	 * Check whether a POST key exists.
	 */
	public function has_post(string $key): bool
	{
		return array_key_exists($key, $_POST);
	}

	/**
	 * Check whether a GET key exists.
	 */
	public function has_get(string $key): bool
	{
		return array_key_exists($key, $_GET);
	}

	/**
	 * Check whether a file input exists.
	 */
	public function has_file(string $key): bool
	{
		if(!array_key_exists($key, $_FILES))
			return false;

		$file = $_FILES[$key];

		if(!isset($file['name']))
			return false;

		if(is_array($file['name']))
		{
			foreach($file['name'] as $name)
			{
				if($name !== '')
					return true;
			}
			return false;
		}

		return $file['name'] !== '';
	}

	/**
	 * Get upload errors.
	 */
	public function get_upload_errors(): array
	{
		return $this->upload_errors;
	}

	/**
	 * Get upload error code.
	 */
	public function get_upload_error_code(): int
	{
		return (int)$this->upload_error_code;
	}

	/**
	 * Check whether upload errors exist.
	 */
	public function has_upload_errors(): bool
	{
		return !empty($this->upload_errors);
	}

	/**
	 * Clear upload errors.
	 */
	public function clear_upload_errors(): static
	{
		$this->upload_errors = [];
		$this->upload_error_code = 0;
		return $this;
	}

	/**
	 * Set global upload folder.
	 */
	public function set_upload_folder(string $folder): static
	{
		$this->upload_folder = trim($folder);
		return $this;
	}

	/**
	 * Set global upload max size in MB.
	 */
	public function set_upload_max_size(int|float $size_mb): static
	{
		$this->upload_max_size = max(0, (float)$size_mb);
		return $this;
	}

	/**
	 * Set global allowed MIME types.
	 */
	public function set_upload_file_types(array $types): static
	{
		$this->upload_file_types = array_values(array_unique(array_map('strtolower', $types)));
		return $this;
	}

	/**
	 * Set global allowed file extensions.
	 */
	public function set_upload_extensions(array $extensions): static
	{
		$extensions = array_map(function($item){
			return ltrim(strtolower(trim((string)$item)), '.');
		}, $extensions);

		$this->upload_extensions = array_values(array_unique(array_filter($extensions)));
		return $this;
	}

	/**
	 * Set filename prefix.
	 */
	public function set_file_prefix(string $prefix): static
	{
		$this->file_prefix = $prefix;
		return $this;
	}

	/**
	 * Enable or disable image verification.
	 */
	public function set_verify_images(bool $verify = true): static
	{
		$this->verify_images = $verify;
		return $this;
	}

	/**
	 * Set image dimension rules globally.
	 */
	public function set_image_dimensions(
		int $min_width = 0,
		int $min_height = 0,
		int $max_width = 0,
		int $max_height = 0
	): static
	{
		$this->image_min_width = max(0, $min_width);
		$this->image_min_height = max(0, $min_height);
		$this->image_max_width = max(0, $max_width);
		$this->image_max_height = max(0, $max_height);

		return $this;
	}

	/**
	 * Set a callback to generate filenames.
	 * Callback receives:
	 * ($original_name, $safe_name, $extension, $field_name, $file_info)
	 */
	public function set_filename_callback(?callable $callback): static
	{
		$this->filename_callback = $callback;
		return $this;
	}

	/**
	 * Set per-field upload rules.
	 *
	 * Supported keys:
	 * folder
	 * max_size
	 * mime_types
	 * extensions
	 * verify_image
	 * min_width
	 * min_height
	 * max_width
	 * max_height
	 * prefix
	 */
	public function set_upload_rule(string $field_name, array $rules): static
	{
		$this->field_rules[$field_name] = $rules;
		return $this;
	}

	/**
	 * Get per-field upload rule if present.
	 */
	public function get_upload_rule(string $field_name): array
	{
		return $this->field_rules[$field_name] ?? [];
	}

	/**
	 * Upload files and return uploaded file path(s).
	 *
	 * If a specific $key is given:
	 * - returns string for one uploaded file
	 * - returns array for multiple uploaded files from one input e.g files[]
	 *
	 * If no $key is given:
	 * - returns grouped array by field name
	 */
	public function upload_files(string $key = ''): string|array
	{
		$this->clear_upload_errors();

		$normalized_files = $this->normalize_files($key);

		if(empty($normalized_files))
			return $key !== '' ? '' : [];

		$uploaded_grouped = [];

		foreach($normalized_files as $file)
		{
			$rules = $this->resolve_rules($file['field_name']);
			$folder = $this->prepare_upload_folder($rules['folder']);

			if($folder === false)
				continue;

			$result = $this->process_single_upload($file, $folder, $rules, false);

			if($result['success'])
			{
				$uploaded_grouped[$file['field_name']][] = $result['path'];
			}
			else
			{
				$this->upload_errors[$file['field_name']][] = $result['error'];

				if(!empty($result['code']))
					$this->upload_error_code = $result['code'];
			}
		}

		if($key !== '')
		{
			$paths = $uploaded_grouped[$key] ?? [];

			if(count($paths) <= 1)
				return $paths[0] ?? '';

			return $paths;
		}

		return $uploaded_grouped;
	}

	/**
	 * Upload files and return detailed metadata.
	 */
	public function upload_files_detailed(string $key = ''): array
	{
		$this->clear_upload_errors();

		$normalized_files = $this->normalize_files($key);

		if(empty($normalized_files))
			return [];

		$results = [];

		foreach($normalized_files as $file)
		{
			$rules = $this->resolve_rules($file['field_name']);
			$folder = $this->prepare_upload_folder($rules['folder']);

			if($folder === false)
			{
				$results[] = [
					'success'       => false,
					'field_name'    => $file['field_name'],
					'original_name' => $file['name'],
					'error'         => "Upload folder is not available",
					'code'          => 1,
				];
				continue;
			}

			$result = $this->process_single_upload($file, $folder, $rules, true);

			if(!$result['success'])
			{
				$this->upload_errors[$file['field_name']][] = $result['error'];

				if(!empty($result['code']))
					$this->upload_error_code = $result['code'];
			}

			$results[] = $result;
		}

		return $results;
	}

	/**
	 * Normalize uploaded files into a flat structure.
	 */
	protected function normalize_files(string $key = ''): array
	{
		$source_files = $key !== '' ? [$key => $this->files($key)] : $this->files();
		$output = [];

		foreach($source_files as $field_name => $file)
		{
			if(empty($file) || !isset($file['name']))
				continue;

			if(is_array($file['name']))
			{
				$count = count($file['name']);

				for($i = 0; $i < $count; $i++)
				{
					$name = $file['name'][$i] ?? '';
					$tmp_name = $file['tmp_name'][$i] ?? '';
					$error = $file['error'][$i] ?? UPLOAD_ERR_NO_FILE;

					if($name === '' && $tmp_name === '' && $error === UPLOAD_ERR_NO_FILE)
						continue;

					$output[] = [
						'field_name' => $field_name,
						'name'       => $name,
						'type'       => $file['type'][$i] ?? '',
						'tmp_name'   => $tmp_name,
						'error'      => $error,
						'size'       => (int)($file['size'][$i] ?? 0),
					];
				}
			}
			else
			{
				$name = $file['name'] ?? '';
				$tmp_name = $file['tmp_name'] ?? '';
				$error = $file['error'] ?? UPLOAD_ERR_NO_FILE;

				if($name === '' && $tmp_name === '' && $error === UPLOAD_ERR_NO_FILE)
					continue;

				$output[] = [
					'field_name' => $field_name,
					'name'       => $name,
					'type'       => $file['type'] ?? '',
					'tmp_name'   => $tmp_name,
					'error'      => $error,
					'size'       => (int)($file['size'] ?? 0),
				];
			}
		}

		return $output;
	}

	/**
	 * Merge global rules with field rules.
	 */
	protected function resolve_rules(string $field_name): array
	{
		$field = $this->get_upload_rule($field_name);

		return [
			'folder'       => $field['folder'] ?? $this->upload_folder,
			'max_size'     => $field['max_size'] ?? $this->upload_max_size,
			'mime_types'   => $field['mime_types'] ?? $this->upload_file_types,
			'extensions'   => $field['extensions'] ?? $this->upload_extensions,
			'verify_image' => $field['verify_image'] ?? $this->verify_images,
			'min_width'    => $field['min_width'] ?? $this->image_min_width,
			'min_height'   => $field['min_height'] ?? $this->image_min_height,
			'max_width'    => $field['max_width'] ?? $this->image_max_width,
			'max_height'   => $field['max_height'] ?? $this->image_max_height,
			'prefix'       => $field['prefix'] ?? $this->file_prefix,
		];
	}

	/**
	 * Prepare upload folder if needed.
	 */
	protected function prepare_upload_folder(string $folder): string|false
	{
		$folder = rtrim(trim($folder), '/\\') . '/';

		if(!is_dir($folder))
		{
			if(!mkdir($folder, 0755, true) && !is_dir($folder))
			{
				$this->upload_errors['_system'][] = "Failed to create upload folder: {$folder}";
				$this->upload_error_code = 1;
				return false;
			}
		}

		if(!is_writable($folder))
		{
			$this->upload_errors['_system'][] = "Upload folder is not writable: {$folder}";
			$this->upload_error_code = 1;
			return false;
		}

		return $folder;
	}

	/**
	 * Validate and move a single uploaded file.
	 */
	protected function process_single_upload(array $file, string $folder, array $rules, bool $detailed = false): array
	{
		$error = $this->validate_uploaded_file($file, $rules);

		if($error !== null)
		{
			return [
				'success'       => false,
				'field_name'    => $file['field_name'],
				'original_name' => $file['name'],
				'error'         => $error['message'],
				'code'          => $error['code'],
			];
		}

		$mime_type = $this->detect_mime_type($file['tmp_name']);
		$image_info = $this->get_image_info($file['tmp_name']);

		$destination = $this->generate_destination(
			$folder,
			$file['name'],
			$file['field_name'],
			$file,
			$rules
		);

		if(!move_uploaded_file($file['tmp_name'], $destination))
		{
			return [
				'success'       => false,
				'field_name'    => $file['field_name'],
				'original_name' => $file['name'],
				'error'         => "Failed to move uploaded file: {$file['name']}",
				'code'          => 1,
			];
		}

		$result = [
			'success'       => true,
			'field_name'    => $file['field_name'],
			'original_name' => $file['name'],
			'path'          => $destination,
			'mime_type'     => $mime_type,
			'extension'     => $this->get_extension($file['name']),
			'size'          => (int)$file['size'],
			'size_text'     => $this->format_bytes((int)$file['size']),
		];

		if(!empty($image_info))
		{
			$result['width'] = $image_info['width'];
			$result['height'] = $image_info['height'];
		}

		return $result;
	}

	/**
	 * Validate one uploaded file.
	 */
	protected function validate_uploaded_file(array $file, array $rules): ?array
	{
		$error_code = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);

		if($error_code !== UPLOAD_ERR_OK)
		{
			return [
				'message' => $this->code_to_message($error_code, $file['name']),
				'code'    => $error_code,
			];
		}

		if(empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name']))
		{
			return [
				'message' => "Invalid uploaded file: {$file['name']}",
				'code'    => 1,
			];
		}

		$max_bytes = (int)$rules['max_size'] * 1024 * 1024;
		if((int)$file['size'] > $max_bytes)
		{
			return [
				'message' => "File too large: {$file['name']}. Max allowed is " . $this->format_bytes($max_bytes),
				'code'    => 1,
			];
		}

		$mime_type = strtolower($this->detect_mime_type($file['tmp_name']));
		$extension = $this->get_extension($file['name']);

		if(
			!in_array('any', $rules['mime_types'], true) &&
			!in_array($mime_type, array_map('strtolower', $rules['mime_types']), true)
		){
			return [
				'message' => "Invalid file type: {$file['name']}",
				'code'    => 1,
			];
		}

		if(
			!in_array('any', $rules['extensions'], true) &&
			!in_array($extension, array_map('strtolower', $rules['extensions']), true)
		){
			return [
				'message' => "Invalid file extension: {$file['name']}",
				'code'    => 1,
			];
		}

		if(!empty($rules['verify_image']))
		{
			$image_info = $this->get_image_info($file['tmp_name']);

			if(empty($image_info))
			{
				return [
					'message' => "The uploaded file is not a valid image: {$file['name']}",
					'code'    => 1,
				];
			}

			if(!empty($rules['min_width']) && $image_info['width'] < $rules['min_width'])
			{
				return [
					'message' => "Image width is too small: {$file['name']}",
					'code'    => 1,
				];
			}

			if(!empty($rules['min_height']) && $image_info['height'] < $rules['min_height'])
			{
				return [
					'message' => "Image height is too small: {$file['name']}",
					'code'    => 1,
				];
			}

			if(!empty($rules['max_width']) && $image_info['width'] > $rules['max_width'])
			{
				return [
					'message' => "Image width is too large: {$file['name']}",
					'code'    => 1,
				];
			}

			if(!empty($rules['max_height']) && $image_info['height'] > $rules['max_height'])
			{
				return [
					'message' => "Image height is too large: {$file['name']}",
					'code'    => 1,
				];
			}
		}

		return null;
	}

	/**
	 * Detect the real MIME type of the uploaded temporary file.
	 */
	protected function detect_mime_type(string $tmp_name): string
	{
		if(function_exists('finfo_open'))
		{
			$finfo = finfo_open(FILEINFO_MIME_TYPE);

			if($finfo)
			{
				$mime = finfo_file($finfo, $tmp_name);
				finfo_close($finfo);

				if($mime !== false && $mime !== null)
					return $mime;
			}
		}

		if(function_exists('mime_content_type'))
		{
			$mime = mime_content_type($tmp_name);
			if($mime !== false && $mime !== null)
				return $mime;
		}

		return 'application/octet-stream';
	}

	/**
	 * Get extension from filename.
	 */
	protected function get_extension(string $filename): string
	{
		return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
	}

	/**
	 * Get image info.
	 */
	protected function get_image_info(string $tmp_name): array
	{
		if(!function_exists('getimagesize'))
			return [];

		$info = @getimagesize($tmp_name);

		if($info === false || empty($info[0]) || empty($info[1]))
			return [];

		return [
			'width'  => (int)$info[0],
			'height' => (int)$info[1],
			'mime'   => $info['mime'] ?? '',
		];
	}

	/**
	 * Generate a safe destination path for the uploaded file.
	 */
	protected function generate_destination(
		string $folder,
		string $original_name,
		string $field_name,
		array $file_info,
		array $rules
	): string
	{
		$original_name = basename($original_name);
		$extension = $this->get_extension($original_name);
		$filename = pathinfo($original_name, PATHINFO_FILENAME);
		$safe_name = $this->sanitize_filename($filename);

		if($safe_name === '')
			$safe_name = 'file';

		$prefix = $rules['prefix'] ?? '';

		if(is_callable($this->filename_callback))
		{
			$custom_name = call_user_func(
				$this->filename_callback,
				$original_name,
				$safe_name,
				$extension,
				$field_name,
				$file_info
			);

			$custom_name = trim((string)$custom_name);

			if($custom_name !== '')
			{
				$custom_name = basename($custom_name);

				if(pathinfo($custom_name, PATHINFO_EXTENSION) === '' && $extension !== '')
					$custom_name .= '.' . $extension;

				return $folder . $custom_name;
			}
		}

		$random = bin2hex(random_bytes(8));
		$new_name = $prefix . $safe_name . '_' . $random;

		if($extension !== '')
			$new_name .= '.' . $extension;

		return $folder . $new_name;
	}

	/**
	 * Sanitize a filename.
	 */
	protected function sanitize_filename(string $filename): string
	{
		$filename = trim($filename);
		$filename = preg_replace('/[^a-zA-Z0-9\-_]+/', '_', $filename);
		$filename = preg_replace('/_+/', '_', $filename);
		$filename = trim($filename, '_-');

		return strtolower($filename);
	}

	/**
	 * Convert upload error code into a readable message.
	 */
	protected function code_to_message(int $code, string $filename = ''): string
	{
		$name = $filename !== '' ? " ({$filename})" : '';

		return match($code) {
			UPLOAD_ERR_INI_SIZE   => "The uploaded file exceeds the server upload limit{$name}",
			UPLOAD_ERR_FORM_SIZE  => "The uploaded file exceeds the form upload limit{$name}",
			UPLOAD_ERR_PARTIAL    => "The file was only partially uploaded{$name}",
			UPLOAD_ERR_NO_FILE    => "No file was uploaded{$name}",
			UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder{$name}",
			UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk{$name}",
			UPLOAD_ERR_EXTENSION  => "A PHP extension stopped the file upload{$name}",
			default               => "An error occurred while uploading the file{$name}",
		};
	}

	/**
	 * Format bytes into readable text.
	 */
	public function format_bytes(int $bytes, int $precision = 2): string
	{
		$units = ['B', 'KB', 'MB', 'GB', 'TB'];

		$bytes = max($bytes, 0);
		$pow = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
		$pow = min($pow, count($units) - 1);

		$bytes /= pow(1024, $pow);

		return round($bytes, $precision) . ' ' . $units[$pow];
	}

	/**
	 * Get the client IP address.
	 */
	public function ip(): string
	{
		return $_SERVER['REMOTE_ADDR'] ?? '';
	}

	/**
	 * Get the current request URI.
	 */
	public function uri(): string
	{
		return $_SERVER['REQUEST_URI'] ?? '';
	}

	/**
	 * Get one server variable or all server variables.
	 */
	public function server(string $key = '', mixed $default = ''): mixed
	{
		if($key === '')
			return $_SERVER;

		return array_key_exists($key, $_SERVER) ? $_SERVER[$key] : $default;
	}

	/**
	 * Get one request header if available.
	 */
	public function header(string $key, mixed $default = ''): mixed
	{
		$server_key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
		return $_SERVER[$server_key] ?? $default;
	}
}