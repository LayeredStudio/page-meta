<?php

require 'vendor/autoload.php';


echo '<pre>';

echo '<h1>Instagram site</h1>';
$url = new Layered\UrlPreview('https://instagr.am');
print_r($url->getPreview());
print_r($url->getProfile());
print_r($url->getSite());

echo '<h1>Instagram photo</h1>';
$url = new Layered\UrlPreview('https://www.instagram.com/p/BYjGxqkgQCm/?taken-by=daria_solak_illustrations');
print_r($url->getPreview());
print_r($url->getProfile());
print_r($url->getSite());

echo '<h1>Instagram video</h1>';
$url = new Layered\UrlPreview('https://www.instagram.com/p/BPezVTTjP7g/?taken-by=andreihere');
print_r($url->getPreview());
print_r($url->getProfile());
print_r($url->getSite());

echo '<h1>YouTube video</h1>';
$url = new Layered\UrlPreview('https://www.youtube.com/watch?v=3iTYpWfP0Og');
print_r($url->getPreview());
print_r($url->getProfile());
print_r($url->getSite());

echo '<h1>Tweet</h1>';
$url = new Layered\UrlPreview('https://twitter.com/AndreiIgna/status/890510906819571719');
print_r($url->getPreview());
print_r($url->getProfile());
print_r($url->getSite());

echo '<h1>Medium site</h1>';
$url = new Layered\UrlPreview('https://rokm.ro/');
print_r($url->getPreview());
print_r($url->getProfile());
print_r($url->getSite());

echo '<h1>Medium article</h1>';
$url = new Layered\UrlPreview('https://medium.com/@andrewcouldwell/plasma-design-system-4d63fb6c1afc');
print_r($url->getPreview());
print_r($url->getProfile());
print_r($url->getSite());

echo '<h1>Medium article</h1>';
$url = new Layered\UrlPreview('https://rokm.ro/what-makes-a-good-fitness-app-b5416d410002');
print_r($url->getPreview());
print_r($url->getProfile());
print_r($url->getSite());

echo '<h1>WordPress article</h1>';
$url = new Layered\UrlPreview('http://roberthajnal.ro/blog/2017/07/23/maraton-7500-2017/');
print_r($url->getPreview());
print_r($url->getProfile());
print_r($url->getSite());

echo '<h1>Facebook profile</h1>';
$url = new Layered\UrlPreview('https://facebook.com/andreiigna');
print_r($url->getPreview());
print_r($url->getProfile());
print_r($url->getSite());

echo '<h1>Facebook post</h1>';
$url = new Layered\UrlPreview('https://www.facebook.com/mxgur/posts/10107274070905477');
print_r($url->getPreview());
print_r($url->getProfile());
print_r($url->getSite());

echo '</pre>';
