<?php

namespace App\Http\Controllers\Ppk;

use App\Http\Controllers\Wadir2\MakController as MakWadir2;

/** Permohonan MAK PPK (dashboard + detail): sama persis seperti Wadir 2, hanya awalan route dan layout yang berganti ke "ppk" */
class MakController extends MakWadir2
{
    protected string $prefixRute = 'ppk';
}
