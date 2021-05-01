<?php
require('./exampleBase.php');

$name = $erc20->getName();
echo 'Name: ' . $name . PHP_EOL;

$symbol = $erc20->getSymbol();
echo 'Symbol: ' . $symbol . PHP_EOL;

$decimals = $erc20->getDecimals();
echo 'Decimals: ' . $decimals . PHP_EOL;

$totalSupply = $erc20->getTotalSupply();
$totalSupply = $erc20->toDecimal($totalSupply);
echo 'Total Supply: ' . $totalSupply . PHP_EOL;
