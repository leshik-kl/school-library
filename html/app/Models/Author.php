<?php
// app/Models/Author.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Author extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'biography',
        'birth_date',
        'death_date',
        'photo'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'death_date' => 'date',
    ];

    public function books()
    {
        return $this->belongsToMany(Book::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function getFullNameAttribute()
    {
        return trim($this->last_name . ' ' . $this->first_name . ' ' . $this->middle_name);
    }

    public function getShortNameAttribute()
    {
        if ($this->middle_name) {
            return $this->last_name . ' ' . mb_substr($this->first_name, 0, 1) . '.' . mb_substr($this->middle_name, 0, 1) . '.';
        }
        return $this->last_name . ' ' . mb_substr($this->first_name, 0, 1) . '.';
    }
}
