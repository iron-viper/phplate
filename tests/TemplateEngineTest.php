<?php
/**
 * Created by PhpStorm.
 * User: maestroprog
 * Date: 10.08.17
 * Time: 9:36
 */

namespace Iassasin\Phplate\Tests;

use Iassasin\Phplate\Template;
use Iassasin\Phplate\TemplateEngine;
use Iassasin\Phplate\Exception\PhplateCompilerException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Iassasin\Phplate\Template
 * @covers \Iassasin\Phplate\TemplateEngine
 * @covers \Iassasin\Phplate\TemplateOptions
 * @covers \Iassasin\Phplate\PipeFunctionsContainer
 * @covers \Iassasin\Phplate\TemplateCompiler
 * @covers \Iassasin\Phplate\TemplateLexer
 * @covers \Iassasin\Phplate\Exception\PhplateException
 * @covers \Iassasin\Phplate\Exception\PhplateCompilerException
 */
class TemplateEngineTest extends TestCase {
	/**
	 * @var TemplateEngine
	 */
	private static $e;

	public static function setUpBeforeClass(){
		self::$e = TemplateEngine::init(__DIR__ . '/resources/');
		self::$e->getOptions()->setCacheEnabled(false);
		parent::setUpBeforeClass();
	}

	public function testUnknownTemplate(){
		$this->expectException(PhplateCompilerException::class);
		$result = self::$e->build('unknown-template', []);
	}

	public function testOverridingPipeFunctions(){
		self::$e->addUserFunctionHandler('raw', function (){
			return 'test';
		});
		// тестируем переопределенную функцию
		$this->assertNotEquals('hello', $res = self::$e->buildStr("{{ 'hello'|raw }}", []));
		$this->assertEquals('test', $res);
		// тестируем пайп-функцию из нового контейнера ("восстановленная")
		$this->assertEquals('hello', Template::init('.')->buildStr("{{ 'hello'|raw }}", []));
	}

	public function testInvalidTpl(){
		$this->expectException(PhplateCompilerException::class);
		$result = self::$e->build('invalid_tpl', []);
	}

	public function testBuildFile(){
		$result = self::$e->buildFile(__DIR__ . '/resources/template_test.html', ['message' => 'hello']);
		$this->assertEquals('hello', $result);
	}
}
