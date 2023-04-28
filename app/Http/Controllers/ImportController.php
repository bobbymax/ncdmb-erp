<?php

namespace App\Http\Controllers;

use App\Http\Resources\ModuleResource;
use App\Http\Resources\SubBudgetHeadResource;
use App\Http\Resources\UserResource;
use App\Models\BudgetHead;
use App\Models\Department;
use App\Models\Fund;
use App\Models\GradeLevel;
use App\Models\Module;
use App\Models\Role;
use App\Models\SubBudgetHead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ImportController extends Controller
{
    protected $parent, $result;
    protected $bulk = [];

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function importDependencies(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array',
            'type' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors!!'
            ], 500);
        }


        $this->result = match ($request->type) {
            'modules' => $this->loadModules($request->data),
            'grade-levels' => $this->loadGradeLevels($request->data),
            'staff' => $this->loadStaff($request->data),
            'departments' => $this->loadDepartments($request->data),
            'budget-heads' => $this->loadBudgetHeads($request->data),
            'sub-budget-heads' => $this->loadSubBudgetHeads($request->data),
            'funds' => $this->loadFunds($request->data),
            'roles' => $this->loadRoles($request->data),
            'claims' => $this->loadClaims($request->data),
            default => []
        };

        return response()->json([
            'data' => $this->result,
            'status' => 'success',
            'message' => 'Dependency uploaded successfully!!'
        ], 200);
    }

    protected function loadBudgetHeads(array $data): bool
    {
        $dataChunk = [];

        foreach ($data as $key => $value) {
            $budgetHead = BudgetHead::where('budgetId', $value['BH'])->first();

            if (! $budgetHead) {
                $insertData = [
                    'budgetId' => $value['BH'],
                    'name' => $value['NAME'],
                    'label' => Str::slug($value['NAME']),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];

                $dataChunk[] = $insertData;
            }


        }

        $dataChunk = collect($dataChunk);
        $chunks = $dataChunk->chunk(100);
        $this->insertInto('budget_heads', $chunks);

        return true;
    }

    protected function loadSubBudgetHeads(array $data): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        foreach ($data as $value) {

            $budgetHead = BudgetHead::where('budgetId', $value['BH'])->first();
            $department = Department::where('code', $value['DEPARTMENT'])->first();
            $subBudgetHead = SubBudgetHead::where('code', $value['CODE'])->first();

            if ($budgetHead && $department && ! $subBudgetHead) {

                $type = match(substr($value['CODE'], 0)) {
                    'R' => 'recurrent',
                    'P' => 'personnel',
                    default => 'capital'
                };

                $subBudgetHead = SubBudgetHead::create([
                    'budget_head_id' => $budgetHead->id,
                    'department_id' => $department->id,
                    'code' => $value['CODE'],
                    'name' => $value['NAME'],
                    'label' => Str::slug($value['NAME']) . "-" . time() . uniqid(),
                    'type' => $type,
                ]);
            }

            $this->bulk[] = $subBudgetHead;
        }

        return SubBudgetHeadResource::collection($this->bulk);
    }

    protected function loadFunds(array $data): array
    {
        foreach ($data as $value) {
            $subBudgetHead = SubBudgetHead::where('code', $value['CODE'])->first();

            if ($subBudgetHead) {
                $fund = Fund::create([
                    'sub_budget_head_id' => $subBudgetHead->id,
                    'approved_amount' => $value['APPROVED'],
                    'booked_expenditure' => $value['COMMITMENT'],
                    'actual_expenditure' => $value['ACTUAL'],
                    'booked_balance' => $value['CBAL'],
                    'actual_balance' => $value['ABAL'],
                    'year' => $value['YEAR']
                ]);

                $this->bulk[] = $fund;
            }
        }

        return $this->bulk;
    }

    protected function loadModules(array $data): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        foreach ($data as $value) {
            $label = Str::slug($value['NAME']);
            $module = Module::where('label', $label)->first();
            $role = Role::where('label', 'super-administrator')->first();

            if (! $module) {
                if ($value['PARENT'] !== "none") {
                    $this->parent = Module::where('label', $value['PARENT'])->first();
                }

                $module = Module::create([
                    'name' => $value['NAME'],
                    'label' => $label,
                    'code' => $value['CODE'],
                    'url' => $value['URL'],
                    'type' => $value['TYPE'],
                    'parentId' => $this->parent ? $this->parent->id : 0,
                    'icon' => $value['ICON']
                ]);

                $module->addRole($role);
            }

            $this->bulk[] = $module;
        }

        return ModuleResource::collection($this->bulk);
    }

    protected function loadGradeLevels(array $data): array
    {
        foreach($data as $value) {
            $code = strtoupper($value['KEY']);
            $gradeLevel = GradeLevel::where('key', $code)->first();

            if (! $gradeLevel) {

                $gradeLevel = GradeLevel::create([
                    'name' => $value['NAME'],
                    'key' => $code,
                ]);

            }

            $this->bulk[] = $gradeLevel;
        }

        return $this->bulk;
    }

    protected function loadStaff(array $data): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        foreach ($data as $value) {
            $role = Role::where('label', 'staff')->first();
            $gradeLevel = GradeLevel::where('key', $value['GRADE'])->first();
            $department = Department::where('code', $value['DEPARTMENT'])->first();
            $staff = User::where('email', $value['EMAIL'])->first();

            $password = trim(strtolower($value['FIRSTNAME'])) . "." . trim(strtolower($value['SURNAME']));

            if (! $staff && $role && $gradeLevel && $department) {
                $staff = User::create([
                    'staff_no' => $value['ID'],
                    'email' => $value['EMAIL'],
                    'firstname' => $value['FIRSTNAME'],
                    'middlename' => $value['MIDDLENAME'] ?? '',
                    'surname' => $value['SURNAME'],
                    'grade_level_id' => $gradeLevel->id,
                    'department_id' => $department->id,
                    'password' => Hash::make($password),
                ]);

                if ($staff) {
                    $staff->roles()->save($role);
                }
            }

            $this->bulk[] = $staff;
        }

        return UserResource::collection($this->bulk);
    }

    protected function loadClaims(array $data) {

    }

    protected function loadDepartments(array $data): array
    {
        foreach ($data as $value) {
            $department = Department::where('code', $value['CODE'])->first();

            if (! $department) {
                $parent = $value['PARENT'] !== 'NA' ? Department::where('code', $value['PARENT'])->first() : null;
                $parentId = $parent !== null ? $parent->id : 0;

                $department = Department::create([
                    'name' => $value['NAME'],
                    'label' => $value['LABEL'],
                    'code' => $value['CODE'],
                    'type' => strtolower($value['TYPE']),
                    'parentId' => $parentId,
                ]);
            }

            $this->bulk[] = $department;
        }

        return $this->bulk;
    }

    protected function loadRoles(array $data): bool
    {
        $dataChunk = [];

        foreach ($data as $value) {
            $label = Str::slug($value['NAME']);
            $role = Role::where('label', $label)->first();

            if (! $role) {
                $insertData = [
                    'name' => $value['NAME'],
                    'label' => $label,
                    'slots' => $value['SLOT'],
                    'type' => $value['TYPE'],
                    'isSuper' => $value['SUPER'] == 1,
                    'no_expiration' => $value['EXPIRE'] == 1,
                    'start' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                $dataChunk[] = $insertData;
            }
        }

        $dataChunk = collect($dataChunk);
        $chunks = $dataChunk->chunk(100);
        $this->insertInto('roles', $chunks);

        return true;
    }

    protected function insertInto($table, $chunks)
    {
        foreach ($chunks as $chunk) {
            DB::table($table)->insert($chunk->toArray());
        }

        return;
    }
}
