<?php
namespace Layered\PageMeta\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Dispatched each time a page scrape is started
 */
class PageScrapeEvent extends Event {
	const NAME = 'page.scrape';

	protected $data;
	protected $crawler;

	public function __construct(array $data, Crawler $crawler) {
		$this->data = $data;
		$this->crawler = $crawler;
	}

	public function setData(string $section, array $data) {
		$this->data[$section] = $data;
	}

	public function addData(string $section, array $data) {
		$this->data[$section] = array_merge($this->data[$section], array_filter($data));
	}

	public function getData(): array {
		return $this->data;
	}

	public function getCrawler(): Crawler {
		return $this->crawler;
	}

}
