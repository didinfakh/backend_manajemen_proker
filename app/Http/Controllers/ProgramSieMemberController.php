<?php

namespace App\Http\Controllers;

use App\Models\ProgramSieMember;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProgramSieMemberController extends BaseController
{
    protected $model;

    public function __construct(ProgramSieMember $model)
    {
        $this->model = $model;
    }

    #[OA\Get(
        path: '/api/program-sie-member',
        summary: 'Daftar Anggota Sie (Paginated)',
        description: 'Mengambil daftar seluruh anggota sie dengan pagination.',
        security: [['sanctum' => []]],
        tags: ['Program Sie Member'],
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
        path: '/api/program-sie-member/{id}',
        summary: 'Detail Anggota Sie',
        description: 'Menampilkan detail satu anggota sie berdasarkan ID.',
        security: [['sanctum' => []]],
        tags: ['Program Sie Member'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'ID Member Sie'),
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
        path: '/api/program-sie-member',
        summary: 'Tambah Anggota Sie Baru',
        description: 'Menambahkan anggota baru ke dalam sie.',
        security: [['sanctum' => []]],
        tags: ['Program Sie Member'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['id_sie', 'id_auth_user'],
                properties: [
                    new OA\Property(property: 'id_sie', type: 'integer', example: 1),
                    new OA\Property(property: 'id_auth_user', type: 'integer', example: 1),
                    new OA\Property(property: 'role', type: 'string', example: 'Koordinator'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Berhasil ditambahkan', content: new OA\JsonContent(properties: [new OA\Property(property: 'data', type: 'object'), new OA\Property(property: 'message', type: 'string')])),
            new OA\Response(response: 422, description: 'Validasi gagal'),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        return parent::store($request);
    }

    #[OA\Put(
        path: '/api/program-sie-member/{id}',
        summary: 'Update Anggota Sie',
        description: 'Memperbarui data anggota sie yang sudah ada.',
        security: [['sanctum' => []]],
        tags: ['Program Sie Member'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'ID Member Sie'),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(properties: [new OA\Property(property: 'role', type: 'string')])
        ),
        responses: [
            new OA\Response(response: 200, description: 'Data berhasil diupdate', content: new OA\JsonContent(properties: [new OA\Property(property: 'data', type: 'object'), new OA\Property(property: 'message', type: 'string')])),
            new OA\Response(response: 404, description: 'Data tidak ditemukan'),
        ]
    )]
    public function update($id = null, Request $request): JsonResponse
    {
        return parent::update($id, $request);
    }

    #[OA\Delete(
        path: '/api/program-sie-member/{id}',
        summary: 'Hapus Anggota Sie',
        description: 'Menghapus anggota sie berdasarkan ID.',
        security: [['sanctum' => []]],
        tags: ['Program Sie Member'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'ID Member Sie'),
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
