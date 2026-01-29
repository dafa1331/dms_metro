<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\LoginResponse as BaseLoginResponse;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse extends BaseLoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = auth()->user();

        if ($user->hasRole('kasubbag_kepegawaian')) {
            return redirect()->to('/kasubbag');
        }

        if ($user->hasRole('admin')) {
            return redirect()->to('/admin');
        }

        abort(403);
    }
}

