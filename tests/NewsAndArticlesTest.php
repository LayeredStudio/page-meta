<?php declare(strict_types=1);

use Layered\PageMeta\UrlPreview;
use PHPUnit\Framework\TestCase;

final class NewsAndArticlesTest extends TestCase {

	protected $urlPreview;

	protected function setUp(): void {
		$this->urlPreview = new UrlPreview;
	}

	public function testWiredArticle(): void {

		$fb = $this->urlPreview->loadUrl('https://www.wired.com/story/best-sonos-speakers-buying-guide/');
		$data = $fb->getAll();

		// site details
		$this->assertEquals('https://www.wired.com', $data['site']['url']);
		$this->assertEquals('Wired', $data['site']['name']);

		// article details
		$this->assertEquals('article', $data['page']['type']);
		$this->assertStringContainsString('Sonos', $data['page']['title']);
		$this->assertArrayHasKey('url', $data['page']['image']);

		// author details
		$this->assertArrayHasKey('name', $data['author']);
		$this->assertEquals('https://www.wired.com/contributor/jeffrey-van-camp', $data['author']['url']);
	}

	public function testNYTimesArticle(): void {

		$fb = $this->urlPreview->loadUrl('https://www.nytimes.com/2020/09/26/technology/ebay-cockroaches-stalking-scandal.html');
		$data = $fb->getAll();

		// site details
		$this->assertEquals('https://www.nytimes.com', $data['site']['url']);
		$this->assertEquals('NYTimes', $data['site']['name']);

		// article details
		$this->assertEquals('article', $data['page']['type']);
		$this->assertStringContainsString('eBay', $data['page']['title']);
		$this->assertArrayHasKey('url', $data['page']['image']);
	}

	public function testVergeArticle(): void {

		$fb = $this->urlPreview->loadUrl('https://www.theverge.com/2017/10/31/16579748/apple-iphone-x-review');
		$data = $fb->getAll();

		// site details
		$this->assertEquals('https://www.theverge.com', $data['site']['url']);
		$this->assertEquals('The Verge', $data['site']['name']);

		// article details
		$this->assertEquals('article', $data['page']['type']);
		$this->assertStringContainsString('iPhone', $data['page']['title']);
		$this->assertArrayHasKey('url', $data['page']['image']);

		// author details
		$this->assertArrayHasKey(['url', 'name'], $data['author']);
		$this->assertEquals('Nilay Patel', $data['author']['name']);
	}

	public function testWordpressArticle(): void {

		$fb = $this->urlPreview->loadUrl('https://en.blog.wordpress.com/2018/02/02/reader-conversations/');
		$data = $fb->getAll();

		// site details
		$this->assertEquals('https://wordpress.com', $data['site']['url']);
		$this->assertEquals('The WordPress.com Blog', $data['site']['name']);

		// article details
		$this->assertEquals('article', $data['page']['type']);
		$this->assertStringContainsString('Conversations', $data['page']['title']);
		$this->assertArrayHasKey('url', $data['page']['image']);

		// author details
		$this->assertStringContainsString('Jan', $data['author']['name']);
	}

	public function testMediumArticle(): void {

		$fb = $this->urlPreview->loadUrl('https://medium.com/@ev/welcome-to-medium-9e53ca408c48');
		$data = $fb->getAll();

		// site details
		$this->assertEquals('https://medium.com', $data['site']['url']);
		$this->assertEquals('Medium', $data['site']['name']);

		// article details
		$this->assertEquals('article', $data['page']['type']);
		$this->assertStringContainsString('Welcome', $data['page']['title']);
		$this->assertArrayHasKey('url', $data['page']['image']);

		// author details
		$this->assertEquals('Ev Williams', $data['author']['name']);
		$this->assertEquals('https://medium.com/@ev', $data['author']['url']);
	}

}
