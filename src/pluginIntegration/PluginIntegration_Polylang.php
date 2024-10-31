<?php

namespace EC_PublishApproval;

class PluginIntegration_Polylang
{
	public static function adminInit()
	{
		add_filter('pll_copy_post_metas', [self::class, 'remove_publish_approval_meta'], 0, 5);
	}

	public static function remove_publish_approval_meta($meta)
	{
		$prefix = Constants::META_APPROVAL_PREFIX;

		foreach ($meta as $key => $metaName) {
			if (strpos($metaName, $prefix) === 0) {
				unset($meta[$key]);
			}
		}

		// Clean up sparsiness
		sort($meta);

		return $meta;
	}
}