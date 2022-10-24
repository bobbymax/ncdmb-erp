<?php

namespace App\Console\Commands;

use App\Models\Department;
use App\Models\GradeLevel;
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
     * @return int
     */
    public function handle()
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
        $user = $this->addAdminRecord();

        if (! $user) {
            $this->error('Something went wrong!!');
        }

        Record::create([
            'user_id' => $user->id,
            'designation' => 'Technical Administrator',
            'department_id' => $department->id,
            'staffId' => 'ADMIN01',
            'gradeLevel' => $level->id,
            'mobile' => '09093479079',
            'location' => 'Abuja FCT',
            'dob' => Carbon::parse(Carbon::now()),
            'date_joined' => Carbon::parse(Carbon::now()),
            'type' => 'permanent',
            'status' => 'in-service'
        ]);

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

    protected function addAdminRecord()
    {
        return User::create([
            'firstname' => 'Super',
            'middlename' => 'Technical',
            'surname' => 'Administrator',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'isAdministrator' => true
        ]);
    }

    protected function addGradeLevel()
    {
        return GradeLevel::create([
            'name' => 'Admin Level',
            'key' => 'AD01'
        ]);
    }

    protected function addOrganization()
    {

    }
}
