ShareCount API
===========

A PHP script to fetch the count of social shares on Facebook, Twitter, Google +1, Reddit, LinkedIn, Delicious, StumbleUpon and Pinterest as JSON, JSONP or XML dat with the option to cache the results locally.

## Instructions

### Hosted API

The API URL is `http://share-count.appspot.com/` and allows the following parameters:

|  Parameter              |  Default         |  Descriptio                                                       |
| ----------------------- | ---------------- | ----------------------------------------------------------------- |
| **url**  (required)     | `none`           | The URL of the page you want to fetch the social shares for.      |
| **format** (optional)   | `json`           | The format of the output. Can be either `json`, `jsonp` or `xml`. |
| **callback** (optional) | `processShares`  | The JavaScript callback to execute. 

### Examples 

`http://share-count.appspot.com/?url=http://google.com` outputs:
```javascript
{
  "url": "http://google.com",
  "shares": {
    "total": 12460819,
    "facebook": 7988103,
    "twitter": 7485,
    "google": 4440601,
    "reddit": 0,
    "linkedin": 10387,
    "delicious": 3815,
    "stumbleupon": 0,
    "pinterest": 10428
  }
}
```

`http://share-count.appspot.com/?url=http://google.com&format=jsonp` outputs:
```javascript
processShares({
  "url": "http://google.com",
  "shares": {
    "total": 12460819,
    "facebook": 7988103,
    "twitter": 7485,
    "google": 4440601,
    "reddit": 0,
    "linkedin": 10387,
    "delicious": 3815,
    "stumbleupon": 0,
    "pinterest": 10428
  }
})
```
`http://share-count.appspot.com/?url=http://google.com&format=xml` outputs:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<shares>
	<url>http://google.com</url>
	<shares>
		<total>12460918</total>
		<facebook>7988103</facebook>
		<twitter>7485</twitter>
		<google>4440700</google>
		<reddit>0</reddit>
		<linkedin>10387</linkedin>
		<delicious>3815</delicious>
		<stumbleupon>0</stumbleupon>
		<pinterest>10428</pinterest>
	</shares>
</shares>
```

### Host it yourself

Download the package and unzip in on your computer. Open config.php with a text-editor of your choice and amend the few configuration values, then upload the whole folder to your server.

