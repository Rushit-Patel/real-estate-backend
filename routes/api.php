<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PropertyTypeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\WhatsAppTemplateController;
use App\Http\Controllers\TriggerController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserActivityController;
use App\Http\Controllers\UserLoginController;
use App\Http\Controllers\FailedLoginAttemptController;
use App\Http\Controllers\EnquirySourceController;
use App\Http\Controllers\EnquiryStatusController;
use App\Http\Controllers\VariableTypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\BulkOperationController;
use App\Http\Controllers\PublicProjectController;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::prefix('public')->group(function () {
    Route::get('projects/{id}', [PublicProjectController::class, 'show']);
    Route::post('enquiries', [PublicProjectController::class, 'submitEnquiry']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('profile', [ProfileController::class, 'getProfile']);
    Route::put('profile', [ProfileController::class, 'updateProfile']);
    Route::post('profile/change-password', [ProfileController::class, 'changePassword']);

    Route::get('notifications', [NotificationController::class, 'index'])->middleware('permission:view notifications');
    Route::put('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->middleware('permission:manage notifications');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->middleware('permission:manage notifications');
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy'])->middleware('permission:manage notifications');
    Route::post('fcm/store-token', [NotificationController::class, 'fcmTokens'])->middleware('permission:manage notifications');

    // Client Routes with Permission Middleware
    Route::get('clients', [ClientController::class, 'index'])->middleware('permission:view clients');
    Route::post('clients', [ClientController::class, 'store'])->middleware('permission:create clients');
    Route::get('clients/{client}', [ClientController::class, 'show'])->middleware('permission:view clients');
    Route::put('clients/{client}', [ClientController::class, 'update'])->middleware('permission:update clients');
    Route::delete('clients/{client}', [ClientController::class, 'destroy'])->middleware('permission:delete clients');

    Route::apiResource('projects', ProjectController::class);
    Route::post('projects/{project}/upload-file', [ProjectController::class, 'uploadFile']);

    Route::apiResource('enquiries', EnquiryController::class);
    Route::post('enquiries/{enquiry}/assign', [EnquiryController::class, 'assign']);
    Route::post('enquiries/{enquiry}/properties', [EnquiryController::class, 'attachProperties']);
    Route::post('enquiries/{enquiry}/junk', [EnquiryController::class, 'junk']);
    Route::post('enquiries/{enquiry}/close', [EnquiryController::class, 'close']);
    Route::put('enquiries/{enquiry}/followup', [EnquiryController::class, 'followup']);
    Route::put('enquiries/{enquiry}/reminder', [EnquiryController::class, 'reminder']);
    Route::put('reminders', [EnquiryController::class, 'reminders']);

    Route::put('enquiries/{enquiry}/reminders/{reminder}/complete', [EnquiryController::class, 'complete']);


    Route::get('test', [EnquiryController::class, 'TestFunction']);


    Route::apiResource('bookings', BookingController::class);
    Route::post('bookings/{booking}/cancel', [BookingController::class, 'cancel']);
    Route::post('bookings/{booking}/confirm', [BookingController::class, 'confirm']);

    Route::apiResource('email-templates', EmailTemplateController::class);
    
    Route::apiResource('whatsapp-templates', WhatsAppTemplateController::class);
    Route::get('whatsapp-templates/{whatsappTemplate}/variables', [WhatsAppTemplateController::class, 'getVariables']);
    Route::put('whatsapp-templates/{whatsappTemplate}/variables', [WhatsAppTemplateController::class, 'updateVariables']);
    Route::get('whatsapp-templates-synce', [WhatsAppTemplateController::class, 'TempleteDataSynce']);


    Route::apiResource('triggers', TriggerController::class);
    
    Route::get('activity_log', [UserActivityController::class, 'index']);
    Route::get('user_logins', [UserLoginController::class, 'index']);
    Route::get('failed_logins', [FailedLoginAttemptController::class, 'index']);

    Route::prefix('settings')->group(function () {
        Route::apiResource('variable-types', VariableTypeController::class);
        Route::apiResource('users', UserController::class);
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('permissions', PermissionController::class);
        Route::apiResource('property-types', PropertyTypeController::class);
        Route::apiResource('enquiry-sources', EnquirySourceController::class);
        Route::apiResource('enquiry-statuses', EnquiryStatusController::class);
    });

    Route::prefix('bulk')->group(function () {
        Route::post('whatsapp', [BulkOperationController::class, 'sendBulkWhatsApp']);
        Route::post('email', [BulkOperationController::class, 'sendBulkEmail']);
    });
});
