<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Лизинговый Калькулятор</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

</body>
<script>
    function updateMaxTime(f) {
        f.time.max = f.amortizationTime;
        //f.time.value = f.time.max;
        f.timeOutput.value = f.time.value;
    }
</script>
<?php
require_once __DIR__ . '/backend.php';
if (empty($_POST)) {
    $_POST['purchaseType'] = '3';
    $_POST['cost'] = '1000';
    $_POST['advancePayment'] = '0';
    $_POST['time'] = '3';
    $_POST['period'] = 'year';
    $_POST['paymentType'] = 're-calculating';
    $_POST['CreditRate'] = CreditRate();
    $_POST['ProfitRate'] = ProfitRate();
    $_POST['taxRate'] = taxRate();
    $_POST['amortizationTime'] = '5';
}
?>
<form name="form1" action="index.php" method="post">
    <p><b>Амортизационная группа транспортного средства:</b></p>
    <p><label>
            <input type="radio" name="purchaseType" value="3" oninput="updateMaxTime(form1)" checked>
            3я группа: легковые автомобили с бензиновым двигателем до 3.5л, грузовые автомобили до 3.5тонн, автобусы до
            7,5м, мотоциклы, мотороллеры, мопеды, скутеры, велосипеды
        </label>
    </p>
    <p><label><input type="radio" name="purchaseType" value="4" oninput="updateMaxTime(form1)">4ая группа: Автобусы от
            7.5м до 12м, автобусы дальнего следования, автобусы городские от 16.5м до24м, самосвалы, бетоновозы,
            лесовозы
        </label></p>
    <p><label><input type="radio" name="purchaseType" value="5" oninput="updateMaxTime(form1)">5ая группа: легковые
            автомобили с двигателем выше 3.5л, легковые автомобили с дизельным двигателем, грузовые автомобили выше 3.5
            тонн, автобусы прочие от 16.5 до 24м, автокраны.
        </label></p>

    <p><label>
            Цена
            <input type="number" min="0" name="cost" value="<?= $_POST['cost'] ?>"/>
            рублей
        </label></p>
    <p><label>Аванс <input type="number" name="advancePayment" value="<?= $_POST['advancePayment'] ?>"> </label></p>
    <p><label>Срок лизинга
            <input type="range" name="time" min="1" step="0.25" max="<?= $_POST['amortizationTime'] ?>"
                   value="<?= $_POST['time'] ?>" oninput="timeOutput.value=time.value">
            <output name="timeOutput"><?= $_POST['time'] ?></output>
            лет
        </label></p>

    <p><b>Период оплаты</b></p>
    <p><label><input type="radio" name="period" value="month">месяц</label>
        <label><input type="radio" name="period" value="quarter">квартал</label>
        <label><input type="radio" name="period" value="year" checked>год</label></p>

    <p><b>Тип оплаты</b></p>
    <p><label><input type="radio" name="paymentType" value="flat">Равные платежи</label>
        <label><input type="radio" name="paymentType" value="re-calculating" checked>Спадающие платежи</label></p>

    <input type="hidden" name="amortizationTime" value="<?= $_POST['amortizationTime']; ?>">
    <input type="hidden" name="CreditRate" value="<?= $_POST['CreditRate']; ?>">
    <input type="hidden" name="ProfitRate" value="<?= $_POST['ProfitRate']; ?>">
    <input type="hidden" name="taxRate" value="<?= $_POST['taxRate']; ?>">

    <p>Срок амортизации
        <output>
            <?php
            require_once __DIR__ . '/backend.php';
            echo number_format(amortizationTime());
            ?>
        </output>
        лет, банковская ставка по кредиту
        <output>
            <?php
            echo number_format(CreditRate());
            ?>
        </output>
        %, компенсация лизинговой компании и прочие услуги
        <output>
            <?php
            echo number_format(ProfitRate());
            ?>
        </output>
        %, НДС
        <output>
            <?php
            echo number_format(taxRate());
            ?>
        </output>
        %.
    </p>

    <input type="submit" value="Рассчитать"/>

</form>

<output>
    <?php
    //var_dump($_POST);
    $payments = calculate();
    //$payments = [123];
    echo '<table>' . "\n";
    if (!empty($payments)) {
        foreach ($payments as $key => $value) {
            echo '<tr><td>' . ($key + 1) . '</td><td>' . sprintf('%.2f', $value) . '</td><tr>' . "\n";
        }
    }
    echo '</table>' . "\n";
    ?>
</output>

</html>
