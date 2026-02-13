<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'document_type', 'title', 'file_path', 'file_name',
        'file_type', 'file_size', 'status', 'rejection_reason', 'expiry_date',
        'verified_by', 'verified_at', 'notes'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'verified_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        // When a document is created or updated, update all related applications' status
        static::saved(function ($document) {
            if ($document->student) {
                $document->student->applications()->each(function ($application) {
                    $application->updateStatusAutomatically();
                });
            }
        });
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
