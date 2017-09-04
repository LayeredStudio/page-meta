URL Preview
=========

**URL Preview** is a PHP library than can get preview info on any URL from the internet! It uses data from [OpenGraph](http://ogp.me/) tags with fallback to HTML meta tags.

## Use cases

* Display Info Cards for links in a article
* Rich preview for links in messaging apps
* Enable file/image upload from URLs
* *many more*

## How to use

#### Installation

Add `layered/url-preview` as a require dependency in your `composer.json` file:
``` bash
$ composer require layered/url-preview
```

#### Usage

For each URL, create a `UrlPreview` instance with URL as first argument. Data is retrieved with `getData()` method:
```
$url = new Layered\UrlPreview('https://instagr.am/p/BYOgma8hoUK/');
$urlData = $url->getData();

// shorthand method
$urlData = Layered\UrlPreview::load('https://instagr.am/p/BYOgma8hoUK/')->getData();
```

#### Preview data

The returned data from URLs is an Array with the following structure:
```
[
  'type'        =>  'photo',     // content type: website, article, photo, etc
  'url'         =>  'https://www.instagram.com/p/BYOgma8hoUK/',   // processed URL: follows redirects, formatted by site
  'title'       =>  'Instagram post by Andrei • Aug 25, 2017 at 6:23pm UTC',
  'image'       =>  'https://scontent-mad1-1.cdninstagram.com/t51.2885-15/e35/21149037_113894809286463_3471808960858685440_n.jpg',
  'description' =>  'Who needs a gym membership when there’s this? Outdoor gym, on the beach #barcelona #beach #gym…'
]
```
Each URL may return extra fields, present in OpenGraph tags or added by a `Layered\UrlPreview\Formatter`. Example of `profile` field for Twitter & Instagram URLS:

```
[
  // always present tags
  'profile'     =>  [
    'name'        =>  'Andrei',
    'username'    =>  '@andreihere',
    'url'         =>  'https://www.instagram.com/andreihere/'
  ]
]
```


## More

Please report any issues here on GitHub.
[Any contributions are welcome](CONTRIBUTING.md)
