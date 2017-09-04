<?php

namespace Layered;

use Goutte\Client;


class UrlPreview {

	private static $previewers = [];
	private static $formatters = [];

	protected $data = [];
	protected $previewer = false;

	public function __construct($url) {
		$client = new Client();


		// check for valid URL
		if (!filter_var($url, FILTER_VALIDATE_URL)) {
			throw new \Exception('Invalid URL');
		}


		// load content from URL
		$crawler = $client->request('GET', $url);


		// extract data
		foreach (self::$previewers as $priority => $previewers) {
			if (!$this->previewer) {
				foreach ($previewers as $previewer) {
					if (!$this->previewer && $previewer->test($crawler)) {
						$this->previewer = $previewer;
						$this->data = $previewer->getData($crawler);
						//$this->data['previewer'] = $previewer->getName();
					}
				}
			}
		}


		// format data
		foreach (self::$formatters as $priority => $formatters) {
			foreach ($formatters as $formatter) {
				if ($formatter->test($crawler, $this->previewer)) {
					$this->data = $formatter->format($this->data, $crawler, $previewer);
				}
			}
		}


		return $this;
	}

	public static function load($url) {
		return new static($url);
	}

	public function getData() {
		return $this->data;
	}

	public static function addPreviewer($previewer, $priority = 10) {
		self::$previewers[$priority][] = $previewer;
	}

	public static function addFormatter($formatter, $priority = 10) {
		self::$formatters[$priority][] = $formatter;
	}

}


// add basic Previewers
UrlPreview::addPreviewer(new Previewer\OpenGraph);
UrlPreview::addPreviewer(new Previewer\SimpleHtml, 100);

// Formatters to get extra Twitter & Instagram profile info
UrlPreview::addFormatter(new Formatter\InstagramFromOpenGraph);
UrlPreview::addFormatter(new Formatter\TwitterFields);
