<?php

namespace App\Http\Controllers;

use App\Models\SysGroup;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class SysGroupController extends BaseController
{
    protected $model;

    public function __construct(SysGroup $model)
    {
        $this->model = $model;
    }

    #[OA\Get(
        path: '/api/sys-groups',
        summary: 'Daftar Grup / Role (Paginated)',
        description: 'Mengambil daftar seluruh grup pengguna dengan pagination.',
        security: [['sanctum' => []]],
        tags: ['Sys Groups'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'), description: 'Halaman aktif'),
            new OA\Parameter(name: 'pagesize', in: 'query', required: false, schema: new OA\Schema(type: 'integer'), description: 'Jumlah data per halaman'),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Berhasil',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'id_sys_group', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Super Admin'),
                                new OA\Property(property: 'description', type: 'string', example: 'Administrator utama'),
                            ]
                        )),
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
        path: '/api/sys-groups/{id}',
        summary: 'Detail Grup',
        description: 'Menampilkan detail satu grup berdasarkan ID.',
        security: [['sanctum' => []]],
        tags: ['Sys Groups'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'ID Grup'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Berhasil', content: new OA\JsonContent(properties: [new OA\Property(property: 'data', type: 'object')])),
            new OA\Response(response: 404, description: 'Data tidak ditemukan'),
        ]
    )]
    public function show($id = null): JsonResponse
    {
        return parent::show($id);
    }

    #[OA\Post(
        path: '/api/sys-groups',
        summary: 'Tambah Grup Baru',
        description: 'Membuat grup / role pengguna baru.',
        security: [['sanctum' => []]],
        tags: ['Sys Groups'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Editor'),
                    new OA\Property(property: 'description', type: 'string', example: 'Role untuk editor konten'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Grup berhasil dibuat', content: new OA\JsonContent(properties: [new OA\Property(property: 'data', type: 'object'), new OA\Property(property: 'message', type: 'string', example: 'data created')])),
            new OA\Response(response: 422, description: 'Validasi gagal'),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->model->getStoreRules(), [
            'name.unique' => 'Nama group sudah digunakan pada organisasi ini.',
        ]);

        $record = $this->model->create($validated);

        return $this->respondCreated($record, 'data created');
    }

    #[OA\Put(
        path: '/api/sys-groups/{id}',
        summary: 'Update Grup',
        description: 'Memperbarui data grup yang sudah ada.',
        security: [['sanctum' => []]],
        tags: ['Sys Groups'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'ID Grup'),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Editor Updated'),
                    new OA\Property(property: 'description', type: 'string', example: 'Deskripsi baru'),
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
        if (!$record = $this->model->find($id)) {
            return $this->failNotFound(sprintf('item with id %d not found', $id));
        }

        $validated = $request->validate($this->model->getUpdateRules((int) $id), [
            'name.unique' => 'Nama group sudah digunakan pada organisasi ini.',
        ]);

        $record->update($validated);

        return $this->respond($record->fresh(), 200, 'data updated');
    }

    #[OA\Delete(
        path: '/api/sys-groups/{id}',
        summary: 'Hapus Grup',
        description: 'Menghapus grup berdasarkan ID.',
        security: [['sanctum' => []]],
        tags: ['Sys Groups'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'ID Grup'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Data berhasil dihapus', content: new OA\JsonContent(properties: [new OA\Property(property: 'data', type: 'object'), new OA\Property(property: 'message', type: 'string', example: 'data deleted')])),
            new OA\Response(response: 404, description: 'Data tidak ditemukan'),
        ]
    )]
    public function destroy($id = null): JsonResponse
    {
        $record = $this->model->withCount(['userGroups', 'permissions'])->find($id);

        if (!$record) {
            return $this->failNotFound(sprintf('item with id %d not found', $id));
        }

        if ($record->user_groups_count > 0) {
            return $this->fail('group masih dipakai oleh user dan tidak bisa dihapus', 422);
        }

        if ($record->permissions_count > 0) {
            return $this->fail('group masih memiliki permission dan tidak bisa dihapus', 422);
        }

        $record->delete();

        return $this->respondDeleted(['id' => $id], 'data deleted');
    }
}
