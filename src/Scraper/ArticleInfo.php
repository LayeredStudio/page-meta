<?php
namespace Layered\PageMeta\Scraper;

use Symfony\Component\EventDispatcher\Event;

/**
 * Scrape data related to news, blog posts and articles
 */
class ArticleInfo {

	public static function scrape(Event $event) {
		$crawler = $event->getCrawler();
		$page = [
			'date'			=>	''
		];

		$profile = [
			'name'			=>	'',
			'url'			=>	''
		];

		if (count($crawler->filter('title'))) {

			// Extract Author name
			$crawler->filter('meta[property=author], meta[name="parsely-author"], meta[property="sailthru.author"]')->each(function($node) use(&$profile) {
				$profile['name'] = $node->attr('content');
			});

			// Extract Author profile URL
			$crawler->filter('meta[property="article:author"]')->each(function($node) use(&$profile) {
				$profile['url'] = $node->attr('content');
			});

			if (!$profile['name']) {
				$authorName = $crawler->filter('.post-author-name, .entry .author, [class*="byline"], [id*="byline"]');
				if (count($authorName)) {

					// does it have an link inside?
					$hasLink = $authorName->filter('a');
					if ($authorName->nodeName() === 'a') {
						$profile['name'] = $authorName->text();
						$profile['url'] = $authorName->attr('href');
					} elseif (count($hasLink)) {
						$profile['name'] = $hasLink->text();
						$profile['url'] = $hasLink->attr('href');
					} else {
						$profile['name'] = $authorName->text();
					}
				}
			}

			if (!$profile['url']) {
				$authorLinks = $crawler->filter('a[href*="/author"]');
				if (count($authorLinks)) {
					$profile['url'] = $authorLinks->attr('href');
				}
			}

			if ($profile['url']) {
				$profile['url'] = \Layered\PageMeta\UrlPreview::makeAbsoluteUri($event->getCrawler()->getUri(), $profile['url']);
			}

			// Extract article date
			$crawler->filter('meta[property="article:published"], meta[property="article:published_time"], meta[name="iso-8601-publish-date"], meta[name="parsely-pub-date"], meta[property="sailthru.date"]')->each(function($node) use(&$page) {
				$page['date'] = date(DATE_ATOM, strtotime($node->attr('content')));
			});

			if (!$page['date']) {
				$dateElement = $crawler->filter('.entry time[datetime]');
				if (count($dateElement)) {
					$page['date'] = $dateElement->attr('datetime');
				}
			}

		}

		if (strpos($crawler->getUri(), 'nytimes.com') !== false) {
			$siteData = $event->getData()['site'];
			$profile['name'] = $siteData['author'];
			$siteData['author'] = '';
			$event->setData('site', $siteData);
		}

		// pass along the scraped info
		$event->addData('page', $page);
		$event->addData('profile', $profile);
	}

}
