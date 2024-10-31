jQuery(document).ready(function () {
	var $ = jQuery;
	var $table = $('#publish-approval-settings');
	if ($table.length === 0) {
		return 0;
	}

	var $rowTemplate = $table.find('#users-template').remove();

	$table.find('.toggle-enable').on('click', function () {
		var $this = $(this);
		var $rows = $table.find($this.attr('data-selector'));

		if ($this.prop('checked')) {
			$rows.removeClass('hidden');
		} else {
			$rows.addClass('hidden');
		}
	});

	$table.on('click', '.remove-editor-button', function (e) {
		e.preventDefault();
		e.stopPropagation();

		$(this).parent().parent().remove();
	});

	$table.on('click', '.add-editor-button', function (e) {
		e.preventDefault();
		e.stopPropagation();

		var $this = $(e.target);
		var name = $this.attr('data-name');
		var index = Date.now();
		var $newRow = $(
			'<tr class="subrow row-' + name + '">'
			+ $rowTemplate.html().replace(/%name%/g, name).replace(/%index%/g, index)
			+ '</tr>'
		);
		$this.parent().parent().before($newRow);
	});
});