<?php

/**
 * This file is part of the ThunderPHP Framework.
 * It contains the Session class to handle user session data
 * 
 * @package ThunderPHP
 * @version 1.2.0
 * @author Eathorne Choongo <eathorne2012@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * 
 */

namespace Core;

defined('ROOTPATH') or die("Direct script access denied");

/**
 * Session class makes it easier to read & write session data
 */
class Session
{
	private string $root_key = 'THUNDER';
	private string $var_key = 'APP';
	private string $user_key = 'USER';
	private string $flash_key = 'FLASH';
	private string $old_key = 'OLD';

	private function start_session(): void
	{
		if(session_status() === PHP_SESSION_NONE){
			session_start();
		}

		if(!isset($_SESSION[$this->root_key]) || !is_array($_SESSION[$this->root_key])){
			$_SESSION[$this->root_key] = [];
		}

		if(!isset($_SESSION[$this->root_key][$this->var_key]) || !is_array($_SESSION[$this->root_key][$this->var_key])){
			$_SESSION[$this->root_key][$this->var_key] = [];
		}

		if(!isset($_SESSION[$this->root_key][$this->flash_key]) || !is_array($_SESSION[$this->root_key][$this->flash_key])){
			$_SESSION[$this->root_key][$this->flash_key] = [];
		}

		if(!isset($_SESSION[$this->root_key][$this->old_key]) || !is_array($_SESSION[$this->root_key][$this->old_key])){
			$_SESSION[$this->root_key][$this->old_key] = [];
		}
	}

	private function key_parts(string $key): array
	{
		$key = trim($key, '.');

		if($key === ''){
			return [];
		}

		return explode('.', $key);
	}

	private function data_get(array $source, string $key, mixed $default = null): mixed
	{
		$parts = $this->key_parts($key);

		if(empty($parts)){
			return $source;
		}

		$current = $source;

		foreach($parts as $part){
			if(is_array($current) && array_key_exists($part, $current)){
				$current = $current[$part];
				continue;
			}

			return $default;
		}

		return $current;
	}

	private function data_has(array $source, string $key): bool
	{
		$parts = $this->key_parts($key);

		if(empty($parts)){
			return false;
		}

		$current = $source;

		foreach($parts as $part){
			if(is_array($current) && array_key_exists($part, $current)){
				$current = $current[$part];
				continue;
			}

			return false;
		}

		return true;
	}

	private function data_set(array &$source, string $key, mixed $value): void
	{
		$parts = $this->key_parts($key);

		if(empty($parts)){
			return;
		}

		$current = &$source;

		foreach($parts as $index => $part){
			$is_last = $index === array_key_last($parts);

			if($is_last){
				$current[$part] = $value;
				return;
			}

			if(!isset($current[$part]) || !is_array($current[$part])){
				$current[$part] = [];
			}

			$current = &$current[$part];
		}
	}

	private function data_remove(array &$source, string $key): bool
	{
		$parts = $this->key_parts($key);

		if(empty($parts)){
			return false;
		}

		$current = &$source;

		foreach($parts as $index => $part){
			$is_last = $index === array_key_last($parts);

			if($is_last){
				if(is_array($current) && array_key_exists($part, $current)){
					unset($current[$part]);
					return true;
				}

				return false;
			}

			if(!isset($current[$part]) || !is_array($current[$part])){
				return false;
			}

			$current = &$current[$part];
		}

		return false;
	}

	public function set(string|array $key_or_array, mixed $value = null): bool
	{
		$this->start_session();

		if(is_array($key_or_array)){
			foreach($key_or_array as $key => $item){
				$this->data_set($_SESSION[$this->root_key][$this->var_key], (string)$key, $item);
			}
			return true;
		}

		$this->data_set($_SESSION[$this->root_key][$this->var_key], $key_or_array, $value);
		return true;
	}

	public function get(string $key, mixed $default = null): mixed
	{
		$this->start_session();
		return $this->data_get($_SESSION[$this->root_key][$this->var_key], $key, $default);
	}

	public function has(string $key): bool
	{
		$this->start_session();
		return $this->data_has($_SESSION[$this->root_key][$this->var_key], $key);
	}

	public function remove(string $key): bool
	{
		$this->start_session();
		return $this->data_remove($_SESSION[$this->root_key][$this->var_key], $key);
	}

	public function forget(string $key): bool
	{
		return $this->remove($key);
	}

	public function pop(string $key, mixed $default = null): mixed
	{
		$this->start_session();

		$value = $this->get($key, $default);
		$this->remove($key);

		return $value;
	}

	public function push(string $key, mixed $value): bool
	{
		$this->start_session();

		$current = $this->get($key, []);

		if(!is_array($current)){
			$current = [];
		}

		$current[] = $value;
		$this->set($key, $current);

		return true;
	}

	public function prepend(string $key, mixed $value): bool
	{
		$this->start_session();

		$current = $this->get($key, []);

		if(!is_array($current)){
			$current = [];
		}

		array_unshift($current, $value);
		$this->set($key, $current);

		return true;
	}

	public function all(): array
	{
		$this->start_session();
		return $_SESSION[$this->root_key][$this->var_key];
	}

	public function clear(): bool
	{
		$this->start_session();
		$_SESSION[$this->root_key][$this->var_key] = [];
		return true;
	}

	public function auth(object|array $row, bool $regenerate_id = false): bool
	{
		$this->start_session();

		if($regenerate_id){
			$this->regenerate();
		}

		$_SESSION[$this->root_key][$this->user_key] = $row;
		return true;
	}

	public function login(object|array $row, bool $regenerate_id = false): bool
	{
		return $this->auth($row, $regenerate_id);
	}

	public function logout(bool $regenerate_id = false): bool
	{
		$this->start_session();

		if(isset($_SESSION[$this->root_key][$this->user_key])){
			unset($_SESSION[$this->root_key][$this->user_key]);
		}

		if($regenerate_id){
			$this->regenerate();
		}

		return true;
	}

	public function is_logged_in(): bool
	{
		$this->start_session();

		if(!isset($_SESSION[$this->root_key][$this->user_key])){
			return false;
		}

		if(is_object($_SESSION[$this->root_key][$this->user_key])){
			return true;
		}

		if(is_array($_SESSION[$this->root_key][$this->user_key])){
			return true;
		}

		return false;
	}

	public function user(string $key = '', mixed $default = null): mixed
	{
		$this->start_session();

		if(!isset($_SESSION[$this->root_key][$this->user_key])){
			return $default;
		}

		$user = $_SESSION[$this->root_key][$this->user_key];

		if($key === ''){
			return $user;
		}

		if(is_object($user) && property_exists($user, $key)){
			return $user->$key;
		}

		if(is_array($user) && array_key_exists($key, $user)){
			return $user[$key];
		}

		return $default;
	}

	public function has_user(): bool
	{
		return $this->is_logged_in();
	}

	public function is_admin(): bool
	{
		if(!$this->is_logged_in()){
			return false;
		}

		$arr = do_filter('before_check_admin', ['is_admin' => false]);

		if(!empty($arr['is_admin'])){
			return true;
		}

		return false;
	}

	public function regenerate(bool $delete_old_session = true): bool
	{
		$this->start_session();
		return session_regenerate_id($delete_old_session);
	}

	public function invalidate(): bool
	{
		$this->start_session();

		$_SESSION = [];

		if(ini_get('session.use_cookies')){
			$params = session_get_cookie_params();

			setcookie(
				session_name(),
				'',
				time() - 42000,
				$params['path'],
				$params['domain'],
				$params['secure'],
				$params['httponly']
			);
		}

		session_destroy();
		return true;
	}

	public function reset(): bool
	{
		return $this->invalidate();
	}

	public function flash(string|array $key_or_array, mixed $value = null): bool
	{
		$this->start_session();

		if(is_array($key_or_array)){
			foreach($key_or_array as $key => $item){
				$this->data_set($_SESSION[$this->root_key][$this->flash_key], (string)$key, $item);
			}
			return true;
		}

		$this->data_set($_SESSION[$this->root_key][$this->flash_key], $key_or_array, $value);
		return true;
	}

	public function get_flash(string $key, mixed $default = null): mixed
	{
		$this->start_session();

		$value = $this->data_get($_SESSION[$this->root_key][$this->flash_key], $key, $default);
		$this->data_remove($_SESSION[$this->root_key][$this->flash_key], $key);

		return $value;
	}

	public function has_flash(string $key): bool
	{
		$this->start_session();
		return $this->data_has($_SESSION[$this->root_key][$this->flash_key], $key);
	}

	public function remove_flash(string $key): bool
	{
		$this->start_session();
		return $this->data_remove($_SESSION[$this->root_key][$this->flash_key], $key);
	}

	public function all_flash(): array
	{
		$this->start_session();
		return $_SESSION[$this->root_key][$this->flash_key];
	}

	public function clear_flash(): bool
	{
		$this->start_session();
		$_SESSION[$this->root_key][$this->flash_key] = [];
		return true;
	}

	public function old(string|array $key_or_array, mixed $value = null): mixed
	{
		$this->start_session();

		if(is_array($key_or_array)){
			foreach($key_or_array as $key => $item){
				$this->data_set($_SESSION[$this->root_key][$this->old_key], (string)$key, $item);
			}
			return true;
		}

		if(func_num_args() > 1){
			$this->data_set($_SESSION[$this->root_key][$this->old_key], $key_or_array, $value);
			return true;
		}

		return $this->data_get($_SESSION[$this->root_key][$this->old_key], $key_or_array);
	}

	public function set_old(string|array $key_or_array, mixed $value = null): bool
	{
		$this->start_session();

		if(is_array($key_or_array)){
			foreach($key_or_array as $key => $item){
				$this->data_set($_SESSION[$this->root_key][$this->old_key], (string)$key, $item);
			}
			return true;
		}

		$this->data_set($_SESSION[$this->root_key][$this->old_key], $key_or_array, $value);
		return true;
	}

	public function get_old(string $key, mixed $default = null): mixed
	{
		$this->start_session();
		return $this->data_get($_SESSION[$this->root_key][$this->old_key], $key, $default);
	}

	public function has_old(string $key): bool
	{
		$this->start_session();
		return $this->data_has($_SESSION[$this->root_key][$this->old_key], $key);
	}

	public function remove_old(string $key): bool
	{
		$this->start_session();
		return $this->data_remove($_SESSION[$this->root_key][$this->old_key], $key);
	}

	public function clear_old(): bool
	{
		$this->start_session();
		$_SESSION[$this->root_key][$this->old_key] = [];
		return true;
	}

	public function pull_old(string $key, mixed $default = null): mixed
	{
		$this->start_session();

		$value = $this->get_old($key, $default);
		$this->remove_old($key);

		return $value;
	}

	public function reflash(): bool
	{
		$this->start_session();
		return true;
	}

	public function keep_flash(string|array $keys): bool
	{
		$this->start_session();
		return true;
	}

	public function increment(string $key, int|float $amount = 1): int|float
	{
		$this->start_session();

		$current = $this->get($key, 0);

		if(!is_numeric($current)){
			$current = 0;
		}

		$current += $amount;
		$this->set($key, $current);

		return $current;
	}

	public function decrement(string $key, int|float $amount = 1): int|float
	{
		return $this->increment($key, -$amount);
	}

	public function pull_user(): mixed
	{
		$this->start_session();

		if(isset($_SESSION[$this->root_key][$this->user_key])){
			$user = $_SESSION[$this->root_key][$this->user_key];
			unset($_SESSION[$this->root_key][$this->user_key]);
			return $user;
		}

		return null;
	}

	public function raw(): array
	{
		$this->start_session();
		return $_SESSION[$this->root_key] ?? [];
	}

	public function message(string $type, string $message = '', bool $erase = false): mixed
	{
		$this->start_session();

		if($message !== ''){
			$this->flash($type, $message);
			return true;
		}

		if($erase){
			return $this->get_flash($type);
		}

		return $this->data_get($_SESSION[$this->root_key][$this->flash_key], $type);
	}
}