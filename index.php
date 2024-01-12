<?php
include "db.php";


$arr = process_argv($argv);

$file_name = $arr["file_name"];
$tb_name = $arr["table_name"];
// Specify the path to your XML file
$xmlFilePath = './' . $file_name . '.xml';

// Check if the file exists
if (file_exists($xmlFilePath)) {
    // Load the XML file
    $xml = simplexml_load_file($xmlFilePath);

    // Check if the XML file is valid
    if ($xml) {
        // Print the XML content
        if (!check_if_table_exists($connect, "products")){
            create_table($connect);
        }
        insert_to_db($connect,$tb_name, $xml);
    } else {
        // Handle the case where the XML file is not valid
        echo "Was not succesfull. Check log file for error.\n";
        logger('Failed to load XML file.');
    }
} else {
    // Handle the case where the file does not exist
    echo "Was not successfull. Check log file for error. \n";
    logger("File not found.");
}

function process_argv($params){

    $arr = array();
    foreach($params as $param){
        if (str_contains($param, "file=")){
            $arr["file_name"] = explode("=", $param)[1];
        }
        elseif (str_contains($param, "table=")){
            $arr["table_name"] = explode("=", $param)[1];
        }
    }

    if (count($arr) != 2){
        logger("Not enough parameter. Please enter sufficient parameters.");
        die("Not enough parameter.\n");
    }
    return $arr;
}

?>
