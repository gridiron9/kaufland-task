<?php
include "log.php";
global $connect;

try {
    $connect = mysqli_connect(
        'db', # service name
        'php_docker', # username
        'password', # password
        'php_docker' # db table
    );
}catch (mysqli_sql_exception $e){
    logger("Unfortunately, the details you entered for connection are incorrect! " .$e );
    die("Unfortunately, the details you entered for connection are incorrect!\n");
}


function check_if_table_exists($connect, $table_name)
{
    $sql = "SHOW TABLES IN php_docker";

    $result = mysqli_query($connect, $sql);

    foreach ($result as $re){
        if ($re["Tables_in_php_docker"] == $table_name)
            return true;
    }

    return false;
}

function create_table($connect){
    $sql = "CREATE TABLE php_docker.products (id INT NOT NULL AUTO_INCREMENT , entity_id INT NULL , category_name VARCHAR(255) NULL , sku VARCHAR(255) NULL , name VARCHAR(255) NULL , description LONGTEXT NULL , short_desc TEXT NULL , price DOUBLE NULL , link VARCHAR(255) NULL , image VARCHAR(255) NULL , brand VARCHAR(255) NULL , rating DOUBLE NULL , caffeine_type VARCHAR(255) NULL , count INT NULL , flavored BOOLEAN NULL , seasonal BOOLEAN NULL , instock BOOLEAN NULL , facebook BOOLEAN NULL , is_k_cup BOOLEAN NULL , PRIMARY KEY (id)) ENGINE = InnoDB;";

// perform the query and store the result
    $result = mysqli_query($connect, $sql);

    if ($result){
        print_r("Table created successfully. \n");
        return true;
    }

    die("Was not able to create table.\n");
}

function insert_to_db($connect,$db_name, $xml)
{
    $start = microtime(true);
    $batchSize = 100;
    $sql = 'INSERT INTO ' . $db_name . ' (entity_id, category_name, sku, name, description, short_desc, price, link, image, brand, rating, caffeine_type, count, flavored, seasonal, instock, facebook, is_k_cup) VALUES ';
    $j = 0;
    try {
    foreach ($xml->item as $product) {
        $entityId = $product->entity_id;
        $categoryName = $product->CategoryName == "" ? "NULL" : "'" . addslashes($product->CategoryName) . "'";
        $sku = $product->sku == "" ? "NULL" : "'$product->sku'";
        $itemName = $product->name == "" ? "NULL" : "'" . addslashes($product->name) . "'";
        $description = $product->description == "" ? "NULL" : "'" . addslashes($product->description) . "'";
        $shortDescription = $product->shortdesc == "" ? "NULL" : "'" . addslashes($product->shortdesc) . "'";
        $price = $product->price == "" ? "NULL" : "'$product->price'";
        $link = $product->link == "" ? "NULL" : "'$product->link'";
        $image = $product->image == "" ? "NULL" : "'$product->image'";
        $brand = $product->Brand == "" ? "NULL" : "'" . addslashes($product->Brand) . "'";
        $rating = $product->Rating == "" ? "NULL" : "'$product->Rating'";
        $caffeineType = $product->CaffeineType == "" ? "NULL" : "'" . addslashes($product->CaffeineType) . "'";
        $count = $product->Count == 0 ? "NULL" : $product->Count;
        $flavored = $product->Flavored == "No" ? 0 : 1;
        $seasonal = $product->Seasonal == "No" ? 0 : 1;
        $inStock = $product->Instock == "No" ? 0 : 1;
        $facebook = $product->Facebook;
        $isKCup = $product->IsKCup;
        $j++;

        if ($j == $batchSize) {
            $sql = rtrim($sql, ',');

                $response = mysqli_query($connect, $sql);
                if ($response) {
                    die(1231231);
                }


            $j = 0;
            $sql = 'INSERT INTO ' . $db_name . ' (entity_id, category_name, sku, name, description, short_desc, price, link, image, brand, rating, caffeine_type, count, flavored, seasonal, instock, facebook, is_k_cup) VALUES ';
        }


        $sql .= "($entityId, $categoryName, $sku,
                $itemName, $description,  $shortDescription ,  $price
                ,  $link ,  $image,  $brand,  $rating ,
                $caffeineType , $count, $flavored , $seasonal
                , $inStock, $facebook, $isKCup),";
        $j++;
    }

    $sql = rtrim($sql, ',');
    mysqli_query($connect, $sql);

    } catch (mysqli_sql_exception $e) {
        logger(mysqli_error($connect));
        die("Data was not imported. Check log file for info.\n");
    }


    $time_elapsed_secs = microtime(true) - $start;
    print_r("Data was imported successfully\n");
    print_r("Execution time: " . $time_elapsed_secs . "\n");
    return true;

}


?>