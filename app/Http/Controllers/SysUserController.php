<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class SysUserController extends BaseController
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    #[OA\Get(
        path: '/api/sys-users',
        summary: 'Daftar User (Paginated)',
        description: 'Mengambil daftar seluruh user dengan pagination.',
        security: [['sanctum' => []]],
        tags: ['System User'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'), description: 'Halaman aktif'),
            new OA\Parameter(name: 'pagesize', in: 'query', required: false, schema: new OA\Schema(type: 'integer'), description: 'Jumlah data per halaman'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Berhasil', content: new OA\JsonContent(properties: [new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'object'))])),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        return parent::index($request);
    }

    #[OA\Get(
        path: '/api/sys-users/{id}',
        summary: 'Detail User',
        description: 'Menampilkan detail satu user berdasarkan ID.',
        security: [['sanctum' => []]],
        tags: ['System User'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'ID User'),
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
        path: '/api/sys-users',
        summary: 'Tambah User Baru',
        description: 'Membuat user baru.',
        security: [['sanctum' => []]],
        tags: ['System User'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Jane Doe'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'jane@example.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'password123'),
                    new OA\Property(property: 'telegram_number', type: 'string', example: '08123456789'),
                    new OA\Property(property: 'id_sys_group', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Berhasil dibuat', content: new OA\JsonContent(properties: [new OA\Property(property: 'data', type: 'object'), new OA\Property(property: 'message', type: 'string')])),
            new OA\Response(response: 422, description: 'Validasi gagal'),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $request->validate($this->model->rules);
        
        $data = $request->all();
        
        if ($request->has('password')) {
            $data['password'] = bcrypt($request->password);
        }
        
        return \DB::transaction(function() use ($data, $request) {
            $record = $this->model->create($data);
            
            if ($request->has('id_sys_group')) {
                \App\Models\SysUserGroups::create([
                    'id_user' => $record->id_user,
                    'id_sys_group' => $request->id_sys_group,
                    'id_organization' => $record->id_organization
                ]);
            }
            
            return $this->respondCreated($record, 'data created');
        });
    }

    #[OA\Put(
        path: '/api/sys-users/{id}',
        summary: 'Update User',
        description: 'Memperbarui data user yang sudah ada.',
        security: [['sanctum' => []]],
        tags: ['System User'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'ID User'),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(properties: [
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'email', type: 'string'),
                new OA\Property(property: 'password', type: 'string'),
                new OA\Property(property: 'telegram_number', type: 'string'),
                new OA\Property(property: 'id_sys_group', type: 'integer'),
            ])
        ),
        responses: [
            new OA\Response(response: 200, description: 'Data berhasil diupdate', content: new OA\JsonContent(properties: [new OA\Property(property: 'data', type: 'object'), new OA\Property(property: 'message', type: 'string')])),
            new OA\Response(response: 404, description: 'Data tidak ditemukan'),
            new OA\Response(response: 422, description: 'Validasi gagal'),
        ]
    )]
    public function update($id = null, Request $request): JsonResponse
    {
        $request->validate($this->model->getUpdateRules($id));
        
        if (!$record = $this->model->find($id)) {
            return $this->failNotFound(sprintf('item with id %d not found', $id));
        }

        $data = $request->only(['name', 'email', 'telegram_number']);
        
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        return \DB::transaction(function() use ($record, $data, $request) {
            $record->update($data);

            if ($request->has('id_sys_group')) {
                \App\Models\SysUserGroups::updateOrCreate(
                    ['id_user' => $record->id_user],
                    [
                        'id_sys_group' => $request->id_sys_group,
                        'id_organization' => $record->id_organization
                    ]
                );
            }

            return $this->respond($record, 200, 'data updated');
        });
    }

    #[OA\Delete(
        path: '/api/sys-users/{id}',
        summary: 'Hapus User',
        description: 'Menghapus user berdasarkan ID.',
        security: [['sanctum' => []]],
        tags: ['System User'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'ID User'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Data berhasil dihapus', content: new OA\JsonContent(properties: [new OA\Property(property: 'data', type: 'object'), new OA\Property(property: 'message', type: 'string')])),
            new OA\Response(response: 404, description: 'Data tidak ditemukan'),
        ]
    )]
    public function destroy($id = null): JsonResponse
    {
        return parent::destroy($id);
    }
}
