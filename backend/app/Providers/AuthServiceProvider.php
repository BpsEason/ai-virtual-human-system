<?php

namespace App\Providers;

use App\Models\Character;
use App\Models\Conversation;
use App\Models\Document;
use App\Models\User;
use App\Policies\CharacterPolicy;
use App\Policies\ConversationPolicy;
use App\Policies\DocumentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Character::class => CharacterPolicy::class,
        Document::class => DocumentPolicy::class,
        Conversation::class => ConversationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('view-admin-dashboard', function (User $user) {
            return $user->isAdmin();
        });
    }
}
