<?php

// app/Models/Loan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Loan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'loan_number',
        'book_id',
        'reader_id',
        'loan_date',
        'due_date',
        'return_date',
        'status',
        'notes',
        'fine_amount',
        'fine_paid',
        'issued_by',
        'returned_by'
    ];

    protected $casts = [
        'loan_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'fine_amount' => 'decimal:2',
        'fine_paid' => 'boolean'
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function reader()
    {
        return $this->belongsTo(Reader::class);
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function returnedBy()
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    public function isOverdue()
    {
        return $this->status === 'active' && Carbon::now()->startOfDay()->gt($this->due_date);
    }

    public function calculateFine()
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        $daysOverdue = Carbon::now()->startOfDay()->diffInDays($this->due_date);
        return $daysOverdue * 10; // 10 рублей в день
    }

    public function markAsReturned()
    {
        $this->return_date = Carbon::now();
        $this->status = 'returned';

        if ($this->isOverdue()) {
            $this->fine_amount = $this->calculateFine();
        }

        $this->save();

        // Увеличиваем количество доступных книг
        $this->book->increment('quantity_available');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($loan) {
            $loan->loan_number = 'LN-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // Уменьшаем количество доступных книг
            if ($loan->book) {
                $loan->book->decrement('quantity_available');
            }
        });
    }
}
