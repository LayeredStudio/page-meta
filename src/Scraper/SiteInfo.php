<?php
namespace Layered\PageMeta\Scraper;

use Symfony\Component\EventDispatcher\Event;

/**
 * Make data consistent across sites
 */
class SiteInfo {

	public static $siteNames = [
		'nytimes.com'	=>	'NYTimes',
		'amazon.com'	=>	'Amazon',
		'amazon.ca'		=>	'Amazon',
		'amazon.co.uk'	=>	'Amazon',
		'amazon.es'		=>	'Amazon',
		'amazon.de'		=>	'Amazon',
		'amazon.fr'		=>	'Amazon',
		'amazon.it'		=>	'Amazon',
		'facebook.com'	=>	'Facebook',
		'netflix.com'	=>	'Netflix'
	];

	public static function addSiteNames(Event $event) {
		$crawler = $event->getCrawler();
		$data = $event->getData();

		if ($event->getSection() == 'site' && !$data['name']) {
			$host = str_replace('www.', '', parse_url($data['url'], PHP_URL_HOST));
			if (isset(self::$siteNames[$host])) {
				$data['name'] = self::$siteNames[$host];
				$event->setData($data);
			}
		}
	}

	public static function mediaUrlToArray(Event $event) {
		$data = $event->getData();

		foreach (['image', 'video'] as $field) {
			if (isset($data[$field]) && is_string($data[$field])) {
				$data[$field] = [
					'url'	=>	\Layered\PageMeta\UrlPreview::makeAbsoluteUri($event->getCrawler()->getUri(), $data[$field])
				];
				$event->setData($data);
			}
		}
	}

	public static function ecommerceSites(Event $event) {
		$data = $event->getData();
		$crawler = $event->getCrawler();

		if (strpos($crawler->getUri(), 'amazon.') !== false) {
			$imageUrl = $crawler->filter('.a-dynamic-image');
			if (count($imageUrl)) {
				$event->addData('page', [
					'type'	=>	'product',
					'image'	=>	$imageUrl->attr('data-old-hires')
				]);
			}
		} elseif (strpos($crawler->getUri(), 'ebay.') !== false) {
			$sellerLink = $crawler->filter('a[href*="/usr"]');
			if (count($sellerLink)) {
				$event->addData('profile', [
					'name'	=>	$sellerLink->text(),
					'url'	=>	$sellerLink->attr('href')
				]);
			}
		}
	}

}
