<?php

namespace App\Http\Controllers;

use App\Http\Requests\CharacterRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Character;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CharacterController extends Controller
{
    public function index()
    {
        $characters = Character::all();
        return ApiResponse::success($characters);
    }

    public function store(CharacterRequest $request)
    {
        Gate::authorize('create', Character::class);
        $character = Character::create($request->validated());
        return ApiResponse::success($character, 'Character created successfully', 201);
    }

    public function show(Character $character)
    {
        return ApiResponse::success($character);
    }

    public function update(CharacterRequest $request, Character $character)
    {
        Gate::authorize('update', $character);
        $character->update($request->validated());
        return ApiResponse::success($character, 'Character updated successfully');
    }

    public function destroy(Character $character)
    {
        Gate::authorize('delete', $character);
        $character->delete();
        return ApiResponse::success(null, 'Character deleted successfully');
    }
}
