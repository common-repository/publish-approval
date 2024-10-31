<?php

namespace EC_PublishApproval;

class HandlePublishCircumvent
{
	public static function filterPostData($data)
	{
		if (!ApprovalState::isEnabled()) {
			return $data;
		}

		$oldPost = get_post(ApprovalState::getPostId());
		$wasOldPostPublished = $oldPost && $oldPost->post_status === 'publish';

		if (
			!$wasOldPostPublished
			&& $data['post_status'] === 'publish'
			&& !ApprovalState::hasEnoughApprovals()
		) {
			EcPluginNotifications::addError(__('Post cannot be published, not enough approvals.', 'publish-approval'));
			$data['post_status'] = 'draft';
		}

		return $data;
	}
}