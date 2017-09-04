<?php

require 'vendor/autoload.php';


echo '<pre>';

echo '<h1>YouTube</h1>';
print_r(Layered\UrlPreview::load('https://www.youtube.com/')->getData());
print_r(Layered\UrlPreview::load('https://www.youtube.com/watch?v=3iTYpWfP0Og')->getData());

echo '<h1>WordPress</h1>';
print_r(Layered\UrlPreview::load('http://roberthajnal.ro/blog/2017/07/23/maraton-7500-2017/')->getData());
print_r(Layered\UrlPreview::load('http://roberthajnal.ro/blog/2017/07/01/lavaredo-ultra-trail-la-ultra-si-cei-mai-buni-din-lume-se-taie/')->getData());

echo '<h1>Medium</h1>';
print_r(Layered\UrlPreview::load('https://rokm.ro/')->getData());
print_r(Layered\UrlPreview::load('https://medium.com/@andrewcouldwell/plasma-design-system-4d63fb6c1afc')->getData());
print_r(Layered\UrlPreview::load('https://rokm.ro/what-makes-a-good-fitness-app-b5416d410002')->getData());

echo '<h1>Instagram</h1>';
print_r(Layered\UrlPreview::load('https://instagram.com')->getData());
print_r(Layered\UrlPreview::load('https://instagram.com/andreihere')->getData());
print_r(Layered\UrlPreview::load('https://instagr.am/p/BYOgma8hoUK/')->getData());
print_r(Layered\UrlPreview::load('https://www.instagram.com/p/BYjGxqkgQCm/?taken-by=daria_solak_illustrations')->getData());
print_r(Layered\UrlPreview::load('https://www.instagram.com/p/BPezVTTjP7g/?taken-by=andreihere')->getData());

echo '<h1>Facebook</h1>';
print_r(Layered\UrlPreview::load('https://facebook.com')->getData());
print_r(Layered\UrlPreview::load('https://facebook.com/andreiigna')->getData());
print_r(Layered\UrlPreview::load('https://www.facebook.com/cciuca/posts/1857443374282334')->getData());
print_r(Layered\UrlPreview::load('https://www.facebook.com/mxgur/posts/10107274070905477')->getData());
print_r(Layered\UrlPreview::load('https://www.facebook.com/photo.php?fbid=1889887641037956&set=a.764654416894623.1073741851.100000502141017&type=3&theater')->getData());

echo '<h1>Twitter</h1>';
print_r(Layered\UrlPreview::load('https://twitter.com')->getData());
print_r(Layered\UrlPreview::load('https://twitter.com/AndreiIgna')->getData());
print_r(Layered\UrlPreview::load('https://twitter.com/AndreiIgna/status/903558303065759744')->getData());
print_r(Layered\UrlPreview::load('https://twitter.com/AndreiIgna/status/890510906819571719')->getData());
print_r(Layered\UrlPreview::load('https://twitter.com/AndreiIgna/status/902280395336626176')->getData());

echo '</pre>';
