<?php
namespace Webchaind;

use Web3\Web3;

/**
 * Webchaind
 */
class Webchaind extends Web3
{
	
	function __construct(string $provider)
	{
		parent::__construct($provider);
	}
}