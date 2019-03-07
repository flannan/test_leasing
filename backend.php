<?php
declare(strict_types=1);
/** Возвращает срок амортизации
 *
 * @return int
 */
function amortizationTime()
{
    if ($_POST) {
        $amortizationType = $_POST['form1_purchaseType'];
    } else {
        $amortizationType = '3';
    }
// заглушка
    $amortizationTime = 5;
    if ($amortizationType === '4') {
        $amortizationTime = 7;
    } elseif ($amortizationType === '5') {
        $amortizationTime = 10;
    }
    return $amortizationTime;
}

/** Возвращает процент по кредитам
 *
 * @return int
 */
function CreditRate()
{
    // заглушка
    return 15;
}

/** Возвращает компенсацию лизинговой компании (в процентах)
 *
 * @return int
 */
function ProfitRate()
{
    // заглушка
    return 10;
}

/** Возвращает НДС
 *
 * @return int
 */
function taxRate()
{
    // заглушка
    return 15;
}

function calculate()
{
    if ($_POST) {
        $paymentType = $_POST['form1_paymentType'];
    } else {
        $paymentType = 're-calculating';
    }
    if ($paymentType === 're-calculating') {

    }

}
