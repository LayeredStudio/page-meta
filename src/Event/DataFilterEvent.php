<?php
namespace Layered\PageMeta\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Dispatched each time data is retuned
 */
class DataFilterEvent extends Event {
	const NAME = 'data.filter';

	protected $data;
	protected $section;
	protected $crawler;

	public function __construct(array $data, string $section, $crawler) {
		$this->data = $data;
		$this->section = $section;
		$this->crawler = $crawler;
	}

	public function setData(array $data) {
		$this->data = $data;
	}

	public function getData(): array {
		return $this->data;
	}

	public function getSection(): string {
		return $this->section;
	}

	public function getCrawler() {
		return $this->crawler;
	}

}
