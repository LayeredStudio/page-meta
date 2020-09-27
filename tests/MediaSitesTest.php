<?php declare(strict_types=1);

use Layered\PageMeta\UrlPreview;
use PHPUnit\Framework\TestCase;

final class MediaSitesTest extends TestCase {

	protected $urlPreview;

	protected function setUp(): void {
		$this->urlPreview = new UrlPreview;
	}

	public function testYoutubeVideo(): void {

		$fb = $this->urlPreview->loadUrl('https://www.youtube.com/watch?v=L3pk_TBkihU');
		$data = $fb->getAll();

		// site details
		$this->assertEquals('https://www.youtube.com', $data['site']['url']);
		$this->assertEquals('YouTube', $data['site']['name']);

		// video details
		$this->assertEquals('video', $data['page']['type']);
		$this->assertStringContainsString('TENET', $data['page']['title']);
		$this->assertArrayHasKey('url', $data['page']['image']);
		$this->assertArrayHasKey('video', $data['page']);
	}

	public function testNetflix(): void {

		$fb = $this->urlPreview->loadUrl('https://www.netflix.com/title/80014749');
		$data = $fb->getAll();

		// site details
		$this->assertEquals('https://www.netflix.com', $data['site']['url']);
		$this->assertEquals('Netflix', $data['site']['name']);

		// video details
		$this->assertEquals('website', $data['page']['type']);
		$this->assertStringContainsString('Rick and Morty', $data['page']['title']);
		$this->assertArrayHasKey('url', $data['page']['image']);
	}

}
