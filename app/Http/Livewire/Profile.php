<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    public User $user;
    public $upload;

    public $rules = [
        'user.username' => 'max:24',
        'user.about' => 'max:124',
        'user.birthday' => 'sometimes',
        'upload' => 'nullable|image:max:1000'
    ];

    public function mount()
    {
        $this->user = auth()->user();
    }

    public function updated($field)
    {
        if ($field !== 'saved') {
            $this->saved = false;
        }
    }

    public function save()
    {
        $this->validate();

        $this->user->save();

        $this->upload && $this->user->update([
            'avatar' => $this->upload->store('/', 'avatars')
        ]);

        $this->emitSelf('notify-saved');
    }

    public function render()
    {
        return view('livewire.profile');
    }
}
