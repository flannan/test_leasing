<?php
declare(strict_types=1);

/** Создаёт базу данных, с которой остальные функции могут работать.
 *
 * @return int
 */

$mysqli = new mysqli('localhost', 'stud08', 'stud08', 'test');
$sql = <<<SQL
DROP TABLE IF EXISTS Settings;
CREATE TABLE Settings
(
  CreditRate int,
  TaxRate    int,
  ProfitRate int
);

INSERT INTO Settings (CreditRate, TaxRate, ProfitRate)
VALUES (15, 10, 10);
SQL;
echo mysqli_multi_query($mysqli, $sql);
echo "\n";

$mysqli = new mysqli('localhost', 'stud08', 'stud08', 'test');
$sql = <<<SQL
DROP TABLE IF EXISTS Amortization;
CREATE TABLE Amortization
(
  Type int,
  Time int,
  description text
);
SQL;
echo mysqli_multi_query($mysqli, $sql);
echo "\n";

$mysqli = new mysqli('localhost', 'stud08', 'stud08', 'test');
$sql = <<<SQL

INSERT INTO Amortization
VALUES (3, 5, '3я группа: легковые автомобили с бензиновым двигателем до 3.5л, грузовые автомобили до 3.5тонн, автобусы до 7,5м, мотоциклы, мотороллеры, мопеды, скутеры, велосипеды'),
       (4, 7, '4ая группа: Автобусы от 7.5м до 12м, автобусы дальнего следования, автобусы городские от 16.5м до24м, самосвалы, бетоновозы, лесовозы'),
       (5, 10, '5ая группа: легковые автомобили с двигателем выше 3.5л, легковые автомобили с дизельным двигателем, грузовые автомобили выше 3.5 тонн, автобусы прочие от 16.5 до 24м, автокраны.');
SQL;

echo mysqli_multi_query($mysqli, $sql);
echo "\n";
