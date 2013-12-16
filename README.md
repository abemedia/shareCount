Social Share Count API
===========

A simple PHP script to fetch the count of social shares on Facebook, Twitter, Google +1, Reddit, LinkedIn, Delicious, StumbleUpon and Pinterest as JSON, JSONP or XML dat with the option to cache the results locally.

# Instructions

## Hosted API

The API URL is `http://share-count.appspot.com/` and allows the following parameters:

|  Parameter              |  Default         |  Descriptio                                                       |
| ----------------------- | ---------------- | ----------------------------------------------------------------- |
| **url**  (required)     | `none`           | The URL of the page you want to fetch the social shares for.      |
| **format** (optional)   | `json`           | The format of the output. Can be either `json`, `jsonp` or `xml`. |
| **callback** (optional) | `processShares`  | The JavaScript callback to execute. 

### Social Shares as JSON Data

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
    "stumbleupon": 254953,
    "pinterest": 10428
  }
}
```

### Social Shares as JSONP Data

`http://share-count.appspot.com/?url=http://google.com&format=jsonp` and `http://share-count.appspot.com/?url=http://google.com&callback=processShares` would both output:
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
    "stumbleupon": 254953,
    "pinterest": 10428
  }
})
```

### Social Shares as XML Data

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
		<stumbleupon>254953</stumbleupon>
		<pinterest>10428</pinterest>
	</shares>
</shares>
```

## jQuery & Bootstrap Example

In the following example we're going to display a few pretty Bootstrap buttons with FontAwesome icons and the social share count in bubbles.

### The JavaScript
```javascript
$(document).ready(function () {
    // Get current URL from canonical tag
    var shareUrl = $("link[rel=canonical]").attr("href");
    
    // Ajax request to read share counts. Notice "&callback=?" is appended to the URL.
    $.getJSON('http://share-count.appspot.com/?url=' + encodeURIComponent(shareUrl) + "&callback=?", function (data) {
        shares = data.shares;
        $(".count").each(function (index, el) {
            var $service = $(el).parents(".share-btn").attr("data-service");
            $(el).html(shares[$service]);
        });
    });
});
```
### The HTML
```html
<div class='share'>
    <div class='share-btn' data-service="total">
        <div class="count h4"></div>
        <div class="h3">SOCIAL</div>
        <div class="h2">SHARES</div>
    </div>
    <div class='share-btn' data-service="facebook">
        <div class="count"></div> <a class="btn btn-primary"><i class="fa fa-facebook fa-fw fa-3x"></i></a>
    </div>
    <div class='share-btn' data-service="twitter">
        <div class="count"></div> <a class="btn btn-primary"><i class="fa fa-twitter fa-fw fa-3x"></i></a>
    </div>
    <div class='share-btn' data-service="google">
        <div class="count"></div> <a class="btn btn-primary"><i class="fa fa-google-plus fa-fw fa-3x"></i></a>
    </div>
</div>
```
### The CSS
```css
.share-btn {
    float: left;
    margin:60px 10px;
    position: relative;
}
.share-btn .count {
    border: 1px solid #ccc;
    height: 40px;
    line-height: 40px;
    position: absolute;
    border-radius: 4px;
    padding: 0 5px;
    display: inline-block;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    background: #fff;
    bottom:100%;
    left:0;
    right:0;
    margin-bottom:10px;
}
```
### See a working demo

Here is a working fiddle: http://jsfiddle.net/LeNbh/

### Host it yourself

Download the package and unzip in on your computer. Open config.php with a text-editor of your choice and amend the few configuration values, then upload the whole folder to your server.

## Copyright
&copy; 2013 Adam Bouqdib - http://abemedia.co.uk

Released under GNU GPL 2. See licence.md for further information.

