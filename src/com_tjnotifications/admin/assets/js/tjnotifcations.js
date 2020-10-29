/**
 * @package    TJNotifications
 *
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2020 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

/**
 * Front end JavaScript
 */
var tjnotifications = {
};

/**
 * Backend end JavaScript
 */
var tjnotificationsAdmin = {
	notification: {
		validateSmsLength: function(element){
			var smsBodyId = element.getAttribute("id");
			var curentSmsLength = jQuery("#"+smsBodyId).val().length;
			var maxSmsLength = 160;
			var remainingCharLimit = maxSmsLength - curentSmsLength;
			var parentDiv = document.querySelector("#"+smsBodyId);
			var smsSubformFieldNum = smsBodyId.split("jform_sms__smsfields__smsfields")["1"].split("__")["0"]

			var remainingCharLimitMsg = (parseInt(remainingCharLimit) > 0 ) ? remainingCharLimit + " " + Joomla.JText._('COM_TJNOTIFICATIONS_NOTIFICATION_SMS_REMAINING_CHARACTER') : (remainingCharLimit * -1) + " " + Joomla.JText._('COM_TJNOTIFICATIONS_NOTIFICATION_SMS_REMAINING_EXCEEDED');

			if(parentDiv.parentNode.parentNode.parentNode.querySelector("p") !== null)
			{
				jQuery(".smsfieldsCharLimit"+smsSubformFieldNum).text(remainingCharLimitMsg);
				var isalertInfo = parentDiv.parentNode.parentNode.parentNode.querySelector("p").getAttribute("class").split(" ").includes("alert-info");

				if (isalertInfo && (parseInt(remainingCharLimit) < 0 ))
				{
					jQuery(".smsfieldsCharLimit"+smsSubformFieldNum).removeClass("alert-info");
					jQuery(".smsfieldsCharLimit"+smsSubformFieldNum).addClass("alert-danger");
				}
				else
				{
					jQuery(".smsfieldsCharLimit"+smsSubformFieldNum).removeClass("alert-danger");
					jQuery(".smsfieldsCharLimit"+smsSubformFieldNum).addClass("alert-info");
				}
			}
			else
			{
				var newP = document.createElement("p");
				var newPCustomClass = "smsfieldsCharLimit" + smsSubformFieldNum;
				var remainingCharLimitClass = (parseInt(remainingCharLimit) > 0 ) ? "alert-info" : "alert-danger" ;
				newP.setAttribute("class", remainingCharLimitClass + " center " + newPCustomClass);
				var textNode = document.createTextNode(remainingCharLimitMsg);
				newP.appendChild(textNode);
				parentDiv.parentNode.parentNode.parentNode.appendChild(newP);
			}
		}
	}
};
