<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use App\Models\Employee;
use App\Models\Leave;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create a Manager User
        $admin = User::create([
            'name' => 'สมชาย มั่นคง (Manager)',
            'email' => 'manager@scg.com',
            'password' => Hash::make('password123'),
        ]);

        // 2. Create Departments
        $depts = [
            [
                'name' => 'HR & Organization Development',
                'description' => 'ดูแลทรัพยากรบุคคล การรับสมัคร และพัฒนาองค์กร',
            ],
            [
                'name' => 'Digital Technology (IT)',
                'description' => 'พัฒนาซอฟต์แวร์ ดูแลโครงสร้างพื้นฐาน และระบบ IT ในองค์กร',
            ],
            [
                'name' => 'Corporate Engineering',
                'description' => 'ฝ่ายวิศวกรรมการผลิตและพัฒนาประสิทธิภาพโรงงาน',
            ],
            [
                'name' => 'Sales & Marketing',
                'description' => 'ฝ่ายขาย การตลาด และลูกค้าสัมพันธ์',
            ],
        ];

        $createdDepts = [];
        foreach ($depts as $dept) {
            $createdDepts[] = Department::create([
                'name' => $dept['name'],
                'description' => $dept['description'],
                'manager_id' => $admin->id,
            ]);
        }

        // 3. Create Positions
        $positions = [
            // HR (Dept 0)
            ['title' => 'HR Specialist', 'department_id' => 1, 'base_salary' => 35000],
            ['title' => 'Recruitment Officer', 'department_id' => 1, 'base_salary' => 28000],
            ['title' => 'Training Developer', 'department_id' => 1, 'base_salary' => 30000],
            
            // IT (Dept 1)
            ['title' => 'Senior Developer', 'department_id' => 2, 'base_salary' => 65000],
            ['title' => 'Junior Developer', 'department_id' => 2, 'base_salary' => 32000],
            ['title' => 'Co-op Web Developer', 'department_id' => 2, 'base_salary' => 15000],
            ['title' => 'System Administrator', 'department_id' => 2, 'base_salary' => 45000],
            
            // ENG (Dept 2)
            ['title' => 'Process Engineer', 'department_id' => 3, 'base_salary' => 48000],
            ['title' => 'Automation Engineer', 'department_id' => 3, 'base_salary' => 52000],
            ['title' => 'Safety Officer', 'department_id' => 3, 'base_salary' => 35000],

            // Sales & Marketing (Dept 3)
            ['title' => 'Marketing Manager', 'department_id' => 4, 'base_salary' => 55000],
            ['title' => 'Brand Executive', 'department_id' => 4, 'base_salary' => 32000],
        ];

        $createdPositions = [];
        foreach ($positions as $pos) {
            $createdPositions[] = Position::create($pos);
        }

        // 4. Create Mock Employees and their Users (Removed to keep database clean)
        // 5. Create Leave Requests (Removed to keep database clean)
    }
}
