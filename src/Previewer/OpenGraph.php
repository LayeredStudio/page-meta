<?php

namespace Layered\Previewer;

class OpenGraph {

	private $name = 'Open Graph';

	public function __construct() {
		return $this;
	}

	public function getName() {
		return $this->name;
	}

	public function test($crawler) {
		return count($crawler->filter('meta[property^="og:"]'));
	}

	public function getData($crawler) {
		$data = [
			'type'	=>	'website',
			'url'	=>	$crawler->getUri()
		];

		$crawler->filter('meta[property^="og:"]')->each(function($node) use(&$data) {
			$property = substr($node->attr('property'), 3);
			$content = $node->attr('content');

			if (strpos($property, ':') !== false) {
				$property = explode(':', $property, 2);

				if (!isset($data[$property[0]])) {
					$data[$property[0]] = [];
				} elseif (isset($data[$property[0]]) && !is_array($data[$property[0]])) {
					$data[$property[0]] = [
						$this->guessFieldType($data[$property[0]])	=>	$data[$property[0]]
					];
				}

				$data[$property[0]][$property[1]] = $content;

			} else {
				$data[$property] = $content;
			}
		});

		return $data;
	}

	protected function guessFieldType($string) {
		$type = 'text';

		if (filter_var($string, FILTER_VALIDATE_URL)) {
			$type = 'url';
		} elseif (filter_var($string, FILTER_VALIDATE_EMAIL)) {
			$type = 'email';
		}

		return $type;
	}

}
