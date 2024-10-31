<?php

namespace EC_PublishApproval;

class Constants
{
	const VERSION = '1.1.0';
	const MENU_SLUG = 'publish-approval';

	const META_APPROVAL_PREFIX = 'approval_by_';

	const CAPABILITY_EDIT_OPTIONS = 'activate_plugins';

	const NOTIFICATION_COOKIE = '_ec_aprov_notifications';

	public static function getOptionsUrl()
	{
		return admin_url('options-general.php?page=' . self::MENU_SLUG);
	}
}