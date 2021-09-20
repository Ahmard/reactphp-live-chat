<?php


function view_path(?string $viewPath): string
{
    global $slash;
    return root_path("resources{$slash}views{$slash}{$viewPath}");
}

function clientCounter(): int
{
    static $counter = 0;
    $counter++;
    return $counter;
}