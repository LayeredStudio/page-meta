<?php
namespace Layered\PageMeta;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Goutte\Client;
use Layered\PageMeta\Event\PageScrapeEvent;
use Layered\PageMeta\Event\DataFilterEvent;

/**
 * UrlPreview
 *
 * @author Andrei Igna <andrei.igna@layered.studio>
 */
class UrlPreview {

	private $eventDispatcher;
	private $headers = [
		'accept'		=>	'text/html,application/xhtml+xml,application/xml;q=0.9',
		'user-agent'	=>	'Mozilla/5.0 (compatible; MetaApis/1.0; +https://apis.blue/page-meta)'
	];

	protected $crawler;
	protected $url;
	protected $data;

	public function __construct(array $headers = []) {
		$this->eventDispatcher = new EventDispatcher();
		$this->goutteClient = new Client();

		foreach (array_merge($this->headers, $headers) as $header => $content) {
			$this->goutteClient->setHeader($header, $content);
		}

		// Scrape data from common HTML tags
		$this->addListener('page.scrape', ['\Layered\PageMeta\Scraper\SimpleHtml', 'scrape']);
		$this->addListener('page.scrape', ['\Layered\PageMeta\Scraper\OpenGraph', 'scrape']);

		// Site specific data scrape
		$this->addListener('page.scrape', ['\Layered\PageMeta\Scraper\ArticleInfo', 'scrape']);
		$this->addListener('page.scrape', ['\Layered\PageMeta\Scraper\SocialNetworkProfile', 'getProfiles']);
		$this->addListener('page.scrape', ['\Layered\PageMeta\Scraper\SiteInfo', 'ecommerceSites']);
		$this->addListener('page.scrape', ['\Layered\PageMeta\Scraper\SiteInfo', 'appLinks']);

		// Filter data to a consistent format across sites
		$this->addListener('data.filter', ['\Layered\PageMeta\Scraper\SiteInfo', 'addSiteNames']);
		$this->addListener('data.filter', ['\Layered\PageMeta\Scraper\SiteInfo', 'mediaUrlToArray']);

		return $this;
	}

	public function loadUrl(string $url) {
		$this->data = [
			'site'		=>	[
				'url'			=>	$url,
				'name'			=>	'',
				'secure'		=>	false,
				'responsive'	=>	false,
				'author'		=>	'',
				'generator'		=>	'',
				'icon'			=>	'',
				'language'		=>	''
			],
			'page'		=>	[
				'type'		=>	'website'
			],
			'profile'	=>	[],
			'app_links'	=>	[],
			'extra'		=>	[]
		];

		if (!filter_var($url, FILTER_VALIDATE_URL)) {
			throw new \Exception('Invalid URL');
		}

		// load content from URL
		$this->url = $url;
		$this->crawler = $this->goutteClient->request('GET', $this->url);

		// extract site info
		$parsedUrl = $this->parseUrl($this->crawler->getUri());
		$this->data['site']['secure'] = strpos($this->crawler->getUri(), 'https://') !== false;
		$this->data['site']['url'] = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];

		// start scraping page
		$pageScrapeEvent = new PageScrapeEvent($this->data, $this->crawler);
		$this->data = $this->eventDispatcher->dispatch($pageScrapeEvent::NAME, $pageScrapeEvent)->getData();

		return $this;
	}

	public function get(string $section): array {
		$dataFilterEvent = new DataFilterEvent($this->data[$section], $section, $this->crawler);
		return $this->eventDispatcher->dispatch($dataFilterEvent::NAME, $dataFilterEvent)->getData();
	}

	public function getAll(): array {
		return [
			'site'		=>	$this->get('site'),
			'page'		=>	$this->get('page'),
			'profile'	=>	$this->get('profile'),
			'app_links'	=>	$this->get('app_links')
		];
	}

	public function addListener(string $eventName, callable $listener, $priority = 0) {
		$this->eventDispatcher->addListener($eventName, $listener, $priority);
		return $this;
	}

	protected function parseUrl(string $url): array {
		// TODO maybe use a better URL parser
		return parse_url($url);
	}

	public static function makeAbsoluteUri(string $baseUrl, string $url): string {
		if (strpos($url, 'http') === false) {
			if (substr($url, 0, 2) === '//') {
				$url = 'https:' . $url;
			} elseif ($url['0'] === '/') {
				$baseUrl = parse_url($baseUrl);
				$url = $baseUrl['scheme'] . '://' . $baseUrl['host'] . $url;
			} else {
				$url = rtrim($baseUrl, '/') . '/' . $url;
			}
		}

		return $url;
	}

}
