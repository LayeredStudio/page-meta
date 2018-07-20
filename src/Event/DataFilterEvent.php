<?php
namespace Layered\PageMeta\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Dispatched each time data is retuned
 */
class DataFilterEvent extends Event {
	const NAME = 'data.filter';

	protected $data;
	protected $section;
	protected $crawler;

	public function __construct(array $data, string $section, Crawler $crawler) {
		$this->data = $data;
		$this->section = $section;
		$this->crawler = $crawler;
	}

	public function setData(array $data) {
		$this->data = $data;
	}

	public function addData(array $data) {
		$this->data = array_merge($this->data, array_filter($data));
	}

	public function getData(): array {
		return $this->data;
	}

	public function getSection(): string {
		return $this->section;
	}

	public function getCrawler(): Crawler {
		return $this->crawler;
	}

}
