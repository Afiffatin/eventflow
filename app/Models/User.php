<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'phone'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Role Helpers ─────────────────────────────────────

    public function isAdmin(): bool     { return $this->role === 'admin'; }
    public function isOrganizer(): bool  { return $this->role === 'organizer'; }
    public function isCustomer(): bool   { return $this->role === 'customer'; }

    public function hasAdminAccess(): bool
    {
        return in_array($this->role, ['admin', 'organizer']);
    }

    // ── Relationships ────────────────────────────────────

    public function events()
    {
        return $this->hasMany(Event::class, 'organizer_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
// PRK42-22: Model relationships defined
