<?php

namespace Illuminate\Filesystem;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\PathTraversalDetected;

class ReceiveFile
{
    /**
     * Create a new invokable controller to receive files.
     */
    public function __construct(
        protected string $disk,
        protected array $config,
        protected bool $isProduction,
    ) {
        //
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $path): Response
    {
        abort_unless(
            $this->hasValidSignature($request),
            $this->isProduction ? 404 : 403
        );

        try {
            Storage::disk($this->disk)->put($path, $request->getContent());

            return response()->noContent();
        } catch (PathTraversalDetected $e) {
            abort(404);
        }
    }

    /**
     * Determine if the request has a valid signature if applicable.
     */
    protected function hasValidSignature(Request $request): bool
    {
        return $request->boolean('upload') && $request->hasValidRelativeSignature();
    }
}
