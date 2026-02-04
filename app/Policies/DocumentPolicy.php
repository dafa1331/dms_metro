<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function view(User $user, Document $document): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

           // pastikan relasi ada
        if (! $document->pegawai || ! $document->pegawai->jabatanAktif) {
            return false;
        }

        $opdIds = \App\Models\Opd::find($user->opd_id)
            ?->getAllChildrenIds() ?? [];

        return in_array(
            $document->pegawai->jabatanAktif->opd_id,
            $opdIds
        );
    }
}
