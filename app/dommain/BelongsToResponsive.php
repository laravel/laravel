<?php
namespace App\Dommain;

use Psr\Http\Message\ResponseInterface;

interface BelongsToResponse {
    /** @return ResponseInterface */
    public function getResponse();
}
