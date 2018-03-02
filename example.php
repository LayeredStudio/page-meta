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
