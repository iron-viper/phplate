<?php
/**
 * Author: maestroprog <maestroprog@gmail.com>
 * License: beerware
 * Use for good
 */

use Iassasin\Phplate\Template;
use Iassasin\Phplate\TemplateOptions;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Iassasin\Phplate\Template
 * @covers \Iassasin\Phplate\TemplateEngine
 * @covers \Iassasin\Phplate\PipeFunctionsContainer
 * @covers \Iassasin\Phplate\TemplateCompiler
 * @covers \Iassasin\Phplate\TemplateLexer
 * @covers \Iassasin\Phplate\TemplateOptions
 */
class TemplateCacheTest extends TestCase {
	private static $cacheFileNoCacheDir = __DIR__ . '/resources/template_test.ctpl';
	private static $cacheFileInCacheDir = __DIR__ . '/resources/cache/template_test.html-213832f0f0f9925fcb6f1346aba2961d.ctpl';

	private static function cleanFile($file){
		if (file_exists($file)){
			unlink($file);
		}
	}

	private static function cleanCaches(){
		self::cleanFile(self::$cacheFileNoCacheDir);
		self::cleanFile(self::$cacheFileInCacheDir);
	}

	public static function setUpBeforeClass(){
		self::cleanCaches();
	}

	public static function tearDownAfterClass(){
		self::cleanCaches();
	}

	public function testCacheEnabledNoDir(){
		Template::init(__DIR__ . '/resources/', (new TemplateOptions())
			->setCacheEnabled(true)
			->setCacheDir('')
			->setAutoSafeEnabled(true)
		);

		self::cleanCaches();

		$this->assertEquals('msg', Template::build('template_test', ['message' => 'msg']));
		$this->assertTrue(file_exists(self::$cacheFileNoCacheDir));
		$this->assertFalse(file_exists(self::$cacheFileInCacheDir));

		$this->assertEquals('message', Template::build('template_test', ['message' => 'message']));
		$this->assertTrue(file_exists(self::$cacheFileNoCacheDir));
		$this->assertFalse(file_exists(self::$cacheFileInCacheDir));
	}

	public function testCacheEnabledWithDir(){
		Template::init(__DIR__ . '/resources/', (new TemplateOptions())
			->setCacheEnabled(true)
			->setCacheDir(__DIR__ . '/resources/cache')
			->setAutoSafeEnabled(true)
		);

		self::cleanCaches();

		$this->assertEquals('msg', Template::build('template_test', ['message' => 'msg']));
		$this->assertFalse(file_exists(self::$cacheFileNoCacheDir));
		$this->assertTrue(file_exists(self::$cacheFileInCacheDir));

		$this->assertEquals('message', Template::build('template_test', ['message' => 'message']));
		$this->assertFalse(file_exists(self::$cacheFileNoCacheDir));
		$this->assertTrue(file_exists(self::$cacheFileInCacheDir));
	}

	public function testCacheDisabled(){
		Template::init(__DIR__ . '/resources/', (new TemplateOptions())
			->setCacheEnabled(false)
			->setAutoSafeEnabled(false)
		);

		self::cleanCaches();

		$this->assertEquals('msg', Template::build('template_test', ['message' => 'msg']));
		$this->assertFalse(file_exists(self::$cacheFileNoCacheDir) || file_exists(self::$cacheFileInCacheDir));
		$this->assertEquals('message', Template::build('template_test', ['message' => 'message']));
		$this->assertFalse(file_exists(self::$cacheFileNoCacheDir) || file_exists(self::$cacheFileInCacheDir));
	}
}
