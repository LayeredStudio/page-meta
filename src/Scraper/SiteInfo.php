<?php
namespace Layered\PageMeta\Scraper;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Make data consistent across sites
 */
class SiteInfo {

	public static $siteNames = [
		'nytimes.com'	=>	'NYTimes',
		'ebay.com'		=>	'eBay',
		'ebay.es'		=>	'eBay',
		'ebay.co.uk'	=>	'eBay',
		'amazon.com'	=>	'Amazon',
		'amazon.ca'		=>	'Amazon',
		'amazon.co.uk'	=>	'Amazon',
		'amazon.es'		=>	'Amazon',
		'amazon.de'		=>	'Amazon',
		'amazon.fr'		=>	'Amazon',
		'amazon.it'		=>	'Amazon',
		'facebook.com'	=>	'Facebook',
		'netflix.com'	=>	'Netflix',
		'dribbble.com'	=>	'Dribbble',
		'medium.com'	=>	'Medium',
		'twitter.com'	=>	'Twitter',
		'youtube.com'	=>	'YouTube',
		'instagram.com'	=>	'Instagram',
		'trello.com'	=>	'Trello'
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

	public static function siteNameFromHtml(Event $event) {
		$crawler = $event->getCrawler();
		$data = $event->getData();
		$parsedUrl = parse_url($crawler->getUri());

		if (empty($data['site']['name']) && !isset($parsedUrl['query']) && !isset($parsedUrl['path'])) {
			$event->addData('site', [
				'name'	=>	$data['page']['title']
			]);
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

	public static function appLinks(Event $event) {
		$crawler = $event->getCrawler();
		$appLinks = [];

		$crawler->filter('meta[property^="al:"]')->each(function($node) use(&$appLinks) {
			$property = substr($node->attr('property'), 3);
			$content = trim($node->attr('content'));

			if (strpos($property, ':') !== false) {
				$property = explode(':', $property, 2);

				if (!isset($appLinks[$property[0]])) {
					$appLinks[$property[0]] = [];
				}

				$appLinks[$property[0]][$property[1]] = $content;
			} else {
				$appLinks[$property] = $content;
			}
		});

		foreach ($appLinks as $platform => $value) {
			if (in_array($platform, ['ios', 'ipad', 'iphone'])) {
				$appLinks[$platform]['store_url'] = 'https://itunes.apple.com/us/app/' . $appLinks[$platform]['app_name'] . '/id' . $appLinks[$platform]['app_store_id'];
			}
			if ($platform === 'android') {
				$appLinks['android']['store_url'] = 'https://play.google.com/store/apps/details?id=' . $appLinks['android']['package'];
			}
		}

		$event->setData('app_links', $appLinks);
	}

}
