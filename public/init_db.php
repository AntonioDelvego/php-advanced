<?php
require_once dirname(__DIR__) . '/app/init_app.php';

use Database\Database;
use Classes\LoremIpsum;

function createTable($db, $sqlQuery)
{
    $db->exec($sqlQuery);
    echo "table created successfully</br>";
}

function fillTable($db, $tableName, $rowQuantity)
{
    $loremIpsum = new LoremIpsum();
    try {
        for ($i = 0; $i < $rowQuantity; $i++) {
            $name = $loremIpsum->word();
            $description = $loremIpsum->words(12);
            $price = rand(1, 100000);
            $addRowQuery = "INSERT INTO $tableName (`name`, `description`, `price`) VALUES ('$name', '$description', $price);";
            $db->exec($addRowQuery);
        }
        echo "$tableName filled successfully. Added rows - $rowQuantity</br>";
    } catch (PDOException $exception) {
        echo $exception->getMessage();
    }
}

$usersTableQuery = "CREATE TABLE `users` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `name` VARCHAR(255) NOT NULL , `email` VARCHAR(255) NOT NULL , `password` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`), UNIQUE (`email`)) ENGINE = InnoDB;";

$goodsTableQuery = "CREATE TABLE `goods` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `name` VARCHAR(255) NOT NULL, `description` TEXT NOT NULL, `price` INT UNSIGNED NOT NULL, PRIMARY KEY (`id`)) ENGINE = InnoDB;";

echo "init_db.php </br>";

if ($_GET['pass'] === $_ENV['DB_PASS']) {
    echo "<h3>you pass</h3>";
    try {
        $db = Database::getInstance()->getDb();
        createTable($db, $goodsTableQuery);
        createTable($db, $usersTableQuery);
        fillTable($db,'goods', 200);
    } catch (PDOException $exception) {
        echo $exception->getMessage();
    }
}
