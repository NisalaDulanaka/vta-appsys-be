<?php

    try{
        $env = parse_ini_file('.env');

        foreach ($env as $key => $value) {
            putenv($key."=".$value);
        }

    }catch(ErrorException $e){
        printErrorMessage("Error: .env file not found. <br><br>
        Possible solutions:<br>
        <li> Create a .env file in the root folder.</li>
        <br>");
    }

    // @TODO: move this to a separate better suited file
    function include_all(string $folderName) {
        foreach (glob("$folderName/*.php") as $filename) { 
            include_once($filename); 
        } 
    }
?>
