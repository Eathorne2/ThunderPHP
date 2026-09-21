<?php

namespace ThunderConfig\Controllers;

use Core\Request;
use Core\Session;

if(!defined('ROOT')) exit('No direct script access allowed');

class ConfigController
{
    protected Request $request;
    protected Session $session;

    protected array $allowed = [
        'USE_SESSIONS' => 'bool',
        'DEBUG' => 'bool',
        'APP_NAME' => 'string',
        'APP_DESCRIPTION' => 'string',
        'APP_LOGO' => 'string',
        'LOCAL_ROOT' => 'string',
        'REMOTE_ROOT' => 'string',
        'LOCAL_DB_NAME' => 'string',
        'LOCAL_DB_USER' => 'string',
        'LOCAL_DB_PASSWORD' => 'string',
        'LOCAL_DB_HOST' => 'string',
        'LOCAL_DB_DRIVER' => 'string',
        'LOCAL_DB_PORT' => 'string',
        'REMOTE_DB_NAME' => 'string',
        'REMOTE_DB_USER' => 'string',
        'REMOTE_DB_PASSWORD' => 'string',
        'REMOTE_DB_HOST' => 'string',
        'REMOTE_DB_DRIVER' => 'string',
        'REMOTE_DB_PORT' => 'string',
    ];

    public function __construct()
    {
        $this->request = new Request();
        $this->session = new Session();
    }

    public function get_vals()
    {
        return $this->read_config_values();
    }

    public function handle(): void
    {
        $this->require_access();

        if(URL(2) === 'upload-logo')
        {
            $this->upload_logo();
            return;
        }

        if($this->request->posted())
        {
            $this->save();
            return;
        }

        $this->prepare_view();
    }

    protected function require_access(): void
    {
        if(method_exists($this->session, 'is_logged_in') && !$this->session->is_logged_in())
        {
            redirect(('login'));
        }

        if(function_exists('user_can') && !user_can('manage-config'))
        {
            message('fail', 'You do not have permission to manage configuration.');
            redirect(('admin'));
        }
    }

    protected function prepare_view(): void
    {
        set_value([
            'thunder_admin_title' => 'Configuration',
            'thunder_config_values' => $this->read_config_values(),
            'thunder_config_path' => $this->config_path(),
            'thunder_config_writable' => is_writable($this->config_path()),
        ]);
    }

    protected function save(): void
    {
        $post = $this->request->post();

        if(function_exists('csrf_verify') && !csrf_verify($post))
        {
            message('fail', 'Invalid security token. Please try again.');
            redirect(('admin/thunder-config'));
        }

        $values = [];

        foreach($this->allowed as $key => $type)
        {
            if($type === 'bool')
            {
                $values[$key] = !empty($post[$key]);
                continue;
            }

            $values[$key] = $this->clean_string($post[$key] ?? '');
        }

        $result = $this->write_config_values($values);

        if($result['success'])
        {
            message('success', 'Configuration saved.');
        }
        else
        {
            message('fail', $result['message']);
        }

        redirect(('admin/thunder-config'));
    }

    protected function upload_logo(): void
    {
        header('Content-Type: application/json');

        $post = $this->request->post();

        if(function_exists('csrf_verify') && !csrf_verify($post))
        {
            echo json_encode(['success' => false, 'message' => 'Invalid security token.']);
            exit;
        }

        if(empty($_FILES['logo']) || !is_array($_FILES['logo']))
        {
            echo json_encode(['success' => false, 'message' => 'No logo file was uploaded.']);
            exit;
        }

        $file = $_FILES['logo'];

        if(($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK)
        {
            echo json_encode(['success' => false, 'message' => 'Upload failed. Error code: ' . (int)$file['error']]);
            exit;
        }

        $tmp = $file['tmp_name'] ?? '';
        $info = @getimagesize($tmp);

        if(empty($info) || empty($info['mime']))
        {
            echo json_encode(['success' => false, 'message' => 'The uploaded file is not a valid image.']);
            exit;
        }

        $mimeMap = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        ];

        if(empty($mimeMap[$info['mime']]))
        {
            echo json_encode(['success' => false, 'message' => 'Only JPG, PNG, GIF, and WEBP images are allowed.']);
            exit;
        }

        $ext = $mimeMap[$info['mime']];
        $dirInfo = $this->logo_directory();

        if(!is_dir($dirInfo['path']))
        {
            @mkdir($dirInfo['path'], 0755, true);
        }

        if(!is_writable($dirInfo['path']))
        {
            echo json_encode(['success' => false, 'message' => 'Logo upload directory is not writable: ' . $dirInfo['path']]);
            exit;
        }

        $filename = 'app-logo-' . date('YmdHis') . '.' . $ext;
        $dest = rtrim($dirInfo['path'], '/\\') . DIRECTORY_SEPARATOR . $filename;

        if(!move_uploaded_file($tmp, $dest))
        {
            echo json_encode(['success' => false, 'message' => 'Could not move uploaded logo.']);
            exit;
        }

        $this->resize_logo($dest, 512);

        $webPath = rtrim($dirInfo['url'], '/') . '/' . $filename;

        echo json_encode([
            'success' => true,
            'message' => 'Logo uploaded successfully.',
            'logo' => $webPath,
        ]);
        exit;
    }

    protected function read_config_values(): array
    {
        $content = @file_get_contents($this->config_path());
        $values = [];

        foreach($this->allowed as $key => $type)
        {
            $values[$key] = defined($key) ? constant($key) : ($type === 'bool' ? false : '');
        }

        if($content === false)
        {
            return $values;
        }

        foreach($this->allowed as $key => $type)
        {
            if(preg_match('/define\s*\(\s*[\'\"]' . preg_quote($key, '/') . '[\'\"]\s*,\s*(.*?)\s*\)\s*;/s', $content, $match))
            {
                $values[$key] = $this->parse_php_value($match[1], $type);
            }
        }

        return $values;
    }

    protected function write_config_values(array $values): array
    {

        $path = $this->config_path();

        if(!file_exists($path))
        {
            return ['success' => false, 'message' => 'Config file was not found: ' . $path];
        }

        if(!is_writable($path))
        {
            return ['success' => false, 'message' => 'Config file is not writable: ' . $path];
        }

        $content = file_get_contents($path);

        if($content === false)
        {
            return ['success' => false, 'message' => 'Could not read config file.'];
        }

        $newContent = $content;

        foreach($this->allowed as $key => $type)
        {
            if(!array_key_exists($key, $values))
            {
                continue;
            }

            $phpValue = $this->to_php_value($values[$key], $type);
            $pattern = '/define\s*\(\s*([\'\"])' . preg_quote($key, '/') . '\1\s*,\s*(.*?)\s*\)\s*;/s';
            $replacement = "define('" . $key . "', " . $phpValue . ");";

            if(preg_match($pattern, $newContent))
            {
                $newContent = preg_replace($pattern, $replacement, $newContent, 1);
            }
            else
            {
                $newContent .= "\n" . $replacement . "\n";
            }
        }

        if($newContent === $content)
        {
            return ['success' => true, 'message' => 'No changes were made.'];
        }

        $backup = $path . '.bak-' . date('Ymd-His');
        @copy($path, $backup);

        $written = file_put_contents($path, $newContent, LOCK_EX);

        if($written === false)
        {
            return ['success' => false, 'message' => 'Could not write changes to config file.'];
        }

        return ['success' => true, 'message' => 'Configuration saved.'];
    }

    protected function parse_php_value(string $value, string $type)
    {
        $value = trim($value);

        if($type === 'bool')
        {
            return strtolower($value) === 'true' || $value === '1';
        }

        if((str_starts_with($value, "'") && str_ends_with($value, "'")) || (str_starts_with($value, '"') && str_ends_with($value, '"')))
        {
            $inner = substr($value, 1, -1);
            return stripcslashes($inner);
        }

        return trim($value, "'\"");
    }

    protected function to_php_value($value, string $type): string
    {
        if($type === 'bool')
        {
            return $value ? 'true' : 'false';
        }

        return var_export($this->clean_string((string)$value), true);
    }

    protected function clean_string(string $value): string
    {
        $value = trim($value);
        $value = str_replace(["\0"], '', $value);
        return $value;
    }

    protected function config_path(): string
    {
        $path = defined('ROOTPATH') ? rtrim(ROOTPATH, '/\\') . DIRECTORY_SEPARATOR . 'config.php' : dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'config.php';
 
        $path = do_filter('thunder_config_config_path', $path);
 
        return $path;
    }

    protected function logo_directory(): array
    {
        $root = defined('ROOTPATH') ? rtrim(ROOTPATH, '/\\') . DIRECTORY_SEPARATOR : dirname(__DIR__, 3) . DIRECTORY_SEPARATOR;

        $publicDir = $root . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images';
        $plainDir = $root . 'assets' . DIRECTORY_SEPARATOR . 'images';

        if(is_dir($plainDir))
        {
            return ['path' => $plainDir, 'url' => '/assets/images'];
        }

        return ['path' => $publicDir, 'url' => '/assets/images'];
    }

    protected function resize_logo(string $path, int $max): void
    {
        if(class_exists('\\Core\\Image'))
        {
            try {
                $image = new \Core\Image();

                if(method_exists($image, 'allow_upscale'))
                {
                    $image->allow_upscale(false);
                }

                if(method_exists($image, 'resize'))
                {
                    $image->resize($path, $max);
                    return;
                }
            } catch(\Throwable $e) {
                // Fall back to GD below.
            }
        }

        $this->resize_with_gd($path, $max);
    }

    protected function resize_with_gd(string $path, int $max): void
    {
        $info = @getimagesize($path);

        if(empty($info[0]) || empty($info[1]) || empty($info['mime']))
        {
            return;
        }

        $width = (int)$info[0];
        $height = (int)$info[1];

        if($width <= $max && $height <= $max)
        {
            return;
        }

        $ratio = min($max / $width, $max / $height);
        $newWidth = max(1, (int)round($width * $ratio));
        $newHeight = max(1, (int)round($height * $ratio));

        switch($info['mime'])
        {
            case 'image/jpeg':
                $src = imagecreatefromjpeg($path);
                break;
            case 'image/png':
                $src = imagecreatefrompng($path);
                break;
            case 'image/gif':
                $src = imagecreatefromgif($path);
                break;
            case 'image/webp':
                if(!function_exists('imagecreatefromwebp')) return;
                $src = imagecreatefromwebp($path);
                break;
            default:
                return;
        }

        if(empty($src))
        {
            return;
        }

        $dst = imagecreatetruecolor($newWidth, $newHeight);

        if(in_array($info['mime'], ['image/png', 'image/gif', 'image/webp'], true))
        {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefilledrectangle($dst, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        switch($info['mime'])
        {
            case 'image/jpeg':
                imagejpeg($dst, $path, 85);
                break;
            case 'image/png':
                imagepng($dst, $path, 7);
                break;
            case 'image/gif':
                imagegif($dst, $path);
                break;
            case 'image/webp':
                if(function_exists('imagewebp')) imagewebp($dst, $path, 82);
                break;
        }

        imagedestroy($src);
        imagedestroy($dst);
    }
}
