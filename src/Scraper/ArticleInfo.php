<?php
namespace Layered\PageMeta\Scraper;

use Symfony\Component\EventDispatcher\Event;

/**
 * Scrape data related to news, blog posts and articles
 */
class ArticleInfo {

	public static function scrape(Event $event) {
		$crawler = $event->getCrawler();

		if (count($crawler->filter('title'))) {
			$page = [
				'date'			=>	''
			];

			$profile = [
				'name'			=>	'',
				'url'			=>	''
			];

			// Extract Author name
			$crawler->filter('meta[property=author], meta[name="parsely-author"], meta[property="sailthru.author"]')->each(function($node) use(&$profile) {
				if (!empty($node->attr('content'))) {
					$profile['name'] = $node->attr('content');
				}
			});

			// Extract Author profile URL
			$crawler->filter('meta[property="article:author"]')->each(function($node) use(&$profile) {
				if (!empty($node->attr('content'))) {
					$profile['url'] = $node->attr('content');
				}
			});

			// Extract article date
			$crawler->filter('meta[property="article:published_time"], meta[name="iso-8601-publish-date"], meta[name="parsely-pub-date"], meta[property="sailthru.date"]')->each(function($node) use(&$page) {
				if (!empty($node->attr('content'))) {
					$page['date'] = $node->attr('content');
				}
			});

			// pass along the scraped info
			$event->addData('page', $page);
			$event->addData('profile', $profile);
		}

	}

}
