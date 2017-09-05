<?php

namespace Layered;

use Goutte\Client;


class UrlPreview {

	private static $previewers = [];
	private static $formatters = [];

	protected $previewer = false;
	protected $crawler = null;
	protected $url;
	protected $data = [];

	public function __construct($url) {

		// check for valid URL
		if (!filter_var($url, FILTER_VALIDATE_URL)) {
			throw new \Exception('Invalid URL');
		}

		$this->url = $url;

		return $this;
	}

	public static function load($url) {
		return new static($url);
	}

	protected function process() {

		// load content from URL
		$client = new Client();
		$this->crawler = $client->request('GET', $this->url);


		// extract data
		foreach (self::$previewers as $priority => $previewers) {
			if (!$this->previewer) {
				foreach ($previewers as $previewer) {
					if (!$this->previewer && $previewer->test($this->crawler)) {
						$this->previewer = $previewer;
						$this->data = $previewer->getData($this->crawler);
						//$this->data['previewer'] = $previewer->getName();
					}
				}
			}
		}


		// format data
		foreach (self::$formatters as $priority => $formatters) {
			foreach ($formatters as $formatter) {
				if ($formatter->test($this->crawler, $this->previewer)) {
					$this->data = $formatter->format($this->data, $this->crawler, $previewer);
				}
			}
		}

	}

	public function getAll() {

		if (!$this->crawler) {
			$this->process();
		}

		return $this->data;
	}

	public function getPreview() {

		if (!$this->crawler) {
			$this->process();
		}

		$preview = [];
		$fields = ['type', 'url', 'title', 'description', 'image'];

		foreach ($fields as $field) {
			$preview[$field] = isset($this->data[$field]) ? $this->data[$field] : '';
		}

		if (isset($preview['image']) && is_array($preview['image'])) {
			$preview['image']['url'] = $this->parseImageUrl($preview['url'], $preview['image']['url']);
		} elseif (isset($preview['image']) && !empty($preview['image'])) {
			$preview['image'] = $this->parseImageUrl($preview['url'], $preview['image']);
		}

		return $preview;
	}

	public function getProfile() {

		if (!$this->crawler) {
			$this->process();
		}

		return isset($this->data['profile']) ? $this->data['profile'] : false;
	}

	public function getSite() {

		if (!$this->crawler) {
			$this->process();
		}

		$url = $this->parseUrl($this->crawler->getUri());

		$site = [
			'url'	=>	$url['scheme'] . '://' . $url['host'],
			'name'	=>	isset($this->data['site_name']) ? $this->data['site_name'] : '',
			'icon'	=>	''
		];

		try {
			$this->crawler->filter('link[rel=apple-touch-icon]')->each(function($link) use(&$site, $url) {
				$site['icon'] = $this->parseImageUrl($this->crawler->getUri(), $link->attr('href'));
			});

			if (empty($site['icon'])) {
				$this->crawler->filter('link[rel=icon]')->each(function($link) use(&$site, $url) {
					$site['icon'] = $this->parseImageUrl($this->crawler->getUri(), $link->attr('href'));
				});
			}
		} catch (\Exception $e) {
			// it's fine
		}

		return $site;
	}

	public function getEmbed() {
		return $this->embedData;
	}

	public static function addPreviewer($previewer, $priority = 10) {
		self::$previewers[$priority][] = $previewer;
	}

	public static function addFormatter($formatter, $priority = 10) {
		self::$formatters[$priority][] = $formatter;
	}

	protected function parseUrl($url) {
		// TODO maybe use a better parser
		return parse_url($url);
	}

	protected function parseImageUrl($siteUrl, $imageUrl) {

		if (strpos($imageUrl, 'http') === false) {
			if ($imageUrl['0'] === '/') {
				$siteUrl = $this->parseUrl($siteUrl);
				$imageUrl = $siteUrl['scheme'] . '://' . $siteUrl['host'] . $imageUrl;
			} else {
				$imageUrl = rtrim($siteUrl, '/') . '/' . $imageUrl;
			}
		}

		return $imageUrl;
	}

}


// add basic Previewers
UrlPreview::addPreviewer(new Previewer\OpenGraph);
UrlPreview::addPreviewer(new Previewer\SimpleHtml, 100);

// Formatters to get extra Twitter & Instagram profile info
UrlPreview::addFormatter(new Formatter\InstagramFromOpenGraph);
UrlPreview::addFormatter(new Formatter\TwitterFields);
