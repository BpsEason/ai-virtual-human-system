<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Character;
use App\Models\Conversation;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function stats()
    {
        Gate::authorize('view-admin-dashboard');

        $stats = [
            'total_users' => User::count(),
            'total_characters' => Character::count(),
            'total_documents' => Document::count(),
            'total_conversations' => Conversation::count(),
        ];

        return ApiResponse::success($stats);
    }
}
