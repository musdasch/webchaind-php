<?php
namespace Webchaind;

use Litipk\BigNumbers\Decimal as Decimal;
use GMP as GMP;

use Webchaind\Webchaind;
use Webchaind\ERC20;
use Webchaind\Account;

/**
 * Object that represents a mintme account with erc20 tokens.
 *
 * @author Musdasch <musdasch@gmail.com>
 * @license WTFPL
 */
class ERC20Account extends Account
{
	private $erc20;

	private $account;

	/**
	 * Constructer
	 * @param Webchaind
	 * @param string
	 * @param string
	 */
	function __construct(ERC20 $erc20, Webchaind $webchaind, string $address, string $pw)
	{
		parent::__construct($webchaind, $address, $pw);
		$this->erc20 = $erc20;
	}

	public function getBalance()
	{
		return $this->erc20->balanceOf(parent::getAddress());
	}

	public function getMintMeBalance()
	{
		return parent::getBalance();
	}

	public function sendTransaction(string $to, GMP $amount)
	{
		$tx = '0x';
		if(0 < gmp_cmp($amount, $this->getBalance()))
			return $tx;


		if(parent::unlockAccount())
		{
			$tx = $this->erc20->transfer(parent::getAddress(), $to, $amount);
			parent::lockAccount();
		}

		return $tx;
	}


	public function toToken(GMP $value)
	{
		$value = Decimal::fromString((string) $value);
		$value = $value->div(Decimal::fromInteger(gmp_intval($this->erc20->getDecimals())));
		return $value;
	}

	public function toBits(Decimal $value)
	{
		$value = $value->mul(Decimal::fromInteger(gmp_intval($this->erc20->getDecimals())), 0);
		return gmp_init((string) $value);
	}
}