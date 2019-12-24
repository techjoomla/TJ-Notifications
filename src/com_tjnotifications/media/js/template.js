/**
 * @package     TJNotifications
 * @subpackage  com_tjnotifications
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
var template = {
    previewTemplate: function() {
        var e = new URL(window.location.href).searchParams.get("id");
        jQuery(document).on("click", 'button[data-target="#templatePreview"]', function() {
            jQuery.ajax({
                url: Joomla.getOptions("system.paths").base + "/index.php?option=com_tjnotifications&task=notification.getSampleData&id=" + e,
                type: "GET",
                data: {
                    data: jQuery("#jform_email_body").serialize()
                },
                success: function(e) {
                    jQuery("#previewTempl").append(e)
                },
                error: function(e, t, a) {}
            })
        })
    }
};
