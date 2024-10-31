<?php


namespace EC_PublishApproval;


class ApprovalBoxes
{
	public static function renderApprovalBox($post)
	{
		if (ApprovalState::getCanApproveThisPost() && !ApprovalState::getIsAlreadyPublished()) {
			if (ApprovalState::hasUserApproved()) {
				self::renderUnapproveButton();
			} else {
				self::renderApproveButton();
			}
		} else if (ApprovalState::getCanApproveThisType() && !ApprovalState::getIsAlreadyPublished()) {
			self::renderDisabledApproveButton();
		}


		self::renderApprovalsList();
	}

	private static function renderUnapproveButton()
	{
		$value = __('Unapprove', 'publish-approval');

		echo '<div id="unapproval-action">';
		echo '<input type="submit" name="unapprove" id="unapprove" class="button button-secondary button-large" value="' . $value . '">';
		echo '</div>';
	}

	private static function renderApproveButton()
	{
		$value = __('Approve', 'publish-approval');

		echo '<div id="approval-action">';
		echo '<input type="submit" name="approve" id="approve" class="button button-primary button-large" value="' . $value . '">';
		echo '</div>';
	}

	private static function renderDisabledApproveButton()
	{
		$value = __('Approve', 'publish-approval');

		echo '<div id="approval-action">';
		echo '<input title="You cannot approve your own posts." type="submit" name="approve" id="approve" class="button button-primary button-large" value="' . $value . '" disabled>';
		echo '</div>';
	}

	private static function renderApprovalsList()
	{
		echo '<div id="approval-list">';
		echo '<h3>' . __('Approvals:', 'publish-approval') . '</h3>';

		if (count(ApprovalState::getApprovals()) === 0) {
			echo '<p>No approvals.</p>';
		} else {
			echo '<ul>';
			foreach (ApprovalState::getApprovals() as $approval) {
				$user = get_user_by('id', $approval->getApproverId());
				$time = self::getFormattedTime($approval->getTimestamp());

				$string = sprintf(__('<strong>%s</strong> on <i>%s</i>', 'publish-approval'), $user->display_name, $time);
				echo "<li>{$string}</li>";
			}
			echo '</ul>';
		}

		echo '</div>';
	}

	private static function getFormattedTime($timestamp)
	{
		return date_i18n(get_option('date_format'), $timestamp)
			. ' '
			. date_i18n(get_option('time_format'), $timestamp);
	}
}