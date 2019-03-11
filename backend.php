<?php
declare(strict_types=1);

/** Возвращает срок амортизации
 *
 * @return int
 */
function amortizationTime()
{
    if ($_POST) {
        $amortizationType = $_POST['purchaseType'];
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

/** performs actual calculations
 *
 * @return array
 */
function calculate()
{
    $payments = [];
    if ($_POST) {
        $paymentType = $_POST['paymentType'];
        //$payments = [];
        $numberOfPayments = (float)$_POST['time'];
        if ($_POST['period'] === 'quarter') {
            $numberOfPayments *= 4;
        } elseif ($_POST['period'] === 'month') {
            $numberOfPayments *= 12;
        }
        $numberOfPayments = (int)ceil($numberOfPayments);

        if ($paymentType === 'flat') {
            $totalPayment = ($_POST['cost'] - $_POST['advancePayment']) * (1 +
                    ((1 + $_POST['CreditRate'] / 100) ** $_POST['amortizationTime'] - 1
                        + $_POST['ProfitRate'] / 100) * (1 + $_POST['taxRate'] / 100));
            $payments = array_fill(0, $numberOfPayments, $totalPayment / $numberOfPayments);
        } else {
            $amortizationTime = (int)$_POST['amortizationTime'];
            if ($_POST['period'] === 'quarter') {
                $amortizationTime *= 4;
            } elseif ($_POST['period'] === 'month') {
                $amortizationTime *= 12;
            }
            $cost = array_fill(0, $amortizationTime, $_POST['cost'] - $_POST['advancePayment']);
            $depreciation = $cost[0] / $amortizationTime;
            foreach ($cost as $key => $currentCost) {
                if ($key === 0) {
                    $cost[$key] -= $depreciation / 2;
                } else {
                    $cost[$key] = $cost[$key - 1] - $depreciation;
                }
            }
            foreach ($cost as $key => $value) {
                if ($key < $numberOfPayments) {
                    $payments[$key] = $value * ($_POST['CreditRate'] / 100 + $_POST['ProfitRate'] / 100)
                        * (1 + $_POST['taxRate'] / 100)
                        + $depreciation;
                }
            }
        }
    }
    return $payments;
}

function payment_times()
{

}