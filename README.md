# webchaind-php
Webchaind php interface.

# Install

ToDo

# Usage

### Start Webchaind
```bash
webchaind --fast --rpc --rpc-api "eth,net,web3,personal" --rpc-port 8545 --rpc-cors-domain "*"
```

### New instance
```php
use Webchaind\Webchaind;

$webchaind = new Webchaind('http://localhost:8545');

$account = new Account(
	$webchaind,
	'[WALLET ADDRESS]',
	'[PASSWORD]'
);

echo 'Balance: ' . $account->getBalance() . PHP_EOL;
```

### ERC20

```php
use Webchaind\Webchaind;
use Webchaind\ERC20;
use Webchaind\Account;
use Webchaind\ERC20Account;

$webchaind = new Webchaind('http://localhost:8545');

$erc20 = new ERC20(
	$webchaind,
	'[ERC20 ADDRESS]',
	'[ERC20 ABI JSON FILE]'
);

$account = new Account(
	$webchaind,
	'[WALLET ADDRESS]',
	'[PASSWORD]'
);

$erc20Account = new ERC20Account(
	$erc20,
	$webchaind,
	'[WALLET ADDRESS]',
	'[PASSWORD]'
);

$name = $erc20->getName();
echo 'Name: ' . $name . PHP_EOL;

$symbol = $erc20->getSymbol();
echo 'Symbol: ' . $symbol . PHP_EOL;

$decimals = $erc20->getDecimals();
echo 'Decimals: ' . $decimals . PHP_EOL;

$totalSupply = $erc20->getTotalSupply();
$totalSupply = $erc20->toDecimal($totalSupply);
echo 'Total Supply: ' . $totalSupply . PHP_EOL;

$balance = $erc20Account->getBalance();
$balance = $erc20Account->toToken($balance)
echo 'Total Supply: ' . $balance . PHP_EOL;
```

# License
WTFPL
