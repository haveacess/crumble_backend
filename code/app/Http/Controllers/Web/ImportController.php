<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use function view;

class ImportController extends Controller
{
    public function steamAccount(string $accountAlias): View
    {
        return view('import/steam', [
            'action' => "/backend/api/import/steam/$accountAlias",
            'accountName' => $accountAlias
        ]);
    }
}
