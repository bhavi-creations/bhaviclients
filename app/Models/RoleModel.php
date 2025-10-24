<?php namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    // The database table the model primarily uses.
    protected $table = 'roles';
    // The name of the primary key field.
    protected $primaryKey = 'id';

    // The type of result to return (e.g., array, object, custom).
    protected $returnType = 'array';
    // Whether to use soft deletes.
    protected $useSoftDeletes = false;

    // Fields that can be set during insertion or update.
    protected $allowedFields = ['name', 'description']; // <-- 'description' must be included here

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'name'        => 'required|min_length[3]|max_length[100]',
        'description' => 'permit_empty|max_length[255]',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
