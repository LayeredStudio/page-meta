<?php
namespace Layered\PageMeta\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Dispatched each time a page scrape is started
 */
class PageScrapeEvent extends Event {
	const NAME = 'page.scrape';

	protected $data;
	protected $crawler;

	public function __construct(array $data, $crawler) {
		$this->data = $data;
		$this->crawler = $crawler;
	}

	public function setData(string $section, array $data) {
		$this->data[$section] = $data;
	}

	public function addData(string $section, array $data) {
		$this->data[$section] = array_merge($this->data[$section], array_filter($data));
	}

	public function getData() {
		return $this->data;
	}

	public function getCrawler() {
		return $this->crawler;
	}

}
