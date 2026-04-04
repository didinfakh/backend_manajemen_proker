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
        // Encrypt password if present
        if ($request->has('password')) {
            $request->merge(['password' => bcrypt($request->password)]);
        }
        return parent::store($request);
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
            ])
        ),
        responses: [
            new OA\Response(response: 200, description: 'Data berhasil diupdate', content: new OA\JsonContent(properties: [new OA\Property(property: 'data', type: 'object'), new OA\Property(property: 'message', type: 'string')])),
            new OA\Response(response: 404, description: 'Data tidak ditemukan'),
        ]
    )]
    public function update($id = null, Request $request): JsonResponse
    {
        // Encrypt password if present
        if ($request->has('password') && $request->password != null) {
            $request->merge(['password' => bcrypt($request->password)]);
        }
        return parent::update($id, $request);
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
