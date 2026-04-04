<?php

namespace App\Http\Controllers;

use App\Models\ProgramSie;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProgramSieController extends BaseController
{
    protected $model;

    public function __construct(ProgramSie $model)
    {
        $this->model = $model;
    }

    #[OA\Get(
        path: '/api/program-sie',
        summary: 'Daftar Sie Program (Paginated)',
        description: 'Mengambil daftar seluruh sie program dengan pagination.',
        security: [['sanctum' => []]],
        tags: ['Program Sie'],
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
        path: '/api/program-sie/{id}',
        summary: 'Detail Sie Program',
        description: 'Menampilkan detail satu sie program berdasarkan ID.',
        security: [['sanctum' => []]],
        tags: ['Program Sie'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'ID Sie'),
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
        path: '/api/program-sie',
        summary: 'Tambah Sie Baru',
        description: 'Membuat sie program baru.',
        security: [['sanctum' => []]],
        tags: ['Program Sie'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['id_program', 'sie_name'],
                properties: [
                    new OA\Property(property: 'id_program', type: 'integer', example: 1),
                    new OA\Property(property: 'sie_name', type: 'string', example: 'Humas'),
                    new OA\Property(property: 'description', type: 'string', example: 'Sie Hubungan Masyarakat'),
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
        path: '/api/program-sie/{id}',
        summary: 'Update Sie Program',
        description: 'Memperbarui data sie program yang sudah ada.',
        security: [['sanctum' => []]],
        tags: ['Program Sie'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'ID Sie'),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(properties: [new OA\Property(property: 'sie_name', type: 'string'), new OA\Property(property: 'description', type: 'string')])
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
        path: '/api/program-sie/{id}',
        summary: 'Hapus Sie Program',
        description: 'Menghapus sie program berdasarkan ID.',
        security: [['sanctum' => []]],
        tags: ['Program Sie'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'ID Sie'),
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
