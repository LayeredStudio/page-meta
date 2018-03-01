<?php

namespace Layered\PageMeta\Scraper;

use Symfony\Component\EventDispatcher\Event;

/**
 * Scrape OpenGraph data
 */
class OpenGraph {

	public static function scrape(Event $event) {
		$crawler = $event->getCrawler();

		if (count($crawler->filter('meta[property^="og:"]'))) {
			$data = [];
			$site = [
				'site_name'	=>	''
			];
			$page = [
				'url'			=>	$crawler->getUri(),
				'type'			=>	'',
				'title'			=>	'',
				'description'	=>	'',
				'image'			=>	'',
				'video'			=>	''
			];
			$extra = [];

			$crawler->filter('meta[property^="og:"]')->each(function($node) use(&$data) {
				$property = substr($node->attr('property'), 3);
				$content = $node->attr('content');

				if (strpos($property, ':') !== false) {
					$property = explode(':', $property, 2);

					if (!isset($data[$property[0]])) {
						$data[$property[0]] = [];
					} elseif (isset($data[$property[0]]) && !is_array($data[$property[0]])) {
						$data[$property[0]] = [
							self::guessFieldType($data[$property[0]])	=>	$data[$property[0]]
						];
					}

					$data[$property[0]][$property[1]] = $content;

				} else {
					$data[$property] = $content;
				}
			});

			foreach ($data as $key => $value) {
				if (isset($site[$key])) {
					$site[$key] = $value;
				} else if (isset($page[$key])) {
					$page[$key] = $value;
				} else {
					$extra[$key] = $value;
				}
			}

			// rename 'site_name' to 'name'
			$site['name'] = $site['site_name'];
			unset($site['site_name']);

			// pass along the scraped info
			$event->addData('site', $site);
			$event->addData('page', $page);
			$event->addData('extra', $extra);
		}

	}

	protected static function guessFieldType($string) {
		$type = 'text';

		if (filter_var($string, FILTER_VALIDATE_URL)) {
			$type = 'url';
		} elseif (filter_var($string, FILTER_VALIDATE_EMAIL)) {
			$type = 'email';
		}

		return $type;
	}

}
