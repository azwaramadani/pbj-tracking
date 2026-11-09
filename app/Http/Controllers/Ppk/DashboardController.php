<?php

namespace App\Http\Controllers\Ppk;

use App\Http\Controllers\Wadir2\DashboardController as DashboardWadir2;

/** Dashboard PPK: sama persis seperti Dashboard Wadir 2, hanya awalan route dan layout yang berganti ke "ppk" */
class DashboardController extends DashboardWadir2
{
    protected string $prefixRute = 'ppk';
}
