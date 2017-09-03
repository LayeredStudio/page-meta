<?php

namespace Layered\Formatter;

class TwitterFields {

	private $name = 'Format Twitter data';

	public function __construct() {
		return $this;
	}

	public function getName() {
		return $this->name;
	}

	public function test($crawler, $previewer) {
		return strpos($crawler->getUri(), 'twitter.com') !== false;
	}

	public function format($fields, $crawler, $previewer) {

		if ($previewer->getName() == 'Simple HTML' && preg_match('/^https:\/\/twitter.com\/([A-Za-z0-9_]{1,15})[\/]?$/', $crawler->getUri())) {
			preg_match('/([\s\S]+) \(@([A-Za-z0-9_]{1,15})\) | Twitter/', $fields['title'], $titleMatches);

			$fields['site_name'] = 'Twitter';
			$fields['type'] = 'profile';
			$fields['profile'] = [
				'name'		=>	$titleMatches[1],
				'username'	=>	'@' . $titleMatches[2],
				'url'		=>	$crawler->getUri()
			];

			$jsonData = $crawler->filter('#init-data')->attr('value');

			if ($jsonData) {
				$jsonData = json_decode($jsonData, true);
				$fields['description'] = $jsonData['profile_user']['description'];
				$fields['image'] = [
					'url'		=>	str_replace('normal', '400x400', $jsonData['profile_user']['profile_image_url_https']),
					'width'		=>	400,
					'height'	=>	400
				];
			}

		} elseif ($previewer->getName() == 'Open Graph' && $fields['type'] == 'article') {
			preg_match('/twitter.com\/([A-Za-z0-9_]{1,15})\/status/', $fields['url'], $urlMatches);
			preg_match('/^([\s\S]+) on Twitter$/', $fields['title'], $titleMatches);

			$fields['description'] = trim($fields['description'], '“”');
			$fields['profile'] = [
				'name'		=>	$titleMatches[1],
				'username'	=>	'@' . $urlMatches[1],
				'url'		=>	'https://twitter.com/' . $urlMatches[1]
			];
		}

		return $fields;
	}

}
