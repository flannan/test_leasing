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
 * @param array $post
 *
 * @return array
 */
function calculate(array $post)
{
    $payments = [];
    if ($post) {
        $paymentType = $post['paymentType'];
        //$payments = [];
        $numberOfPayments = (float)$post['time'];
        if ($post['period'] === 'quarter') {
            $numberOfPayments *= 4;
        } elseif ($post['period'] === 'month') {
            $numberOfPayments *= 12;
        }
        $numberOfPayments = (int)ceil($numberOfPayments);

        if ($paymentType === 'flat') {
            $totalPayment = ($post['cost'] - $post['advancePayment']) * (1 +
                    ((1 + $post['CreditRate'] / 100) ** $post['amortizationTime'] - 1
                        + $post['ProfitRate'] / 100) * (1 + $post['taxRate'] / 100));
            $payments = array_fill(0, $numberOfPayments, $totalPayment / $numberOfPayments);
        } else {
            $payments = descendingPayments($post, $numberOfPayments);
        }
    }

    return $payments;
}

/**Расчёт спадающих платежей.
 *
 * @param $post
 * @param $numberOfPayments
 *
 * @return mixed
 */
function descendingPayments($post, $numberOfPayments)
{
    $payments = [];
    $amortizationTime = (int)$post['amortizationTime'];
    if ($post['period'] === 'quarter') {
        $amortizationTime *= 4;
    } elseif ($post['period'] === 'month') {
        $amortizationTime *= 12;
    }
    $cost = array_fill(0, $amortizationTime, $post['cost'] - $post['advancePayment']);
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
            $payments[$key] = $value * ((int)$post['CreditRate'] / 100 + (int)$post['ProfitRate'] / 100)
                * (1 + (int)$post['taxRate'] / 100)
                + $depreciation;
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

/** делает из списка платежей нумерованную таблицу
 *
 * @param $payments
 */
function reshape(array &$payments)
{
    foreach ($payments as $key => $value) {
        $payments[$key] = [$key + 1, $value];
    }
}
