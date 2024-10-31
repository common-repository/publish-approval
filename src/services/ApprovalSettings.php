<?php

namespace EC_PublishApproval;

class ApprovalSettings
{
	const OPTION_NAME = 'ec_publish_approval_options';

	const BEHAVIOR_GROUP_OPTION = '_behavior';
	const ALLOW_SELF_APPROVE_OPTION = 'self_approve';

	private static $options;

	public static function updateOptions($options)
	{
		update_option(self::OPTION_NAME, $options);
	}

	public static function getForType($postType)
	{
		$options = self::getOptions();
		if (isset($options[$postType])) {
			return $options[$postType];
		}

		return [
			'enabled' => false,
			'editors' => [],
			'requiredApprovals' => 0
		];
	}

	public static function canAuthorApproveTheirOwnContent()
	{
		$options = self::getOptions();

		return isset($options[self::BEHAVIOR_GROUP_OPTION][self::ALLOW_SELF_APPROVE_OPTION])
			? $options[self::BEHAVIOR_GROUP_OPTION][self::ALLOW_SELF_APPROVE_OPTION]
			: false;
	}

	private static function getOptions()
	{
		if (!self::$options) {
			self::$options = get_option(self::OPTION_NAME, []);
		}

		return self::$options;
	}

	public static function createSettings($enabled, $editorIds, $requiredApprovals)
	{
		return [
			'enabled' => (bool)$enabled,
			'editors' => array_unique(array_filter(array_map('intval', $editorIds))),
			'requiredApprovals' => max(1, min(100, intval($requiredApprovals)))
		];
	}
}

/*

Options format:

{
	<post-type>:
		enabled: bool,
		editors:  userId[],
		requiredApprovals: int
}

 */