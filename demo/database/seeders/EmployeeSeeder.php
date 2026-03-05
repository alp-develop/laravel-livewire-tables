<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $firstNames = [
            'James',
            'Maria',
            'Carlos',
            'Sarah',
            'David',
            'Emily',
            'Michael',
            'Sofia',
            'Daniel',
            'Jessica',
            'Lucas',
            'Emma',
            'Matthew',
            'Olivia',
            'Chris',
            'Isabella',
            'Andrew',
            'Mia',
            'Ryan',
            'Charlotte',
            'Nathan',
            'Amelia',
            'Kevin',
            'Harper',
            'Tyler',
            'Evelyn',
            'Brandon',
            'Abigail',
            'Justin',
            'Luna',
            'Austin',
            'Ella',
            'Eric',
            'Grace',
            'Adam',
            'Chloe',
            'Pablo',
            'Nadia',
            'Marcus',
            'Camila',
        ];

        $lastNames = [
            'Smith',
            'Johnson',
            'Williams',
            'Brown',
            'Jones',
            'Garcia',
            'Miller',
            'Davis',
            'Martinez',
            'Hernandez',
            'Lopez',
            'Wilson',
            'Anderson',
            'Thomas',
            'Taylor',
            'Moore',
            'Jackson',
            'Martin',
            'Lee',
            'Perez',
            'Thompson',
            'White',
            'Harris',
            'Sanchez',
            'Clark',
            'Ramirez',
            'Lewis',
            'Robinson',
            'Walker',
            'Young',
            'Allen',
            'King',
            'Wright',
            'Scott',
            'Torres',
            'Nguyen',
            'Hill',
            'Adams',
            'Baker',
            'Nelson',
        ];

        $departments = ['Engineering', 'Marketing', 'Sales', 'Finance', 'HR', 'Operations', 'Design', 'Legal', 'Support', 'Product'];

        $positionsByDept = [
            'Engineering' => ['Software Engineer', 'Senior Engineer', 'Tech Lead', 'Backend Developer', 'Frontend Developer', 'DevOps Engineer'],
            'Marketing' => ['Marketing Specialist', 'Content Manager', 'SEO Analyst', 'Brand Manager', 'Campaign Manager', 'Social Media Manager'],
            'Sales' => ['Sales Representative', 'Account Executive', 'Sales Manager', 'Business Developer', 'Territory Manager', 'Sales Analyst'],
            'Finance' => ['Financial Analyst', 'Accountant', 'Controller', 'Treasury Analyst', 'Audit Manager', 'CFO Assistant'],
            'HR' => ['HR Specialist', 'Recruiter', 'HR Manager', 'People Operations', 'Talent Acquisition', 'HR Business Partner'],
            'Operations' => ['Operations Analyst', 'Project Manager', 'Supply Chain Manager', 'Logistics Coordinator', 'Process Analyst', 'COO Assistant'],
            'Design' => ['UI Designer', 'UX Researcher', 'Product Designer', 'Graphic Designer', 'Motion Designer', 'Design Lead'],
            'Legal' => ['Legal Counsel', 'Compliance Officer', 'Contract Manager', 'Paralegal', 'Legal Analyst', 'General Counsel'],
            'Support' => ['Support Specialist', 'Customer Success', 'Technical Support', 'Support Manager', 'QA Engineer', 'Documentation Writer'],
            'Product' => ['Product Manager', 'Product Owner', 'Product Analyst', 'VP of Product', 'Growth Manager', 'Strategy Manager'],
        ];

        $salaryRangeByDept = [
            'Engineering' => [75000, 175000],
            'Marketing' => [50000, 110000],
            'Sales' => [45000, 130000],
            'Finance' => [60000, 140000],
            'HR' => [45000, 100000],
            'Operations' => [50000, 115000],
            'Design' => [55000, 120000],
            'Legal' => [70000, 180000],
            'Support' => [40000, 90000],
            'Product' => [80000, 170000],
        ];

        $statuses = ['active', 'active', 'active', 'inactive'];
        $startYear = 2016;
        $now = now();
        $rows = [];

        for ($i = 1; $i <= 1000; $i++) {
            $firstName = $firstNames[($i * 7 + 3) % count($firstNames)];
            $lastName = $lastNames[($i * 13 + 5) % count($lastNames)];
            $department = $departments[$i % count($departments)];
            $positions = $positionsByDept[$department];
            $position = $positions[$i % count($positions)];
            [$minS, $maxS] = $salaryRangeByDept[$department];
            $salary = round($minS + (($i * 97 + 41) % ($maxS - $minS)), -2);
            $status = $statuses[$i % count($statuses)];
            $year = $startYear + ($i % 8);
            $month = ($i % 12) + 1;
            $day = ($i % 28) + 1;
            $hireDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $emailLocal = strtolower($firstName.'.'.$lastName.$i);

            $rows[] = [
                'name' => $firstName.' '.$lastName,
                'email' => $emailLocal.'@company.demo',
                'department' => $department,
                'position' => $position,
                'salary' => $salary,
                'status' => $status,
                'hire_date' => $hireDate,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($rows) === 100) {
                DB::table('employees')->insert($rows);
                $rows = [];
            }
        }

        if (! empty($rows)) {
            DB::table('employees')->insert($rows);
        }
    }
}
