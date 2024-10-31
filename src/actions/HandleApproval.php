<?php

namespace EC_PublishApproval;

use WP_Post;

class HandleApproval
{
	/**
	 * @param int $postId
	 * @param WP_Post $post
	 * @param bool $wasUpdated
	 */
	public static function handlePostSaved($postId, $post, $wasUpdated)
	{
		if (empty($_POST['approve'])) {
			return;
		}

		if ($post->post_status !== 'draft') {
			return;
		}

		if (!ApprovalState::getCanApproveThisPost() || ApprovalState::hasUserApproved()) {
			return;
		}

		if (!ApprovalStore::registerPostApproval($postId, get_current_user_id())) {
			EcPluginNotifications::addError(__('Failed to save approval, please try again.', 'publish-approval'));
		}
	}
}