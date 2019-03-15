<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Лизинговый Калькулятор</title>
    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.5.4/css/buttons.dataTables.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.4/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.4/js/buttons.html5.min.js"></script>

</head>
<body onload="updateTime(form1)">
<?php
require_once __DIR__ . '/backend.php';
require_once __DIR__ . '/vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// create a log channel
$log = new Logger('name');
try {
    $log->pushHandler(new StreamHandler(__DIR__ . '/requests.log', Logger::INFO));
} catch (Exception $e) {
    echo 'внимание, ваши запросы не записываются. ';
    echo $e . "\n";
}


//default $_POST values
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
    function updateMaxTime(form, amortizationTime) {
        form.time.max = amortizationTime;
        form.timeOutput.value = form.time.value;
        form.amortizationTime.value = amortizationTime;
    }

    function updateTime(form) {
        form.timeOutput.value = form.time.value;

        if ("1" === form.time.value) {
            form.wordForYears.value = "год";
        } else if (5 > Number(form.time.value)) {
            form.wordForYears.value = "года";
        } else {
            form.wordForYears.value = "лет";
        }
    }
</script>
<script type="text/javascript" class="init">    $(document).ready(function () {
        $('#outputTable').DataTable({
            dom       : 'Bfrtip',
            buttons   : [
                'excelHtml5',
                'pdfHtml5'
            ],
            pageLength: 12,
            language  : {
                url: "//cdn.datatables.net/plug-ins/1.10.19/i18n/Russian.json"
            },
            ordering : false
        });
    });
</script>


<form name="form1" action="index.php" method="post">
    <p><b>Лизинговый Калькулятор</b></p>

    <p><b>Амортизационная группа транспортного средства:</b></p>
    <p><label>
            <input type="radio" name="purchaseType" value="3"
                   oninput="updateMaxTime(form1,<?= amortizationTime('3') ?>)"
                <?= check($_POST['purchaseType'], '3') ?> >
            3я группа: легковые автомобили с бензиновым двигателем до 3.5л,
            грузовые автомобили до 3.5тонн, автобусы до 7,5м, мотоциклы, мотороллеры, мопеды, скутеры, велосипеды
        </label></p>
    <p><label>
            <input type="radio" name="purchaseType" value="4"
                   oninput="updateMaxTime(form1,<?= amortizationTime('4') ?>)"
                <?= check($_POST['purchaseType'], '4') ?> >
            4ая группа: Автобусы от 7.5м до 12м, автобусы дальнего следования, автобусы городские от 16.5м до24м,
            самосвалы, бетоновозы, лесовозы
        </label></p>
    <p><label>
            <input type="radio" name="purchaseType" value="5"
                   oninput="updateMaxTime(form1,<?= amortizationTime('5') ?>)"
                <?= check($_POST['purchaseType'], '5') ?> >
            5ая группа: легковые автомобили с двигателем выше 3.5л, легковые автомобили с дизельным двигателем,
            грузовые автомобили выше 3.5 тонн, автобусы прочие от 16.5 до 24м, автокраны.
        </label></p>

    <p><label>
            Цена
            <input type="number" min="0" name="cost" value="<?= $_POST['cost'] ?>"/>
            рублей
        </label></p>
    <p><label>Аванс <input type="number" name="advancePayment" value="<?= $_POST['advancePayment'] ?>"> </label></p>
    <p><label>Срок лизинга
            <input type="range" name="time" min="1" step="0.25" max="<?= $_POST['amortizationTime'] ?>"
                   value="<?= $_POST['time'] ?>" oninput="updateTime(form1)">
            <output name="timeOutput"><?= $_POST['time'] ?></output>
            <output name="wordForYears">года</output>
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
    $log->info('received a calculation request ', $_POST);
    $payments = calculate($_POST);
    $sum = array_sum($payments);
    ?>
    <p>Экспорт</p>

    <table id="outputTable" class="display">
        <thead>
        <tr>
            <?php
            reshape($payments);
            $payments[] = ['Итого', $sum];
            foreach ((array)$payments[0] as $key => $value) {
                if ($key % 2 === 0) {
                    echo '<th>№</th>' . "\n";
                } else {
                    echo '<th>Платёж</th>' . "\n";
                }
            }
            ?>
        </tr>
        </thead>
        <tbody>
        <?php

        foreach ($payments as $line) {
            echo '<tr>';
            echo '<td>' . $line[0] . '</td>';
            echo '<td>' . number_format($line[1], 2, '.', ' ') . '</td>';
            echo '</tr>' . PHP_EOL;
        }
        ?>
        </tbody>
    </table>
</output>
</body>

</html>
