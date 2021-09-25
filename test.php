<?php

use Carbon\Carbon;

require 'vendor/autoload.php';

$today = Carbon::createFromTimestamp(time())->toString();

echo $today;