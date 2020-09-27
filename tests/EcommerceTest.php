<?php declare(strict_types=1);

use Layered\PageMeta\UrlPreview;
use PHPUnit\Framework\TestCase;

final class EcommerceTest extends TestCase {

	protected $urlPreview;

	protected function setUp(): void {
		$this->urlPreview = new UrlPreview;
	}

	public function testAmazonProduct(): void {

		$fb = $this->urlPreview->loadUrl('https://www.amazon.co.uk/dp/B0748CLMD5/');
		$data = $fb->getAll();

		// site details
		$this->assertEquals('https://www.amazon.co.uk', $data['site']['url']);
		$this->assertEquals('Amazon', $data['site']['name']);

		// product details
		$this->assertEquals('product', $data['page']['type']);
		$this->assertStringContainsString('Moon', $data['page']['title']);
		$this->assertArrayHasKey('url', $data['page']['image']);

		// seller details (author)
		$this->assertArrayHasKey('name', $data['author']);
		$this->assertArrayHasKey('url', $data['author']);
	}

	public function testGoogleStore(): void {

		$fb = $this->urlPreview->loadUrl('https://store.google.com/us/');
		$data = $fb->getAll();

		// site details
		$this->assertEquals('https://store.google.com', $data['site']['url']);
		$this->assertEquals('Google Store', $data['site']['name']);

		// page details
		$this->assertEquals('website', $data['page']['type']);
		$this->assertArrayHasKey('url', $data['page']['image']);
	}

	public function testGoogleStoreProduct(): void {

		$fb = $this->urlPreview->loadUrl('https://store.google.com/us/product/google_nest_hub_max');
		$data = $fb->getAll();

		// site details
		$this->assertEquals('https://store.google.com', $data['site']['url']);
		$this->assertEquals('Google Store', $data['site']['name']);

		// product details
		$this->assertEquals('website', $data['page']['type']);
		$this->assertStringContainsString('Nest Hub Max', $data['page']['title']);
		$this->assertArrayHasKey('url', $data['page']['image']);
	}

}
