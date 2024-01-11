<?php

$file_name = $argv[1];
$tb_name = $argv[2];
// Specify the path to your XML file
$xmlFilePath = './' . $file_name . '.xml';

// Check if the file exists
if (file_exists($xmlFilePath)) {
    // Load the XML file
    $xml = simplexml_load_file($xmlFilePath);

    // Check if the XML file is valid
    if ($xml) {
        // Print the XML content

        insert_to_db($tb_name, $xml);
    } else {
        // Handle the case where the XML file is not valid
        echo "Was not successfull. Check log file for error.";
        logger('Failed to load XML file.');
    }
} else {
    // Handle the case where the file does not exist
    echo "Was not successfull. Check log file for error.";
    logger("File not found.");
}

function insert_to_db($db_name, $xml)
{
    $start = microtime(true);
    $connect = mysqli_connect(
        'db', # service name
        'php_docke', # username
        'password', # password
        'php_docker' # db table,
    );
    $batchSize = 100;
    $sql = 'INSERT INTO ' . $db_name . ' (entity_id, category_name, sku, name, description, short_desc, price, link, image, brand, rating, caffeine_type, count, flavored, seasonal, instock, facebook, is_k_cup) VALUES ';
    $j = 0;

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
            try {
                $response = mysqli_query($connect, $sql);
                if ($response) {
                    die(1231231);
                }

            } catch (Exception $e) {
                logger(mysqli_error($connect));
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
    try {
        $response = mysqli_query($connect, $sql);
    } catch (Exception $e) {
        logger(mysqli_error($connect));
    }


    $time_elapsed_secs = microtime(true) - $start;

    print_r("Execution time: " . $time_elapsed_secs);
    return true;

}

function logger($log)
{
    if (!file_exists("log.txt")) {
        file_put_contents("log.txt", '');
    }
    $time = date("m/d/y h:iA", time());

    $contents = file_get_contents("log.txt");
    $contents .= "$time\t$log\r";

    file_put_contents('log.txt', $contents);
}

?>
