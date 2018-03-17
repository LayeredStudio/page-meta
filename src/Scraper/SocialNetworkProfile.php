<?php
namespace Layered\PageMeta\Scraper;

use Symfony\Component\EventDispatcher\Event;

/**
 * Scrape profile info from social networks
 */
class SocialNetworkProfile {

	public static function getProfiles(Event $event) {
		$crawler = $event->getCrawler();
		$data = $event->getData();

		$site = [];
		$page = [];
		$profile = [];

		if (preg_match('/^https:\/\/twitter.com\/([A-Za-z0-9_]{1,15})[\/]?$/', $crawler->getUri())) {
			preg_match('/([\s\S]+) \(@([A-Za-z0-9_]{1,15})\) | Twitter/', $data['page']['title'], $titleMatches);

			$site['site_name'] = 'Twitter';
			$page['type'] = 'profile';
			$profile = [
				'name'		=>	$titleMatches[1],
				'username'	=>	'@' . $titleMatches[2],
				'url'		=>	$crawler->getUri()
			];

			$jsonData = $crawler->filter('#init-data')->attr('value');

			if ($jsonData) {
				$jsonData = json_decode($jsonData, true);

				$page['description'] = $jsonData['profile_user']['description'];
				$page['image'] = [
					'url'		=>	str_replace('normal', '400x400', $jsonData['profile_user']['profile_image_url_https']),
					'width'		=>	400,
					'height'	=>	400
				];
			}

		} elseif (strpos($crawler->getUri(), 'twitter.com') !== false && $data['page']['type'] == 'article') {
			preg_match('/twitter.com\/([A-Za-z0-9_]{1,15})\/status/', $crawler->getUri(), $urlMatches);
			preg_match('/^([\s\S]+) on Twitter$/', $data['page']['title'], $titleMatches);

			$page['date'] = date(DATE_ATOM, $crawler->filter('.time > a > span')->attr('data-time'));
			$page['description'] = trim($data['page']['description'], '“”');
			$profile = [
				'name'		=>	$titleMatches[1],
				'username'	=>	'@' . $urlMatches[1],
				'url'		=>	'https://twitter.com/' . $urlMatches[1]
			];
		} elseif (strpos($crawler->getUri(), 'instagram.com') !== false && $data['page']['type'] == 'profile') {
			$title = explode('•', $data['page']['title']);
			$name = explode('(', $title[0]);

			$profile = [
				'name'		=>	trim($name[0]),
				'username'	=>	trim(str_replace(')', '', $name[1])),
				'url'		=>	$data['page']['url']
			];
		} elseif (strpos($crawler->getUri(), 'instagram.com') !== false && in_array($data['page']['type'], ['photo', 'video'])) {
			preg_match('/ - ([\s\S]+) \(@([a-zA-Z0-9._]+)\) on Instagram: “([\s\S]+)”/', $data['page']['description'], $matches);

			$page['title'] = current(explode(':', $data['page']['title']));
			$page['description'] = $matches[3];
			$profile = [
				'name'		=>	$matches[1],
				'username'	=>	'@' . $matches[2],
				'url'		=>	'https://www.instagram.com/' . $matches[2] . '/'
			];
		}

		$event->addData('site', $site);
		$event->addData('page', $page);
		$event->addData('profile', $profile);
	}

}
