<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Лизинговый Калькулятор</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

</body>
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
<script>
    function updateMaxTime(f) {
        f.time.max = <?= amortizationTime() ?>;
        f.timeOutput.value = f.time.value;
    }
</script>
<script src="xlsx.core.js"></script>
<script src="FileSaver.js"></script>
<script src="tableexport.js"></script>

<form name="form1" action="index.php" method="post">
    <p><b>Амортизационная группа транспортного средства:</b></p>
    <?php echo generateAmortizationGroups() ?>

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
    <p><label><input type="radio" name="period" value="month"
                <?= check($_POST['period'], 'month') ?>>месяц</label>
        <label><input type="radio" name="period" value="quarter"
                <?= check($_POST['period'], 'quarter') ?> >квартал</label>
        <label><input type="radio" name="period" value="year"
                <?= check($_POST['period'], 'year') ?> >год</label>
    </p>

    <p><b>Тип оплаты</b></p>
    <p><label><input type="radio" name="paymentType" value="flat"
                <?= check($_POST['paymentType'], 'flat') ?>>Равные платежи</label>
        <label><input type="radio" name="paymentType" value="re-calculating"
                <?= check($_POST['paymentType'], 're-calculating') ?>>Спадающие платежи</label></p>

    <input type="hidden" name="amortizationTime" value="<?= (int)$_POST['amortizationTime']; ?>">
    <input type="hidden" name="CreditRate" value="<?= (int)$_POST['CreditRate']; ?>">
    <input type="hidden" name="ProfitRate" value="<?= (int)$_POST['ProfitRate']; ?>">
    <input type="hidden" name="taxRate" value="<?= (int)$_POST['taxRate']; ?>">

    <p>
        <output>
            Срок амортизации <?= $_POST['amortizationTime'] ?> лет,
            банковская ставка по кредиту <?= $_POST['CreditRate'] ?> %,
            компенсация лизинговой компании и прочие услуги <?= $_POST['ProfitRate'] ?> %,
            НДС <?= $_POST['taxRate'] ?> %.
        </output>
    </p>
    <input type="submit" value="Рассчитать"/>

</form>

<output>
    <?php
    $payments = calculate();
    echo '<table>' . "\n";
    if (!empty($payments)) {
        foreach ($payments as $key => $value) {
            echo '<tr><td>' . ($key + 1) . '</td><td>' . sprintf('%.2f', $value) . '</td><tr>' . "\n";
        }
    }
    echo '</table>' . "\n";
    ?>

</output>
<script>new TableExport(document.getElementsByTagName("table"));</script>
</html>
