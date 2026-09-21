<?php

/**
 * This file is part of the ThunderPHP Framework.
 * Used to resize, crop, contain, convert images, generate thumbnails,
 * process uploaded images, and output images
 * 
 * @package ThunderPHP
 * @version 1.0.0
 * @author Eathorne Choongo <eathorne2012@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * 
 */

namespace Core;

defined('ROOTPATH') or die("Direct script access denied");

class Image
{
	public string $error = '';

	protected int $jpeg_quality = 90;
	protected int $png_compression = 6;
	protected int $webp_quality = 90;
	protected bool $upscale = false;
	protected string $thumbnail_dir = '';
	protected bool $use_hashed_names = false;

	public function set_jpeg_quality(int $quality):self
	{
		$this->jpeg_quality = max(0, min(100, $quality));
		return $this;
	}

	public function set_png_compression(int $compression):self
	{
		$this->png_compression = max(0, min(9, $compression));
		return $this;
	}

	public function set_webp_quality(int $quality):self
	{
		$this->webp_quality = max(0, min(100, $quality));
		return $this;
	}

	public function allow_upscale(bool $upscale = true):self
	{
		$this->upscale = $upscale;
		return $this;
	}

	public function set_thumbnail_dir(string $dir):self
	{
		$this->thumbnail_dir = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR;
		return $this;
	}

	public function use_hashed_names(bool $use_hashed_names = true):self
	{
		$this->use_hashed_names = $use_hashed_names;
		return $this;
	}

	/**
	 * Build a standard result array
	 */
	public function result(bool $success, ?string $path = null, ?string $mime = null, ?int $width = null, ?int $height = null):array
	{
		$size = null;

		if($success && !empty($path) && is_file($path))
			$size = filesize($path) ?: null;

		return [
			'success' => $success,
			'path' => $path,
			'mime' => $mime,
			'width' => $width,
			'height' => $height,
			'size' => $size,
			'error' => $success ? '' : $this->error,
		];
	}

	/**
	 * Process an uploaded file array
	 * Expected keys: name, tmp_name, error, size, type
	 */
	public function from_upload(array $file, string $dest, bool $move = true):array
	{
		$this->error = '';

		if(empty($file['tmp_name']) || !is_file($file['tmp_name']))
		{
			$this->error = 'Invalid upload file';
			return $this->result(false);
		}

		if(isset($file['error']) && (int)$file['error'] !== 0)
		{
			$this->error = 'Upload error code: ' . (int)$file['error'];
			return $this->result(false);
		}

		$dir = dirname($dest);
		if(!$this->ensure_directory($dir))
			return $this->result(false);

		$ok = false;

		if($move && function_exists('is_uploaded_file') && is_uploaded_file($file['tmp_name']))
		{
			$ok = @move_uploaded_file($file['tmp_name'], $dest);
		}
		else
		{
			$ok = @copy($file['tmp_name'], $dest);
		}

		if(!$ok)
		{
			$this->error = 'Failed to store uploaded file';
			return $this->result(false);
		}

		$meta = $this->get_size($dest);

		return $this->result(
			true,
			$dest,
			$meta->mime ?? null,
			$meta->width ?? null,
			$meta->height ?? null
		);
	}

	/**
	 * Create an image file from binary string data
	 */
	public function from_string(string $data, string $dest):array
	{
		$this->error = '';

		if($data === '')
		{
			$this->error = 'Empty image data';
			return $this->result(false);
		}

		$dir = dirname($dest);
		if(!$this->ensure_directory($dir))
			return $this->result(false);

		if(@file_put_contents($dest, $data) === false)
		{
			$this->error = 'Failed to write image data';
			return $this->result(false);
		}

		$meta = $this->get_size($dest);
		if(!$meta)
		{
			@unlink($dest);
			$this->error = 'Written data is not a valid image';
			return $this->result(false);
		}

		return $this->result(true, $dest, $meta->mime, $meta->width, $meta->height);
	}

	/**
	 * Generic image processor
	 * mode: resize, crop, contain
	 */
	public function process(string $filename, string $mode = 'resize', array $options = []):array
	{
		$this->error = '';

		if(!is_file($filename))
		{
			$this->error = 'File not found';
			return $this->result(false);
		}

		$width = (int)($options['width'] ?? 700);
		$height = (int)($options['height'] ?? 700);
		$max_size = (int)($options['max_size'] ?? max($width, $height));
		$dest = $options['dest'] ?? null;
		$position = $options['position'] ?? 'center';
		$bg_color = $options['bg_color'] ?? [255,255,255];
		$output_ext = $options['output_ext'] ?? null;

		if(!empty($output_ext))
		{
			if(empty($dest))
			{
				$dest = $this->replace_extension($filename, $output_ext);
			}
			else
			{
				$dest = $this->replace_extension($dest, $output_ext);
			}
		}

		switch(strtolower($mode))
		{
			case 'crop':
				$path = $this->crop($filename, $width, $height, $dest, $position);
				break;

			case 'contain':
				$path = $this->fit_contain($filename, $width, $height, $dest, $bg_color);
				break;

			case 'resize':
			default:
				$path = $this->resize($filename, $max_size, $dest);
				break;
		}

		if(empty($path) || !is_file($path))
			return $this->result(false);

		$meta = $this->get_size($path);

		return $this->result(
			true,
			$path,
			$meta->mime ?? null,
			$meta->width ?? null,
			$meta->height ?? null
		);
	}

	public function resize(?string $filename, int $max_size = 700, ?string $dest = null):string
	{
		$this->error = '';

		if(empty($filename) || !is_file($filename))
			return (string)$filename;

		$info = $this->open_image($filename);
		if($info === false)
			return (string)$filename;

		$image = $this->fix_orientation($filename, $info['image'], $info['mime']);
		$type = $info['mime'];

		$src_w = imagesx($image);
		$src_h = imagesy($image);

		$canvas = $this->process_resize_resource($image, $src_w, $src_h, $type, $max_size);

		imagedestroy($image);

		if(!$canvas)
		{
			$this->error = 'Failed to process resize';
			return (string)$filename;
		}

		$dest = $dest ?: $filename;

		if(!$this->save_image($canvas, $dest, $type))
		{
			imagedestroy($canvas);
			$this->error = 'Failed to save resized image';
			return (string)$filename;
		}

		imagedestroy($canvas);
		return $dest;
	}

	public function crop(string $filename, int $max_width = 700, int $max_height = 700, ?string $dest = null, string $position = 'center'):string
	{
		$this->error = '';

		if(!is_file($filename))
			return $filename;

		$info = $this->open_image($filename);
		if($info === false)
			return $filename;

		$image = $this->fix_orientation($filename, $info['image'], $info['mime']);
		$type = $info['mime'];

		$src_w = imagesx($image);
		$src_h = imagesy($image);

		$canvas = $this->process_crop_resource($image, $src_w, $src_h, $type, $max_width, $max_height, $position);

		imagedestroy($image);

		if(!$canvas)
		{
			$this->error = 'Failed to process crop';
			return $filename;
		}

		$dest = $dest ?: $filename;

		if(!$this->save_image($canvas, $dest, $type))
		{
			imagedestroy($canvas);
			$this->error = 'Failed to save cropped image';
			return $filename;
		}

		imagedestroy($canvas);
		return $dest;
	}

	public function fit_contain(string $filename, int $max_width = 700, int $max_height = 700, ?string $dest = null, array $bg_color = [255,255,255]):string
	{
		$this->error = '';

		if(!is_file($filename))
			return $filename;

		$info = $this->open_image($filename);
		if($info === false)
			return $filename;

		$image = $this->fix_orientation($filename, $info['image'], $info['mime']);
		$type = $info['mime'];

		$src_w = imagesx($image);
		$src_h = imagesy($image);

		$canvas = $this->process_contain_resource($image, $src_w, $src_h, $type, $max_width, $max_height, $bg_color);

		imagedestroy($image);

		if(!$canvas)
		{
			$this->error = 'Failed to process contain';
			return $filename;
		}

		$dest = $dest ?: $filename;

		if(!$this->save_image($canvas, $dest, $type))
		{
			imagedestroy($canvas);
			$this->error = 'Failed to save contained image';
			return $filename;
		}

		imagedestroy($canvas);
		return $dest;
	}

	public function save_as(string $filename, string $dest):string
	{
		$this->error = '';

		if(!is_file($filename))
			return $filename;

		$info = $this->open_image($filename);
		if($info === false)
			return $filename;

		$image = $this->fix_orientation($filename, $info['image'], $info['mime']);
		$output_type = $this->mime_from_extension($dest);

		if(empty($output_type))
		{
			imagedestroy($image);
			$this->error = 'Unsupported destination format';
			return $filename;
		}

		if(!$this->save_image($image, $dest, $output_type))
		{
			imagedestroy($image);
			$this->error = 'Failed to save image';
			return $filename;
		}

		imagedestroy($image);
		return $dest;
	}

	public function convert_to_webp(string $filename, ?string $dest = null):string
	{
		$this->error = '';

		if(!is_file($filename))
			return $filename;

		$dest = $dest ?: $this->replace_extension($filename, 'webp');
		return $this->save_as($filename, $dest);
	}

	public function get_thumbnail(string $filename, int $width = 700, int $height = 700, bool $replace = false, string $mode = 'crop', array $bg_color = [255,255,255], ?string $output_ext = null):string
	{
		$this->error = '';

		if(!is_file($filename))
			return $filename;

		$dest = $this->build_thumbnail_name($filename, $width, $height, $mode, $output_ext);

		if(is_file($dest) && !$replace)
			return $dest;

		return $this->make_thumbnail($filename, $dest, $width, $height, $mode, $bg_color);
	}

	public function make_thumbnail(string $filename, string $dest, int $width = 700, int $height = 700, string $mode = 'crop', array $bg_color = [255,255,255]):string
	{
		$this->error = '';

		if(!is_file($filename))
			return $filename;

		switch(strtolower($mode))
		{
			case 'resize':
				return $this->resize($filename, max($width, $height), $dest);

			case 'contain':
				return $this->fit_contain($filename, $width, $height, $dest, $bg_color);

			case 'crop':
			default:
				return $this->crop($filename, $width, $height, $dest);
		}
	}

	public function make_thumbnail_as(string $filename, string $dest, int $width = 700, int $height = 700, string $mode = 'crop', array $bg_color = [255,255,255], string $position = 'center'):string
	{
		$this->error = '';

		if(!is_file($filename))
			return $filename;

		$info = $this->open_image($filename);
		if($info === false)
			return $filename;

		$image = $this->fix_orientation($filename, $info['image'], $info['mime']);
		$src_w = imagesx($image);
		$src_h = imagesy($image);

		$output_type = $this->mime_from_extension($dest);
		if(empty($output_type))
		{
			imagedestroy($image);
			$this->error = 'Unsupported destination format';
			return $filename;
		}

		switch(strtolower($mode))
		{
			case 'resize':
				$canvas = $this->process_resize_resource($image, $src_w, $src_h, $output_type, max($width, $height));
				break;

			case 'contain':
				$canvas = $this->process_contain_resource($image, $src_w, $src_h, $output_type, $width, $height, $bg_color);
				break;

			case 'crop':
			default:
				$canvas = $this->process_crop_resource($image, $src_w, $src_h, $output_type, $width, $height, $position);
				break;
		}

		imagedestroy($image);

		if(!$canvas)
		{
			$this->error = 'Failed to process thumbnail';
			return $filename;
		}

		if(!$this->save_image($canvas, $dest, $output_type))
		{
			imagedestroy($canvas);
			$this->error = 'Failed to save thumbnail';
			return $filename;
		}

		imagedestroy($canvas);
		return $dest;
	}

	/**
	 * Generate many thumbnails in one call
	 *
	 * Example specs:
	 * [
	 *   ['width'=>300,'height'=>300,'mode'=>'crop','ext'=>'webp'],
	 *   ['width'=>1200,'height'=>630,'mode'=>'crop','ext'=>'jpg'],
	 * ]
	 */
	public function smart_thumbnail(string $filename, array $specs = []):array
	{
		$this->error = '';
		$results = [];

		if(!is_file($filename))
		{
			$this->error = 'File not found';
			return [
				'success' => false,
				'items' => [],
				'error' => $this->error,
			];
		}

		foreach($specs as $i => $spec)
		{
			$width = (int)($spec['width'] ?? 700);
			$height = (int)($spec['height'] ?? 700);
			$mode = $spec['mode'] ?? 'crop';
			$ext = $spec['ext'] ?? null;
			$bg_color = $spec['bg_color'] ?? [255,255,255];
			$position = $spec['position'] ?? 'center';
			$replace = (bool)($spec['replace'] ?? true);
			$dest = $spec['dest'] ?? $this->build_thumbnail_name($filename, $width, $height, $mode, $ext);

			if(is_file($dest) && !$replace)
			{
				$meta = $this->get_size($dest);
				$results[] = $this->result(true, $dest, $meta->mime ?? null, $meta->width ?? null, $meta->height ?? null);
				continue;
			}

			if(!empty($ext))
			{
				$dest = $this->replace_extension($dest, $ext);
				$path = $this->make_thumbnail_as($filename, $dest, $width, $height, $mode, $bg_color, $position);
			}
			else
			{
				if($mode === 'contain')
					$path = $this->make_thumbnail($filename, $dest, $width, $height, $mode, $bg_color);
				else
					$path = $this->make_thumbnail($filename, $dest, $width, $height, $mode, $bg_color);
			}

			if(!empty($path) && is_file($path))
			{
				$meta = $this->get_size($path);
				$results[] = $this->result(true, $path, $meta->mime ?? null, $meta->width ?? null, $meta->height ?? null);
			}
			else
			{
				$results[] = $this->result(false);
			}
		}

		return [
			'success' => true,
			'items' => $results,
			'error' => '',
		];
	}

	/**
	 * Output an image directly to browser
	 * If $download_name is provided, sends as attachment
	 */
	public function output(string $filename, bool $exit_after = true, ?string $download_name = null):bool
	{
		$this->error = '';

		if(!is_file($filename))
		{
			$this->error = 'File not found';
			return false;
		}

		$meta = $this->get_size($filename);
		$mime = $meta->mime ?? 'application/octet-stream';

		if(headers_sent())
		{
			$this->error = 'Headers already sent';
			return false;
		}

		header('Content-Type: ' . $mime);
		header('Content-Length: ' . filesize($filename));

		if(!empty($download_name))
			header('Content-Disposition: attachment; filename="' . basename($download_name) . '"');
		else
			header('Content-Disposition: inline; filename="' . basename($filename) . '"');

		readfile($filename);

		if($exit_after)
			exit;

		return true;
	}

	public function delete_thumbnails(string $filename):void
	{
		$info = pathinfo($filename);
		$name = $info['filename'] ?? '';
		$dir = $this->thumbnail_dir ?: ($info['dirname'] ?? '');

		if(empty($name) || empty($dir))
			return;

		$dir = rtrim($dir, '/\\');
		$pattern = $dir . DIRECTORY_SEPARATOR . $name . '_*';

		foreach(glob($pattern) as $file)
		{
			if(is_file($file) && strpos(basename($file), $name . '_') === 0)
				@unlink($file);
		}
	}

	public function get_size(string $filename):object|false
	{
		if(!is_file($filename))
			return false;

		$info = @getimagesize($filename);
		if(!$info)
			return false;

		return (object)[
			'width' => $info[0] ?? 0,
			'height' => $info[1] ?? 0,
			'mime' => $info['mime'] ?? '',
		];
	}

	protected function build_thumbnail_name(string $filename, int $width, int $height, string $mode = 'crop', ?string $output_ext = null):string
	{
		$info = pathinfo($filename);

		$source_dir = $info['dirname'] ?? '.';
		$dir = !empty($this->thumbnail_dir) ? rtrim($this->thumbnail_dir, '/\\') : $source_dir;
		$name = $info['filename'] ?? 'image';
		$ext = $output_ext ?: ($info['extension'] ?? 'jpg');

		if($this->use_hashed_names)
		{
			$hash = sha1($filename . '|' . $width . '|' . $height . '|' . $mode . '|' . $ext);
			return $dir . DIRECTORY_SEPARATOR . $hash . '.' . ltrim($ext, '.');
		}

		return $dir . DIRECTORY_SEPARATOR . $name . '_' . $mode . '_' . $width . 'x' . $height . '.' . ltrim($ext, '.');
	}

	protected function open_image(string $filename):array|false
	{
		$info = @getimagesize($filename);

		if(!$info || empty($info['mime']))
		{
			$this->error = 'Invalid image file';
			return false;
		}

		$type = $info['mime'];

		switch($type)
		{
			case 'image/jpeg':
				$image = @imagecreatefromjpeg($filename);
				break;

			case 'image/png':
				$image = @imagecreatefrompng($filename);
				break;

			case 'image/gif':
				$image = @imagecreatefromgif($filename);
				break;

			case 'image/webp':
				$image = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($filename) : false;
				break;

			default:
				$this->error = 'Unsupported image type';
				return false;
		}

		if(!$image)
		{
			$this->error = 'Failed to open image';
			return false;
		}

		return [
			'image' => $image,
			'mime' => $type,
			'width' => $info[0],
			'height' => $info[1],
		];
	}

	protected function create_canvas(int $width, int $height, string $type, array $bg_color = [255,255,255])
	{
		$canvas = imagecreatetruecolor($width, $height);

		if(in_array($type, ['image/png', 'image/webp', 'image/gif']))
		{
			imagealphablending($canvas, false);
			imagesavealpha($canvas, true);

			if(count($bg_color) >= 4)
				$fill = imagecolorallocatealpha($canvas, $bg_color[0], $bg_color[1], $bg_color[2], $bg_color[3]);
			else
				$fill = imagecolorallocatealpha($canvas, 0, 0, 0, 127);

			imagefilledrectangle($canvas, 0, 0, $width, $height, $fill);

			if($type === 'image/gif')
				imagecolortransparent($canvas, $fill);
		}
		else
		{
			$r = $bg_color[0] ?? 255;
			$g = $bg_color[1] ?? 255;
			$b = $bg_color[2] ?? 255;
			$fill = imagecolorallocate($canvas, $r, $g, $b);
			imagefilledrectangle($canvas, 0, 0, $width, $height, $fill);
		}

		return $canvas;
	}

	protected function save_image($image, string $filename, string $type):bool
	{
		$dir = dirname($filename);

		if(!$this->ensure_directory($dir))
			return false;

		switch($type)
		{
			case 'image/jpeg':
				return imagejpeg($image, $filename, $this->jpeg_quality);

			case 'image/png':
				return imagepng($image, $filename, $this->png_compression);

			case 'image/gif':
				return imagegif($image, $filename);

			case 'image/webp':
				return function_exists('imagewebp') ? imagewebp($image, $filename, $this->webp_quality) : false;
		}

		$this->error = 'Unsupported save format';
		return false;
	}

	protected function ensure_directory(string $dir):bool
	{
		if(is_dir($dir))
			return true;

		if(@mkdir($dir, 0777, true))
			return true;

		if(is_dir($dir))
			return true;

		$this->error = 'Failed to create destination directory';
		return false;
	}

	protected function fix_orientation(string $filename, $image, string $type)
	{
		if($type !== 'image/jpeg' || !function_exists('exif_read_data'))
			return $image;

		$exif = @exif_read_data($filename);
		$orientation = $exif['Orientation'] ?? 1;

		switch($orientation)
		{
			case 2:
				if(function_exists('imageflip'))
					imageflip($image, IMG_FLIP_HORIZONTAL);
				break;

			case 3:
				$image = imagerotate($image, 180, 0);
				break;

			case 4:
				if(function_exists('imageflip'))
					imageflip($image, IMG_FLIP_VERTICAL);
				break;

			case 5:
				if(function_exists('imageflip'))
					imageflip($image, IMG_FLIP_VERTICAL);
				$image = imagerotate($image, -90, 0);
				break;

			case 6:
				$image = imagerotate($image, -90, 0);
				break;

			case 7:
				if(function_exists('imageflip'))
					imageflip($image, IMG_FLIP_HORIZONTAL);
				$image = imagerotate($image, -90, 0);
				break;

			case 8:
				$image = imagerotate($image, 90, 0);
				break;
		}

		return $image;
	}

	protected function get_crop_offset(int $source_size, int $crop_size, string $position, string $axis = 'x'):int
	{
		$diff = max(0, $source_size - $crop_size);
		$position = strtolower(trim($position));

		if($axis === 'x')
		{
			if($position === 'left')
				return 0;

			if($position === 'right')
				return $diff;
		}
		else
		{
			if($position === 'top')
				return 0;

			if($position === 'bottom')
				return $diff;
		}

		return (int)round($diff / 2);
	}

	protected function replace_extension(string $filename, string $new_ext):string
	{
		$info = pathinfo($filename);
		$dir = $info['dirname'] ?? '.';
		$name = $info['filename'] ?? 'image';

		return $dir . DIRECTORY_SEPARATOR . $name . '.' . ltrim($new_ext, '.');
	}

	protected function mime_from_extension(string $filename):string
	{
		$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

		return match($ext)
		{
			'jpg', 'jpeg' => 'image/jpeg',
			'png' => 'image/png',
			'gif' => 'image/gif',
			'webp' => 'image/webp',
			default => '',
		};
	}

	protected function process_resize_resource($image, int $src_w, int $src_h, string $type, int $max_size)
	{
		if($src_w < 1 || $src_h < 1)
			return false;

		if(!$this->upscale)
			$max_size = min($max_size, max($src_w, $src_h));

		if($src_w >= $src_h)
		{
			$dst_w = $max_size;
			$dst_h = (int)round(($src_h / $src_w) * $max_size);
		}
		else
		{
			$dst_h = $max_size;
			$dst_w = (int)round(($src_w / $src_h) * $max_size);
		}

		$dst_w = max(1, $dst_w);
		$dst_h = max(1, $dst_h);

		$canvas = $this->create_canvas($dst_w, $dst_h, $type);
		imagecopyresampled($canvas, $image, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);

		return $canvas;
	}

	protected function process_crop_resource($image, int $src_w, int $src_h, string $type, int $max_width, int $max_height, string $position = 'center')
	{
		if($src_w < 1 || $src_h < 1 || $max_width < 1 || $max_height < 1)
			return false;

		if(!$this->upscale && ($src_w < $max_width || $src_h < $max_height))
		{
			$ratio = min($src_w / $max_width, $src_h / $max_height);
			$ratio = min(1, $ratio);

			$max_width = max(1, (int)floor($max_width * $ratio));
			$max_height = max(1, (int)floor($max_height * $ratio));
		}

		$src_ratio = $src_w / $src_h;
		$dst_ratio = $max_width / $max_height;

		$crop_x = 0;
		$crop_y = 0;
		$crop_w = $src_w;
		$crop_h = $src_h;

		if($src_ratio > $dst_ratio)
		{
			$crop_w = (int)round($src_h * $dst_ratio);
			$crop_x = $this->get_crop_offset($src_w, $crop_w, $position, 'x');
		}
		else
		{
			$crop_h = (int)round($src_w / $dst_ratio);
			$crop_y = $this->get_crop_offset($src_h, $crop_h, $position, 'y');
		}

		$canvas = $this->create_canvas($max_width, $max_height, $type);
		imagecopyresampled($canvas, $image, 0, 0, $crop_x, $crop_y, $max_width, $max_height, $crop_w, $crop_h);

		return $canvas;
	}

	protected function process_contain_resource($image, int $src_w, int $src_h, string $type, int $max_width, int $max_height, array $bg_color = [255,255,255])
	{
		if($src_w < 1 || $src_h < 1 || $max_width < 1 || $max_height < 1)
			return false;

		$ratio = min($max_width / $src_w, $max_height / $src_h);

		if(!$this->upscale)
			$ratio = min(1, $ratio);

		$dst_w = max(1, (int)round($src_w * $ratio));
		$dst_h = max(1, (int)round($src_h * $ratio));

		$offset_x = (int)floor(($max_width - $dst_w) / 2);
		$offset_y = (int)floor(($max_height - $dst_h) / 2);

		$canvas = $this->create_canvas($max_width, $max_height, $type, $bg_color);
		imagecopyresampled($canvas, $image, $offset_x, $offset_y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);

		return $canvas;
	}
}
