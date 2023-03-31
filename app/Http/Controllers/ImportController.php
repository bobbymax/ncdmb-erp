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


        $this->result = match ($request->data) {
            'modules' => $this->loadModules($request->data),
            'levels' => $this->loadGradeLevels($request->data),
            'staff' => $this->loadStaff($request->data),
            'departments' => $this->loadDepartments($request->data),
            'budget-heads' => $this->loadBudgetHeads($request->data),
            'sub-budget-heads' => $this->loadSubBudgetHeads($request->data),
            'roles' => $this->loadRoles($request->data),
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

            if ($budgetHead && $department) {

                $year = config('site.budget_year') ?? config('budget.budget_year');
                $currentYear = isset($value['YEAR']) ? $value['YEAR'] : $year;

                if (! $subBudgetHead) {

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
                        'label' => Str::slug($value['NAME']),
                        'type' => $type,
                        'active' => true
                    ]);
                }

                $fund = $subBudgetHead->fund;

                if (! $fund) {
                    Fund::create([
                        'sub_budget_head_id' => $subBudgetHead->id,
                        'approved_amount' => $value['AMOUNT'],
                        'booked_balance' => $value['AMOUNT'],
                        'actual_balance' => $value['AMOUNT'],
                        'year' => $currentYear
                    ]);
                }
            }

            $this->bulk[] = $subBudgetHead;
        }

        return SubBudgetHeadResource::collection($this->bulk);
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
                    'path' => $value['PATH'],
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
            $label = Str::slug($value['NAME']);
            $gradeLevel = GradeLevel::where('label', $label)->first();

            if (! $gradeLevel) {

                $gradeLevel = GradeLevel::create([
                    'name' => $value['NAME'],
                    'label' => $label,
                    'code' => $value['KEY'],
                ]);

            }

            $this->bulk[] = $gradeLevel;
        }

        return $this->bulk;
    }

    protected function loadStaff(array $data): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        foreach ($data as $value) {
            $middlename = (isset($value["MIDDLENAME"]) && $value["MIDDLENAME"] !== "-") ? $value["MIDDLENAME"] . " " : "";
            $name = $value['FIRSTNAME'] . " " . $middlename . $value["SURNAME"];

            $role = Role::where('label', 'staff')->first();
            $gradeLevel = GradeLevel::where('code', $value['GRADE'])->first();
            $department = Department::where('code', $value['DEPARTMENT'])->first();
            $staff = User::where('email', $value['EMAIL'])->first();

            $password = trim(strtolower($value['FIRSTNAME'])) . "." . trim(strtolower($value['SURNAME']));

            if (! $staff && $role && $gradeLevel && $department) {
                $staff = User::create([
                    'staff_no' => $value['STAFF ID'],
                    'email' => $value['EMAIL'],
                    'name' => $name,
                    'firstname' => $value['FIRSTNAME'],
                    'middlename' => $middlename,
                    'surname' => $value["SURNAME"],
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

    protected function loadDepartments(array $data): array
    {
        foreach ($data as $value) {
            $department = Department::where('code', $value['CODE'])->first();

            if (! $department) {
                $parent = $value['PARENT'] !== 'none' ? Department::where('code', $value['PARENT'])->first() : null;
                $parentId = $parent !== null ? $parent->id : 0;

                $department = Department::create([
                    'name' => $value['NAME'],
                    'label' => Str::slug($value['NAME']),
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

        foreach ($data as $key => $value) {
            $label = Str::slug($value['NAME']);
            $role = Role::where('label', $label)->first();

            if (! $role) {
                $insertData = [
                    'name' => $value['NAME'],
                    'label' => $label,
                    'slots' => $value['SLOT'],
                    'type' => $value['TYPE'],
                    'isSuper' => $value['SUPER'] == 1 ? true : false,
                    'no_expiration' => $value['EXPIRE'] == 1 ? true : false,
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
