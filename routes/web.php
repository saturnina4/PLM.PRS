<?php
declare( STRICT_TYPES = 1 );

use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

/* COMMON ROUTES */

Route::get( '/', function() {
    return Auth::check() ? redirect()->route('getDashboard' ) : view('Application.Authentication' );
})->name( 'getIndex' );

// oAuth2 Microsoft Azure Authentication Routes
Route::get( '/auth', 'Application\Authentication\OAuth2Ctrl@getRedirect' )
    ->name( 'getOAuth2Redirect' );

Route::get( '/auth/callback', 'Application\Authentication\OAuth2Ctrl@getCallback' )
    ->name( 'getOAuth2Callback' );

Route::get( '/auth/logout', function() {
    Auth::logout();
    session()->flush();
    return redirect()->route( 'getIndex' );
})->name( 'getLogout' );

/* ROUTES ACCESSIBLE AFTER AUTHENTICATION */
//Route::group( ['middleware' => ['auth']], function() {
    // Dashboard
    Route::get( '/dashboard', function() {
        return view('Application.Dashboard' );
    })->name( 'getDashboard' );

    /* GENERAL PAYROLL ROUTES */

    // Generate
    Route::get( '/gp/report/generate/', 'Application\Features\GPGenerateCtrl@getAction' )
        ->name( 'getGPGenReport' );
    Route::post( '/gp/report/generate/', 'Application\Features\GPGenerateCtrl@postAction' )
        ->name( 'postGPGenReport' );

    // Find
    Route::get( '/gp/report/find/', 'Application\Features\GPFindReportCtrl@getAction' )
        ->name( 'getGPFindReport' );
    Route::post( '/gp/report/find/', 'Application\Features\GPFindReportCtrl@postAction' )
        ->name( 'postGPFindReport' );

    // View
    Route::get( '/gp/report/view/id/{recordId}', 'Application\Features\GPViewReportCtrl@getAction' )
        ->name( 'getGPViewReport' );

    Route::post( '/gp/report/view/id/{recordId}', 'Application\Features\GPViewReportCtrl@postAction' )
        ->name( 'postGPViewReport' );

    // Edit Withholding Tax
    Route::post( '/gp/report/edit/whTax', 'Application\Features\GPViewReportCtrl@postEditWhTax' )
        ->name( 'postEditWhTax' );

    // Download
    Route::get( '/gp/report/download/id/{id}', 'Application\Features\GPDownloadReportCtrl@getAction' )
        ->name( 'getGPPrintReport' );

    /* PART TIME PAYROLL ROUTES */

    // Generate Report
    Route::get( '/parttime/report/generate/', 'Application\Features\PartTime\GenerateCtrl@getAction' )
        ->name( 'getPTGenReport' );
    Route::post( '/parttime/report/generate/', 'Application\Features\PartTime\GenerateCtrl@postAction' )
        ->name( 'postPTGenReport' );

    // Fetch employees of selected department
    Route::post( '/parttime/report/fetch/employees/', 'Application\Features\PartTime\GenerateCtrl@postFetchEmployees' )
        ->name( 'postFetchEmployees' );

    // Find
    Route::get( '/parttime/report/find/', 'Application\Features\PartTime\FindReportCtrl@getAction' )
        ->name( 'getPTFindReport' );
    Route::post( '/parttime/report/find/', 'Application\Features\PartTime\FindReportCtrl@postAction' )
        ->name( 'postPTFindReport' );

    // View Report
    Route::get( '/parttime/report/view/id/{recordId}', 'Application\Features\PartTime\ViewReportCtrl@getAction' )
        ->name( 'getPTViewReport' );

    Route::post( '/parttime/report/view/id/{recordId}', 'Application\Features\PartTime\ViewReportCtrl@postAction' )
        ->name( 'postPTViewReport' );

    Route::post( '/parttime/report/exclude/', 'Application\Features\PartTime\ViewReportCtrl@postExclude' )
        ->name( 'postPTExclude' );

    Route::post( '/parttime/report/data/fetch', 'Application\Features\PartTime\ViewReportCtrl@postFetch' )
        ->name( 'postPTFetchData' );

    Route::post( '/parttime/report/data/edit', 'Application\Features\PartTime\ViewReportCtrl@postEdit' )
        ->name( 'postPTEditData' );

    Route::post( '/parttime/report/data/add', 'Application\Features\PartTime\ViewReportCtrl@postAdd' )
        ->name( 'postPTAddData' );

    // View Summary
    Route::get( '/parttime/report/summary', 'Application\Features\PartTime\ViewReportSummaryCtrl@getAction' )
        ->name( 'getPTReportSummary' );

    Route::post( '/parttime/report/summary', 'Application\Features\PartTime\ViewReportSummaryCtrl@postAction' )
        ->name( 'postPTReportSummary' );

    // Signatories Summary
    Route::get( '/parttime/report/signatories', 'Application\Features\PartTime\SignatoriesCtrl@getAction' )
        ->name( 'getPTSignatories' );

    Route::post( '/parttime/report/signatories', 'Application\Features\PartTime\SignatoriesCtrl@postAction' )
        ->name( 'postPTSignatories' );

    // Download
    Route::get( '/parttime/report/download/id/{id}', 'Application\Features\PartTime\DownloadReportCtrl@getAction' )
        ->name( 'getPTPrintReport' );

    /* CASUAL PAYROLL ROUTES */

    // Generate Report
    Route::get( '/casual/report/generate/', 'Application\Features\Casual\GenerateCtrl@getAction' )
        ->name( 'getCPGenReport' );
    Route::post( '/casual/report/generate/', 'Application\Features\Casual\GenerateCtrl@postAction' )
        ->name( 'postCPGenReport' );

    // Find
    Route::get( '/casual/report/find/', 'Application\Features\Casual\FindReportCtrl@getAction' )
        ->name( 'getCPFindReport' );
    Route::post( '/casual/report/find/', 'Application\Features\Casual\FindReportCtrl@postAction' )
        ->name( 'postCPFindReport' );

    // View Report
    Route::get( '/casual/report/view/id/{recordId}', 'Application\Features\Casual\ViewReportCtrl@getAction' )
        ->name( 'getCPViewReport' );

    Route::post( '/casual/report/view/id/{recordId}', 'Application\Features\Casual\ViewReportCtrl@postAction' )
        ->name( 'postCPViewReport' );

    // Signatories
    Route::get( '/casual/report/signatories', 'Application\Features\Casual\SignatoriesCtrl@getAction' )
        ->name( 'getCPSignatories' );

    Route::post( '/casual/report/signatories', 'Application\Features\Casual\SignatoriesCtrl@postAction' )
        ->name( 'postCPSignatories' );

    // Download
    Route::get( '/casual/report/download/id/{id}', 'Application\Features\Casual\DownloadReportCtrl@getAction' )
        ->name( 'getCPPrintReport' );

    /* EXCLUDED PAYROLL ROUTES */

    // Generate Report
    Route::get( '/excluded/report/generate/', 'Application\Features\Excluded\GenerateCtrl@getAction' )
        ->name( 'getEPGenReport' );
    Route::post( '/excluded/report/generate/', 'Application\Features\Excluded\GenerateCtrl@postAction' )
        ->name( 'postEPGenReport' );

    // Find
    Route::get( '/excluded/report/find/', 'Application\Features\Excluded\FindReportCtrl@getAction' )
        ->name( 'getEPFindReport' );
    Route::post( '/excluded/report/find/', 'Application\Features\Excluded\FindReportCtrl@postAction' )
        ->name( 'postEPFindReport' );

    // View Report
    Route::get( '/excluded/report/view/id/{recordId}', 'Application\Features\Excluded\ViewReportCtrl@getAction' )
        ->name( 'getEPViewReport' );

    Route::post( '/excluded/report/view/id/{recordId}', 'Application\Features\Excluded\ViewReportCtrl@postAction' )
        ->name( 'postEPViewReport' );

    // Signatories
    Route::get( '/excluded/report/signatories', 'Application\Features\Excluded\SignatoriesCtrl@getAction' )
        ->name( 'getEPSignatories' );

    Route::post( '/excluded/report/signatories', 'Application\Features\Excluded\SignatoriesCtrl@postAction' )
        ->name( 'postEPSignatories' );

    // Download
    Route::get( '/excluded/report/download/id/{id}', 'Application\Features\Excluded\DownloadReportCtrl@getAction' )
        ->name( 'getEPPrintReport' );

    /* JOB ORDER PAYROLL ROUTES */

    // Generate
    Route::get( '/jo/report/generate/', 'Application\Features\JobOrder\JoGenerateCtrl@getAction' )
        ->name( 'getJoGenReport' );
    Route::post( '/jo/report/generate/', 'Application\Features\JobOrder\JoGenerateCtrl@postAction' )
        ->name( 'postJoGenReport' );

    // Find
    Route::get( '/jo/report/find/', 'Application\Features\JobOrder\JoFindReportCtrl@getAction' )
        ->name( 'getJoFindReport' );
    Route::post( '/jo/report/find/', 'Application\Features\JobOrder\JoFindReportCtrl@postAction' )
        ->name( 'postJoFindReport' );

    // View
    Route::get( '/jo/report/view/id/{recordId}', 'Application\Features\JobOrder\JoViewReportCtrl@getAction' )
        ->name( 'getJoViewReport' );

    // Download
    Route::get( '/jo/report/download/id/{id}', 'Application\Features\JobOrder\JoDownloadReportCtrl@getAction' )
        ->name( 'getJoPrintReport' );

    // Employee Parameters List
    Route::get( '/jo/params/list/', 'Application\Features\JobOrder\JoParametersListCtrl@getAction' )
        ->name( 'getJoParamList' );

    // Add Employee Parameters
    Route::get( '/jo/params/add', 'Application\Features\JobOrder\JoAddParametersCtrl@getAction' )
        ->name( 'getJoAddEmpParams' );
    Route::post( '/jo/params/add', 'Application\Features\JobOrder\JoAddParametersCtrl@postAction' )
        ->name( 'postJoAddEmpParams' );

    // Modify Employee Parameters
    Route::get( '/jo/params/modify/id/{recordId}', 'Application\Features\JobOrder\JoModifyParametersCtrl@getAction' )
        ->name( 'getJoModifyEmpParams' );
    Route::post( '/jo/params/modify/id/{recordId}', 'Application\Features\JobOrder\JoModifyParametersCtrl@postAction' )
        ->name( 'postJoModifyEmpParams' );

    /* DEDUCTION ROUTES */

    // View List
    Route::get( '/deductions/view/', 'Application\Features\ViewDeductionsListCtrl@getAction' )
        ->name( 'getViewDeductionList' );

    // Add Employee Deduction
    Route::get( '/deductions/add', 'Application\Features\AddEmpDeductionsCtrl@getAction' )
        ->name( 'getAddEmpDeduction' );
    Route::post( '/deductions/add', 'Application\Features\AddEmpDeductionsCtrl@postAction' )
        ->name( 'postAddEmpDeduction' );

    // View Employee Deduction
    Route::get( '/deductions/view/id/{recordId}', 'Application\Features\ViewEmpDeductionsCtrl@getAction' )
        ->name( 'getViewEmpDeduction' );
    Route::post( '/deductions/view/id/{recordId}', 'Application\Features\ViewEmpDeductionsCtrl@postAction' )
        ->name( 'postViewEmpDeduction' );

    /* UNCATEGORIZED */

    // Soo to be used routes
    Route::get( '/settings', 'SettingsController@getSettingsIndex' );
    Route::get( '/settings/tranche/view', 'SettingsController@getViewTranche' );
    Route::get( '/settings/tranche/set', 'SettingsController@getSetTranche' );
    Route::post( '/settings/tranche/set', 'SettingsController@postSetTranche' );
    Route::get( '/settings/tranche/upload', 'SettingsController@getUploadTranche' );
    Route::post( '/settings/tranche/upload', 'SettingsController@postUploadTranche' );

    /* E-Payslip */
    // Route::get( '/payslip/mail/{id}', 'PayslipController@getAction' );
    Route::get( '/payslip/mail', 'PayslipController@getAction' )
        ->name( 'getPayslip' );
    Route::post( '/payslip/mail', 'PayslipController@postAction' );
//});
