<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Document;

class LandingPage extends Component
{

    public $total;
    public $approved;
    public $pending;
    public $rejected;

    public function mount()
    {
        $this->total = Document::count();
        $this->approved = Document::where('status_dokumen', 'terima')->count();
        $this->pending = Document::where('status_dokumen', 'proses')->count();
        $this->rejected = Document::where('status_dokumen', 'tolak')->count();
    }

    public function render()
    {
        return view('livewire.landing-page')
        ->layout('layouts.landing');
    }
}
