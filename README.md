ec2-metadata [![Build Status](https://travis-ci.org/razorpay/ec2-metadata.svg?branch=master)](https://travis-ci.org/razorpay/ec2-metadata)
================

This tool is a rewrite of the [EC2 Instance Metadata API](http://aws.amazon.com/code/1825) for PHP.

This is a fork of [d9magai/Ec2MetadataGetter](https://github.com/d9magai/Ec2MetadataGetter).

The differences from the fork are:

- An additional method called `getMultiple` which fetches multiple instance attributes specified in an array
- Caching support. The same request repeated twice on the same machine will give a cached response.
- Custom caching directory. The constructor now takes a cache storage directory and uses that.
- Support for returning dummy data

### Usage:

In composer.json

```json
"require": {
	"razorpay/ec2-metadata": "dev-master"
}
```

In your code:

```php
<?php
use Razorpay\EC2Metadata\Ec2MetadataGetter;

$client = new Ec2MetadataGetter($cache_dir);

$client->getNetwork(); // Will return network info

// You can also enable use in dev environments with the following call:

$client->allowDummy();

$client->getAmiId(); // Will always return "ami-12345678"

// Another extra feature from the upstream is the inclusion of a getMultiple method:
// Dummy is always given priority

$client->getMultiple(['Network', ['AmiId']]);

// This returns both Network and AmiId in a properly keyed array

?>
```

## LICENSE

This is licensed under the MIT License.
