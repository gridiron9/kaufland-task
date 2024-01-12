<?php

function logger($log)
{
    if (!file_exists("log.txt")) {
        file_put_contents("log.txt", '');
    }
    $time = date("m/d/y h:iA", time());
    date_default_timezone_set('Europe/Berlin');
    $contents = file_get_contents("log.txt");
    $contents .= "$time\t$log\r";

    file_put_contents('log.txt', $contents);
}

?>

