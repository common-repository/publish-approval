<?php
/*
 * Plugin Name: Publish Approval
 * Description: Disallow publishing content until it is approved by a specified number of people
 * Author: Maurycy Zarzycki Evidently Cube
 * Version: 1.1
 * Requires PHP: 5.6
 * Requires at least: 4.6
 * Text Domain: publish-approval
 * Domain path: /i18n
 */


use EC_PublishApproval\AdminBodyClassHandler;
use EC_PublishApproval\ApprovalBoxes;
use EC_PublishApproval\ApprovalState;
use EC_PublishApproval\Constants;
use EC_PublishApproval\ECPlugin;
use EC_PublishApproval\EcPluginNotifications;
use EC_PublishApproval\HandleApproval;
use EC_PublishApproval\HandleAuthorChange;
use EC_PublishApproval\HandleOptionsSave;
use EC_PublishApproval\HandlePublishCircumvent;
use EC_PublishApproval\HandleUnapproval;
use EC_PublishApproval\PluginIntegration_Polylang;

require_once __DIR__ . '/ECPlugin.php';
require_once __DIR__ . '/src/Constants.php';
require_once __DIR__ . '/src/actions/HandleApproval.php';
require_once __DIR__ . '/src/actions/HandleAuthorChange.php';
require_once __DIR__ . '/src/actions/HandleOptionsSave.php';
require_once __DIR__ . '/src/actions/HandlePublishCircumvent.php';
require_once __DIR__ . '/src/actions/HandleUnapproval.php';
require_once __DIR__ . '/src/model/Approval.php';
require_once __DIR__ . '/src/pluginIntegration/PluginIntegration_Polylang.php';
require_once __DIR__ . '/src/services/ApprovalSettings.php';
require_once __DIR__ . '/src/services/ApprovalState.php';
require_once __DIR__ . '/src/services/ApprovalStore.php';
require_once __DIR__ . '/src/services/ApprovalTools.php';
require_once __DIR__ . '/src/ui/AdminBodyClassHandler.php';
require_once __DIR__ . '/src/ui/ApprovalBoxes.php';

function _pubapprove_activate_hook()
{
	PublishApprovalPlugin::activateHook();
}

class PublishApprovalPlugin
{
	public static function activateHook()
	{
		EcPluginNotifications::addSuccess(
			sprintf(__(
				'<strong>Publish Approval</strong> has been enabled. <a href="%s">Click here</a> to configure it.',
				'publish-approval'
			), Constants::getOptionsUrl())
		);
	}

	public static function adminNotices()
	{
		EcPluginNotifications::printNotifications();
	}

	public static function loadPluginTextDomain()
	{
		load_plugin_textdomain('publish-approval', false, basename(__DIR__) . '/i18n/');
	}

	public static function init()
	{
		EcPluginNotifications::initialize();

		add_action('admin_init', [self::class, 'handleAdminInit']);

		register_activation_hook(__FILE__, '_pubapprove_activate_hook');

		if (is_admin()){
			add_action('admin_menu', function () {
				ECPlugin::registerMenu(
					'Publish approval',
					'Publish approval',
					Constants::CAPABILITY_EDIT_OPTIONS,
					Constants::MENU_SLUG,
					__DIR__ . '/templates/admin-view.php'
				);
			});
			add_action('plugins_loaded', [self::class, 'loadPluginTextDomain']);
		}
	}

	public static function handleAdminInit()
	{
		add_action('current_screen', [ApprovalState::class, 'handleScreenSet']);
		add_action('admin_body_class', [AdminBodyClassHandler::class, 'addAdminClasses']);
		add_action('post_submitbox_start', [ApprovalBoxes::class, 'renderApprovalBox']);

		add_action('admin_notices', [self::class, 'adminNotices']);
		add_action('admin_enqueue_scripts', [self::class, 'registerScriptsAndStyles']);

		add_action('wp_insert_post_data', [HandlePublishCircumvent::class, 'filterPostData'], 10, 1);
		add_action('wp_insert_post_data', [HandleAuthorChange::class, 'filterPostData'], 20, 2);
		add_action('admin_post_publish_save_option', [HandleOptionsSave::class, 'handleActionOptionsUpdate']);

		add_action('save_post', [ApprovalState::class, 'handlePostSaved'], 0, 3);
		add_action('save_post', [HandleApproval::class, 'handlePostSaved'], 10, 3);
		add_action('save_post', [HandleUnapproval::class, 'handlePostSaved'], 10, 3);

		PluginIntegration_Polylang::adminInit();
	}

	public static function registerScriptsAndStyles()
	{
		wp_register_style('publish_approval_admin_style', plugin_dir_url(__FILE__) . 'admin.css', [], Constants::VERSION);
		wp_enqueue_style('publish_approval_admin_style');

		wp_register_script('publish_approval_script', plugin_dir_url(__FILE__) . 'admin.js', ['jquery'], Constants::VERSION, true);
		wp_enqueue_script('publish_approval_script');
	}
}

PublishApprovalPlugin::init();