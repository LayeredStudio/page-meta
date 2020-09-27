<?php declare(strict_types=1);

use Layered\PageMeta\UrlPreview;
use PHPUnit\Framework\TestCase;

final class PopularSitesTest extends TestCase {

	protected $urlPreview;

	protected function setUp(): void {
		$this->urlPreview = new UrlPreview;
	}

	public function testGithubPageAfterRedirect(): void {

		$fb = $this->urlPreview->loadUrl('https://layered.dev');
		$data = $fb->getAll();

		// site details
		$this->assertEquals('https://github.com', $data['site']['url']);
		$this->assertEquals('GitHub', $data['site']['name']);

		// page details
		$this->assertEquals('profile', $data['page']['type']);
		$this->assertEquals('https://github.com/LayeredStudio', $data['page']['url']);
		$this->assertStringContainsString('Layered', $data['page']['title']);
		$this->assertArrayHasKey('url', $data['page']['image']);
	}

}
