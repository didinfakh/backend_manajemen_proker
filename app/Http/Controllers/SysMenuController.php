<?php

namespace App\Http\Controllers;

use App\Models\SysMenu;
use App\Models\SysMenuPermission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SysMenuController extends BaseController
{
    protected $model;

    public function __construct(SysMenu $model)
    {
        $this->model = $model;
    }

    /**
     * Create a new resource object, from "posted" parameters
     * Custom override to also create default permissions 1, 2, 3, 4
     *
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $rules = property_exists($this->model, 'rules') ? $this->model->rules : [];
        if (!empty($rules)) {
            $request->validate($rules);
        }

        $data = $request->all();

        // Menggunakan create agar event booting (seperti otomatis set id_organization) tertrigger
        $modelInstance = $this->model->create($data);
        $id = $modelInstance->getKey();
        $primaryKey = $this->model->getKeyName();
        $data[$primaryKey] = $id;

        // Otomatis menambahkan permissions 1, 2, 3, 4
        $permissions = [1, 2, 3, 4];
        foreach ($permissions as $permissionId) {
            SysMenuPermission::create([
                'id_sys_menu' => $id,
                'id_sys_permission' => $permissionId,
            ]);
        }

        return $this->respondCreated($data, 'data created');
    }

    /**
     * Tampilkan detail sebuah menu
     *
     * @param mixed $id
     * @return JsonResponse
     */
    public function show($id = null): JsonResponse
    {
        $record = $this->model->find($id);
        if (!$record) {
            return $this->failNotFound(sprintf('item with id %d not found', $id));
        }

        return $this->respond($record);
    }

    /**
     * Update data menu, dari properties "posted"
     *
     * @param mixed $id
     * @param Request $request
     * @return JsonResponse
     */
    public function update($id = null, Request $request): JsonResponse
    {
        $rules = property_exists($this->model, 'rules') ? $this->model->rules : [];
        if (!empty($rules)) {
            $request->validate($rules);
        }

        if (!$data_before = $this->model->find($id)) {
            return $this->failNotFound(sprintf('item with id %d not found', $id));
        }

        $updateData = $request->all();
        
        // Kita menggunakan fungsi update bawaan BaseModel/Model (model eloquent)
        // atau update yang ada dari extend jika ada. BaseController melakukan modifikasi data_before.
        // Agar konsisten, kita assign dan save:
        $data_before->fill($updateData);
        $data_before->save();

        return $this->respond($data_before->toArray(), 200, 'data updated');
    }

    /**
     * Hapus sebuah menu berdasarkan id
     *
     * @param mixed $id
     * @return JsonResponse
     */
    public function destroy($id = null): JsonResponse
    {
        $model = $this->model->find($id);

        if (!$model) {
            return $this->failNotFound(sprintf('item with id %d not found', $id));
        }

        // Lakukan logging apabila method tersebut ada
        if (method_exists($this->model, 'logging')) {
            $this->model->logging(array(
                "action" => "delete",
                "table_name" => $this->model->getTable(),
                "activity" => "Menghapus data",
                "data" => $model->toArray()
            ));
        }

        $ret = $model->delete();
        if (!$ret) {
            return $this->failNotFound(sprintf('item with id %d not found or already deleted', $id));
        }

        // Hapus juga permission yang berkaitan menggunakan observer sebenarnya bagus, namun dilakukan manual saja (bila belum ditangani foreign key onDelete cascade):
        \App\Models\SysMenuPermission::where('id_sys_menu', $id)->delete();

        return $this->respondDeleted(['id' => $id], 'data deleted');
    }
}