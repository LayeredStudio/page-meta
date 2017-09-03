<?php

namespace Layered\Formatter;

class InstagramFromOpenGraph {

	private $name = 'Format Instagram data from OpenGraph';

	public function __construct() {
		return $this;
	}

	public function getName() {
		return $this->name;
	}

	public function test($crawler, $previewer) {
		return strpos($crawler->getUri(), 'instagram.com') !== false && $previewer->getName() == 'Open Graph';
	}

	public function format($fields, $crawler, $previewer) {

		if ($fields['type'] == 'profile') {
			$title = explode('•', $fields['title']);
			$name = explode('(', $title[0]);

			$fields['profile'] = [
				'name'		=>	trim($name[0]),
				'username'	=>	trim(str_replace(')', '', $name[1])),
				'url'		=>	$fields['url']
			];
		} elseif ($fields['type'] == 'instapp:photo' || $fields['type'] == 'video') {
			preg_match('/ - ([\s\S]+) \(@([a-zA-Z0-9._]+)\) on Instagram: “([\s\S]+)”/', $fields['description'], $matches);

			$fields['description'] = $matches[3];
			$fields['type'] = str_replace('instapp:photo', 'photo', $fields['type']);
			$fields['profile'] = [
				'name'		=>	$matches[1],
				'username'	=>	'@' . $matches[2],
				'url'		=>	'https://www.instagram.com/' . $matches[2] . '/'
			];
		}

		return $fields;
	}

}
