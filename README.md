ShareCount API
===========

A PHP script to fetch the count of social shares on Facebook, Twitter, Google +1, Reddit, LinkedIn, Delicious, StumbleUpon and Pinterest as JSON, JSONP or XML dat with the option to cache the results locally.

## Instructions

### Hosted API

The API URL is `http://share-count.appspot.com/` and allows the following parameters:

|  Parameter             |  Default         |  Descriptio                                                       |
| ---------------------- | ---------------- | ----------------------------------------------------------------- |
|  `url`  (required)     | none             | The URL of the page you want to fetch the social shares for.      |
|  `format` (optional)   | `json`           | The format of the output. Can be either `json`, `jsonp` or `xml`. |
|  `callback` (optional) | `processShares`  | The JavaScript callback to execute. 

### Host it yourself

Download the package and unzip in on your computer. Open config.php with a text-editor of your choice and amend the few configuration values, then upload the whole folder to your server.

