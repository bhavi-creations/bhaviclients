<?php
// C:\xampp\htdocs\bhaviclients\app\Models\EmployeeModel.php
namespace App\Models;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table = 'employees';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'employee_code',
        'user_id',
        'department_id',
        'role_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'parent_name',
        'parent_phone',
        'date_of_joining',
        'status',
        'file_uploads',
        'remarks'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // DISABLE MODEL-LEVEL VALIDATION (We'll validate in controller)
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    /**
     * Get all employees with current salary (NO DUPLICATES)
     */
    public function getAllEmployeesWithSalary()
    {
        return $this->select('employees.*, departments.name AS department_name, roles.name AS role_name')
            ->select('(SELECT salary_amount FROM employee_salary_history WHERE employee_id = employees.id ORDER BY effective_date DESC, id DESC LIMIT 1) as current_salary')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->join('roles', 'roles.id = employees.role_id', 'left')
            ->groupBy('employees.id')
            ->orderBy('employees.id', 'DESC')
            ->findAll();
    }

    /**
     * Get employee with current salary (NO DUPLICATES)
     */
    public function getEmployeeWithSalary($id)
    {
        return $this->select('employees.*, departments.name AS department_name, roles.name AS role_name')
            ->select('(SELECT salary_amount FROM employee_salary_history WHERE employee_id = employees.id ORDER BY effective_date DESC, id DESC LIMIT 1) as current_salary')
            ->select('(SELECT effective_date FROM employee_salary_history WHERE employee_id = employees.id ORDER BY effective_date DESC, id DESC LIMIT 1) as salary_effective_date')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->join('roles', 'roles.id = employees.role_id', 'left')
            ->where('employees.id', $id)
            ->first();
    }


    /**
     * Get employee files as array
     */
    public function getEmployeeFiles($id)
    {
        $employee = $this->find($id);
        if ($employee && !empty($employee['file_uploads'])) {
            return json_decode($employee['file_uploads'], true);
        }
        return [];
    }

    /**
     * Add file to employee
     */
    public function addFile($id, $fileName)
    {
        $files = $this->getEmployeeFiles($id);
        $files[] = $fileName;

        return $this->update($id, [
            'file_uploads' => json_encode($files)
        ]);
    }

    /**
     * Remove file from employee
     */
    public function removeFile($id, $fileName)
    {
        $files = $this->getEmployeeFiles($id);
        $files = array_values(array_filter($files, function ($file) use ($fileName) {
            return $file !== $fileName;
        }));

        return $this->update($id, [
            'file_uploads' => json_encode($files)
        ]);
    }
}
