<?php

// app/Models/Reader.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reader extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'readers';

    protected $fillable = [
        'library_card_number',
        'first_name',
        'last_name',
        'middle_name',
        'birth_date',
        'class',
        'phone',
        'email',
        'address',
        'photo',
        'registration_date',
        'expiry_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'registration_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function activeLoans()
    {
        return $this->hasMany(Loan::class)->whereIn('status', ['active', 'overdue']);
    }

    public function getFullNameAttribute()
    {
        return trim($this->last_name . ' ' . $this->first_name . ' ' . $this->middle_name);
    }

    public function getClassLabelAttribute()
    {
        $classes = [
            '1' => '1 класс',
            '2' => '2 класс',
            '3' => '3 класс',
            '4' => '4 класс',
            '5' => '5 класс',
            '6' => '6 класс',
            '7' => '7 класс',
            '8' => '8 класс',
            '9' => '9 класс',
            '10' => '10 класс',
            '11' => '11 класс',
            'teacher' => 'Учитель',
            'staff' => 'Сотрудник'
        ];

        return $classes[$this->class] ?? $this->class;
    }

    public function canBorrow()
    {
        return $this->status === 'active' &&
            $this->expiry_date->isFuture() &&
            $this->activeLoans()->count() < 5; // Максимум 5 книг
    }

    public function hasOverdueBooks()
    {
        return $this->loans()
            ->where('status', 'overdue')
            ->exists();
    }
}
