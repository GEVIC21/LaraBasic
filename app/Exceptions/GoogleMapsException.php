<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class GoogleMapsException extends Exception
{
    public function report()
    {
        // Logique de rapport d'erreur personnalisée
        Log::error('Google Maps API Error: ' . $this->getMessage());
    }

    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $this->getMessage()
            ], 422);
        }

        return back()->with('error', $this->getMessage());
    }
}
