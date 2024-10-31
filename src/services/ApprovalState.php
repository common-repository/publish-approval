<?php


namespace EC_PublishApproval;


use Approval;
use WP_Post;

class ApprovalState
{
	private static $options;
	private static $approvals;
	private static $canApprove;
	private static $isAlreadyPublished;
	private static $currentUserId;
	private static $isPostByCurrentUser;

	private static $isInitialized;
	private static $isInValidContext;
	private static $postId;

	public static function handleScreenSet()
	{
		if (self::$isInitialized) {
			return;
		}

		self::$isInitialized = true;

		$screen = get_current_screen();
		if ($screen->base !== 'post') {
			return;
		}
		self::$isInValidContext = true;

		$postId = self::extractPostId();
		self::loadState($screen->post_type, $postId);
	}

	private static function extractPostId()
	{
		if (isset($_POST['post_ID'])) {
			return $_POST['post_ID'];
		}
		if (isset($_GET['post'])) {
			return $_GET['post'];
		}
		return null;
	}

	private static function loadState($postType, $postId)
	{
		self::$currentUserId = get_current_user_id();
		self::$options = ApprovalSettings::getForType($postType);
		self::$approvals = $postId ? ApprovalStore::getPostApprovals($postId) : [];
		self::$canApprove = in_array(self::$currentUserId, self::$options['editors'], false);
		self::$isAlreadyPublished = false;
		self::$isPostByCurrentUser = false;
		self::$postId = $postId;

		if ($postId) {
			$post = get_post($postId);

			self::$isPostByCurrentUser = $post->post_author == self::$currentUserId;
			self::$isAlreadyPublished = $post->post_status === 'publish';
		}
	}

	/**
	 * @param WP_Post $post
	 * @param int $postId
	 * @param bool $wasUpdated
	 */
	public static function handlePostSaved($postId, $post, $wasUpdated)
	{
		self::loadState($post->post_type, $postId);
	}

	public static function getIsInValidContext()
	{
		return self::$isInValidContext;
	}

	public static function isEnabled()
	{
		return self::$options['enabled'];
	}

	public static function hasEnoughApprovals()
	{
		return self::$options['requiredApprovals'] <= count(self::$approvals);
	}

	public static function getCanApproveThisPost()
	{
		return self::$canApprove && (
				!self::$isPostByCurrentUser || ApprovalSettings::canAuthorApproveTheirOwnContent()
			);
	}

	public static function getCanApproveThisType()
	{
		return self::$canApprove;
	}

	public static function getIsAlreadyPublished()
	{
		return self::$isAlreadyPublished;
	}

	/**
	 * @return Approval[]
	 */
	public static function getApprovals()
	{
		return self::$approvals;
	}

	public static function getPostId()
	{
		return self::$postId;
	}

	public static function hasUserApproved()
	{
		return ApprovalTools::hasApprovalFromUser(self::$currentUserId, self::$approvals);
	}
}