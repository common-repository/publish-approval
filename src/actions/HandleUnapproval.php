<?php

namespace EC_PublishApproval;

class HandleUnapproval
{
	public static function handlePostSaved($postId, $post, $wasUpdated)
	{
		if (empty($_POST['unapprove'])) {
			return;
		}

		if ($post->post_status !== 'draft') {
			return;
		}

		if (!ApprovalState::getCanApproveThisPost() || !ApprovalState::hasUserApproved()) {
			return;
		}

		if (!ApprovalStore::removePostApproval($postId, get_current_user_id())) {
			EcPluginNotifications::addError(__('Failed to save unapproval, please try again.', 'publish-approval'));
		}
	}
}