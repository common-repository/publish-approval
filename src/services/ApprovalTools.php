<?php

namespace EC_PublishApproval;

use Approval;
use WP_Post_Type;

class ApprovalTools
{
	/**
	 * @param int $userId
	 * @param Approval[] $approvals
	 */
	public static function hasApprovalFromUser($userId, &$approvals)
	{
		foreach ($approvals as $approval) {
			if ($approval->getApproverId() == $userId) {
				return true;
			}
		}

		return false;
	}

	public static function isValidPostType($postTypeName)
	{
		foreach (self::getValidPostTypes() as $postType) {
			if ($postType->name === $postTypeName) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return WP_Post_Type[]
	 */
	public static function getValidPostTypes()
	{
		static $postTypeCache;

		if (!$postTypeCache) {
			$postTypeCache = get_post_types([], 'objects');

			$postTypeCache = array_filter($postTypeCache, function ($type) {
				return !$type->_builtin || in_array($type->name, ['post', 'page']);
			});
		}

		return $postTypeCache;
	}
}