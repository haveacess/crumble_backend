<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\DeniedOperationException;
use App\Exceptions\InvalidCookieException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Steam\AddSteamAccountRequest;
use App\Http\Responses\ApiResponse;
use App\Services\Steam\ImportService\ImportService;

class ImportController extends Controller
{
    public function addSteamAccount(string $profileAlias, AddSteamAccountRequest $request): ApiResponse
    {
        try {
            $cookieContent = $request->hasFile('cookie_file')
                ? $request->file('cookie_file')->get()
                : $request->input('cookie');

            $service = new ImportService($profileAlias);

            $allowReplace = $request->input('allow_rewrite', 0);
            $isCreatedFromScratch = $service->addOrReplaceCookie($cookieContent, $allowReplace);

            $apiResponse = new ApiResponse($isCreatedFromScratch ? 201 : 200);
            return $apiResponse->withMessage("Account imported! Great job. Get back into console");
        } catch (InvalidCookieException $e) {
            $apiResponse = new ApiResponse(400, false);
            return $apiResponse->withMessage("Import cookie failed, reason: {$e->getReason()}");
        } catch (DeniedOperationException $e) {
            $apiResponse = new ApiResponse($e->getStatusCode(), false);

            $errMsg = 'Cookie container for this account is not empty. After import, all cookies will be overwritten.';
            $solveArgs = ['allow_rewrite', 'rewrite cookie_container'];
            return $apiResponse->withMessage("{$errMsg} {$e->getSolve(...$solveArgs)}");
        }

    }
}
