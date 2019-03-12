<?php
declare(strict_types=1);

/** Возвращает срок амортизации
 *
 * @return int
 */
function amortizationTime()
{
    $mysqli = new mysqli('localhost', 'stud08', 'stud08', 'test');
    $sql = <<<SQL
SELECT time
FROM Amortization
WHERE type=3
SQL;
    if ($_POST) {
        $amortizationType = $_POST['purchaseType'];
        $sql = rtrim($sql, '1..9') . $amortizationType;
    }
    //$sql=$sql.$amortizationType;

    $res = $mysqli->query($sql);
    $result = $res->fetch_row();
    return $result[0];
}

/** создаёт радиокнопки по данным из базы данных.
 *
 * @return string
 */
function generateAmortizationGroups()
{
    $mysqli = new mysqli('localhost', 'stud08', 'stud08', 'test');
    $sql = <<<SQL
SELECT *
FROM Amortization
SQL;
    $res = $mysqli->query($sql);
    $result = $res->fetch_all();
    //var_dump($result);

    $output = '';
    foreach ((array)$result as $type) {
        $output = $output . '<p><label><input type="radio" name="purchaseType" value="' . $type[0] .
            '" oninput="updateMaxTime(form1)" ';
        if ($_POST['purchaseType'] === $type[0]) {
            $output .= ' checked';
        }
        $output = $output . '> ' . $type[2] . ' </label></p>';
    }

    return $output;
    //foreach
    //'<p><label>'
    //<input type="radio" name="purchaseType" value="3" oninput="updateMaxTime(form1)" checked>
//'</label></p>'
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
 * @param $value1
 * @param $value2
 *
 * @return string
 */
function check($value1, $value2)
{
    $answer='';
    if ($value1===$value2) {
        $answer='checked';
    }
    return $answer;
}
