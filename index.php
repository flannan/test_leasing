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
<body>
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

        f.time.max = "<?php echo amortizationTime() ?>";
        f.timeOutput.value = f.time.value;
        f.time
    }
</script>
<script type="text/javascript" class="init">    $(document).ready(function () {
        $('#outputTable').DataTable({
            dom    : 'Bfrtip',
            buttons: [
                'excelHtml5',
                'pdfHtml5'
            ],
            pageLength : 12
        });
    });
</script>


<form name="form1" action="index.php" method="post">
    <p><b>Лизинговый Калькулятор</b></p>

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
    ?>
    <table id="outputTable" class="display">
        <thead>
        <tr>
            <?php
            reshape($payments, $_POST['period']);
            foreach ((array)$payments[0] as $key => $value) {
                if ($key % 2 === 0) {
                    echo '<th>Number</th>' . "\n";
                } else {
                    echo '<th>Payment</th>' . "\n";
                }
            }
            ?>
        </tr>
        </thead>
        <tbody>
        <?php
        if (!empty($payments)) {
            foreach ($payments as $line) {
                echo '<tr>';
                foreach ($line as $value) {
                    if (is_int($value)) {
                        echo '<td>' . $value . '</td>';
                    } else {
                        echo '<td>' . sprintf('%.2f', $value) . '</td>';
                    }
                }
                echo '</tr>' . "\n";
            }
        }
        ?>
        </tbody>
    </table>
</output>
</body>

</html>
