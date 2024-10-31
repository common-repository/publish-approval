<?php


namespace EC_PublishApproval;


class AdminBodyClassHandler
{
	public static function addAdminClasses($classes)
	{
		if (!ApprovalState::getIsInValidContext() || !ApprovalState::isEnabled() || ApprovalState::getIsAlreadyPublished()) {
			return $classes;
		}

		$newClasses = [];

		if (ApprovalState::getCanApproveThisPost()) {
			$newClasses[] = 'ec-approval-available';
		}
		if (ApprovalState::getCanApproveThisType()) {
			$newClasses[] = 'ec-approval-type-available';
		}

		if (ApprovalState::hasEnoughApprovals()) {
			$newClasses[] = 'ec-approval-publishable';
		} else {
			$newClasses[] = 'ec-approval-not-publishable';
		}

		return $classes . ' ' . implode(' ', $newClasses);
	}
}