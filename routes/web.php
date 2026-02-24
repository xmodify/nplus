<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\MainSettingController;
use App\Http\Controllers\Hnplus\ProductERController;
use App\Http\Controllers\Hnplus\ProductIPDController;
use App\Http\Controllers\Hnplus\ProductOPDController;
use App\Http\Controllers\Hnplus\ProductNCDController;
use App\Http\Controllers\Hnplus\ProductARIController;
use App\Http\Controllers\Hnplus\ProductCKDController;
use App\Http\Controllers\Hnplus\ProductHDController;
use App\Http\Controllers\Hnplus\ProductVIPController;
use App\Http\Controllers\Hnplus\ProductLRController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BudgetYearController;

use App\Http\Controllers\Hnplus\DashboardController;

Route::get('/', [DashboardController::class , 'index'])->name('home');

// Login / Logout
Route::post('/login', [LoginController::class , 'login'])->name('login');
Route::post('/logout', [LoginController::class , 'logout'])->name('logout');

//Admin ################################################################################################################################
Route::prefix('admin')->middleware(['auth', 'is_admin'])->name('admin.')->group(function () {
    Route::post(
        '/git-pull',
        function () {
            try {
                $output = shell_exec('cd ' . base_path() . ' && git pull origin main 2>&1');
                return response()->json(['output' => $output]);
            }
            catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
        )->name('git.pull');
        Route::resource('users', UserController::class);
        Route::get('main_setting', [MainSettingController::class , 'index'])->name('main_setting');
        Route::match (['get', 'post'], 'main_setting/up_structure', [MainSettingController::class , 'up_structure'])->name('up_structure');
        Route::post('main_setting/update', [MainSettingController::class , 'update'])->name('main_setting.update');
        Route::resource('budget_year', BudgetYearController::class)->parameters(['LEAVE_YEAR_ID' => 'LEAVE_YEAR_ID']);
    });

//HN-Plus ################################################################################################################################
// ✅ กลุ่มที่ต้องล็อกอิน
Route::prefix('hnplus')->middleware(['auth', 'hnplus'])->name('hnplus.')->group(function () {
    Route::match (['get', 'post'], 'product/er_report', [ProductERController::class , 'er_report'])->name('product.er_report');
    Route::delete('product/er_product_delete/{id}', [ProductERController::class , 'er_product_delete']);
    Route::match (['get', 'post'], 'product/ipd_report', [ProductIPDController::class , 'ipd_report'])->name('product.ipd_report');
    Route::delete('product/ipd_product_delete/{id}', [ProductIPDController::class , 'ipd_product_delete']);
    Route::match (['get', 'post'], 'product/opd_report', [ProductOPDController::class , 'opd_report'])->name('product.opd_report');
    Route::delete('product/opd_product_delete/{id}', [ProductOPDController::class , 'opd_product_delete']);
    Route::match (['get', 'post'], 'product/ncd_report', [ProductNCDController::class , 'ncd_report'])->name('product.ncd_report');
    Route::delete('product/ncd_product_delete/{id}', [ProductNCDController::class , 'ncd_product_delete']);
    Route::match (['get', 'post'], 'product/ari_report', [ProductARIController::class , 'ari_report'])->name('product.ari_report');
    Route::delete('product/ari_product_delete/{id}', [ProductARIController::class , 'ari_product_delete']);
    Route::match (['get', 'post'], 'product/ckd_report', [ProductCKDController::class , 'ckd_report'])->name('product.ckd_report');
    Route::delete('product/ckd_product_delete/{id}', [ProductCKDController::class , 'ckd_product_delete']);
    Route::match (['get', 'post'], 'product/hd_report', [ProductHDController::class , 'hd_report'])->name('product.hd_report');
    Route::delete('product/hd_product_delete/{id}', [ProductHDController::class , 'hd_product_delete']);
    Route::match (['get', 'post'], 'product/vip_report', [ProductVIPController::class , 'vip_report'])->name('product.vip_report');
    Route::delete('product/vip_product_delete/{id}', [ProductVIPController::class , 'vip_product_delete']);
    Route::match (['get', 'post'], 'product/lr_report', [ProductLRController::class , 'lr_report'])->name('product.lr_report');
    Route::delete('product/lr_product_delete/{id}', [ProductLRController::class , 'lr_product_delete']);
});

// ✅ กลุ่มที่ไม่ต้องล็อกอิน (public)
Route::prefix('hnplus')->name('hnplus.')->group(function () {
    //product ER-----------------------------------------------------------------------------------------------------------
    Route::get('product/er_night_notify', [ProductERController::class , 'er_night_notify']);
    Route::get('product/er_night', [ProductERController::class , 'er_night']);
    Route::post('product/er_night_save', [ProductERController::class , 'er_night_save']);
    Route::get('product/er_morning_notify', [ProductERController::class , 'er_morning_notify']);
    Route::get('product/er_morning', [ProductERController::class , 'er_morning']);
    Route::post('product/er_morning_save', [ProductERController::class , 'er_morning_save']);
    Route::get('product/er_afternoon_notify', [ProductERController::class , 'er_afternoon_notify']);
    Route::get('product/er_afternoon', [ProductERController::class , 'er_afternoon']);
    Route::post('product/er_afternoon_save', [ProductERController::class , 'er_afternoon_save']);
    //product ipd-----------------------------------------------------------------------------------------------------------
    Route::get('product/ipd_night_notify', [ProductIPDController::class , 'ipd_night_notify']);
    Route::get('product/ipd_night', [ProductIPDController::class , 'ipd_night']);
    Route::post('product/ipd_night_save', [ProductIPDController::class , 'ipd_night_save']);
    Route::get('product/ipd_morning_notify', [ProductIPDController::class , 'ipd_morning_notify']);
    Route::get('product/ipd_morning', [ProductIPDController::class , 'ipd_morning']);
    Route::post('product/ipd_morning_save', [ProductIPDController::class , 'ipd_morning_save']);
    Route::get('product/ipd_afternoon_notify', [ProductIPDController::class , 'ipd_afternoon_notify']);
    Route::get('product/ipd_afternoon', [ProductIPDController::class , 'ipd_afternoon']);
    Route::post('product/ipd_afternoon_save', [ProductIPDController::class , 'ipd_afternoon_save']);
    //product OPD-----------------------------------------------------------------------------------------------------------
    Route::get('product/opd_morning_notify', [ProductOPDController::class , 'opd_morning_notify']);
    Route::get('product/opd_morning', [ProductOPDController::class , 'opd_morning']);
    Route::post('product/opd_morning_save', [ProductOPDController::class , 'opd_morning_save']);
    Route::get('product/opd_bd_notify', [ProductOPDController::class , 'opd_bd_notify']);
    Route::get('product/opd_bd', [ProductOPDController::class , 'opd_bd']);
    Route::post('product/opd_bd_save', [ProductOPDController::class , 'opd_bd_save']);
    //product NCD-----------------------------------------------------------------------------------------------------------
    Route::get('product/ncd_morning_notify', [ProductNCDController::class , 'ncd_morning_notify']);
    Route::get('product/ncd_morning', [ProductNCDController::class , 'ncd_morning']);
    Route::post('product/ncd_morning_save', [ProductNCDController::class , 'ncd_morning_save']);
    //product ARI-----------------------------------------------------------------------------------------------------------
    Route::get('product/ari_morning_notify', [ProductARIController::class , 'ari_morning_notify']);
    Route::get('product/ari_morning', [ProductARIController::class , 'ari_morning']);
    Route::post('product/ari_morning_save', [ProductARIController::class , 'ari_morning_save']);
    //product CKD-----------------------------------------------------------------------------------------------------------
    Route::get('product/ckd_morning_notify', [ProductCKDController::class , 'ckd_morning_notify']);
    Route::get('product/ckd_morning', [ProductCKDController::class , 'ckd_morning']);
    Route::post('product/ckd_morning_save', [ProductCKDController::class , 'ckd_morning_save']);
    //product HD-----------------------------------------------------------------------------------------------------------
    Route::get('product/hd_morning_notify', [ProductHDController::class , 'hd_morning_notify']);
    Route::get('product/hd_morning', [ProductHDController::class , 'hd_morning']);
    Route::post('product/hd_morning_save', [ProductHDController::class , 'hd_morning_save']);
    //product VIP-----------------------------------------------------------------------------------------------------------
    Route::get('product/vip_night_notify', [ProductVIPController::class , 'vip_night_notify']);
    Route::get('product/vip_night', [ProductVIPController::class , 'vip_night']);
    Route::post('product/vip_night_save', [ProductVIPController::class , 'vip_night_save']);
    Route::get('product/vip_morning_notify', [ProductVIPController::class , 'vip_morning_notify']);
    Route::get('product/vip_morning', [ProductVIPController::class , 'vip_morning']);
    Route::post('product/vip_morning_save', [ProductVIPController::class , 'vip_morning_save']);
    Route::get('product/vip_afternoon_notify', [ProductVIPController::class , 'vip_afternoon_notify']);
    Route::get('product/vip_afternoon', [ProductVIPController::class , 'vip_afternoon']);
    Route::post('product/vip_afternoon_save', [ProductVIPController::class , 'vip_afternoon_save']);
    //product LR-----------------------------------------------------------------------------------------------------------
    Route::get('product/lr_night_notify', [ProductLRController::class , 'lr_night_notify']);
    Route::get('product/lr_night', [ProductLRController::class , 'lr_night']);
    Route::post('product/lr_night_save', [ProductLRController::class , 'lr_night_save']);
    Route::get('product/lr_morning_notify', [ProductLRController::class , 'lr_morning_notify']);
    Route::get('product/lr_morning', [ProductLRController::class , 'lr_morning']);
    Route::post('product/lr_morning_save', [ProductLRController::class , 'lr_morning_save']);
    Route::get('product/lr_afternoon_notify', [ProductLRController::class , 'lr_afternoon_notify']);
    Route::get('product/lr_afternoon', [ProductLRController::class , 'lr_afternoon']);
    Route::post('product/lr_afternoon_save', [ProductLRController::class , 'lr_afternoon_save']);
});
