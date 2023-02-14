<?php
function load_library($class_name): bool
{
    if (empty ($class_name)) {
        return false;
    }

    $file_path = "";
    $file_path = APPLICATION_PATH .'/app/service/'.$class_name. '.php';

    if ($file_path && file_exists($file_path)) {
        require_once($file_path);
        return true;
    }
    return false;
}

spl_autoload_register('load_library');



