Based on https://github.com/Yelp/yelp-api/tree/master/v2/php

# Usage #

```php
<?php
$config = array(
  'consumer_key'    => YOUR_CONSUMER_KEY,
  'consumer_secret' => YOUR_CONSUMER_SECRET,
  'token'           => YOUR_TOKEN,
  'token_secret'    => YOUR_TOKEN_SECRET
);

// Create yelp business api driver
$yelp = new Yelp($config);

// Returns json data if request is successful else throws YelpException
$data = $yelp->get('some-business-name');
```
