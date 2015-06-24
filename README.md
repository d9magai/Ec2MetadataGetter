ec2-metadata
================

This tool is a rewrite of the [EC2 Instance Metadata API](http://aws.amazon.com/code/1825) for PHP.

This is a fork of [d9magai/Ec2MetadataGetter](https://github.com/d9magai/Ec2MetadataGetter).

The differences from the fork are:

- An additional method called `getMultiple` which fetches multiple instance attributes specified in an array
- Caching support. The same request repeated twice on the same machine will give a cached response.
- Custom caching directory. The constructor now takes a cache storage directory and uses that.

## TODO

- Add support for dummy data for local usage

### Usage:

in composer.json

```json
"require": {
	"razorpay/ec2-metadata": "dev-master"
}
```

## LICENSE

This is licensed under the MIT License.
