<?php

namespace App\Http\Controllers;

use App\Models\TaskStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class TaskStatusController extends BaseController
{
    protected $model;

    public function __construct(TaskStatus $model)
    {
        $this->model = $model;
    }

    #[OA\Get(
        path: '/api/task-status',
        summary: 'Get list of task status',
        tags: ['Task Status'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\Parameter(name: 'q[id_program]', in: 'query', description: 'Filter by ID Program', required: false, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'q[id_sie]', in: 'query', description: 'Filter by ID Sie', required: false, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Successful operation')]
    public function index(Request $request): JsonResponse
    {
        return parent::index($request);
    }

    #[OA\Post(
        path: '/api/task-status',
        summary: 'Create new task status',
        tags: ['Task Status'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['id_program', 'nama'],
            properties: [
                new OA\Property(property: 'id_program', type: 'integer'),
                new OA\Property(property: 'id_sie', type: 'integer', nullable: true),
                new OA\Property(property: 'kode', type: 'string', nullable: true),
                new OA\Property(property: 'nama', type: 'string')
            ]
        )
    )]
    #[OA\Response(response: 201, description: 'Created')]
    public function store(Request $request): JsonResponse
    {
        return parent::store($request);
    }

    #[OA\Get(
        path: '/api/task-status/{id}',
        summary: 'Get detail of task status',
        tags: ['Task Status'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Successful operation')]
    public function show($id = null): JsonResponse
    {
        return parent::show($id);
    }

    #[OA\Put(
        path: '/api/task-status/{id}',
        summary: 'Update task status',
        tags: ['Task Status'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'id_program', type: 'integer'),
                new OA\Property(property: 'id_sie', type: 'integer', nullable: true),
                new OA\Property(property: 'kode', type: 'string', nullable: true),
                new OA\Property(property: 'nama', type: 'string')
            ]
        )
    )]
    #[OA\Response(response: 200, description: 'Updated')]
    public function update($id = null, Request $request): JsonResponse
    {
        return parent::update($id, $request);
    }

    #[OA\Delete(
        path: '/api/task-status/{id}',
        summary: 'Delete task status',
        tags: ['Task Status'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Deleted')]
    public function destroy($id = null): JsonResponse
    {
        return parent::destroy($id);
    }
}
