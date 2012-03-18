# Call Tracking Metrics PHP Tracking

This script will provide a different tracking number based on the cookies, referring domain and page of the current unique visitor.
For most use cases, we recommend the JavaScript tracking number script.   However, some users wish to avoid the extra flicker that
occurs before as the page loads and so may find this server side script desirable.   Keep in mind this is not a full replacement
for the client side tracking code, but a supplement and may be used in conjunction with the client side tracking code.

You may find your client side tracking code here: https://calltrackingmetrics.com/embed_code/

### Installation
Requires: PHP 5.x

To use server side tracking script for Call Tracking Metrics, you will need to install the two php files.

    ctm_numbers.php
    ctm_config.php

ctm_config.php must be callable via an HTTP Request.
We recommend the use of a valid SSL cert to protect all communication between our servers and your server.

e.g.

    https://example.com/ctm_config.php


Open the ctm_config.php file and update the 2 security tokens

    //define("CTM_ACCESS_KEY", "");
    //define("CTM_SECRET_KEY", "");

You may need to enable API access in your account by going to "settings" -> "manage settings" page and clicking "Enable API Access"


To have your call tracking metrics account send configuration updates to your server set up an HTTP notification.

Under the "settings" menu click "notifications"

  Choose "After a tracking number change"

  Choose "http"

  Add the location of your ctm_config.php file e.g.
    https://example.com/ctm_config.php

Next to trigger a request to your server and have the configuration file written for the first time, go to the edit screen
for one of your tracking numbers and click "Save Tracking Number".

This will trigger the new notification to send a request to your server.

It's important to make sure your webserver can write to the directory of your ctm_config.php file as it will create a single file
ctm_config.json within the same folder that ctm_number.php will use to read your accounts configuration from on each request.

## Caching

This script is not recommended if you use page caching to optimize your website.


## Example usage in WordPress

place the ctm-php library in your WordPress theme folder as the folder "ctm".


require the ctm_number.php file in your functions.php script.

    require_once "ctm/lib/ctm_number.php";

Replace your phone number with the following code:

    <?php echo ctm_number_for_receiving("(555) 555-5555"); ?>

Where (555) 555-5555 is your current receiving number or primary receiving number configured on the tracking number and also the desired format for your phone numbers.

## Formatting Phone Numbers

We support a wide range of phone number formats.  For example,

    (555) 555-5555
    555.555.5555
    555-555-5555

And many more, try it out... Let us know if you uncover any issues.
