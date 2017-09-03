<?php

namespace Layered\Previewer;

class SimpleHtml {

	private $name = 'Simple HTML';

	public function __construct() {
		return $this;
	}

	public function getName() {
		return $this->name;
	}

	public function test($crawler) {
		return count($crawler->filter('title'));
	}

	public function getData($crawler) {
		$allowedMetas = ['description', 'language', 'author'];
		$data = [
			'type'	=>	'website',
			'title'	=>	trim($crawler->filter('title')->text()),
			'url'	=>	$crawler->getUri()
		];

		$crawler->filter('meta[name]')->each(function($node) use($allowedMetas, &$data) {
			$metaName = $node->attr('name');

			if (in_array($metaName, $allowedMetas) && !empty($node->attr('content'))) {
				$data[$metaName] = $node->attr('content');
			} else {
				// for development - see all meta fields
				//$data['tmp'][$metaName] = $node->attr('content');
			}
		});


		// TODO: Extract snippet/summary/content from page


		return $data;
	}

}
