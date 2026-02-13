<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id', 'student_id', 'university_id', 'counselor_id',
        'intake', 'course_name', 'course_type', 'course_duration', 'status',
        'application_date', 'submission_deadline', 'submitted_at',
        'interview_date', 'interview_mode', 'interview_notes',
        'result_date', 'enrollment_deadline',
        'acceptance_letter_path', 'rejection_reason',
        'coe_status', 'coe_applied_date', 'coe_received_date', 'coe_document_path',
        'application_fee', 'tuition_fee', 'application_fee_paid', 'notes'
    ];

    protected $casts = [
        'application_date' => 'date',
        'submission_deadline' => 'date',
        'submitted_at' => 'date',
        'interview_date' => 'datetime',
        'result_date' => 'date',
        'enrollment_deadline' => 'date',
        'coe_applied_date' => 'date',
        'coe_received_date' => 'date',
        'application_fee' => 'decimal:2',
        'tuition_fee' => 'decimal:2',
        'application_fee_paid' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($application) {
            if (empty($application->application_id)) {
                $year = date('Y');
                $lastApp = self::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
                $nextNumber = $lastApp ? (int)substr($lastApp->application_id, -4) + 1 : 1;
                $application->application_id = 'APP-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }
        });

        static::updating(function ($application) {
            // Auto-calculate status before saving, but skip if status was explicitly set
            // Only auto-update if status is being checked, not manually overridden
            if (!isset($application->attributes['status']) || $application->isDirty('status')) {
                // Status was directly set, keep it
            } else {
                // Auto-calculate based on application data
                $application->status = $application->calculateStatus();
            }
        });

        static::saved(function ($application) {
            // After save, update status if needed based on related changes
            // This handles cases where related models (documents, interview dates, etc.) change
        });
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function counselor()
    {
        return $this->belongsTo(Counselor::class);
    }

    public function visaApplication()
    {
        return $this->hasOne(VisaApplication::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get required documents for this application based on university country
     */
    public function getRequiredDocuments()
    {
        if (!$this->university) {
            return collect();
        }
        
        return DocumentChecklist::forCountry($this->university->country)->get();
    }

    /**
     * Get documents submitted by the student
     */
    public function getStudentDocuments()
    {
        if (!$this->student) {
            return collect();
        }
        
        return $this->student->documents()->get();
    }

    /**
     * Count required documents for this application
     */
    public function getRequiredDocumentsCount()
    {
        return $this->getRequiredDocuments()->count();
    }

    /**
     * Count documents submitted by student
     */
    public function getSubmittedDocumentsCount()
    {
        $required = $this->getRequiredDocuments();
        $submitted = $this->getStudentDocuments();
        
        if ($required->isEmpty()) {
            return 0;
        }
        
        $submittedCount = 0;
        foreach ($required as $requiredDoc) {
            $hasDocument = $submitted->whereIn('document_type', [$requiredDoc->document_type])->isNotEmpty();
            if ($hasDocument) {
                $submittedCount++;
            }
        }
        
        return $submittedCount;
    }

    /**
     * Calculate what the status should be based on application data
     */
    public function calculateStatus()
    {
        // Priority 1: If COE is received (approved), status should be accepted
        if ($this->coe_status === 'approved') {
            return 'accepted';
        }

        // Priority 2: If Interview is scheduled and has a date
        if ($this->interview_date) {
            return 'interview_scheduled';
        }

        // Priority 3: Check document status
        $requiredCount = $this->getRequiredDocumentsCount();
        $submittedCount = $this->getSubmittedDocumentsCount();

        if ($requiredCount > 0) {
            if ($submittedCount >= $requiredCount) {
                // All required documents submitted
                return 'documents_ready';
            } elseif ($submittedCount > 0) {
                // Some documents submitted
                return 'documents_preparing';
            }
        }

        // Default to draft if no documents or other criteria met
        return 'draft';
    }

    /**
     * Auto-update application status based on current data
     */
    public function updateStatusAutomatically()
    {
        $newStatus = $this->calculateStatus();
        if ($this->status !== $newStatus) {
            $this->update(['status' => $newStatus]);
            return true;
        }
        return false;
    }

    /**
     * Check if application is eligible for visa application (after accepted)
     */
    public function isVisaEligible()
    {
        return $this->status === 'accepted' && $this->coe_status === 'approved';
    }
}
