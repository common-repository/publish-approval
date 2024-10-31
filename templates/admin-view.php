<?php

use EC_PublishApproval\ApprovalSettings;
use EC_PublishApproval\ApprovalTools;

$postTypes = ApprovalTools::getValidPostTypes();

function _render_options($selected = '')
{
	static $users;
	if (!$users) {
		$users = get_users();
	}

	foreach ($users as $user) {
		?>
		<option value="<?php echo esc_attr($user->ID); ?>"<?php echo $selected === $user->ID ? 'selected' : ''; ?>><?php echo esc_attr($user->display_name); ?></option><?php
	}
}

?>

<div class="wrap">
	<h1 class="wp-heading-inline"><?php _e('Publish approval', 'publish-approval'); ?></h1>

	<p><?php _e('Select which types of content should require approvals before publish is available. The ones you enabled will have publishing blocked unless the specified number of approvals has been gathered.', 'publish-approval'); ?></p>

	<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
		<input type="hidden" name="action" value="publish_save_option">
		<table id="publish-approval-settings" class="form-table ">
			<tbody>
			<tr>
				<th class="header-row" colspan="2">
					Approvals
				</th>
			</tr>
			<?php foreach ($postTypes as $postType): ?>
				<?php
				$name = $postType->name;
				$label = $postType->label;
				$options = ApprovalSettings::getForType($name);
				$editors = count($options['editors']) > 0 ? $options['editors'] : [0];
				$showClass = $options['enabled'] ? '' : 'hidden';
				?>
				<tr>
					<th>
						<label for="approvals-<?php echo $name; ?>-enable">
							<?php _e('Enable approvals for:', 'publish-approval'); ?>
						</label>
					</th>
					<td>
						<input
								id="approvals-<?php echo $name; ?>-enable"
								name="approvals[<?php echo $name; ?>][enabled]"
								class="toggle-enable"
								type="checkbox"
								data-selector=".row-<?php echo $name; ?>"
							<?php echo $options['enabled'] ? 'checked' : ''; ?>>
						<label for="approvals-<?php echo $name; ?>-enable">
							<?php echo $label; ?>
						</label>
					</td>
				</tr>
				<tr class="subrow row-<?php echo $name; ?> <?php echo $showClass; ?>">
					<th>
						<label for="approvals-<?php echo $name; ?>-required">
							<?php _e('Required approvals:', 'publish-approval'); ?>
						</label>
					</th>
					<td>
						<input
								id="approvals-<?php echo $name; ?>-required"
								name="approvals[<?php echo $name; ?>][required]"
								type="number"
								min="0" max="999" step="1"
								value="<?php echo $options['requiredApprovals']; ?>">
					</td>
				</tr>
				<?php foreach ($editors as $index => $editorId): ?>
					<tr class="subrow row-<?php echo $name; ?> <?php echo $showClass; ?>">
						<th>
							<label for="approvals-<?php echo $name; ?>-editors-<?php echo $index; ?>">
								<?php _e('Editor:', 'publish-approval'); ?>
							</label>
						</th>
						<td>
							<select
									id="approvals-<?php echo $name; ?>-editors-<?php echo $index; ?>"
									name="approvals[<?php echo $name; ?>][editors][]">
								<option><?php _e('--SELECT--', 'publish-approval'); ?></option>
								<?php _render_options($editorId); ?>
							</select>
							<input type="submit" class="remove-editor-button button button-secondary" value="<?php _e('Remove', 'publish-approval'); ?>"/>
						</td>
					</tr>
				<?php endforeach; ?>
				<tr class="subrow row-<?php echo $name; ?> <?php echo $showClass; ?>">
					<th></th>
					<td>
						<input type="submit"
								class="add-editor-button button button-secondary"
								value="<?php _e('Add editor', 'publish-approval'); ?>"
								data-name="<?php echo $name; ?>"/>
					</td>
				</tr>
			<?php endforeach; ?>


			<tr>
				<th class="header-row" colspan="2">
					Behavior
				</th>
			</tr>
			<tr>
				<th>
					<label for="approvals-self-approve">
						<?php _e('Self approval:', 'publish-approval'); ?>
					</label>
				</th>
				<td>
					<input
							id="approvals-self-approve"
							name="approvals-self-approve"
							class="toggle-enable"
							type="checkbox"
						<?php echo ApprovalSettings::canAuthorApproveTheirOwnContent() ? 'checked' : ''; ?>>
					<label for="approvals-self-approve">
						<?php _e('Allow authors to approve their own posts.', 'publish-approval'); ?>
					</label>
				</td>
			</tr>


			<tr id="users-template" class="hidden">
				<th>
					<label for="approvals-%name%-editors-%$index%">
						<?php _e('Editor:', 'publish-approval'); ?>
					</label>
				</th>
				<td>
					<select
							id="approvals-%name%-editors-%index%"
							name="approvals[%name%][editors][]">
						<option><?php _e('--SELECT--', 'publish-approval'); ?></option>
						<?php _render_options(); ?>
					</select>
					<input type="submit" class="remove-editor-button button button-secondary" value="<?php _e('Remove', 'publish-approval'); ?>"/>
				</td>
			</tr>
			</tbody>
		</table>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes', 'publish-approval'); ?>">
		</p>
	</form>
</div>
