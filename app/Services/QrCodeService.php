<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;

class QrCodeService
{
    public function generateQrCode($data)
    {
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($data)
            ->size(300)
            ->margin(10)
            ->build();

        $fileName = 'qr_codes/' . md5($data) . '.png';
        Storage::put('public/' . $fileName, $result->getString());

        return 'storage/' . $fileName;
    }
}
