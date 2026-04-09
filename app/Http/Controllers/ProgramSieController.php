<?php

namespace App\Http\Controllers;

use App\Models\ProgramSie;
use App\Models\ProgramSieMember;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
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
        $search = $request->get('q');
        $page   = $request->get('page') ?? 1;
        $limit  = $request->get('pagesize') ?? $this->limit;
        
        $query = $this->model->with(['members.user'])->search($search);

        // Sorting logic
        $orderby = $request->get('order');
        if ($orderby) {
            $orderby = explode(",", $orderby);
            foreach ($orderby as $v) {
                $exp = explode(" ", trim($v));
                $column = $exp[0];
                $sc = $exp[1] ?? 'asc';
                $query = $query->orderBy($column, $sc);
            }
        } else {
            $query = $query->orderBy($this->model->getKeyName());
        }

        $data = $query->paginate($limit);

        return $this->respond(
            $data->items(),
            200,
            null,
            [
                'page' => $data->currentPage(),
                'page_size' => $data->perPage(),
                'total_page' => $data->lastPage(),
                'total_records' => $data->total()
            ]
        );
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
        $record = $this->model->with(['members.user', 'koordinator'])->find($id);
        if (!$record) {
            return $this->failNotFound("Sie with id $id not found");
        }
        return $this->respond($record);
    }

    #[OA\Post(
        path: '/api/program-sie',
        summary: 'Tambah Sie Baru',
        description: 'Membuat sie program baru beserta anggotanya.',
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
                    new OA\Property(property: 'id_koordinator', type: 'integer', example: 1),
                    new OA\Property(property: 'members', type: 'array', items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id_user', type: 'integer', example: 1),
                            new OA\Property(property: 'role', type: 'string', example: 'Anggota'),
                        ]
                    )),
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
        $request->validate(array_merge($this->model->rules, [
            'members'           => 'nullable|array',
            'members.*.id_user' => 'required|integer',
            'members.*.role'    => 'nullable|string|max:100',
        ]));

        return DB::transaction(function () use ($request) {
            $data = $request->all();
            $record = $this->model->create($data);

            if ($request->has('members')) {
                foreach ($request->members as $member) {
                    $record->members()->create([
                        'id_user' => $member['id_user'],
                        'role'    => $member['role'] ?? null,
                    ]);
                }
            }

            return $this->respondCreated($record->load(['members.user', 'koordinator']), 'Sie created successfully');
        });
    }

    #[OA\Put(
        path: '/api/program-sie/{id}',
        summary: 'Update Sie Program',
        description: 'Memperbarui data sie program dan daftar anggotanya.',
        security: [['sanctum' => []]],
        tags: ['Program Sie'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'ID Sie'),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'sie_name', type: 'string'), 
                    new OA\Property(property: 'description', type: 'string'),
                    new OA\Property(property: 'id_koordinator', type: 'integer'),
                    new OA\Property(property: 'members', type: 'array', items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id_user', type: 'integer'),
                            new OA\Property(property: 'role', type: 'string'),
                        ]
                    )),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Data berhasil diupdate', content: new OA\JsonContent(properties: [new OA\Property(property: 'data', type: 'object'), new OA\Property(property: 'message', type: 'string')])),
            new OA\Response(response: 404, description: 'Data tidak ditemukan'),
        ]
    )]
    public function update($id = null, Request $request): JsonResponse
    {
        $record = $this->model->find($id);
        if (!$record) {
            return $this->failNotFound("Sie with id $id not found");
        }

        $request->validate(array_merge($this->model->rules, [
            'members'           => 'nullable|array',
            'members.*.id_user' => 'required|integer',
            'members.*.role'    => 'nullable|string|max:100',
        ]));

        return DB::transaction(function () use ($request, $record) {
            $record->update($request->all());

            if ($request->has('members')) {
                // Simple sync: delete all and recreate
                $record->members()->delete();
                foreach ($request->members as $member) {
                    $record->members()->create([
                        'id_user' => $member['id_user'],
                        'role'    => $member['role'] ?? null,
                    ]);
                }
            }

            return $this->respond($record->load(['members.user', 'koordinator']), 200, 'Sie updated successfully');
        });
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
