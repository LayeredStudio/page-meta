<?php declare(strict_types=1);

use Layered\PageMeta\UrlPreview;
use PHPUnit\Framework\TestCase;

final class SocialSitesTest extends TestCase {

	protected $urlPreview;

	protected function setUp(): void {
		$this->urlPreview = new UrlPreview;
	}

	public function testInstagramPhoto(): void {

		$instagram = $this->urlPreview->loadUrl('https://www.instagram.com/p/CCOQMUdJK3e/');
		$data = $instagram->getAll();

		$this->assertEquals('Instagram', $data['site']['name']);
		$this->assertEquals('photo', $data['page']['type']);
		$this->assertEquals('NextRace', $data['author']['name']);
	}

	public function testInstagramProfile(): void {

		$instagram = $this->urlPreview->loadUrl('https://www.instagram.com/nextraceapp/');
		$data = $instagram->getAll();

		$this->assertEquals('Instagram', $data['site']['name']);
		$this->assertEquals('profile', $data['page']['type']);
		$this->assertEquals('NextRace', $data['author']['name']);
		$this->assertEquals('@nextraceapp', $data['author']['handle']);
	}

	public function testFacebookImage(): void {

		$fb = $this->urlPreview->loadUrl('https://www.facebook.com/KeyAndPeele/photos/rpp.256107687771505/852998691415732/?type=3&theater');
		$data = $fb->getAll();

		$this->assertEquals('Facebook', $data['site']['name']);
		$this->assertEquals('Key & Peele', $data['page']['title']);
	}

	public function testFacebookPage(): void {

		$fb = $this->urlPreview->loadUrl('https://www.facebook.com/instagram/');
		$data = $fb->getAll();

		$this->assertEquals('Facebook', $data['site']['name']);
		$this->assertEquals('Instagram', $data['page']['title']);
		$this->assertEquals('https://www.facebook.com/instagram/', $data['page']['url']);
	}

	public function testFacebookPagePost(): void {

		$fb = $this->urlPreview->loadUrl('https://www.facebook.com/Microsoft/posts/10157885048368721');
		$data = $fb->getAll();

		$this->assertEquals('Facebook', $data['site']['name']);
		$this->assertEquals('Microsoft', $data['page']['title']);
		$this->assertEquals('website', $data['page']['type']);
		$this->assertEquals('https://www.facebook.com/Microsoft/posts/10157885048368721', $data['page']['url']);
	}

	public function testRedditImage(): void {

		$fb = $this->urlPreview->loadUrl('https://www.reddit.com/r/funny/comments/82ckwf/pic_of_two_plump_pigeons_perched_on_the_ledge_but/');
		$data = $fb->getAll();

		$this->assertEquals('reddit', $data['site']['name']);
		$this->assertEquals('image', $data['page']['type']);
		$this->assertStringStartsWith('r/funny', $data['page']['title']);
		$this->assertArrayHasKey('url', $data['page']['image']);
	}

}
