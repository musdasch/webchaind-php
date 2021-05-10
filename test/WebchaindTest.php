<?php
declare(strict_types=1);

namespace Test;

use PHPUnit\Framework\TestCase;
use Webchaind\Webchaind;

final class WebchaindTest extends TestCase
{
	/**
	 * webchaind
	 * 
	 * @var \Webchaind\Webchaind
	 */
	private $webchaind;

	/**
	 * testHost
	 * 
	 * @var string
	 */
	private $testHost = 'http://localhost:8545';
	
	protected function setUp(): void
	{
		$this->webchaind = new Webchaind($this->testHost);
	}

	public function testInstance(): void
	{
		$this->assertTrue($this->webchaind instanceof Webchaind);
	}
}
