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

	public function __construct(array $data, string $section) {
		$this->data = $data;
		$this->section = $section;
	}

	public function setData(array $data) {
		$this->data = $data;
	}

	public function getData() {
		return $this->data;
	}

	public function getSection() {
		return $this->section;
	}

}
