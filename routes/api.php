<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['cors', 'json.response']], function () {
    // Login User In
    Route::post('login', 'AuthApiController@login');

    // routes here
    Route::apiResource('addresses', 'AddressController');
    Route::apiResource('batches', 'BatchController');
    Route::apiResource('bids', 'BidController');
    Route::apiResource('budgetHeads', 'BudgetHeadController');
    Route::apiResource('subBudgetHeads', 'SubBudgetHeadController');
    Route::apiResource('funds', 'FundController');
    Route::apiResource('cashAdvances', 'CashAdvanceController');
    Route::apiResource('contracts', 'ContractController');
    Route::apiResource('departments', 'DepartmentController');
    Route::apiResource('evaluations', 'EvaluationController');
    Route::apiResource('expenditures', 'ExpenditureController');
    Route::apiResource('gradeLevels', 'GradeLevelController');
    Route::apiResource('milestones', 'MilestoneController');
    Route::apiResource('modules', 'ModuleController');
    Route::apiResource('organizations', 'OrganizationController');
    Route::apiResource('procurementMethods', 'ProcurementMethodController');
    Route::apiResource('procurementProcesses', 'ProcurementProcessController');
    Route::apiResource('projects', 'ProjectController');
    Route::apiResource('records', 'RecordController');
    Route::apiResource('retirements', 'RetirementController');
    Route::apiResource('roles', 'RoleController');
    Route::apiResource('serviceCategories', 'ServiceCategoryController');
    Route::apiResource('settings', 'SettingController');
    Route::apiResource('users', 'StaffController');
    Route::apiResource('timelines', 'TimelineController');
});
