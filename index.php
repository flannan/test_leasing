<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Лизинговый Калькулятор</title>
</head>
<body>

</body>
<script>
    function updateMaxTime(f) {
        f.time.max = f.amortizationTime * 1;
        //f.time.value = f.time.max;
        f.timeOutput.value = f.time.value;
    }
</script>
<?php
require_once __DIR__ . '/backend.php';
$_POST['purchaseType'] = '3';
$_POST['cost'] = '1000';
$_POST['advancePayment'] = '0';
$_POST['time'] = '3';
$_POST['period'] = 'month';
$_POST['paymentType'] = 're-calculating';
$_POST['amortizationTime']='5';
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
            <input type="range" name="time" min="1" step="0.25"
                   value="<?= $_POST['cost'] ?>" oninput="timeOutput.value=time.value">
            <output name="timeOutput">5</output>
            лет
        </label></p>

    <p><b>Период оплаты</b></p>
    <p><label><input type="radio" name="period" value="month">месяц</label>
        <label><input type="radio" name="period" value="quarter">квартал</label>
        <label><input type="radio" name="period" value="year" checked>год</label></p>

    <p><b>Тип оплаты</b></p>
    <p><label><input type="radio" name="paymentType" value="flat">Равные платежи</label>
        <label><input type="radio" name="paymentType" value="re-calculating" checked>Спадающие платежи</label></p>

    <p>Срок амортизации
        <output name="amortizationTime">
            <?php
            require_once __DIR__ . '/backend.php';
            echo number_format(amortizationTime());
            ?>
        </output>
        лет, банковская ставка по кредиту
        <output name="CreditRate">
            <?php
            echo number_format(CreditRate());
            ?>
        </output>
        %, компенсация лизинговой компании и прочие услуги
        <output name="ProfitRate">
            <?php
            echo number_format(ProfitRate());
            ?>
        </output>
        %, НДС
        <output name="taxRate">
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
    $payments=calculate();
    var_export($payments);
    foreach ($payments as $value) {
        echo $value;
    }
    ?>
</output>
</html>
