<?php
declare(strict_types=1);

/** Возвращает срок амортизации
 *
 * @param string $amortizationType
 *
 * @return int
 */
function amortizationTime($amortizationType = null)
{
    $mysqli = new mysqli('localhost', 'stud08', 'stud08', 'test');
    $sql = <<<SQL
SELECT time
FROM Amortization
WHERE type=3
SQL;
    if (!empty($amortizationType)) {
        $sql = rtrim($sql, '1..9') . $amortizationType;
    } elseif ($_POST) {
        $amortizationType = (string) $_POST['purchaseType'];
        $sql = rtrim($sql, '1..9') . $amortizationType;
    }

    $res = $mysqli->query($sql);
    $result = $res->fetch_row();
    return $result[0];
}

/** Возвращает процент по кредитам
 *
 * @return int
 */
function CreditRate()
{
    $mysqli = new mysqli('localhost', 'stud08', 'stud08', 'test');
    $sql = <<<SQL
SELECT CreditRate
FROM Settings
SQL;

    $res = $mysqli->query($sql);
    $result = $res->fetch_row();
    return $result[0];
}

/** Возвращает компенсацию лизинговой компании (в процентах)
 *
 * @return int
 */
function ProfitRate()
{
    $mysqli = new mysqli('localhost', 'stud08', 'stud08', 'test');
    $sql = <<<SQL
SELECT ProfitRate
FROM Settings
SQL;
    //var_dump(CreditRate)
    $res = $mysqli->query($sql);
    $result = $res->fetch_row();
    return $result[0];
}

/** Возвращает НДС
 *
 * @return int
 */
function taxRate()
{
    $mysqli = new mysqli('localhost', 'stud08', 'stud08', 'test');
    $sql = <<<SQL
SELECT TaxRate
FROM Settings
SQL;
    //var_dump(CreditRate)
    $res = $mysqli->query($sql);
    $result = $res->fetch_row();
    return $result[0];
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
                    $payments[$key] = $value * ((int)$_POST['CreditRate'] / 100 + (int)$_POST['ProfitRate'] / 100)
                        * (1 + (int)$_POST['taxRate'] / 100)
                        + $depreciation;
                }
            }
        }
    }
    return $payments;
}

/** Для радиокнопок: Проверяет, что две строчки равны, и выдаёт checked, если это так.
 *
 * @param $value1
 * @param $value2
 *
 * @return string
 */
function check($value1, $value2)
{
    $answer = '';
    if ($value1 === $value2) {
        $answer = 'checked';
    }
    return $answer;
}

/** делает из списка платежей аккуратную таблицу
 *
 * @param $payments
 * @param $period
 */
function reshape(array &$payments, $period)
{

    if ($period === 'quarter') {
        $paymentsPerYear = 4;
    } elseif ($period === 'month') {
        $paymentsPerYear = 12;
    } else {
        $paymentsPerYear = null;
        foreach ($payments as $key => $value) {
            $payments[$key] = [$key + 1, $value];
        }
    }

    if (is_int($paymentsPerYear)) {
        $newPayments = array_fill(0, (int)ceil(count($payments) / $paymentsPerYear), []);
        $periodKey = 0;
        foreach ($payments as $key => $value) {
            $newPayments[$periodKey][] = $key + 1;
            $newPayments[$periodKey][] = $value;
            $periodKey++;
            if ($periodKey >= $paymentsPerYear) {
                $periodKey = 0;
            }
        }
        $payments = $newPayments;
    }
}
