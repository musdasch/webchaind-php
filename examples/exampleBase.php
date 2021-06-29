<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../vendor/autoload.php';

use Webchaind\Webchaind;
use Webchaind\ERC20;
use Webchaind\Account;
use Webchaind\ERC20Account;

$webchaind = new Webchaind('https://node1.mintme.com:443');

$erc20 = new ERC20(
	$webchaind,
	'0x7b535379bBAfD9cD12b35D91aDdAbF617Df902B2',
	'erc20.abi'
);

$account = new Account(
	$webchaind,
	'0x0000000000000000000000000000000000000001',
	'PASSWORD'
);

$erc20Account = new ERC20Account(
	$erc20,
	$webchaind,
	'0x0000000000000000000000000000000000000001',
	'PASSWORD'
);