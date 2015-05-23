Social Share Count API
===========

A simple PHP script to fetch the count of social shares on Facebook, Twitter, Google +1, Reddit, LinkedIn, Delicious, StumbleUpon and Pinterest as JSON, JSONP or XML data with the option to cache the results using memcache, APC or file cache.

## Instructions

The API URL is `https://count.donreach.com/` and allows the following parameters:

|  Parameter              |  Default         |  Description                                                      |
| ----------------------- | ---------------- | ----------------------------------------------------------------- |
| **url**  (required)     | `none`           | The URL of the page you want to fetch the social shares for.      |
| **format** (optional)   | `json`           | The format of the output. Can be either `json`, `jsonp` or `xml`. |
| **callback** (optional) | `processShares`  | The JavaScript callback to execute. 

### Social Shares as JSON Data

Since JSON is the default format `https://count.donreach.com/?url=http://9gag.com` outputs:
```javascript
{
	url: "http://9gag.com",
	shares: {
		total: 635992,
		facebook: 206570,
		twitter: 119585,
		google: 89490,
		linkedin: 618,
		pinterest: 5234,
		stumbleupon: 203013,
		delicious: 0,
		reddit: 0,
		buffer: 752,
		vk: 10730
	}
}
```

### Social Shares as JSONP Data

To use JSONP you can either specify the format or set a callback.  
For example both `https://count.donreach.com/?url=http://9gag.com&format=jsonp` and `https://count.donreach.com/?url=http://9gag.com&callback=processShares` would output:
```javascript
processShares({
	url: "http://9gag.com",
	shares: {
		total: 635992,
		facebook: 206570,
		twitter: 119585,
		google: 89490,
		linkedin: 618,
		pinterest: 5234,
		stumbleupon: 203013,
		delicious: 0,
		reddit: 0,
		buffer: 752,
		vk: 10730
	}
})
```

### Social Shares as XML Data

To get the data in XML just set the format variable: `https://count.donreach.com/?url=http://9gag.com&format=xml` outputs:
```xml
<data>
	<url>http://9gag.com</url>
	<shares>
		<total>432979</total>
		<facebook>206570</facebook>
		<twitter>119585</twitter>
		<google>89490</google>
		<linkedin>618</linkedin>
		<pinterest>5234</pinterest>
		<stumbleupon>0</stumbleupon>
		<delicious>0</delicious>
		<reddit>0</reddit>
		<buffer>752</buffer>
		<vk>10730</vk>
	</shares>
</data>
```

## jQuery & Bootstrap Example

In the following example we're going to display a few pretty Bootstrap buttons with FontAwesome icons and the social share count in bubbles.

### The JavaScript
```javascript
$(document).ready(function () {
    // Get current URL from canonical tag
    var shareUrl = $("link[rel=canonical]").attr("href");
    
    // Ajax request to read share counts. Notice "&callback=?" is appended to the URL to define it as JSONP.
    $.getJSON('https://count.donreach.com/?url=' + encodeURIComponent(shareUrl) + "&callback=?", function (data) {
        shares = data.shares;
        $(".count").each(function (index, el) {
            service = $(el).parents(".share-btn").attr("data-service");
            count = shares[service];
            
            // Divide large numbers eg. 5500 becomes 5.5k
            if(count>1000) {
                count = (count / 1000).toFixed(1);
                if(count>1000) count = (count / 1000).toFixed(1) + "M";
                else count = count + "k";
            }
            $(el).html(count);
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

Here is a working fiddle: http://jsfiddle.net/XM7x7/

## Host it yourself

Download the package and unzip in on your computer. Open config.php with a text-editor of your choice and amend the few configuration values, then upload the whole folder to your server.

## Copyright
&copy; 2013-2014 Adam Bouqdib - http://abemedia.co.uk

Released under GNU GPL 2. See licence.md for further information.

