<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employees';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'position',
        'department',
        'salary',
        'hire_date',
        'phone',
        'address',
        'status'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'salary' => 'decimal:2',
        'hire_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the formatted salary.
     *
     * @return string
     */
    public function getFormattedSalaryAttribute()
    {
        return 'Rp ' . number_format($this->salary, 0, ',', '.');
    }

    /**
     * Get the formatted hire date.
     *
     * @return string
     */
    public function getFormattedHireDateAttribute()
    {
        return $this->hire_date ? $this->hire_date->format('d F Y') : '';
    }

    /**
     * Get the employee's full info for display.
     *
     * @return string
     */
    public function getDisplayInfoAttribute()
    {
        return $this->name . ' - ' . $this->position . ' (' . $this->department . ')';
    }

    /**
     * Scope a query to only include employees from a specific department.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $department
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Scope a query to only include active employees.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include employees hired in a specific year.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $year
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHiredInYear($query, $year)
    {
        return $query->whereYear('hire_date', $year);
    }

    /**
     * Scope a query to order employees by hire date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByHireDate($query, $direction = 'asc')
    {
        return $query->orderBy('hire_date', $direction);
    }

    /**
     * Get all available departments.
     *
     * @return array
     */
    public static function getDepartments()
    {
        return [
            'IT' => 'Information Technology',
            'HR' => 'Human Resources',
            'Finance' => 'Finance',
            'Marketing' => 'Marketing',
            'Operations' => 'Operations'
        ];
    }

    /**
     * Get all available positions by department.
     *
     * @return array
     */
    public static function getPositionsByDepartment()
    {
        return [
            'IT' => [
                'Software Developer',
                'System Administrator',
                'Database Administrator',
                'IT Manager',
                'Network Engineer',
                'DevOps Engineer',
                'Quality Assurance'
            ],
            'HR' => [
                'HR Manager',
                'HR Specialist',
                'Recruiter',
                'Training Coordinator',
                'Compensation Analyst'
            ],
            'Finance' => [
                'Financial Analyst',
                'Accountant',
                'Finance Manager',
                'Budget Analyst',
                'Payroll Specialist'
            ],
            'Marketing' => [
                'Marketing Manager',
                'Marketing Specialist',
                'Digital Marketing Specialist',
                'Content Creator',
                'Brand Manager'
            ],
            'Operations' => [
                'Operations Manager',
                'Operations Specialist',
                'Supply Chain Coordinator',
                'Quality Control',
                'Process Improvement Specialist'
            ]
        ];
    }

    /**
     * Calculate years of service.
     *
     * @return int
     */
    public function getYearsOfServiceAttribute()
    {
        if (!$this->hire_date) {
            return 0;
        }
        
        return $this->hire_date->diffInYears(now());
    }

    /**
     * Calculate months of service.
     *
     * @return int
     */
    public function getMonthsOfServiceAttribute()
    {
        if (!$this->hire_date) {
            return 0;
        }
        
        return $this->hire_date->diffInMonths(now());
    }

    /**
     * Get the age category based on years of service.
     *
     * @return string
     */
    public function getServiceCategoryAttribute()
    {
        $years = $this->years_of_service;
        
        if ($years < 1) {
            return 'New Employee';
        } elseif ($years < 3) {
            return 'Junior Employee';
        } elseif ($years < 7) {
            return 'Senior Employee';
        } else {
            return 'Veteran Employee';
        }
    }

    /**
     * Calculate monthly salary.
     *
     * @return float
     */
    public function getMonthlySalaryAttribute()
    {
        return $this->salary;
    }

    /**
     * Calculate annual salary.
     *
     * @return float
     */
    public function getAnnualSalaryAttribute()
    {
        return $this->salary * 12;
    }

    /**
     * Validate email uniqueness.
     *
     * @param  string  $email
     * @param  int|null  $excludeId
     * @return bool
     */
    public static function isEmailUnique($email, $excludeId = null)
    {
        $query = static::where('email', $email);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->count() === 0;
    }

    /**
     * Get employees statistics.
     *
     * @return array
     */
    public static function getStatistics()
    {
        $employees = static::all();
        
        return [
            'total_employees' => $employees->count(),
            'departments' => $employees->groupBy('department')->map->count(),
            'average_salary' => $employees->avg('salary'),
            'total_salary_expense' => $employees->sum('salary'),
            'newest_employee' => $employees->sortByDesc('hire_date')->first(),
            'oldest_employee' => $employees->sortBy('hire_date')->first(),
            'by_department' => $employees->groupBy('department'),
            'by_years_of_service' => $employees->groupBy('service_category')
        ];
    }
}
