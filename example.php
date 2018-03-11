<?php

require 'vendor/autoload.php';

use Layered\PageMeta\UrlPreview;

echo '<pre>';

$urls = [
	'https://www.instagram.com/p/BdAVIhHD5sR/',
	'https://store.google.com',
	'https://github.com',
	'https://www.youtube.com/watch?v=D_eZxSYRhco',
	'https://twitter.com/SpaceX/status/960910705808609280',
	'https://www.wired.com/story/mind-games-the-tortured-lives-of-targeted-individuals/',
	'https://www.nytimes.com/2018/03/06/automobiles/autoshow/european-automakers-electric-cars-geneva.html',
	'https://www.theverge.com/2017/10/31/16579748/apple-iphone-x-review',
	'https://en.blog.wordpress.com/2018/02/02/reader-conversations/',
	'https://medium.com/@ev/welcome-to-medium-9e53ca408c48',
	'https://dribbble.com/shots/4165767-Notifications-for-iPhone-and-iPad',
	'http://amzn.eu/3tbEF8j',
	'https://www.ebay.es/itm/Bicicleta-FAT-Bike-cambio-Shimano/112562360485',
	'https://www.facebook.com/KeyAndPeele/photos/rpp.256107687771505/852998691415732/?type=3&theater',
	'https://www.reddit.com/r/funny/comments/82ckwf/pic_of_two_plump_pigeons_perched_on_the_ledge_but/',
	'https://www.netflix.com/title/80014749'
];

$previewer = new UrlPreview;

foreach ($urls as $index => $url) {
	print_r($previewer->loadUrl($url)->getAll());
	if ($index >= 15) break;
}
