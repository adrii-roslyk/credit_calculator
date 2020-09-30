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

$sums = []; // создаем массив из сумм выплаты всего кредита

for ($i = 0; $i < count($bankList); $i++) {

    $bankName = $bankList [$i] ['bankName'];

    $term = defineTerm ($price, $payMaxSize, $bankList [$i]); // срок выплаты кредита

    $sum = defineSum ($price, $payMaxSize, $bankList [$i], $term); // сумма выплаты кредита

    $sum = round ($sum, 2); //округляем сумму выплаты;

    $bankList [$i] ['sum'] = $sum;

    $sums[$i] = $bankList [$i] ['sum']; // создаем массив из сумм выплаты всего кредита

    echo  "Банк $bankName: </br> срок выплаты кредита - $term месяцев, </br>    
           сумма выплаты кредита - $sum гривен. </br> </br>";

}

$minSum =  min ($sums); // узнаем минимальную сумму выплаты всего кредита

foreach ($bankList as $i => $bank) {

    $bankName = $bankList [$i] ['bankName'];

    if ($bankList [$i] ['sum'] != $minSum ) continue;

    echo "Наиболее выгодное предложение у $bankName. Всего $minSum гривен.";

}

//FUNCTIONS-------------------------------------------------------------------------------------------------------------

function defineTerm ($price, $payMaxSize, $bank) { //ф-ция для вычисления срока выплаты кредита

    $firstPay = $bank ['firstPay'];
    $percent = $bank ['percent'];
    $commission = $bank ['commission'];
    $addPay = $bank ['addPay'];

    $price = addPay ($price, $addPay); // дополнительный платеж (если есть) одноразово прибавляется к сумме кредита

    $debt = $price - $firstPay; // тело кредита (общее), оставшееся после первого платежа (уменьшается с каждым платежом)

    $month = 1; // количество месяцев;

    while ($debt > 0) {

        $payPercent = ($percent * $debt) / 100; // сумма, которую выплачиваем как процент в каждом платеже

        $payBody = $payMaxSize - $payPercent - $commission;// тело кредита в каждом платеже

        if ($debt > $payBody) {

            $debt -= $payBody;   // совершаем платеж

            $month++;            // и считаем месяц

        } else {                 // если оставшаяся сумма долга меньше тела кредита в отдельном платеже - погашаем кредит

            $debt = 0;

            $month++;

        }

    }

    return $month;
}

function defineSum ($price, $payMaxSize, $bank, $term) { //ф-ция для вычисления суммы выплаты кредита

    $firstPay = $bank ['firstPay'];
    $percent = $bank ['percent'];
    $commission = $bank ['commission'];
    $addPay = $bank ['addPay'];

    $price = addPay ($price, $addPay); // дополнительный платеж (если есть) одноразово прибавляется к сумме кредита

    $sum = $firstPay; // сумма выплаты кредита

    $debt = $price - $firstPay; // тело кредита, оставшееся после первого платежа (уменьшается с каждым платежом)

    $month = $term - 1; // срок выплаты кредита (уменьшается с каждым платежом)

    for ($i = $month; $i; $i--) {

        $payPercent = ($percent * $debt) / 100; // процент в каждом платеже

        $payBody = $payMaxSize - $payPercent - $commission;// тело кредита в каждом платеже

        if ($debt > $payBody) {

            $debt -= $payBody;    // совершаем платеж

            $sum += $payMaxSize;  // и прибавляем к сумме выплаты кредита размер платежа с процентами и комиссией

            $month--;

        } else { //если оставшаяся сумма долга меньше тела кредита в отдельном платеже - погашаем кредит

            $sum += $debt + $payPercent + $commission;

            $month--;

        }

    }

    return $sum;

}

function addPay ($price, $addPay) { // ф-ция для добавления дополнительного платежа к сумме кредита

    if ($addPay) {

        $price += $addPay;

    }

    return $price;

}













