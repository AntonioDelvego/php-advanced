<?php
require_once dirname(__DIR__) . '/app/init_app.php';

use Database\Database;
use Classes\LoremIpsum;

function createTable($sqlQuery)
{
    try {
        $db = Database::getInstance()->getDb();
        $request = $db->prepare($sqlQuery);
        $request->execute();
        echo "table created successfully</br>";
    } catch (Exception $exception) {
        echo $exception->getMessage();
    }

}

function fillTable($rowQuantity)
{
    $loremIpsum = new LoremIpsum();
    try {
        $imagesList = getImages();
        $sqlQuery = "INSERT INTO `goods` (`name`, `description`, `price`, `preview_image`, `big_image`) VALUES (:name, :description, :price, :previewImage, :bigImage);";

        $db = Database::getInstance()->getDb();
        $request = $db->prepare($sqlQuery);

        $imagesPath =  '/assets/images/';

        for ($i = 0; $i < $rowQuantity; $i++) {
            $imageIndex = $i >= sizeof($imagesList) ? $i - sizeof($imagesList) : $i;

            $previewImageFileName =$imagesPath . $imagesList[$imageIndex]['id'] . '-prev.jpg';
            $bigImageFileName =$imagesPath . $imagesList[$imageIndex]['id'] . '-big.jpg';
            $previewImageFile = fopen(__DIR__ . $previewImageFileName, 'w');
            $bigImageFile = fopen(__DIR__ . $bigImageFileName, 'w');
            $curlPrevImg = curl_init($imagesList[$imageIndex]['webformatURL']);
            $curlBigImg = curl_init($imagesList[$imageIndex]['largeImageURL']);

            curl_setopt($curlPrevImg, CURLOPT_FILE, $previewImageFile);
            curl_setopt($curlBigImg, CURLOPT_FILE, $bigImageFile);

            curl_exec($curlPrevImg);
            curl_exec($curlBigImg);

            $requestParams = [
                'name' => $loremIpsum->word(),
                'description' => $loremIpsum->words(12),
                'price' => rand(1, 100000),
                'previewImage' => $previewImageFileName,
                'bigImage' => $bigImageFileName,
            ];
            $request->execute($requestParams);
        }
        echo "table goods filled successfully. Added rows - $rowQuantity</br>";
    } catch (PDOException $exception) {
        echo $exception->getMessage();
    }
}

function getImages() {
    $queryWords = '&q=furniture';
    $imageType = '&image_type=photo';
    $orientation = '&orientation=horizontal';
    $imagesPerPage = '&per_page=200';
    $requestPath = "https://pixabay.com/api/?key=" . $_ENV['PIXABAY_API'] . $queryWords . $imageType . $orientation . $imagesPerPage;

    $response = file_get_contents($requestPath);
    $result = json_decode($response, true);
    return $result['hits'];
}

$usersTableQuery = "CREATE TABLE `users` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `name` VARCHAR(255) NOT NULL , `email` VARCHAR(255) NOT NULL , `password` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`), UNIQUE (`email`)) ENGINE = InnoDB;";

$goodsTableQuery = "CREATE TABLE `goods` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `name` VARCHAR(127) NOT NULL , `description` TEXT NOT NULL , `price` INT UNSIGNED NOT NULL , `preview_image` VARCHAR(255) NOT NULL , `big_image` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;";

echo "init_db.php </br>";

if ($_GET['pass'] === $_ENV['DB_PASS']) {
    echo "<h3>you pass</h3>";
    try {
        $db = Database::getInstance()->getDb();
        createTable($goodsTableQuery);
        createTable($usersTableQuery);
        fillTable(200);
    } catch (PDOException $exception) {
        echo $exception->getMessage();
    }
}
