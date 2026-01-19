<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\MainSettingController;
use App\Http\Controllers\Hnplus\ProductERController;
use App\Http\Controllers\Hnplus\ProductIPDController;
use App\Http\Controllers\Hnplus\ProductOPDController;
use App\Http\Controllers\Hnplus\ProductNCDController;

Route::get('/', function () {
    return view('welcome');
});

// Login / Logout
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

//Admin ################################################################################################################################
Route::prefix('admin')->middleware(['auth', 'is_admin'])->name('admin.')->group(function () {
    Route::post('/git-pull', function () {
        try { $output = shell_exec('cd ' . base_path() . ' && git pull origin main 2>&1');
            return response()->json(['output' => $output]);
        } catch (\Exception $e) { return response()->json(['error' => $e->getMessage()], 500);}})->name('git.pull');
    Route::resource('users', UserController::class);
    Route::get('main_setting', [MainSettingController::class, 'index'])->name('main_setting');
    Route::resource('budget_year', BudgetYearController::class)->parameters(['LEAVE_YEAR_ID' => 'LEAVE_YEAR_ID']);
});

//HN-Plus ################################################################################################################################
    // ✅ กลุ่มที่ต้องล็อกอิน
    Route::prefix('hnplus')->middleware(['auth', 'hnplus'])->name('hnplus.')->group(function () {
        Route::match(['get','post'],'product/er_report', [ProductERController::class, 'er_report'])->name('product.er_report');       
        Route::delete('product/er_product_delete/{id}', [ProductERController::class, 'er_product_delete']);
        Route::match(['get','post'],'product/ipd_report', [ProductIPDController::class, 'ipd_report'])->name('product.ipd_report');       
        Route::delete('product/ipd_product_delete/{id}', [ProductIPDController::class, 'ipd_product_delete']);
        Route::match(['get','post'],'product/opd_report', [ProductOPDController::class, 'opd_report'])->name('product.opd_report');       
        Route::delete('product/opd_product_delete/{id}', [ProductOPDController::class, 'opd_product_delete']);
        Route::match(['get','post'],'product/ncd_report', [ProductNCDController::class, 'ncd_report'])->name('product.ncd_report');       
        Route::delete('product/ncd_product_delete/{id}', [ProductNCDController::class, 'ncd_product_delete']);
    });

    // ✅ กลุ่มที่ไม่ต้องล็อกอิน (public)
    Route::prefix('hnplus')->name('hnplus.')->group(function () {
        //product ER-----------------------------------------------------------------------------------------------------------
        Route::get('product/er_night_notify',[ProductERController::class,'er_night_notify']);
        Route::get('product/er_night',[ProductERController::class,'er_night']);
        Route::post('product/er_night_save',[ProductERController::class,'er_night_save']);
        Route::get('product/er_morning_notify',[ProductERController::class,'er_morning_notify']);
        Route::get('product/er_morning',[ProductERController::class,'er_morning']);
        Route::post('product/er_morning_save',[ProductERController::class,'er_morning_save']);
        Route::get('product/er_afternoon_notify',[ProductERController::class,'er_afternoon_notify']);
        Route::get('product/er_afternoon',[ProductERController::class,'er_afternoon']);
        Route::post('product/er_afternoon_save',[ProductERController::class,'er_afternoon_save']);
        //product ipd-----------------------------------------------------------------------------------------------------------
        Route::get('product/ipd_night_notify',[ProductIPDController::class,'ipd_night_notify']);
        Route::get('product/ipd_night',[ProductIPDController::class,'ipd_night']);
        Route::post('product/ipd_night_save',[ProductIPDController::class,'ipd_night_save']);
        Route::get('product/ipd_morning_notify',[ProductIPDController::class,'ipd_morning_notify']);
        Route::get('product/ipd_morning',[ProductIPDController::class,'ipd_morning']);
        Route::post('product/ipd_morning_save',[ProductIPDController::class,'ipd_morning_save']);
        Route::get('product/ipd_afternoon_notify',[ProductIPDController::class,'ipd_afternoon_notify']);
        Route::get('product/ipd_afternoon',[ProductIPDController::class,'ipd_afternoon']);
        Route::post('product/ipd_afternoon_save',[ProductIPDController::class,'ipd_afternoon_save']);
        //product OPD-----------------------------------------------------------------------------------------------------------
        Route::get('product/opd_morning_notify',[ProductOPDController::class,'opd_morning_notify']);
        Route::get('product/opd_morning',[ProductOPDController::class,'opd_morning']);
        Route::post('product/opd_morning_save',[ProductOPDController::class,'opd_morning_save']);
        Route::get('product/opd_bd_notify',[ProductOPDController::class,'opd_bd_notify']);
        Route::get('product/opd_bd',[ProductOPDController::class,'opd_bd']);
        Route::post('product/opd_bd_save',[ProductOPDController::class,'opd_bd_save']);
        //product NCD-----------------------------------------------------------------------------------------------------------
        Route::get('product/ncd_morning_notify',[ProductNCDController::class,'ncd_morning_notify']);
        Route::get('product/ncd_morning',[ProductNCDController::class,'ncd_morning']);
        Route::post('product/ncd_morning_save',[ProductNCDController::class,'ncd_morning_save']);
    });
