<?php

namespace EC_PublishApproval;

use Approval;

class ApprovalStore
{
	public static function registerPostApproval($postId, $userId)
	{
		$approval = new Approval($userId, time());

		$key = self::getApprovalMetaKey($userId);
		return update_post_meta($postId, $key, $approval->toJsonString());
	}

	private static function getApprovalMetaKey($userId)
	{
		return Constants::META_APPROVAL_PREFIX . $userId;
	}

	public static function removePostApproval($postId, $userId)
	{
		$key = self::getApprovalMetaKey($userId);
		return delete_post_meta($postId, $key);
	}

	/**
	 * @param $postId
	 * @return Approval[]
	 */
	public static function getPostApprovals($postId)
	{
		$metas = get_post_meta($postId);
		$approvals = [];
		foreach ($metas as $key => $meta) {
			if (strpos($key, Constants::META_APPROVAL_PREFIX) !== 0) {
				continue;
			}

			$approvals[] = Approval::fromJsonString($meta[0]);
		}

		return $approvals;
	}
}