<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

// Consultancy Controllers
use App\Http\Controllers\StudentController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\VisaApplicationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\CounselorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentPortalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\ConsultancyProfileController;
use App\Http\Controllers\EmployeeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


// Public Route
Route::get('/', [HomeController::class, 'index'])->name('home');

// Admin/Editor Routes (Role: 1, 2)
Route::middleware(['auth', 'verified', 'role:1,2'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Redirect basic dashboard to consultancy dashboard
    Route::get('/dashboard', fn() => redirect()->route('consultancy.dashboard'))->name('dashboard');
});

Route::middleware(['auth', 'verified', 'role:1'])->group(function () {
    Route::resource('users', UserController::class);
});

// Admin University Management (separate prefix to avoid conflict with public routes)
Route::middleware(['auth', 'verified', 'role:1'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('universities', UniversityController::class);
});

// ==========================================
// CONSULTANCY MANAGEMENT SYSTEM ROUTES
// ==========================================

// Consultancy Admin Routes (Role: 1, 2)
Route::middleware(['auth', 'verified', 'role:1,2'])->prefix('consultancy')->name('consultancy.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Students Management
    Route::resource('students', StudentController::class);
    
    // Inquiries Management
    Route::resource('inquiries', InquiryController::class);
    Route::post('inquiries/{inquiry}/convert', [InquiryController::class, 'convertToStudent'])->name('inquiries.convert');
    
    // Documents Management
    Route::resource('documents', DocumentController::class)->except(['edit', 'update']);
    Route::post('documents/{document}/verify', [DocumentController::class, 'verify'])->name('documents.verify');
    Route::get('document-checklist', [DocumentController::class, 'checklist'])->name('documents.checklist');
    Route::post('document-checklist', [DocumentController::class, 'storeChecklist'])->name('documents.checklist.store');
    
    // Applications Management
    Route::resource('applications', ApplicationController::class);
    
    // Visa Applications Management
    Route::resource('visa', VisaApplicationController::class);
    
    // Payments Management
    Route::resource('payments', PaymentController::class);
    Route::post('payments/{payment}/record', [PaymentController::class, 'recordPayment'])->name('payments.record');
    
    // Tasks Management
    Route::resource('tasks', TaskController::class);
    Route::post('tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    
    // Communications
    Route::resource('communications', CommunicationController::class)->except(['edit', 'update']);
    Route::post('communications/{communication}/follow-up', [CommunicationController::class, 'completeFollowUp'])->name('communications.follow-up');
    
    // Counselors Management
    Route::resource('counselors', CounselorController::class);
    
    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/students', [ReportController::class, 'students'])->name('reports.students');
    Route::get('reports/applications', [ReportController::class, 'applications'])->name('reports.applications');
    Route::get('reports/financial', [ReportController::class, 'financial'])->name('reports.financial');
    Route::get('reports/visa', [ReportController::class, 'visa'])->name('reports.visa');
    
    // Consultancy Profile
    Route::resource('profile', ConsultancyProfileController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::post('profile/{profile}/remove-image', [ConsultancyProfileController::class, 'removeImage'])->name('profile.remove-image');
    
    // Employees Management
    Route::resource('employees', EmployeeController::class);
    Route::post('employees/{employee}/check-in', [EmployeeController::class, 'checkIn'])->name('employees.check-in');
    Route::post('employees/{employee}/check-out', [EmployeeController::class, 'checkOut'])->name('employees.check-out');
    Route::get('employees/{employee}/attendance', [EmployeeController::class, 'attendance'])->name('employees.attendance');
});

// Student Portal Routes (Role: 4 - Student)
Route::middleware(['auth', 'verified'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/dashboard', [StudentPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [StudentPortalController::class, 'profile'])->name('profile');
    Route::put('/profile', [StudentPortalController::class, 'updateProfile'])->name('profile.update');
    Route::get('/documents', [StudentPortalController::class, 'documents'])->name('documents');
    Route::post('/documents', [StudentPortalController::class, 'uploadDocument'])->name('documents.upload');
    Route::get('/applications', [StudentPortalController::class, 'applications'])->name('applications');
    Route::get('/payments', [StudentPortalController::class, 'payments'])->name('payments');
    Route::get('/messages', [StudentPortalController::class, 'messages'])->name('messages');
    Route::post('/messages', [StudentPortalController::class, 'sendMessage'])->name('messages.send');
});

// Public University Routes
Route::get('/universities', [UniversityController::class, 'publicIndex'])->name('universities.public');
Route::get('/universities/{university}', [UniversityController::class, 'show'])->name('universities.show');

Route::get('/lang/{locale}', function ($locale) {
    session()->put('locale', $locale);
    app()->setLocale($locale);
    return redirect()->back();
})->name('lang.switch');

// Theme switching - Admin only
Route::middleware(['auth', 'verified', 'role:1,2'])->group(function () {
    Route::post('/theme/switch', [HomeController::class, 'switchTheme'])->name('theme.switch');
});



// Auth routes
require __DIR__ . '/auth.php';