<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class TaskController extends BaseController
{
    protected $model;

    public function __construct(Task $model)
    {
        $this->model = $model;
    }

    #[OA\Get(
        path: '/api/tasks',
        summary: 'Get list of tasks',
        tags: ['Tasks'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\Parameter(name: 'q[id_program]', in: 'query', description: 'Filter by ID Program', required: false, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'q[id_sie]', in: 'query', description: 'Filter by ID Sie', required: false, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'q[id_task_status]', in: 'query', description: 'Filter by Task Status', required: false, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Successful operation')]
    public function index(Request $request): JsonResponse
    {
        $search = $request->get('q');
        $page   = $request->get('page') ?? 1;
        $limit  = $request->get('pagesize') ?? $this->limit;
        
        $query = $this->model->with(['status', 'assignees'])->search($search);

        // Sorting logic from BaseController
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
        path: '/api/tasks/{id}',
        summary: 'Get detail of task',
        tags: ['Tasks'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Successful operation')]
    public function show($id = null): JsonResponse
    {
        $record = $this->model->with(['status', 'assignees'])->find($id);
        if (!$record) {
            return $this->failNotFound("Task with id $id not found");
        }

        return $this->respond($record);
    }

    #[OA\Post(
        path: '/api/tasks',
        summary: 'Create new task',
        tags: ['Tasks'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['id_program', 'id_task_status', 'title'],
            properties: [
                new OA\Property(property: 'id_program', type: 'integer'),
                new OA\Property(property: 'id_sie', type: 'integer', nullable: true),
                new OA\Property(property: 'id_task_status', type: 'integer'),
                new OA\Property(property: 'title', type: 'string'),
                new OA\Property(property: 'description', type: 'string', nullable: true),
                new OA\Property(property: 'due_date', type: 'string', format: 'date', nullable: true),
                new OA\Property(property: 'id_user', type: 'array', items: new OA\Items(type: 'integer'), description: 'Array of user IDs to assign')
            ]
        )
    )]
    #[OA\Response(response: 201, description: 'Created')]
    public function store(Request $request): JsonResponse
    {
        $request->validate($this->model->rules);

        return DB::transaction(function () use ($request) {
            $data = $request->all();
            $record = $this->model->create($data);

            if ($request->has('id_user')) {
                $record->assignees()->sync($request->id_user);
            }

            return $this->respondCreated($record->load(['status', 'assignees']), 'Task created successfully');
        });
    }

    #[OA\Put(
        path: '/api/tasks/{id}',
        summary: 'Update task',
        tags: ['Tasks'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'id_task_status', type: 'integer'),
                new OA\Property(property: 'title', type: 'string'),
                new OA\Property(property: 'description', type: 'string'),
                new OA\Property(property: 'due_date', type: 'string', format: 'date'),
                new OA\Property(property: 'id_user', type: 'array', items: new OA\Items(type: 'integer'))
            ]
        )
    )]
    #[OA\Response(response: 200, description: 'Updated')]
    public function update($id = null, Request $request): JsonResponse
    {
        $record = $this->model->find($id);
        if (!$record) {
            return $this->failNotFound("Task with id $id not found");
        }

        $request->validate($this->model->rules);

        return DB::transaction(function () use ($request, $record) {
            $record->update($request->all());

            if ($request->has('id_user')) {
                $record->assignees()->sync($request->id_user);
            }

            return $this->respond($record->load(['status', 'assignees']), 200, 'Task updated successfully');
        });
    }
}
