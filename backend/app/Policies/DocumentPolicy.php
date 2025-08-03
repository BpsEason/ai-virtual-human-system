<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DocumentPolicy
{
    public function delete(User $user, Document $document): bool
    {
        return $user->isAdmin() || $user->id === $document->user_id;
    }
}
