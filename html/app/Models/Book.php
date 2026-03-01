<?php

// app/Models/Book.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'isbn',
        'title',
        'subtitle',
        'description',
        'publisher_id',
        'publication_year',
        'pages',
        'language',
        'cover_image',
        'format',
        'price',
        'quantity_total',
        'quantity_available',
        'quantity_lost',
        'quantity_damaged',
        'location',
        'shelf',
        'acquisition_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'pages' => 'integer',
        'price' => 'decimal:2',
        'quantity_total' => 'integer',
        'quantity_available' => 'integer',
        'quantity_lost' => 'integer',
        'quantity_damaged' => 'integer',
        'acquisition_date' => 'date'
    ];

    public function authors()
    {
        return $this->belongsToMany(Author::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function activeLoans()
    {
        return $this->hasMany(Loan::class)->whereIn('status', ['active', 'overdue']);
    }

    public function isAvailable()
    {
        return $this->quantity_available > 0;
    }

    public function getMainAuthorAttribute()
    {
        return $this->authors()->wherePivot('role', 'author')->first() ?? $this->authors()->first();
    }

    public function getAuthorsListAttribute()
    {
        return $this->authors->map(function ($author) {
            return $author->short_name;
        })->implode(', ');
    }

    public function getCoverUrlAttribute()
    {
        return $this->cover_image
            ? asset('storage/' . $this->cover_image)
            : asset('images/default-book-cover.jpg');
    }
}
