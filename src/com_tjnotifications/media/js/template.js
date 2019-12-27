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
                    jQuery("#previewTempl").empty();
                    jQuery("#previewTempl").append(e);
                },
                error: function(e, t, a) {}
            })
        })
    },

		init: function() {
		jQuery("fieldset").click(function()
		{
			status=this.id+'0';
			statusChange=this.id+'1';
			var check=(jQuery("#"+status).attr("checked"));

			if(check=="checked")
			{
				var body=(this.id).replace("status", "body_ifr");
				var bodyData=(jQuery("#"+body).contents().find("body").find("p").html());
				if(bodyData=='<br data-mce-bogus="1">')
				{
					alert('Please fill the data');
					jQuery('#'+this.id).find('label[for='+statusChange+']').attr('class','btn active btn-danger');
					jQuery('#'+this.id).find('label[for='+status+']').attr('class','btn');
					return false;
				}
				else
				{
					jQuery('#'+this.id).find('label[for='+status+']').attr('class','btn active btn-success');
					jQuery('#'+this.id).find('label[for='+statusChange+']').attr('class','btn');
				}
			}
		})
    }
};
