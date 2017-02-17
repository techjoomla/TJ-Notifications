# TJ Notifications
TJ Notifications is an extremely powerful and flexible notifications system for Joomla. Currently, supports sending notifications by email but we are planning to supports SMS, push notifications and in-site notifications.

## User Docs

## Technical Docs
Utility extension and library to send any type of notifications like email, push, SMS etc. Allows users to also set their preferences to receive stop certain types of notifications, or change delivery preferences.

## Database Schema

**#__tj_notification_templates**

| Column Name | Data Type | Description |
|:-----------------|:-------------|:---------------|
| id | INT | NOT NULL  AUTO_INCREMENT PRIMARY KEY |
| client | VARCHAR | NOT NULL |
| key | VARCHAR | NOT NULL |
| email_status | INT| NOT NULL |
| sms_status | INT | NOT NULL |
| push_status |  INT |  NOT NULL|
| web_status | INT | NOT NULL |
| email_body | TEXT | NOT NULL |
| sms_body | TEXT | NOT NULL |
| push_body | TEXT | NOT NULL |
| web_body | TEXT | NOT NULL |
| email_subject | TEXT | NOT NULL |
| sms_subject | TEXT | NOT NULL |
| push_subject | TEXT | NOT NULL |
| web_subject | TEXT | NOT NULL |
| state | INT | NOT NULL |
|created_on  | DATE | NOT NULL |
| updated_on | DATE | NOT NULL |
| is_override | INT | NOT NULL |

**#__tj_notification_providers**

| Column Name | Data Type | Description |
|---------------|-------------|--------------|
| provider | VARCHAR | NOT NULL  PRIMARY KEY |
| state | INT | NOT NULL |

**#__tj_notification_user_exclusions**

| Column Name | Data Type | Description |
|---------------|-----------|---------------|
| user_id | INT | NOT NULL |
| client | VARCHAR | NOT NULL |
| key | VARCHAR | NOT NULL |
| provider | VARCHAR | NOT NULL |

## Function Definition
Function to send notifications.

public static function send(
      $client ,
      $key ,
      $recipients ,
      $replacements ,
      $options 
)

| Parameter | Type | Description |
| -------          | ------ | ------     |
| $client | string | Component name. |
| $key | string | Value of template key. Should be unique within a client  |
| $recipients | array | Array of JUser objects |
| $replacements | object | Replacements for body templates. |
| $options | JParameter  | Options for notification |



## Integrating with 3rd Party Extensions

Include the library file
`jimport('techjoomla.tjnotifications.tjnotifications');`

Prepare the parameters for Tjnotifications::send()

`$client` is the name of your extension, eg: com_jgive

`$key` is the unique reference for the type of email within your extension. Eg: donation.thankyou, campaign.update, order.thankyou

`$recipients` is an array of JUser objects
eg: `$recipients = JAccess::getUsersByGroup(2);`

`$replacements` is an object of objects, containing all replacements. The object and their properties are mapped against the values in the template body. `$replacements->order->id` maps to `{order.id}` for example.

`$options` is an instance of JParameter. Contains additional options that may be used by the notification provider. In case of email options may include cc, bcc, attachments, reply to,from,guestEmails etc

Finally, call  `Tjnotifications::send($client,$key,$recipients,$replacements, $options);` This will send the notifications based on user preferences. If the user has disabled any of the notifications or specific delivery preferences, those notifications will not be sent to the user. The 3rd party developer does not need to be aware of these settings.


## Example Usage

```php
<?php
public function save($data)
{       
  $client = "com_jcregister";
  $key = "order";
	
  $recipients[] = JFactory::getUser(488);
  $recipients[] = JFactory::getUser(489);

  $order_info->id = "236";
  $order_info->amount = "500";
  $order_info->status = "Pending";
  $replacements->order = $order_info;

  $customer_info->name = "hemant";
  $customer_info->address = "";
  $customer_info->zip = "411004";
  $replacements->customer = $customer_info;
     	   	
  $options = new JParameter();
  $options->set('cc', 'demo@mailinator.com');
  $options->set('attachment', '/var/www/html/joomla/media/attach.pdf');
  $options->set('guestEmails', array("testuser@mailinator.com"));
  $options->set('from', "pranoti_p@techjoomla.com");
  $options->set('fromname', "pranoti");

  Tjnotifications::send($client, $key, $recipients, $replacements, $options);	 
}
```
