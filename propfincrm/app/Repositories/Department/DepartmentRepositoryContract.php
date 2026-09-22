<?php
namespace App\Repositories\Department;

interface DepartmentRepositoryContract
{
    public function getAllDepartments();
    
    public function listAllDepartments();

    public function create($requestData);

    public function find($id);

    public function update($id, $requestData);

    public function destroy($id);
}
