<?php



use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DMO\DmoDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PmjayController;
use App\Http\Controllers\Admin\DmoDistrictController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\DMO\AuditController;
use App\Http\Controllers\DMO\VisionController;
use App\Http\Controllers\DMO\LiveAuditController;

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminTelephonicAuditController;
use App\Http\Controllers\Admin\AdminFieldVisitController;
use App\Http\Controllers\Admin\AdminLiveAuditController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login
Route::get('/', fn () => redirect()->route('auth.login'));
Route::get('login', [AuthController::class, 'showLogin'])->name('login');

// ─── Authentication Routes (guests only) ─────────────────────────────────────

Route::middleware('guest')->prefix('auth')->name('auth.')->group(function () {

    Route::get('login', [AuthController::class, 'showLogin'])->name('login');

    // Step 2: Verify OTP
    Route::post('/send-otp', [AuthController::class, 'sendOtp'])->name('send.otp');
    Route::get('/verify', [AuthController::class, 'showVerifyForm'])
    ->name('verify.form');
    Route::post('verify', [AuthController::class, 'verifyOtp'])->name('otp.verify');
    Route::post('resend', [AuthController::class, 'resendOtp'])->name('otp.resend');
});

// ─── Logout ───────────────────────────────────────────────────────────────────


Route::post('logout', [AuthController::class, 'logout'])
     ->middleware('auth')
     ->name('auth.logout');
// ─── Admin Routes ─────────────────────────────────────────────────────────────

Route::middleware(['auth', 'role:admin'])
     ->prefix('admin')
     ->name('admin.')
     ->group(function () {
         Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

          Route::get('/pmjay', [PmjayController::class,'index'])
          ->name('pmjay.index');

          Route::get('/pmjay/upload', [PmjayController::class,'uploadForm'])
               ->name('pmjay.upload');

          Route::post('/pmjay/import', [PmjayController::class,'import'])
               ->name('pmjay.import');

          Route::get('/dmo/{user}/districts',
               [DmoDistrictController::class,'assignForm']
          )->name('dmo.districts');

          Route::post('/dmo/{user}/districts',
               [DmoDistrictController::class,'assignDistricts']
          )->name('dmo.districts.store');


          Route::get('/generate-audits', [AdminController::class, 'generateAuditsPage']);
          Route::post('/generate-audits', [AdminController::class, 'generateAudits']);

          Route::prefix('users')
                    ->name('users.')
                    ->group(function () {
                         Route::get('/', [AuditController::class, 'telephonicAudits'])->name('index');
               });

          Route::prefix('dmos')
                    ->name('dmos.')
                    ->group(function () {
                         Route::get('/', [AuditController::class, 'telephonicAudits'])->name('index');
               });

          Route::prefix('hospitals')
                    ->name('hospitals.')
                    ->group(function () {
                         Route::get('/', [AuditController::class, 'telephonicAudits'])->name('index');
               });

          Route::prefix('districts')
                    ->name('districts.')
                    ->group(function () {
                         Route::get('/', [AuditController::class, 'telephonicAudits'])->name('index');
               });

          Route::prefix('audits')
          ->name('audits.')
          ->group(function () {
               Route::prefix('telephonic')
                    ->name('telephonic.')
                    ->group(function () {
                         Route::get('/', [AdminTelephonicAuditController::class, 'index'])->name('index');
                         Route::get('/{id}',    [AdminTelephonicAuditController::class, 'show']) ->name('show');
               });

               Route::prefix('field')
                    ->name('field.')
                    ->group(function () {
                         Route::get('/', [AdminFieldVisitController::class, 'index'])->name('index');
                         Route::get('/{id}',    [AdminFieldVisitController::class, 'show']) ->name('show');
               });

               Route::prefix('live')
                    ->name('live.')
                    ->group(function () {
                         Route::get('/', [AdminLiveAuditController::class, 'index'])->name('index');
                         Route::get('/{id}',    [AdminLiveAuditController::class, 'show']) ->name('show');
               });
          });
     });



// ─── DMO Routes ───────────────────────────────────────────────────────────────

Route::middleware(['auth', 'role:dmo'])
     ->prefix('dmo')
     ->name('dmo.')
     ->group(function () {
          Route::get('dashboard', [DmoDashboardController::class, 'index'])->name('dashboard');
          Route::prefix('audits')
          ->name('audits.')
          ->group(function () {
               Route::post('/validate-photo', [VisionController::class, 'validatePhoto'])
                    ->name('validate.photo');

               Route::post('/validate-bed-photo', [VisionController::class, 'validateBedPhoto'])
                    ->name('validate.bed.photo');

               Route::prefix('telephonic')
               ->name('telephonic.')
               ->group(function () {
                    Route::get('/', [AuditController::class, 'telephonicAudits'])->name('all');
                    Route::get('/view/{id}', [AuditController::class, 'telephonicAuditForm'])->name('view');
                    Route::post('/view/{id}', [AuditController::class, 'storeTelephonicObservation'])->name('store');
               });

               Route::prefix('field')
               ->name('field.')
               ->group(function () {
                    Route::get('/', [AuditController::class, 'fieldAudits'])->name('all');
                    Route::get('/view/{id}', [AuditController::class, 'fieldAuditForm'])->name('view');
                    Route::post('/view/{id}', [AuditController::class, 'storeFieldVisit'])->name('store');
               });


               Route::prefix('live-audit')
               ->name('live-audit.')
               ->group(function () {
                    Route::get( '/=',    [LiveAuditController::class, 'viewAll'])->name('all');
                    Route::get( '/create',    [LiveAuditController::class, 'create'])->name('create');
                    Route::post('/store',           [LiveAuditController::class, 'store']) ->name('store');
                    Route::get( '/live-audit/{id}',      [LiveAuditController::class, 'show'])  ->name('show');
               });

          });
          
     });