<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Models\MetadataTypes;
use App\Models\Metadatas;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        // GETTING & SHARING METADATAS TO ALL VIEWS
        $array = Metadatas::all();
        foreach ($array as $metadata) {
            $type = MetadataTypes::where('id', '=', $metadata->metadatatype)->first();
            $name = explode("_", $metadata->name);
            $name = implode(".", $name);
            \Config::set($type->code . "." . $name, $metadata->value);
        }
    }
}
