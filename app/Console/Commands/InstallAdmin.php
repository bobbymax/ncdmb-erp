<?php

namespace App\Console\Commands;

use App\Models\Department;
use App\Models\GradeLevel;
use App\Models\Module;
use App\Models\Record;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class InstallAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Admin with other dependencies';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info("Creating Admin Department");
        $department = $this->addDepartment();

        if (! $department) {
            $this->error("Something went wrong!!");
        }

        $this->info("Creating Admin Role");
        $role = $this->addRole();

        if (! $role) {
            $this->error("Something went terribly wrong!!");
        }

        $this->info("Creating Grade Levels");
        $level = $this->addGradeLevel();

        if (! $level) {
            $this->error("Something went wrong here!!");
        }

        $this->info("Creating Admin User");

        User::create([
            'department_id' => $department->id,
            'grade_level_id' => $level->id,
            'firstname' => 'Admin',
            'surname' => 'Staff',
            'staff_no' => 'ADMIN01',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'mobile' => '2349093479079',
            'designation' => 'Super Administrator',
            'location' => 'Abuja FCT',
            'dob' => Carbon::parse(Carbon::now()),
            'date_joined' => Carbon::parse(Carbon::now()),
            'type' => 'permanent',
            'status' => 'in-service',
            'isAdministrator' => true
        ]);

        $this->info("Admin User Created Successfully");
        $this->info("Creating Modules");
        $this->addModules();
        $this->info("Modules Created Successfully");

        $this->info("Admin record has been created successfully!!!");
    }

    protected function addDepartment()
    {
        return Department::create([
            'name' => 'Administration',
            'code' => 'ADD',
            'payment_code' => '00ADD654',
            'parentId' => 0,
            'type' => 'directorate',
            'isActive' => true
        ]);
    }

    protected function addRole()
    {
        return Role::create([
            'name' => 'Super Administrator',
            'label' => 'super-administrator',
            'slots' => 1,
            'type' => 'roles',
            'no_expiration' => true,
            'isSuper' => true,
        ]);
    }

    protected function addGradeLevel()
    {
        return GradeLevel::create([
            'name' => 'Admin Level',
            'key' => 'AD01'
        ]);
    }

    protected function addModules(): bool
    {
        foreach ($this->getAdminModules() as $module) {
            Module::create($module);
        }

        return true;
    }

    protected function getAdminModules(): array
    {
        return [
            [
                'name' => 'Admin',
                'label' => 'admin',
                'code' => 'ADD',
                'icon' => 'settings',
                'url' => '/admin',
                'parentId' => 0,
                'type' => 'application'
            ],
            [
                'name' => 'Modules',
                'label' => 'modules',
                'code' => 'MOD',
                'icon' => 'layers',
                'url' => '/admin/modules',
                'parentId' => 1,
                'type' => 'module'
            ],
        ];
    }
}
