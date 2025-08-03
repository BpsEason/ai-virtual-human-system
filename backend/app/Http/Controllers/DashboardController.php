<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Character;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats()
    {
        $stats = [
            'total_users' => User::count(),
            'total_characters' => Character::count(),
            'total_documents' => Document::count(),
        ];

        return ApiResponse::success($stats);
    }
}
