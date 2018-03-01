<?php
namespace Layered\PageMeta;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Goutte\Client;
use Layered\PageMeta\Event\PageScrapeEvent;
use Layered\PageMeta\Event\DataFilterEvent;

class UrlPreview {
	private static $dispatcher;

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
		// start scraping page
		$pageScrapeEvent = new PageScrapeEvent($this->data, $this->crawler);
		$this->data = self::dispatcher()->dispatch($pageScrapeEvent::NAME, $pageScrapeEvent)->getData();

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
	private static function dispatcher() {
		if (!self::$dispatcher) {
			self::$dispatcher = new EventDispatcher();
		}

		$url = $this->parseUrl($this->crawler->getUri());
		$html = $this->crawler->html();

		$site = [
			'url'			=>	$url['scheme'] . '://' . $url['host'],
			'name'			=>	isset($this->data['site_name']) ? $this->data['site_name'] : '',
			'icon'			=>	'',
			'secure'		=>	null,
			'responsive'	=>	null,
			'mobileSite'	=>	null,
			'tracking'		=>	[],
			'author'		=>	null,
			'generator'		=>	null
		];

		// check if site has security
		$site['secure'] = strpos($this->crawler->getUri(), 'https://') !== false;

		// basic check for responsiveness
		$site['responsive'] = !!count($this->crawler->filter('meta[name="viewport"]'));

		// checks for tracking codes
		if (stripos($html, 'google-analytics') !== false) {
			$site['tracking'][] = 'Google Analytics';
		}
		if (stripos($html, 'piwik') !== false) {
			$site['tracking'][] = 'Piwik';
		}

		// check Generator
		if (count($metaGenerator = $this->crawler->filter('meta[name="generator"]'))) {
			$site['generator'] = $metaGenerator->attr('content');
		}

		// check Author
		if (count($metaAuthor = $this->crawler->filter('meta[name="author"]'))) {
			$site['author'] = $metaAuthor->attr('content');
		}

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
		return self::$dispatcher;
	}

	public static function on(string $eventName, callable $listener, $priority = 0) {
		self::dispatcher()->addListener($eventName, $listener, $priority);
	}

	public function getData(string $section) {
		$dataFilterEvent = new DataFilterEvent($this->data[$section], $section);
		return self::dispatcher()->dispatch($dataFilterEvent::NAME, $dataFilterEvent)->getData();
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
