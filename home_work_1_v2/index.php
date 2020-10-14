<?php
//VARIABLES-------------------------------------------------------------------------------------------------------------

$price = 34499; // стоимость телефона
$payMaxSize = 5000; // максимально возможный ежемесячный платеж

//BANK_LIST-------------------------------------------------------------------------------------------------------------

$bank1 = ['bankName' => 'HomoCredit', // название банка
          'firstPay' => 5000,         // первый платеж
          'percent' => 4,             // процент на сумму остатка
          'commission' => 500,        // ежемесячная коммисия
          'addPay' => 0];             // дополнительный платеж

$bank2 = ['bankName' => 'Softbank',
          'firstPay' => 5000,
          'percent' => 3,
          'commission' => 1000,
          'addPay' => 0];

$bank3 = ['bankName' => 'StrawberryBank',
          'firstPay' => 5000,
          'percent' => 2,
          'commission' => 0,
          'addPay' => 6666];

$bankList = [$bank1, $bank2, $bank3];

//EXECUTION-------------------------------------------------------------------------------------------------------------

$minSum = null;
$bestBank = null;

foreach ($bankList as $i => $bank) {
    $bankName = $bank ['bankName'];
    $termAndSum = defineTermAndSum ($price, $payMaxSize, $bank);
    $term = $termAndSum ['term']; // срок выплаты кредита
    $sum = $termAndSum ['sum']; // сумма выплаты кредита
    $sum = round ($sum, 2); //округляем сумму выплаты;

    if (!$minSum) {
        $minSum = $sum;
        $bestBank = $bankName;
    } elseif ($sum < $minSum) {
        $minSum = $sum;
        $bestBank = $bankName;
    }

    echo "Банк $bankName: </br> срок выплаты кредита - $term месяцев, </br>
           сумма выплаты кредита - $sum гривен. </br> </br>";
}

echo "Наиболее выгодное предложение у $bestBank. Всего $minSum гривен.";

//FUNCTIONS-------------------------------------------------------------------------------------------------------------

function defineTermAndSum ($price, $payMaxSize, $bank) { //ф-ция для вычисления срока выплаты кредита
    $termAndSum = [];
    $firstPay = $bank ['firstPay'];
    $percent = $bank ['percent'];
    $commission = $bank ['commission'];
    $addPay = $bank ['addPay'];
    $price = addPay ($price, $addPay); // дополнительный платеж (если есть) одноразово прибавляется к сумме кредита
    $debt = $price - $firstPay; // тело кредита (общее), оставшееся после первого платежа (уменьшается с каждым платежом)
    $month = 1; // количество месяцев;
    $sum = $firstPay; // сумма выплаты кредита

    while ($debt > 0) {
        $payPercent = ($percent * $debt) / 100; // сумма, которую выплачиваем как процент в каждом платеже
        $payBody = $payMaxSize - $payPercent - $commission;// тело кредита в каждом платеже
        if ($debt > $payBody) {
            $sum += $payMaxSize;
            $debt -= $payBody;   // совершаем платеж
            $month++;            // и считаем месяц
        } else {                 // если оставшаяся сумма долга меньше тела кредита в отдельном платеже - погашаем кредит
            $sum += $debt + $payPercent + $commission;
            $debt = 0;
            $month++;
        }
    }

    $termAndSum ['term'] = $month;
    $termAndSum ['sum'] = $sum;
    return $termAndSum;
}

function addPay ($price, $addPay) { // ф-ция для добавления дополнительного платежа к сумме кредита
    if ($addPay) {
        $price += $addPay;
    }
    return $price;
}













