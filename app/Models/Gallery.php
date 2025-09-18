<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
  public function megabytesToHumanReadable($megabytes) {
    $units = ['MB', 'GB', 'TB', 'PB', 'EB'];
    $power = $megabytes > 0 ? floor(log($megabytes, 1024)) : 0;
    $power = min($power, count($units) - 1);

    return number_format($megabytes / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
  }
}
