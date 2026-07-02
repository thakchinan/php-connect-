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

        // 4. Create Mock Employees and their Users
        $employeesData = [
            // HR
            [
                'first_name' => 'ปิยมาศ',
                'last_name' => 'วงษ์สุวรรณ',
                'email' => 'piyamas@scg.com',
                'phone' => '0812345678',
                'date_of_birth' => '1995-04-12',
                'join_date' => '2021-06-01',
                'department_id' => 1,
                'position_id' => 1, // HR Specialist
                'status' => 'active',
                'performance_score' => 88
            ],
            [
                'first_name' => 'กิตติพงษ์',
                'last_name' => 'ศรีสวัสดิ์',
                'email' => 'kittipong@scg.com',
                'phone' => '0823456789',
                'date_of_birth' => '1998-09-22',
                'join_date' => '2023-01-15',
                'department_id' => 1,
                'position_id' => 2, // Recruitment Officer
                'status' => 'active',
                'performance_score' => 82
            ],
            
            // IT
            [
                'first_name' => 'ทักชินัน',
                'last_name' => 'ปักสงค์',
                'email' => 'thakchinan@scg.com',
                'phone' => '0834567890',
                'date_of_birth' => '2004-10-10',
                'join_date' => '2026-09-14', // Co-op internship date
                'department_id' => 2,
                'position_id' => 6, // Co-op Web Developer (IT)
                'status' => 'active',
                'performance_score' => 95
            ],
            [
                'first_name' => 'กิตติศักดิ์',
                'last_name' => 'มณีมั่งคั่ง',
                'email' => 'kittisak@scg.com',
                'phone' => '0845678901',
                'date_of_birth' => '2004-03-15',
                'join_date' => '2026-09-14',
                'department_id' => 2,
                'position_id' => 6, // Co-op Web Developer (IT)
                'status' => 'active',
                'performance_score' => 92
            ],
            [
                'first_name' => 'ต้นตระกูล',
                'last_name' => 'สุทธิเกิด',
                'email' => 'tontragoon@scg.com',
                'phone' => '0856789012',
                'date_of_birth' => '2004-07-20',
                'join_date' => '2026-09-14',
                'department_id' => 2,
                'position_id' => 6, // Co-op Web Developer (IT)
                'status' => 'active',
                'performance_score' => 90
            ],
            [
                'first_name' => 'อภิชาต',
                'last_name' => 'รักการงาน',
                'email' => 'apichat@scg.com',
                'phone' => '0867890123',
                'date_of_birth' => '1990-11-30',
                'join_date' => '2018-03-01',
                'department_id' => 2,
                'position_id' => 4, // Senior Developer
                'status' => 'active',
                'performance_score' => 91
            ],
            
            // ENG
            [
                'first_name' => 'สมศักดิ์',
                'last_name' => 'ใจดี',
                'email' => 'somsak@scg.com',
                'phone' => '0878901234',
                'date_of_birth' => '1992-05-18',
                'join_date' => '2020-08-10',
                'department_id' => 3,
                'position_id' => 8, // Process Engineer
                'status' => 'active',
                'performance_score' => 85
            ],
            [
                'first_name' => 'ณัฐธิดา',
                'last_name' => 'แสงจันทร์',
                'email' => 'nutthida@scg.com',
                'phone' => '0889012345',
                'date_of_birth' => '1996-02-14',
                'join_date' => '2022-11-01',
                'department_id' => 3,
                'position_id' => 10, // Safety Officer
                'status' => 'leave',
                'performance_score' => 79
            ],

            // Sales & Marketing
            [
                'first_name' => 'พงศกร',
                'last_name' => 'เลิศรัตนชัย',
                'email' => 'pongsakorn@scg.com',
                'phone' => '0890123456',
                'date_of_birth' => '1988-08-08',
                'join_date' => '2016-05-01',
                'department_id' => 4,
                'position_id' => 11, // Marketing Manager
                'status' => 'active',
                'performance_score' => 94
            ],
        ];

        $createdEmployees = [];
        foreach ($employeesData as $empData) {
            $user = User::create([
                'name' => $empData['first_name'] . ' ' . $empData['last_name'],
                'email' => $empData['email'],
                'password' => Hash::make('password123'),
            ]);

            $createdEmployees[] = Employee::create([
                'user_id' => $user->id,
                'first_name' => $empData['first_name'],
                'last_name' => $empData['last_name'],
                'phone' => $empData['phone'],
                'date_of_birth' => $empData['date_of_birth'],
                'join_date' => $empData['join_date'],
                'department_id' => $empData['department_id'],
                'position_id' => $empData['position_id'],
                'status' => $empData['status'],
                'performance_score' => $empData['performance_score'],
            ]);
        }

        // 5. Create Leave Requests
        $leaves = [
            [
                'employee_id' => 1, // ปิยมาศ
                'type' => 'ลาป่วย (Sick Leave)',
                'start_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'end_date' => Carbon::now()->subDays(4)->format('Y-m-d'),
                'reason' => 'มีไข้สูงและปวดศีรษะ แพทย์สั่งให้หยุดพักผ่อน 2 วัน',
                'status' => 'approved',
            ],
            [
                'employee_id' => 8, // ณัฐธิดา
                'type' => 'ลากิจ (Personal Leave)',
                'start_date' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'end_date' => Carbon::now()->addDays(4)->format('Y-m-d'),
                'reason' => 'ทำธุระเรื่องเอกสารที่ต่างจังหวัด',
                'status' => 'pending',
            ],
            [
                'employee_id' => 3, // ทักชินัน (Co-op student)
                'type' => 'ลากิจ (Personal Leave)',
                'start_date' => Carbon::now()->addDays(7)->format('Y-m-d'),
                'end_date' => Carbon::now()->addDays(7)->format('Y-m-d'),
                'reason' => 'ติดต่อธุระสำคัญที่มหาวิทยาลัยวลัยลักษณ์',
                'status' => 'pending',
            ],
        ];

        foreach ($leaves as $lv) {
            Leave::create($lv);
        }
    }
}
