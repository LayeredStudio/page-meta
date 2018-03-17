<?php
namespace Layered\PageMeta\Scraper;

use Symfony\Component\EventDispatcher\Event;

/**
 * Scrape data available in meta tags
 */
class SimpleHtml {

	public static function scrape(Event $event) {
		$crawler = $event->getCrawler();

		if (count($crawler->filter('title'))) {
			$data = $event->getData();

			$site = [
				'icon'			=>	[],
				'language'		=>	current(explode('-', trim($crawler->filter('html')->attr('lang')))),
				'author'		=>	'',
				'generator'		=>	'',
				'theme-color'	=>	''
			];

			$page = [
				'url'			=>	$crawler->getUri(),
				'medium'		=>	'',
				'title'			=>	trim($crawler->filter('title')->text()),
				'description'	=>	'',
				'keywords'		=>	''
			];

			$extra = [];

			$crawler->filter('meta[name]')->each(function($node) use(&$site, &$page, &$extra) {
				$metaName = strtolower($node->attr('name'));

				if (isset($site[$metaName]) && !empty($node->attr('content'))) {
					$site[$metaName] = $node->attr('content');
				} else if (isset($page[$metaName]) && !empty($node->attr('content'))) {
					$page[$metaName] = $node->attr('content');
				} else {
					$extra[$metaName] = $node->attr('content');
				}
			});

			// check for site icon
			$crawler->filter('link[rel=apple-touch-icon], link[rel~=icon]')->each(function($node) use(&$site) {
				if (!empty($node->attr('href'))) {
					$site['icon'][$node->attr('rel')] = $node->attr('href');
				}
			});

			// get the best quality image as icon
			ksort($site['icon']);
			$site['icon'] = $site['icon'] ? \Layered\Pagemeta\UrlPreview::makeAbsoluteUri($crawler->getUri(), current($site['icon'])) : $data['site']['url'] . '/favicon.ico';

			// rename 'medium' to 'type' - consistent with OpenGraph field name
			$page['type'] = $page['medium'];
			unset($page['medium']);

			// basic check for responsiveness
			$site['responsive'] = !!count($crawler->filter('meta[name="viewport"]'));

			// pass along the scraped info
			$event->addData('site', $site);
			$event->addData('page', $page);
			$event->addData('extra', $extra);
		}
	}

}
