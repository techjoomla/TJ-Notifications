# Changelog

#### Legend

- Bug Fix (-)
- Feature Addition (+)
- Improvement (^)

### TJNotifications v3.0.3

##### - Bug fixes:
- #187003 Unable to send notification due to fatal error
- #190387 page limit is not working on the notifications list view
- #190428 BE > Notification Templates > Limit filter is not working
- #191132 BE > Notification Templates > Search Tools > Client filter is not working
- #190428 BE > Notification Templates > If we apply any filter then the extension parameter gets removed from the URL

### TJNotifications v3.0.2

##### - Bug fixes:
- #184788 BE > Subscriptions > Unable to save the Subscription form for joomla 3.10

### TJNotifications v3.0.1

##### ^ Improvements:
- #173952 Chore: Table changes - adding default values for tables of TJnotification (Joomla 4 compatibility)

### TJNotifications v3.0.0

##### + Features Added:
- #175851 Joomla 4 compatible

### TJNotifications v2.0.2

##### ^ Improvements:
- #173952 Chore: Table changes - adding default values for all the tables of TJnotification(Joomla 4 compatibility)

### TJNotifications v2.0.1

##### + Features Added:
- #170906 Add 'Template ID' field in SMS template and pass that template id to SMS gateway

##### - Bug fixes:
- #168104 Backend > Notification Templates > Notice displayed on opening the search tools

##### ^ Improvements:
- #169696 PHP8 compatiblity fixes
- #168311 Remove duplicates language constant from language files

### TJNotifications v2.0.0

##### + Features Added:
- #165665 Add support for shortening URLs using TJ URL Shortner plugin for backends like SMS
- #165665 Add new configs for - enable_url_shortening, url_shortening_provider (choosing TJ URL Shortner plugin), url_shortening_enabled_backends
- #165303 Add config for select the phone number field from the TJ-Notifications.
- #165159 Add user plugin for TJNotifications to add/update on profile update/user creation, the SMS subscription in TJNotifications table
- #162264 Add backend list view for subscriptions
- #162263 Add backend form view for subscription
- #162263 Add API plugin for addind/updating subscription
- #159749 SMS notifications support
- #159490 Multilingual Support for notification templates
- #138487 Log sent notifications details in DB table
- #134717 Email notification improvement - add support to set CC and BCC at template level

##### - Bug fixes:
- #164967 Back end >> Notification template's view >> Search tools >> client drop down list must contain standard name of component

##### ^ Improvements:
- #165070 PHP 5.6, 7.4 compatibility changes (for supporting multilingual templates feature)
- #159156 Backend controllers code refactor
