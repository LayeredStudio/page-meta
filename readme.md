<h1 align="center" style="border-bottom: none;">Page Meta 🕵</h1>
<h3 align="center">Get detailed info for any URL on the internet</h3>

**Page Meta** is a PHP library than can retrieve detailed info on any URL from the internet!
It uses data from HTML meta tags and [OpenGraph](http://ogp.me/) with fallback to detailed HTML scraping.

## Highlights
- Works for any valid URL on the internet!
- Follows page redirects
- Uses all scraping methods available: HTML tags, OpenGraph, Schema data

## Potention use cases
* Display Info Cards for links in a article
* Rich preview for links in messaging apps
* Extract info from a user-submitted URL

## How to use

#### Installation

Add `layered/page-meta` as a dependency in your project's `composer.json` file:
``` bash
$ composer require layered/page-meta
```

#### Usage

Create a `UrlPreview` instance, then call `loadUrl($url)` method with your URL as first argument. Preview data is retrieved with `get($section)` or `getAll()` methods:
```
require 'vendor/autoload.php';

$preview = new Layered\PageMeta\UrlPreview;
$preview->loadUrl('https://www.instagram.com/p/BbRyo_Kjqt1/');

$allPageData = $preview->getAll();	// contains all scraped data
$siteInfo = $preview->get('site');	// get general info about the website
```

#### Returned data

Returned data will be an `Array` with following format:
```
{
	"site": {
		"secure":		true,
		"url":			"https:\/\/www.instagram.com",
		"icon":			"https:\/\/www.instagram.com\/static\/images\/ico\/favicon-192.png\/b407fa101800.png",
		"language":		"en",
		"responsive":	true,
		"name":			"Instagram"
	},
	"page": {
		"type":			"photo",
		"url":			"https:\/\/www.instagram.com\/p\/BbRyo_Kjqt1\/",
		"title":		"GitHub on Instagram",
		"description":	"There\u2019s still time to join the #GitHubGameOff and build a game inspired by throwbacks. Get started\u2026",
		"image":		{
			"url": "https:\/\/scontent-mad1-1.cdninstagram.com\/vp\/73b1790d77548031327e64ee83196706\/5B4AD567\/t51.2885-15\/e35\/23421974_1768724519826754_3855913942043852800_n.jpg"
		}
	},
	"author": {
		"name":			"GitHub",
		"handle":		"@github",
		"url":			"https:\/\/www.instagram.com\/github\/"
	},
	"app_links": {
		"ios": {
			"url": "nflx:\/\/www.netflix.com\/title\/80014749",
			"app_store_id": "363590051",
			"app_name": "Netflix",
			"store_url": "https:\/\/itunes.apple.com\/us\/app\/Netflix\/id363590051"
		},
		"android": {
			"url": "nflx:\/\/www.netflix.com\/title\/80014749",
			"package": "com.netflix.mediaclient",
			"app_name": "Netflix",
			"store_url": "https:\/\/play.google.com\/store\/apps\/details?id=com.netflix.mediaclient"
		}
	}
}
```

## Public API
`UrlPreview` class provides the following public methods:

#### `__construct(array $headers)`
Start the UrlPreview instance. Pass extra headers to send when requesting the page URL

**Returns:** UrlPreview instance

#### `loadUrl(string $url)`
Load and start the scrape process for any valid URL

**Returns:** UrlPreview instance

#### `getAll()`
Get all data scraped from page

**Return:** `Array` with scraped data in following format
- `site` - info about the website
  - `url` - main site URL
  - `name` - site name, ex: 'Instagram' or 'Medium'
  - `secure` - Boolean true|false depending on http connection
  - `responsive` - Boolean true|false. `True` if site has `viewport` meta tag present. Basic check for responsiveness
  - `icon` - site icon
  - `language` - ISO 639-1 language code, ex: `en`, `es`
- `page` - info about the page at current URL
  - `type` - page type, ex: `website`, `article`, `profile`, `video`, etc
  - `url` - canonical URL for the page
  - `title` - page title
  - `description` - page description
  - `image` - `Array` containing image info, if present:
	- `url` - image URL
	- `width` - image width
	- `height` - image width
  - `video` - `Array` containing video info, if found on page:
	- `url` - video URL
	- `width` - video width
	- `height` - video width
- `author` - info about the content author, ex:
  - `name` - Author's name on a blog, person's name on social network sites
  - `handle` - Social media site username
  - `url` - Author URL for more articles or Profile URL on social network sites
- `app_links` - `Array` containing apps linked to page, like:
  - `ios` - iOS app
	- `url` - link for in-app action, ex: 'nflx://www.netflix.com/title/80014749'
	- `app_store_id` - Apple AppStore app ID
	- `app_name` - name of the app
	- `store_url` - link to installable app
  - `android` - Android app
	- `url` - link for in-app action, ex: 'nflx://www.netflix.com/title/80014749'
	- `package` - Android PlayStore app ID
	- `app_name` - name of the app
	- `store_url` - link to installable app

#### `get(string $section)`
Get data in one scraped section `site`, `page`, `profile` or `app_links`

**Return:** `Array` with section scraped data. See `getAll` for data format

#### `addListener(string $eventName, callable $listener, int $priority = 0)`
Attach an event on `UrlPreview` for data processing or scrape process. Arguments:
- `$eventName` - on which event to listen, available:
  - `page.scrape` - fired when the scraping process starts
  - `data.filter` - fired when data is requested by `getData()` or `getAll()` methods
- `$listener` - a callable reference, which will get the `$event` parameter with available data
- `$priority` - order on which the callable should be executed


### Extending the library
If there's need to more scraped data for a URL, more functionality can be attached to **PageMeta** library. Example for returing the 'Terms and Conditions' link from pages:
```
use Symfony\Component\EventDispatcher\Event;

$previewer = new \Layered\PageMeta\UrlPreview;
$previewer->addListener('page.scrape', function(Event $event) {
	$currentScrapedData = $event->getData();	// check data from other scrapers
	$crawler = $event->getCrawler();			// instance of DomCrawler Symfony Component
	$termsLink = '';

	$crawler->filter('a[href*=terms]')->each(function($node) use(&$termsLink) {
		$termsLink = $node->attr('href');
	});

	// forwards the scraped data
	$event->addData('site', [
		'termsLink'	=>	$termsLink
	]);
});
$previewer->loadUrl('http://github.com');
```


## More

Please report any issues here on GitHub.

[Any contributions are welcome](CONTRIBUTING.md)
