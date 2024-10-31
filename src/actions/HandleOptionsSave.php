<?php

namespace EC_PublishApproval;

class HandleOptionsSave
{
	public static function handleActionOptionsUpdate()
	{
		if (!current_user_can(Constants::CAPABILITY_EDIT_OPTIONS)) {
			wp_redirect(admin_url());
			exit;
		}

		$approvals = isset($_POST['approvals']) ? $_POST['approvals'] : null;
		if (!$approvals || !is_array($approvals)) {
			wp_redirect(Constants::getOptionsUrl());
			exit;
		}

		$optionsArray = [];
		foreach ($approvals as $postType => $optionData) {
			if (!ApprovalTools::isValidPostType($postType)) {
				continue;
			}

			$optionsArray[$postType] = ApprovalSettings::createSettings(
				self::extractEnabled($optionData),
				self::extractEditors($optionData),
				self::extractRequiredApprovals($optionData)
			);
		}

		$optionsArray[ApprovalSettings::BEHAVIOR_GROUP_OPTION] = [
			ApprovalSettings::ALLOW_SELF_APPROVE_OPTION => isset($_POST['approvals-self-approve']) ? true : false
		];

		ApprovalSettings::updateOptions($optionsArray);
		wp_redirect(Constants::getOptionsUrl());
	}

	private static function extractEnabled($optionData)
	{
		return is_array($optionData)
			&& isset($optionData['enabled']);
	}

	private static function extractEditors($optionData)
	{
		if (
			is_array($optionData)
			&& isset($optionData['editors'])
			&& is_array($optionData['editors'])
		) {
			$editorIds = array_filter($optionData['editors'], function ($editorId) {
				return is_numeric($editorId) && get_user_by('id', $editorId);
			});
			return array_unique($editorIds);
		}

		return [];
	}

	private static function extractRequiredApprovals($optionData)
	{
		if (
			is_array($optionData)
			&& isset($optionData['required'])
			&& is_numeric($optionData['required'])
		) {
			return $optionData['required'];
		}

		return 1;
	}
}