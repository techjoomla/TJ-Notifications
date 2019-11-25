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

		jQuery(document).on('click', 'button[data-target="#templatePreview"]', function () {

			jQuery('#show-info').hide();

			if (typeof tinyMCE != "undefined")
			{
			   tinyMCE.execCommand('mceToggleEditor', false, 'jform_email_body');
			}
			else if (typeof CodeMirror != "undefined")
			{
				var editor = document.querySelector('.CodeMirror').CodeMirror;
				jQuery('#jform_email_body').html(editor.getValue());
			}
			else
			{
				jQuery('#show-info').show();
			}

			jQuery('#previewTempl').empty();
			jQuery('<style>').html(jQuery('#jform_template_css').val()).appendTo('#previewTempl');
			jQuery('<div>').html(jQuery('#jform_email_body').val()).appendTo('#previewTempl');
		});

		jQuery('#templatePreview').on('hidden.bs.modal', function () {

			if (typeof tinyMCE != "undefined")
			{
			   tinyMCE.execCommand('mceToggleEditor', false, 'jform_email_body');
			}

			jQuery('#previewTempl').empty();
		});
	}
}
