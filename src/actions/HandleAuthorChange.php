<?php

namespace EC_PublishApproval;

class HandleAuthorChange
{
	public static function filterPostData($data, $postarr)
	{
		if (isset($postarr['ID']) && $data['post_status'] !== 'publish' && !ApprovalSettings::canAuthorApproveTheirOwnContent()) {
			ApprovalStore::removePostApproval($postarr['ID'], $data['post_author']);
		}

		return $data;
	}
}