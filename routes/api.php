<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['cors', 'json.response']], function () {
    // Login User In
    Route::post('login', 'AuthApiController@login');

    // Routes here
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
    Route::apiResource('remunerations', 'RemunerationController');
    Route::apiResource('settlements', 'SettlementController');
    Route::apiResource('demands', 'DemandController');
    Route::apiResource('refunds', 'RefundController');
    Route::apiResource('tracks', 'TrackController');
    Route::apiResource('entries', 'EntryController');

    Route::apiResource('brands', 'BrandController');
    Route::apiResource('classifications', 'ClassificationController');
    Route::apiResource('categories', 'CategoryController');
    Route::apiResource('products', 'ProductController');
    Route::apiResource('requisitions', 'RequisitionController');
    Route::apiResource('distributions', 'DistributionController');
    Route::apiResource('items', 'ItemController');
    Route::apiResource('stores', 'StoreController');

    Route::apiResource('learningCategories', 'LearningCategoryController');
    Route::apiResource('qualifications', 'QualificationController');
    Route::apiResource('trainings', 'TrainingController');
    Route::apiResource('joinings', 'JoiningController');

    Route::apiResource('commitments', 'CommitmentController');
    Route::apiResource('targets', 'TargetController');
    Route::apiResource('pillars', 'PillarController');
    Route::apiResource('responsibilities', 'ResponsibilityController');
    Route::apiResource('tasks', 'TaskController');
    Route::apiResource('claims', 'ClaimController');
    Route::apiResource('touringAdvances', 'TouringAdvanceController');
    Route::apiResource('expenses', 'ExpenseController');
    Route::apiResource('processes', 'ProcessController');
    Route::apiResource('stages', 'StageController');

    // Custom Routes
    Route::post('imports', 'ImportController@importDependencies');
    Route::post('verify/{training}', 'CustomRouteController@verifyTraining');
    Route::get('fetch/claims/{claim}', 'ClaimController@fetchClaim');
    Route::get('collect/batches/{batch}', 'BatchController@collectBatch');
    Route::post('assign/roles', 'StaffController@assignRole');
    Route::patch('reset/password/{user}', 'StaffController@passwordReset');
    Route::patch('query/expenditures/{expenditure}', 'ExpenditureController@queryExpenditure');
    Route::patch('clear/expenditures/{expenditure}', 'ExpenditureController@clearPayment');
    Route::patch('response/demands/{demand}', 'DemandController@reversalResponse');
    Route::patch('fulfill/refunds/{refund}', 'RefundController@fulfillRefundRequest');
    Route::get('fetch/expenditures/{subBudgetHead}', 'SubBudgetHeadController@fetExpenditures');
    Route::patch('process/batches/{batch}', 'BatchController@startProcess');
    Route::get('harvest/processes/{process}', 'ProcessController@fetchByType');
});
