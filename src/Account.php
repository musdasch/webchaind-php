<?php
namespace Webchaind;

use Litipk\BigNumbers\Decimal as Decimal;
use GMP as GMP;

use GuzzleHttp\Promise\Promise;
use Web3\Utils;

/**
 * Object that represents a mintme account.
 *
 * @author Musdasch <musdasch@gmail.com>
 * @license WTFPL
 */
class Account
{
	private $address;

	private $webchaind;

	private $personal;

	private $pw;

	private $transactionList = array();

	
	function __construct(Webchaind $webchaind, string $address, string $pw){
		$this->eth = $webchaind->eth;
		$this->personal = $webchaind->personal;
		$this->address = strtolower($address);
		$this->pw = $pw;
	}

	public function getBalance()
	{
		$promise = new Promise();

		$this->eth->getBalance($this->address, function ($err, $balance) use ($promise) {
			if ($err !== null) {
				$promise->reject($err->getMessage());
				return;
			}

			$promise->resolve($balance);
		});
		
		return gmp_init((string) $promise->wait());
	}

	public function sendTransaction(string $to, GMP $amount)
	{
		$tx = '0x';
		if(0 < gmp_cmp($amount, $this->getBalance()))
			return $tx;

		if($this->unlockAccount())
		{
			$promise = new Promise();

			$this->eth->sendTransaction(
				[
					'from' => $this->address,
					'to' => $to,
					'value' => '0x'.gmp_strval($amount, 16)
				],
				function ($err, $transaction) use ($promise) {
					if ($err !== null) {
						$promise->reject($err->getMessage());
						return;
					}

					$promise->resolve($transaction);
				}
			);

			$tx = $promise->wait();
			$this->lockAccount();
		}

		return $tx;
	}

	public function getReceipt(string $tx)
	{
		$promise = new Promise();

		$this->eth->getTransactionReceipt(
			$tx,
			function ($err, $receipt) use ($promise) {
				if ($err !== null) {
					$promise->reject($err->getMessage());
					return;
				}

				$promise->resolve($receipt);
			}
		);
		
		return $promise->wait();
	}

	public function getBlockNumber(){
		$promise = new Promise();

		$this->eth->blockNumber(
			function ($err, $blockNumber) use ($promise) {
				if ($err !== null) {
					$promise->reject($err->getMessage());
					return;
				}

				$promise->resolve($blockNumber);
			}
		);
		
		return gmp_init((string) $promise->wait());
	}

	public function findLatestTransactions(GMP $toBlock)
	{
		$blockNumber = $this->getBlockNumber();
		$address = $this->address;
		$transactionList = &$this->transactionList;

		for ($i=$blockNumber; 0 < gmp_cmp($i, $toBlock) ; $i = gmp_sub($i, gmp_init(1))) {

			$this->eth->getBlockByNumber(
				'0x'.gmp_strval($i, 16),
				true,
				function ($err, $transaction) use ($transactionList, $address) {
					if ($err !== null) {
						return;
					}

					$testAddress = strtolower('0x58A4d37aeef72D8e6AB984fA4113F79bb647F379');

					foreach ($transaction->transactions as $transaction) {
						if(strtolower($transaction->from) == $this->address)
						{
							array_push(
								$this->transactionList,
								$transaction
							);
						}

						if(strtolower($transaction->to) == $this->address)
						{
							array_push(
								$this->transactionList,
								$transaction
							);
						}
					}
				}
			);
		}
	}

	protected function unlockAccount(){
		$promise = new Promise();

		$this->personal->unlockAccount($this->address, $this->pw,
			function ($err, $unlocked) use ($promise) {
				if ($err !== null) {
					$promise->reject($err->getMessage());
					return;
				}

				$promise->resolve($unlocked);
			}
		);

		return $promise->wait();
	}

	protected function lockAccount(){
		$promise = new Promise();

		$this->personal->lockAccount($this->address,
			function ($err, $locked) use ($promise) {
				if ($err !== null) {
					$promise->reject($err->getMessage());
					return;
				}

				$promise->resolve($locked);
			}
		);

		return $promise->wait();
	}

	public function getAddress()
	{
		return $this->address;
	}

	public function getWebchaind()
	{
		return $this->webchaind;
	}

	public function getTransactionList()
	{
		return $this->transactionList;
	}

	public function toEther(GMP $value)
	{
		$value = Decimal::fromString((string) $value);
		$value = $value->div(Decimal::fromInteger(Utils::UNITS['ether']));
		return $value;
	}

	public function toWei(Decimal $value)
	{
		$value = $value->mul(Decimal::fromInteger(Utils::UNITS['ether']), 0);
		return gmp_init((string) $value);
	}
}