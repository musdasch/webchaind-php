<?php
namespace Webchaind;

use Web3\Contract;
use Webchaind\Webchaind;
use GuzzleHttp\Promise\Promise;
use Litipk\BigNumbers\Decimal as Decimal;
use GMP as GMP;

/**
 * Object that represents a erc20 token.
 *
 * @author Musdasch <musdasch@gmail.com>
 * @license WTFPL
 */
class ERC20
{

	/**
	 * [$contract description]
	 * @var Contract
	 */
	private $contract;

	/**
	 * [$coin description]
	 * @var [type]
	 */
	private $coin;

	/**
	 * [$name description]
	 * @var string
	 */
	private $name;

	/**
	 * [$symbol description]
	 * @var string
	 */
	private $symbol;

	/**
	 * [$totalSupply description]
	 * @var [type]
	 */
	private $totalSupply;

	/**
	 * [$decimals description]
	 * @var [type]
	 */
	private $decimals;

	/**
	 * [$divider description]
	 * @var [type]
	 */
	private $divider;

	/**
	 * @param Webchaind
	 * @param [type]
	 * @param [type]
	 */
	function __construct(Webchaind $webchaind, $address, $abi_file)
	{
		$abi = json_decode(file_get_contents($abi_file));
		$this->contract = new Contract($webchaind->provider, $abi);
		$this->coin = $this->contract->at($address);
		
		$this->name = $this->callName();
		$this->symbol = $this->callSymbol();
		$this->totalSupply = $this->callTotalSupply();
		$this->decimals = $this->callDecimals();

		$this->divider = gmp_powm(
			'10',
			$this->decimals,
			gmp_pow('10', 255)
		);
	}

	public function transfer($from, $to, $value)
	{
		$promise = new Promise();

		$this->coin->send('transfer', $to, $value, [
			'from' => $from,
			'gas' => '0x200b20'
			], function ($err, $data) use ($promise) {
			if ($err !== null) {
				$promise->reject($err);
			}
			$promise->resolve($data);
		});

		return $promise->wait();
	}

	public function allowance($owner, $spender)
	{
		$promise = new Promise();

		$this->coin->call(
			'allowance', $owner, $spender,
			function ($err, $data) use ($promise){
				if ($err !== null) {
					$promise->reject($err);
					return;
				}
				$promise->resolve($data[0]->value);
			}
		);

		return $promise->wait();
	}

	public function balanceOf($owner)
	{
		$promise = new Promise();

		$this->coin->call(
			'balanceOf', $owner,
			function ($err, $data) use ($promise){
				if ($err !== null) {
					$promise->reject($err);
					return;
				}
				$promise->resolve($data[0]->value);
			}
		);

		return $promise->wait();
	}

	public function toDecimal(GMP $value)
	{

		$tmp = gmp_div_qr(
			$value,
			$this->divider
		);

		$amount = Decimal::fromInteger(gmp_intval($tmp[1]));
		$amount = $amount->div(Decimal::fromInteger(gmp_intval($this->divider)));
		$amount = $amount->add(Decimal::fromInteger(gmp_intval($tmp[0])));

		return $amount;
	}

	public function toGMP(Decimal $value)
	{
		$value = $value->mul(Decimal::fromInteger(gmp_intval($this->divider)), 0);
		return gmp_init((string) $value);
	}

	public function getName()
	{
		return $this->name;
	}

	public function getSymbol()
	{
		return $this->symbol;
	}

	public function getTotalSupply()
	{
		return $this->totalSupply;
	}

	public function getDecimals()
	{
		return $this->decimals;
	}

	public function getEth()
	{
		return $this->contract->getEth();
	}

	private function callTotalSupply()
	{
		$promise = new Promise();

		$this->coin->call(
			'totalSupply',
			function ($err, $data) use ($promise){
				if ($err !== null) {
					$promise->reject($err);
					return;
				}
				$promise->resolve($data[0]->value);
			}
		);
		
		return $promise->wait();
	}

	private function callName()
	{
		$promise = new Promise();

		$this->coin->call(
			'name',
			function ($err, $data) use ($promise){
				if ($err !== null) {
					$promise->reject($err);
					return;
				}
				$promise->resolve($data[0]);
			}
		);
		
		return $promise->wait();
	}

	private function callSymbol()
	{
		$promise = new Promise();

		$this->coin->call(
			'symbol',
			function ($err, $data) use ($promise){
				if ($err !== null) {
					$promise->reject($err);
					return;
				}
				$promise->resolve($data[0]);
			}
		);
		
		return $promise->wait();
	}

	private function callDecimals()
	{
		$promise = new Promise();

		$this->coin->call(
			'decimals',
			function ($err, $data) use ($promise){
				if ($err !== null) {
					$promise->reject($err);
					return;
				}
				$promise->resolve($data[0]->value);
			}
		);
		
		return $promise->wait();
	}

}