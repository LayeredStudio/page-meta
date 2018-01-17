URL Preview
=========

**URL Preview** is a PHP library than can get preview info on any URL from the internet! It uses data from [OpenGraph](http://ogp.me/) tags with fallback to HTML meta tags. No API Keys or Tokens needed

## Use cases

* Display Info Cards for links in a article
* Rich preview for links in messaging apps
* Enable file/image upload from URLs

## How to use

#### Installation

Add `layered/url-preview` as a require dependency in your `composer.json` file:
``` bash
$ composer require layered/url-preview
```

#### Usage

For each URL, create a `UrlPreview` instance with URL as first argument. Preview data is retrieved with `getPreview()`, `getProfile()`, `getSite()` and `getEmbed()` methods:
```
$preview = new Layered\UrlPreview('https://instagr.am/p/BYOgma8hoUK/');

$preview->getPreview();   // preview data
$preview->getProfile();   // info about user profile / author
$preview->getSite();      // info about the website
$preview->getEmbed();     // embed code for URL, if embeddable
```

#### Preview data

`getPreview()` method returns `Array` with data for URL with following structure:
```
[
  'type'        =>  'photo',     // content type: website, article, photo, etc
  'url'         =>  'https://www.instagram.com/p/BYOgma8hoUK/',   // canonical URL: follows redirects, formatted by site
  'title'       =>  'Instagram post by Andrei • Aug 25, 2017 at 6:23pm UTC',
  'description' =>  'Who needs a gym membership when there’s this? Outdoor gym, on the beach #barcelona #beach #gym…',
  'image'       =>  'https://scontent-mad1-1.cdninstagram.com/t51.2885-15/e35/21149037_113894809286463_3471808960858685440_n.jpg'   // URL of preview image or Array with url, width, height for image
]
```

#### Profile info

`getProfile()` method returns `false` OR `Array` with info about page author / profile. It contains data present in OpenGraph tags or added by a `Layered\UrlPreview\Formatter`. Example for Twitter & Instagram URLS:
```
[
  'name'        =>  'Andrei',
  'username'    =>  '@andreihere',
  'url'         =>  'https://www.instagram.com/andreihere/'
]
```

#### Site info

`getSite()` method returns info about current website. It contains data present in OpenGraph tags or HTML meta tags.
```
[
  'name'        =>  'Instagram',
  'url'         =>  'https://www.instagram.com',
  'icon'        =>  'https://www.instagram.com/static/images/ico/favicon-192.png/b407fa101800.png'
]
```

#### Embed code

`getEmbed()` method returns `false` OR `Array` with data for embedding the URL. Data is retrieved from oEmbed
```
[
  'width'       =>  658,
  'height'      =>  null,
  'html'        =>  '<html>'
]
```

## More

Please report any issues here on GitHub.

[Any contributions are welcome](CONTRIBUTING.md)
