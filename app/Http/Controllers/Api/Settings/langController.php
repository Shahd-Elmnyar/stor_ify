<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\AppController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class langController extends AppController
{
    public function changeLang(Request $request): JsonResponse
    {
        try {
            $this->validateLanguageChange($request);
            $this->updateUserLanguage($request->lang);

            return $this->successResponse();
        } catch (ValidationException $e) {
            Log::error('Error during language change: ' . $e->getMessage());
            return $this->validationErrorResponse($e->getMessage());
        } catch (\Exception $e) {
            Log::error('General error: ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }

    private function validateLanguageChange(Request $request): void
    {
        $request->validate([
            'lang' => 'required|in:en,ar',
        ]);
    }

    private function updateUserLanguage(string $lang): void
    {
        $this->user->lang = $lang;
        $this->user->save();
    }
}
