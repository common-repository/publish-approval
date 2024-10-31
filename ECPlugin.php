<?php

namespace EC_PublishApproval;

class ECPlugin
{
	const OPTION_NAME = 'evidcube_pubapp_meta';

	const KEY_ACTIVATED = '_activated';
	const KEY_SUCCESSES = '_successes';
	const KEY_ERRORS = '_errors';

	private static $option;

	public static function printNotifications($notifications)
	{
		foreach ($notifications[0] as $text) {
			echo "<div class=\"notice notice-success is-dismissible\"><p>{$text}</p></div>";
		}
		foreach ($notifications[1] as $text) {
			echo "<div class=\"notice notice-error is-dismissible\"><p>{$text}</p></div>";
		}
	}

	public static function triggerActivation()
	{
		$option = self::getOption();
		$option[self::KEY_ACTIVATED] = 1;
		self::updateOption($option);
	}

	private static function getOption()
	{
		if (self::$option === null) {
			self::$option = unserialize(get_option(self::OPTION_NAME, serialize([])));
			self::$option = is_array(self::$option) ? self::$option : [];
		}

		return self::$option;
	}

	private static function updateOption($value)
	{
		self::$option = $value;
		update_option(self::OPTION_NAME, serialize($value));
	}

	public static function wasJustActivated()
	{
		$option = self::getOption();

		$wasActive = !empty($option[self::KEY_ACTIVATED]);

		$option[self::KEY_ACTIVATED] = null;
		self::updateOption($option);

		return $wasActive;
	}

	public static function registerMenu($page_title, $menu_title, $capability, $menu_slug, $template_path)
	{
		add_options_page($page_title, $menu_title, $capability, $menu_slug, function () use ($template_path) {
			include $template_path;
		});
	}
}

class EcPluginNotifications
{
	private static $successess = [];
	private static $warnings = [];
	private static $errors = [];

	public static function addSuccess($success)
	{
		self::$successess[] = $success;
	}

	public static function addWarning($warning)
	{
		self::$warnings[] = $warning;
	}

	public static function addError($error)
	{
		self::$errors[] = $error;
	}

	public static function getSuccesses()
	{
		return self::$successess;
	}

	public static function getWarnings()
	{
		return self::$warnings;
	}

	public static function getErrors()
	{
		return self::$errors;
	}

	public static function printNotifications()
	{
		foreach (self::$successess as $text) {
			echo "<div class=\"notice notice-success is-dismissible\"><p>{$text}</p></div>";
		}
		foreach (self::$warnings as $text) {
			echo "<div class=\"notice notice-warning is-dismissible\"><p>{$text}</p></div>";
		}
		foreach (self::$errors as $text) {
			echo "<div class=\"notice notice-error is-dismissible\"><p>{$text}</p></div>";
		}
	}

	public static function initialize()
	{
		add_action('current_screen', [self::class, 'loadNotifications']);
		add_action('activated_plugin', [self::class, 'storeNotifications']);
		add_filter('wp_redirect', [self::class, 'storeNotifications']);
		add_filter('wp_die_ajax_handler', [self::class, 'storeNotifications']);
	}

	public static function loadNotifications()
	{
		$data = isset($_COOKIE[Constants::NOTIFICATION_COOKIE])
			? json_decode(base64_decode($_COOKIE[Constants::NOTIFICATION_COOKIE]), 1)
			: [];

		setcookie(Constants::NOTIFICATION_COOKIE, '[]', time() - 1000, '/', '', false, true);

		self::$successess = isset($data['s']) && is_array($data['s']) ? $data['s'] : [];
		self::$warnings = isset($data['w']) && is_array($data['w']) ? $data['w'] : [];
		self::$errors = isset($data['e']) && is_array($data['e']) ? $data['e'] : [];
	}

	public static function storeNotifications($data)
	{
		$cookieData = json_encode([
			's' => self::$successess,
			'w' => self::$warnings,
			'e' => self::$errors,
		]);

		setcookie(
			Constants::NOTIFICATION_COOKIE,
			base64_encode($cookieData),
			time() + 60,
			'/',
			'',
			false,
			true
		);

		return $data;
	}
}