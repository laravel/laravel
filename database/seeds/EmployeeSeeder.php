<?php

use Illuminate\Database\Seeder;
use App\Employee;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employees = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@company.com',
                'position' => 'Software Developer',
                'department' => 'IT',
                'salary' => 8000000,
                'hire_date' => '2023-01-15',
                'phone' => '081234567890',
                'address' => 'Jl. Sudirman No. 123, Jakarta',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@company.com',
                'position' => 'HR Manager',
                'department' => 'HR',
                'salary' => 12000000,
                'hire_date' => '2022-06-01',
                'phone' => '081234567891',
                'address' => 'Jl. Thamrin No. 456, Jakarta',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike.johnson@company.com',
                'position' => 'Financial Analyst',
                'department' => 'Finance',
                'salary' => 9500000,
                'hire_date' => '2023-03-10',
                'phone' => '081234567892',
                'address' => 'Jl. Gatot Subroto No. 789, Jakarta',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Sarah Wilson',
                'email' => 'sarah.wilson@company.com',
                'position' => 'Marketing Specialist',
                'department' => 'Marketing',
                'salary' => 7500000,
                'hire_date' => '2023-07-20',
                'phone' => '081234567893',
                'address' => 'Jl. Kuningan No. 101, Jakarta',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'David Brown',
                'email' => 'david.brown@company.com',
                'position' => 'Operations Manager',
                'department' => 'Operations',
                'salary' => 11000000,
                'hire_date' => '2022-11-05',
                'phone' => '081234567894',
                'address' => 'Jl. Casablanca No. 202, Jakarta',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.davis@company.com',
                'position' => 'System Administrator',
                'department' => 'IT',
                'salary' => 8500000,
                'hire_date' => '2023-02-28',
                'phone' => '081234567895',
                'address' => 'Jl. Menteng No. 303, Jakarta',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Robert Taylor',
                'email' => 'robert.taylor@company.com',
                'position' => 'Accountant',
                'department' => 'Finance',
                'salary' => 7000000,
                'hire_date' => '2023-05-15',
                'phone' => '081234567896',
                'address' => 'Jl. Kemang No. 404, Jakarta',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Lisa Anderson',
                'email' => 'lisa.anderson@company.com',
                'position' => 'Digital Marketing Specialist',
                'department' => 'Marketing',
                'salary' => 7800000,
                'hire_date' => '2023-04-12',
                'phone' => '081234567897',
                'address' => 'Jl. Pondok Indah No. 505, Jakarta',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Kevin Martinez',
                'email' => 'kevin.martinez@company.com',
                'position' => 'Quality Control',
                'department' => 'Operations',
                'salary' => 6500000,
                'hire_date' => '2023-08-01',
                'phone' => '081234567898',
                'address' => 'Jl. Kelapa Gading No. 606, Jakarta',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Amanda Garcia',
                'email' => 'amanda.garcia@company.com',
                'position' => 'HR Specialist',
                'department' => 'HR',
                'salary' => 6800000,
                'hire_date' => '2023-06-18',
                'phone' => '081234567899',
                'address' => 'Jl. Bintaro No. 707, Jakarta',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }
    }
}
