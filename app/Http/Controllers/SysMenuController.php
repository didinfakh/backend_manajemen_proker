<?php

namespace App\Http\Controllers;

use App\Models\SysMenu;
use App\Models\SysMenuPermission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class SysMenuController extends BaseController
{
    protected $model;

    public function __construct(SysMenu $model)
    {
        $this->model = $model;
    }

    #[OA\Get(
        path: '/api/sys-menus',
        summary: 'Daftar Menu (Paginated)',
        description: 'Mengambil daftar seluruh menu sistem secara paginasi. Mendukung pencarian dan sorting.',
        security: [['sanctum' => []]],
        tags: ['Sys Menu'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'), description: 'Halaman aktif'),
            new OA\Parameter(name: 'pagesize', in: 'query', required: false, schema: new OA\Schema(type: 'integer'), description: 'Jumlah data per halaman'),
            new OA\Parameter(name: 'order', in: 'query', required: false, schema: new OA\Schema(type: 'string'), description: 'Urutan data (kolom asc/desc)'),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Berhasil',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'object')),
                        new OA\Property(property: 'page', type: 'integer'),
                        new OA\Property(property: 'page_size', type: 'integer'),
                        new OA\Property(property: 'total_page', type: 'integer'),
                        new OA\Property(property: 'total_records', type: 'integer'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        return parent::index($request);
    }

    #[OA\Get(
        path: '/api/sys-menus/tree',
        summary: 'Daftar Menu (Tree)',
        description: 'Mengambil daftar seluruh menu sistem dalam format tree (parent-children).',
        security: [['sanctum' => []]],
        tags: ['Sys Menu'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Berhasil',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'object', properties: [
                            new OA\Property(property: 'id_sys_menu', type: 'integer'),
                            new OA\Property(property: 'name', type: 'string'),
                            new OA\Property(property: 'url', type: 'string'),
                            new OA\Property(property: 'id_menu_parent', type: 'integer', nullable: true),
                            new OA\Property(property: 'children', type: 'array', items: new OA\Items(type: 'object'))
                        ])),
                        new OA\Property(property: 'success', type: 'boolean'),
                        new OA\Property(property: 'message', type: 'string', nullable: true),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function getTree(): JsonResponse
    {
        $menus = SysMenu::orderBy('menu_order', 'asc')->get()->toArray();
        $tree = $this->buildTree($menus);
        return $this->respond($tree);
    }

    private function buildTree(array $elements, $parentId = null): array
    {
        $branch = [];

        foreach ($elements as $element) {
            if ($element['id_menu_parent'] == $parentId) {
                $children = $this->buildTree($elements, $element['id_sys_menu']);
                $element['children'] = $children;
                $branch[] = $element;
            }
        }

        return $branch;
    }

    #[OA\Post(
        path: '/api/sys-menus',
        summary: 'Tambah Menu Baru',
        description: 'Membuat menu baru dan otomatis menambahkan permission default (index, create, update, delete).',
        security: [['sanctum' => []]],
        tags: ['Sys Menu'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'url'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Laporan'),
                    new OA\Property(property: 'url', type: 'string', example: '/laporan'),
                    new OA\Property(property: 'description', type: 'string', example: 'Menu Laporan'),
                    new OA\Property(property: 'icon', type: 'string', example: '📊'),
                    new OA\Property(property: 'menu_order', type: 'integer', example: 10),
                    new OA\Property(property: 'visible', type: 'boolean', example: true),
                    new OA\Property(property: 'id_menu_parent', type: 'integer', example: null, nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Menu berhasil dibuat',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'object'),
                        new OA\Property(property: 'message', type: 'string', example: 'data created'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validasi gagal'),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $rules = property_exists($this->model, 'rules') ? $this->model->rules : [];
        if (!empty($rules)) {
            $request->validate($rules);
        }

        $data = $request->all();

        $modelInstance = $this->model->create($data);
        $id = $modelInstance->getKey();
        $primaryKey = $this->model->getKeyName();
        $data[$primaryKey] = $id;

        $permissions = [1, 2, 3, 4];
        foreach ($permissions as $permissionId) {
            SysMenuPermission::create([
                'id_sys_menu' => $id,
                'id_sys_permission' => $permissionId,
            ]);
        }

        return $this->respondCreated($data, 'data created');
    }

    #[OA\Get(
        path: '/api/sys-menus/{id}',
        summary: 'Detail Menu',
        description: 'Menampilkan detail satu menu berdasarkan ID.',
        security: [['sanctum' => []]],
        tags: ['Sys Menu'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'ID Menu'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Berhasil', content: new OA\JsonContent(properties: [new OA\Property(property: 'data', type: 'object')])),
            new OA\Response(response: 404, description: 'Data tidak ditemukan'),
        ]
    )]
    public function show($id = null): JsonResponse
    {
        $record = $this->model->find($id);
        if (!$record) {
            return $this->failNotFound(sprintf('item with id %d not found', $id));
        }
        return $this->respond($record);
    }

    #[OA\Put(
        path: '/api/sys-menus/{id}',
        summary: 'Update Menu',
        description: 'Memperbarui data menu yang sudah ada.',
        security: [['sanctum' => []]],
        tags: ['Sys Menu'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'ID Menu'),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Laporan Baru'),
                    new OA\Property(property: 'url', type: 'string', example: '/laporan-baru'),
                    new OA\Property(property: 'icon', type: 'string', example: '📈'),
                    new OA\Property(property: 'menu_order', type: 'integer', example: 15),
                    new OA\Property(property: 'visible', type: 'boolean', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Data berhasil diupdate', content: new OA\JsonContent(properties: [new OA\Property(property: 'data', type: 'object'), new OA\Property(property: 'message', type: 'string', example: 'data updated')])),
            new OA\Response(response: 404, description: 'Data tidak ditemukan'),
        ]
    )]
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
        $data_before->fill($updateData);
        $data_before->save();

        return $this->respond($data_before->toArray(), 200, 'data updated');
    }

    #[OA\Delete(
        path: '/api/sys-menus/{id}',
        summary: 'Hapus Menu',
        description: 'Menghapus menu beserta mapping permission yang terkait.',
        security: [['sanctum' => []]],
        tags: ['Sys Menu'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'ID Menu'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Data berhasil dihapus', content: new OA\JsonContent(properties: [new OA\Property(property: 'data', type: 'object'), new OA\Property(property: 'message', type: 'string', example: 'data deleted')])),
            new OA\Response(response: 404, description: 'Data tidak ditemukan'),
        ]
    )]
    public function destroy($id = null): JsonResponse
    {
        $model = $this->model->find($id);
        if (!$model) {
            return $this->failNotFound(sprintf('item with id %d not found', $id));
        }

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

        \App\Models\SysMenuPermission::where('id_sys_menu', $id)->delete();

        return $this->respondDeleted(['id' => $id], 'data deleted');
    }
}