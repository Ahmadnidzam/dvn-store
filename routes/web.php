<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\DeveloperController as AdminDeveloper;
use App\Http\Controllers\Admin\ForumController as AdminForum;
use App\Http\Controllers\Admin\PlatformController as AdminPlatform;
use App\Http\Controllers\Admin\TransactionController as AdminTransaction;
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Developer\DashboardController as DevDashboard;
use App\Http\Controllers\Developer\PlatformController as DevPlatform;
use App\Http\Controllers\Developer\WalletController as DevWallet;
use App\Http\Controllers\Developer\WithdrawController as DevWithdraw;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\Payment\MidtransCallbackController;
use App\Http\Controllers\Payment\MidtransPayoutCallbackController;
use App\Http\Controllers\User\HomeController as UserHome;
use App\Http\Controllers\User\LibraryController as UserLibrary;
use App\Http\Controllers\User\PurchaseController as UserPurchase;
use App\Http\Controllers\User\ReviewController as UserReview;
use Illuminate\Support\Facades\Route;

// ============ PUBLIC: AUTH ============
Route::get('/login',  [AuthController::class, 'login']);
Route::post('/login', [AuthController::class, 'validatelogin'])->name('login');

Route::get('/register',           fn () => view('Auth.RegisterChoice'))->name('register.choice');
Route::get('/register/user',      [AuthController::class, 'registerUser'])->name('register.user');
Route::post('/register/user',     [AuthController::class, 'storeUser']);
Route::get('/register/developer', [AuthController::class, 'registerDeveloper'])->name('register.developer');
Route::post('/register/developer',[AuthController::class, 'storeDeveloper']);

Route::get('/forgetpass',         [AuthController::class, 'forget']);
Route::post('/forgetpass',        [AuthController::class, 'validateforget'])->name('forget');
Route::get('/forgetpassword',     [AuthController::class, 'forgetpassword']);
Route::post('/forgetpassword',    [AuthController::class, 'validateforgetpassword'])->name('forgetpassword');
Route::post('/logout',            [AuthController::class, 'logout'])->name('logout');

Route::get('/forbidden403', [AuthController::class, 'forbidden403']);
Route::get('/forbidden404', [AuthController::class, 'forbidden404']);

// ============ PAYMENT CALLBACK (Midtrans webhook) — public, no auth ============
Route::post('/payment/midtrans/notify',         [MidtransCallbackController::class, 'notify']);
Route::post('/payment/iris/notify',             [MidtransPayoutCallbackController::class, 'notify']);
Route::get('/payment/finish',                   [UserPurchase::class, 'finish'])->name('payment.finish');

// ============ USER (customer) ============
Route::middleware(['role:user,developer,admin'])->group(function () {
    // Dashboard publik (semua role bisa lihat catalog)
    Route::get('/dashboard',           [UserHome::class, 'utama']);
    Route::get('/dashboard/game',      [UserHome::class, 'games']);
    Route::get('/dashboard/app',       [UserHome::class, 'apps']);
    Route::get('/top/game',            [UserHome::class, 'topgame']);
    Route::get('/top/app',             [UserHome::class, 'topapp']);
    Route::get('/all/game',            [UserHome::class, 'allgame']);
    Route::get('/all/app',             [UserHome::class, 'allapp']);
    Route::get('/lable/{id}',          [UserHome::class, 'lable'])->name('lable');
    Route::get('/search',              [UserHome::class, 'search'])->name('search');

    Route::get('/profile',             [UserHome::class, 'profile'])->name('profile');
    Route::get('/editprofile',         [UserHome::class, 'editprofile'])->name('profile.edit');
    Route::put('/updateprofile',       [UserHome::class, 'updateprofile'])->name('profile.update');

    // Forum global (semua role bisa baca; user & dev bisa post)
    Route::get('/forum',               [ForumController::class, 'index'])->name('forum.index');
});

// User-only actions (download/buy/review)
Route::middleware(['role:user'])->group(function () {
    Route::post('/lable/{id}/download',  [UserPurchase::class, 'freeDownload'])->name('process.download');
    Route::get('/lable/{id}/buy',        [UserPurchase::class, 'buy'])->name('process.purchase');
    Route::post('/lable/{id}/review',    [UserReview::class, 'store'])->name('process.review');
    Route::post('/review/{id}/helpful',  [UserReview::class, 'toggleHelpful'])->name('review.helpful');

    Route::get('/unduhan',                [UserLibrary::class, 'index'])->name('unduhan.index');
    Route::delete('/unduhan/{id}',        [UserLibrary::class, 'destroy'])->name('unduhan.destroy');
    Route::get('/unduhan/{id}/file',      [UserLibrary::class, 'file'])->name('unduhan.file');
});

// Forum posting (user & developer)
Route::middleware(['role:user,developer'])->group(function () {
    Route::post('/forum',                [ForumController::class, 'store'])->name('forum.store');
    Route::post('/forum/{id}/helpful',   [ForumController::class, 'toggleHelpful'])->name('forum.helpful');
});

// ============ DEVELOPER ============
Route::prefix('developer')->middleware(['role:developer'])->name('developer.')->group(function () {
    Route::get('/',                                  [DevDashboard::class, 'index'])->name('dashboard');

    Route::get('/platforms',                         [DevPlatform::class, 'index'])->name('platforms.index');
    Route::get('/platforms/create',                  [DevPlatform::class, 'create'])->name('platforms.create');
    Route::post('/platforms',                        [DevPlatform::class, 'store'])->name('platforms.store');
    Route::get('/platforms/{id}/upload-fee',         [DevPlatform::class, 'uploadFeePage'])->name('platforms.upload-fee');
    Route::get('/platforms/{id}/edit',               [DevPlatform::class, 'edit'])->name('platforms.edit');
    Route::put('/platforms/{id}',                    [DevPlatform::class, 'update'])->name('platforms.update');
    Route::delete('/platforms/{id}',                 [DevPlatform::class, 'destroy'])->name('platforms.destroy');

    // Update file installer (safe replace via scan)
    Route::get('/platforms/{id}/update-file',        [DevPlatform::class, 'updateFileForm'])->name('platforms.update-file');
    Route::post('/platforms/{id}/update-file',       [DevPlatform::class, 'updateFileStore'])->name('platforms.update-file.store');
    Route::delete('/platforms/{id}/pending-update',  [DevPlatform::class, 'cancelPendingUpdate'])->name('platforms.cancel-pending');

    Route::get('/wallet',                            [DevWallet::class, 'index'])->name('wallet.index');

    Route::get('/withdraws',                         [DevWithdraw::class, 'index'])->name('withdraws.index');
    Route::get('/withdraws/create',                  [DevWithdraw::class, 'create'])->name('withdraws.create');
    Route::post('/withdraws',                        [DevWithdraw::class, 'store'])->name('withdraws.store');
});

// ============ ADMIN ============
Route::prefix('admin')->middleware(['role:admin'])->name('admin.')->group(function () {
    Route::get('/',                                  [AdminDashboard::class, 'index'])->name('dashboard');

    Route::get('/users',                             [AdminUser::class, 'index'])->name('users.index');
    Route::post('/users/{id}/block',                 [AdminUser::class, 'block'])->name('users.block');
    Route::post('/users/{id}/unblock',               [AdminUser::class, 'unblock'])->name('users.unblock');

    Route::get('/developers',                        [AdminDeveloper::class, 'index'])->name('developers.index');
    Route::get('/developers/{id}',                   [AdminDeveloper::class, 'show'])->name('developers.show');
    Route::post('/developers/{id}/block',            [AdminDeveloper::class, 'block'])->name('developers.block');
    Route::post('/developers/{id}/unblock',          [AdminDeveloper::class, 'unblock'])->name('developers.unblock');

    Route::get('/platforms',                         [AdminPlatform::class, 'index'])->name('platforms.index');
    Route::get('/platforms/{id}',                    [AdminPlatform::class, 'show'])->name('platforms.show');
    Route::post('/platforms/{id}/takedown',          [AdminPlatform::class, 'takedown'])->name('platforms.takedown');
    Route::post('/platforms/{id}/restore',           [AdminPlatform::class, 'restore'])->name('platforms.restore');

    Route::get('/transactions',                      [AdminTransaction::class, 'index'])->name('transactions.index');
    Route::get('/withdraws',                         [AdminTransaction::class, 'withdraws'])->name('withdraws.index');

    Route::get('/forum',                             [AdminForum::class, 'index'])->name('forum.index');
    Route::post('/forum/{id}/hide',                  [AdminForum::class, 'hide'])->name('forum.hide');
    Route::post('/forum/{id}/unhide',                [AdminForum::class, 'unhide'])->name('forum.unhide');
    Route::delete('/forum/{id}',                     [AdminForum::class, 'destroy'])->name('forum.destroy');
});

// Root → redirect by role
Route::get('/', function () {
    if (!session('login')) return redirect('/login');
    return match (session('role')) {
        'admin'     => redirect('/admin'),
        'developer' => redirect('/developer'),
        default     => redirect('/dashboard'),
    };
});
