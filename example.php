<?php

require 'vendor/autoload.php';

use Layered\PageMeta\UrlPreview;

echo '<pre>';

echo '<h1>Instagram site</h1>';
$url = new UrlPreview('https://instagr.am');
print_r($url->getAll());

echo '<h1>Instagram profile</h1>';
$url = new UrlPreview('https://www.instagram.com/layereddesign');
print_r($url->getAll());

echo '<h1>Medium article</h1>';
$url = new UrlPreview('https://medium.com/@andrewcouldwell/plasma-design-system-4d63fb6c1afc');
print_r($url->getAll());

echo '<h1>YouTube video</h1>';
$url = new UrlPreview('https://www.youtube.com/watch?v=3iTYpWfP0Og');
print_r($url->getAll());

/*
echo '<h1>article</h1>';
$url = new UrlPreview('https://en.wikipedia.org/wiki/Murcia');
print_r($url->getAll());

echo '<h1>article</h1>';
$url = new UrlPreview('https://soundcloud.com/beautybrainsp/beauty-brain-swag-bandicoot');
print_r($url->getAll());

echo '<h1>article</h1>';
$url = new UrlPreview('https://www.amazon.com/dp/B06XCM9LJ4');
print_r($url->getAll());

echo '<h1>article</h1>';
$url = new UrlPreview('https://vimeo.com/188175573');
print_r($url->getAll());

echo '<h1>article</h1>';
$url = new UrlPreview('https://www.washingtonpost.com/sf/local/2017/10/21/in-the-shadows-of-refinery-row-a-parable-of-redevelopment-and-race/?hpid=hp_hp-top-table-main_corpuschristi743pm%3Ahomepage%2Fstory');
print_r($url->getAll());

echo '<h1>article</h1>';
$url = new UrlPreview('https://techcrunch.com/2017/10/26/super-mario-odyssey-review-a-masterpiece-of-twists-and-turns/');
print_r($url->getAll());

echo '<h1>article</h1>';
$url = new UrlPreview('https://www.theverge.com/2017/10/27/16145498/insecure-broad-city-high-maintenance-web-series-hbo-comedy-central');
print_r($url->getAll());

echo '<h1>product page</h1>';
$url = new UrlPreview('https://www.apple.com/homepod');
print_r($url->getAll());

echo '<h1>article</h1>';
$url = new UrlPreview('http://www.bloomberg.com/news/articles/2016-05-24/as-zenefits-stumbles-gusto-goes-head-on-by-selling-insurance');
print_r($url->getAll());

echo '<h1>Instagram photo</h1>';
$url = new UrlPreview('https://www.instagram.com/p/BYjGxqkgQCm/?taken-by=daria_solak_illustrations');
print_r($url->getAll());

echo '<h1>Instagram video</h1>';
$url = new UrlPreview('https://www.instagram.com/p/BbRyo_Kjqt1/');
print_r(json_encode($url->getAll()));

echo '<h1>Tweet</h1>';
$url = new UrlPreview('https://twitter.com/AndreiIgna');
print_r($url->getAll());

echo '<h1>Tweet</h1>';
$url = new UrlPreview('https://twitter.com/AndreiIgna/status/890510906819571719');
print_r($url->getAll());

echo '<h1>Medium site</h1>';
$url = new UrlPreview('https://rokm.ro/');
print_r($url->getAll());

echo '<h1>Medium article</h1>';
$url = new UrlPreview('https://rokm.ro/what-makes-a-good-fitness-app-b5416d410002');
print_r($url->getAll());

echo '<h1>WordPress article</h1>';
$url = new UrlPreview('http://roberthajnal.ro/blog/2017/07/23/maraton-7500-2017/');
print_r($url->getAll());

echo '<h1>Facebook profile</h1>';
$url = new UrlPreview('https://facebook.com/andreiigna');
print_r($url->getAll());

echo '<h1>Facebook post</h1>';
$url = new UrlPreview('https://www.facebook.com/mxgur/posts/10107274070905477');
print_r($url->getAll());
*/
