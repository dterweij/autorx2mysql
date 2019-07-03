<?php

// page load timer
function timer()
{
    static $start;

    if (is_null($start))
    {
        $start = microtime(true);
    }
    else
    {
        $diff = round((microtime(true) - $start), 4);
        $start = null;
        return $diff;
    }
}

// Start the timer
timer();
session_start();
ob_start();
include("class-database.php");
include("db_data.php");
include("display.php");
