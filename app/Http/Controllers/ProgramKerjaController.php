<?php

namespace App\Http\Controllers;

use App\Models\ProgramKerja;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProgramKerjaController extends BaseController
{
    protected $model;

    public function __construct(ProgramKerja $model)
    {
        $this->model = $model;
    }

    #[OA\Get(
        path: '/api/program-kerja',
        summary: 'Daftar Program Kerja (Paginated)',
        description: 'Mengambil daftar seluruh program kerja dengan pagination.',
        security: [['sanctum' => []]],
        tags: ['Program Kerja'],
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
        path: '/api/program-kerja/{id}',
        summary: 'Detail Program Kerja',
        description: 'Menampilkan detail satu program kerja berdasarkan ID.',
        security: [['sanctum' => []]],
        tags: ['Program Kerja'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'ID Program Kerja'),
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
        path: '/api/program-kerja',
        summary: 'Tambah Program Kerja Baru',
        description: 'Membuat program kerja baru.',
        security: [['sanctum' => []]],
        tags: ['Program Kerja'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Penyusunan Anggaran'),
                    new OA\Property(property: 'description', type: 'string', example: 'Langkah persiapan anggaran...'),
                    new OA\Property(property: 'start_date', type: 'string', format: 'date', example: '2026-04-01'),
                    new OA\Property(property: 'end_date', type: 'string', format: 'date', example: '2026-12-31'),
                    new OA\Property(property: 'id_user_leader', type: 'integer', example: 1),
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
        return parent::store($request);
    }

    #[OA\Put(
        path: '/api/program-kerja/{id}',
        summary: 'Update Program Kerja',
        description: 'Memperbarui data program kerja yang sudah ada.',
        security: [['sanctum' => []]],
        tags: ['Program Kerja'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'ID Program Kerja'),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(properties: [new OA\Property(property: 'name', type: 'string'), new OA\Property(property: 'description', type: 'string')])
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
        path: '/api/program-kerja/{id}',
        summary: 'Hapus Program Kerja',
        description: 'Menghapus program kerja berdasarkan ID.',
        security: [['sanctum' => []]],
        tags: ['Program Kerja'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'ID Program Kerja'),
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
