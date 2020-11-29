<?php
namespace Layered\PageMeta;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\Event;
use Goutte\Client;
use Layered\PageMeta\Event\PageScrapeEvent;
use Layered\PageMeta\Event\DataFilterEvent;

/**
 * UrlPreview
 *
 * @author Andrei Igna <andrei@laye.red>
 */
class UrlPreview {

	private $eventDispatcher;
	private $headers = [
		'HTTP_ACCEPT'		=>	'text/html,application/xhtml+xml,application/xml;q=0.9',
		'HTTP_USER_AGENT'	=>	'Mozilla/5.0 (compatible; PageMeta/1.0; +https://layered.dev)'
	];

	protected $crawler;
	protected $url;
	protected $data;

	public function __construct(array $headers = []) {
		$this->eventDispatcher = new EventDispatcher();
		$this->goutteClient = new Client();
		$this->goutteClient->setServerParameters(array_merge($this->headers, $headers));

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
		$this->addListener('page.scrape', ['\Layered\PageMeta\Scraper\SiteInfo', 'siteNameFromHtml']);
		$this->addListener('data.filter', ['\Layered\PageMeta\Scraper\SiteInfo', 'mediaUrlToArray']);

		return $this;
	}

	public function loadUrl(string $url): self {
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
			'author'	=>	[],
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
		$this->data = $this->eventDispatcher->dispatch($pageScrapeEvent, PageScrapeEvent::NAME)->getData();

		return $this;
	}

	public function get(string $section): array {
		$dataFilterEvent = new DataFilterEvent($this->data[$section] ?? [], $section, $this->crawler);
		return $this->eventDispatcher->dispatch($dataFilterEvent, DataFilterEvent::NAME)->getData();
	}

	public function getAll(): array {
		return [
			'site'		=>	$this->get('site'),
			'page'		=>	$this->get('page'),
			'author'	=>	$this->get('author'),
			'app_links'	=>	$this->get('app_links')
		];
	}

	public function addListener(string $eventName, callable $listener, int $priority = 0): self {
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
