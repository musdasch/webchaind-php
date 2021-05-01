<?php
namespace Webchaind;

use Litipk\BigNumbers\Decimal as Decimal;
use GuzzleHttp\Promise\Promise;
use Webchaind\ERC20;

class Invoice
{
	private $erc20;

	private $depositAddress;

	private $mainAddress;

	private $amount;

	private $amountInProcess;

	private $div;

	private $open = true;

	private $dependent = true;

	private $tx;

	public function __construct(ERC20 $erc20, string $depositAddress, Decimal $amount, string $mainAddress)
	{
		$this->erc20 = $erc20;
		$this->depositAddress = $depositAddress;
		$this->mainAddress = $mainAddress;
		$this->amount = $amount;
	}

	public function checkForPayment()
	{
		if($this->dependent && $this->open){
			$balance = $this->erc20->balanceOf($this->depositAddress);
			if(0 < gmp_cmp($balance, "0")){
				$this->tx = $this->erc20->transfer(
					$this->depositAddress,
					$this->mainAddress,
					$balance
				);

				$this->amountInProcess = $this->erc20->toDecimal($balance);
				$this->open = false;
			}

			return false;
		} elseif ($this->dependent) {
			
			$eth = $this->erc20->getEth();

			$promise = new Promise();
			$eth->getTransactionReceipt(
				$this->tx,
				function ($err, $data) use ($promise) {
					if ($err !== null) {
						$promise->reject($err);
					}
					$promise->resolve($data);
				}
			);

			print_r($promise->wait());

			$this->div = $this->amount->sub($this->amountInProcess);
			$this->dependent = false;

			return false;
		} else {
			return true;
		}
	}

	public function getTX()
	{
		return $this->tx;
	}

	public function getAmount()
	{
		return $this->amount;
	}

	public function getDiv()
	{
		return $this->div;
	}

	public function getIPA()
	{
		$this->amountInProcess;
	}
}