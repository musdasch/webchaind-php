<?php
require('./exampleBase.php');

$account->findLatestTransactions(gmp_init(0));
print_r($account->getTransactionList());