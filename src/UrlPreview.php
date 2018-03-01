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
	protected $data = [
		'site'		=>	[
			'secure'	=>	false
		],
		'page'		=>	[
			'type'		=>	'website'
		],
		'profile'	=>	[],
		'extra'		=>	[]
	];

	public function __construct(string $url) {

		// check for valid URL
		if (!filter_var($url, FILTER_VALIDATE_URL)) {
			throw new \Exception('Invalid URL');
		}

		$this->url = $url;

		// load content from URL
		$client = new Client();
		$this->crawler = $client->request('GET', $this->url);

		// extract site info
		$parsedUrl = $this->parseUrl($this->crawler->getUri());
		$this->data['site']['secure'] = strpos($this->crawler->getUri(), 'https://') !== false;
		$this->data['site']['url'] = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];

		// start scraping page
		$pageScrapeEvent = new PageScrapeEvent($this->data, $this->crawler);
		$this->data = self::dispatcher()->dispatch($pageScrapeEvent::NAME, $pageScrapeEvent)->getData();

		return $this;
	}

	public static function load(string $url) {
		return new static($url);
	}

	private static function dispatcher() {
		if (!self::$dispatcher) {
			self::$dispatcher = new EventDispatcher();
		}

		return self::$dispatcher;
	}

	public static function on(string $eventName, callable $listener, $priority = 0) {
		self::dispatcher()->addListener($eventName, $listener, $priority);
	}

	public function getData(string $section) {
		$dataFilterEvent = new DataFilterEvent($this->data[$section], $section);
		return self::dispatcher()->dispatch($dataFilterEvent::NAME, $dataFilterEvent)->getData();
	}

	public function getAll() {
		return [
			'site'		=>	$this->getData('site'),
			'page'		=>	$this->getData('page'),
			'profile'	=>	$this->getData('profile')
		];
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

// Add default scrapers
UrlPreview::on('page.scrape', ['\Layered\PageMeta\Scraper\SimpleHtml', 'scrape']);
UrlPreview::on('page.scrape', ['\Layered\PageMeta\Scraper\OpenGraph', 'scrape']);

// add basic Previewers
UrlPreview::addPreviewer(new Previewer\OpenGraph);
UrlPreview::addPreviewer(new Previewer\SimpleHtml, 100);

// Formatters to get extra Twitter & Instagram profile info
UrlPreview::addFormatter(new Formatter\InstagramFromOpenGraph);
UrlPreview::addFormatter(new Formatter\TwitterFields);
