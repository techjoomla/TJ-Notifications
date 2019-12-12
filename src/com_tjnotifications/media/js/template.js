/**
 * @package     TJNotifications
 * @subpackage  com_tjnotifications
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

var template = {

	previewTemplate: function () {

	var url = new URL(window.location.href);
	var id= url.searchParams.get('id');

	jQuery(document).on('click', 'button[data-target="#templatePreview"]', function () {

			jQuery.ajax({
				url: Joomla.getOptions('system.paths').base + "/index.php?option=com_tjnotifications&task=notification.getSampleData&id=" + id,
				type: 'GET',
				success: function(data) {

						jQuery("#previewTempl").append(data);
				},
				error: function(xhr, ajaxOptions, thrownError) {}
			})

	});
		jQuery('#templatePreview').on('hidden.bs.modal', function () {

			if (typeof tinyMCE != "undefined")
			{
			   tinyMCE.execCommand('mceToggleEditor', false, 'jform_body');
			}

			jQuery('#previewTempl').empty();
		});
	}
}
