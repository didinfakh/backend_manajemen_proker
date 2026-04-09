<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class BaseController extends ResourceController
{
    /**
     *
     * @var int limit data to show
     */
    protected $limit = 10;

    protected $data = [];

    /**
     * Menampilkan List Data (Paginated)
     * 
     * Mengambil daftar data dengan pagination. Mendukung sorting, filtering, dan pencarian spesifik.
     * 
     * @queryParam q array Filter array (contoh: q[nama]=keyword). Example: {"nama": "test"}
     * @queryParam page int Halaman yang aktif. Example: 1
     * @queryParam pagesize int Jumlah baris data per halaman. Example: 10
     * @queryParam order string Urutan data (kolom asc/desc). Example: id_sys_group desc
     * 
     * @response array{data: array, page: int, page_size: int, total_page: int, total_records: int}
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->get('q');
        if ($search) {
            if (!empty($search['nama']))
                $search['nama'] = "%" . $search['nama'] . "%";
            if (!empty($search['kode']))
                $search['kode'] = "%" . $search['kode'] . "%";
        }
        // $filter = $request->get('q');
        $page = $request->get('page') ?? 1;
        $limit = $request->get('pagesize') ?? $this->limit;
        $db = $this->model->search($search);

        $orderby = $request->get('order');
        if ($orderby) {
            $orderby = explode(",", $orderby);
            if (!is_array($orderby))
                $orderby = array($orderby);

            foreach ($orderby as $v) {
                $exp = explode(" ", $v);
                $column = $exp[0];
                if ($exp[1])
                    $sc = $exp[1];

                $db = $db->orderBy($column, $sc);
            }
        } else if (isset($this->model->orderDefault)) {
            $exp = explode(",", $this->model->orderDefault);
            if (!is_array($exp))
                $exp = array($exp);

            foreach ($exp as $v)
                $db = $db->orderByRaw(trim($v));
        } else {
            $primaryKey = $this->model->getKeyName();
            if ($primaryKey) {
                $db = $db->orderBy($primaryKey);
            }
        }

        // if($filter)
        // 	$db = $db->where($filter);
        $data = $db->paginate($limit);

        return $this->respond(
            $data->items(),
            200,
            null,
            [
                'page' => $data->currentPage(),
                'page_size' => $data->perPage(),
                'total_page' => ceil($data->total() / $limit),
                'total_records' => $data->total()
            ]
        );
        // return $this->respond([
        //     'page' => $this->model->pager->getCurrentPage(),
        //     'page_size' => $limit,
        //     'data' => $data,
        //     'total_page' => $this->model->pager->getPageCount(),
        //     'total_records' => $this->model->pager->getDataCount()
        // ]);
    }

    /**
     * Menampilkan Detail Data
     *
     * Berfungsi untuk mendapatkan properti lengkap dari satu buah record berdasarkan Primary Key.
     * 
     * @param int $id Primary ID dari tabel model yang bersangkutan.
     * 
     * @response 200 array{data: object}
     * @response 404 array{success: boolean, message: string}
     */
    public function show($id = null): JsonResponse
    {
        $record = $this->model->find($id);
        if (!$record) {
            return $this->failNotFound(sprintf(
                'item with id %d not found',
                $id
            ));
        }

        return $this->respond($record);
    }

    /**
     * Menambahkan Data Baru
     *
     * Digunakan untuk meng-insert data/resource baru. Validasi dikontrol melalui properti model `$rules`.
     * 
     * @param Request $request
     * 
     * @response 201 array{data: object, message: string}
     * @response 400 array{success: boolean, message: string, errors: array}
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate($this->model->rules);

        $data = $request->all();

        // Use Eloquent create() instead of raw insert() to handle:
        // 1. Auto-incrementing IDs (especially for Postgres)
        
        // 2. Timestamps (created_at, updated_at)
        // 3. BaseModel events (like auto-setting organization_id)
        $record = $this->model->create($data);
        
        return $this->respondCreated($record, 'data created');
    }

    /**
     * Update (Edit) Data
     *
     * Berfungsi untuk memperbarui record / object yang ada di tabel. Tervalidasi sebelum diproses.
     * 
     * @param int $id Primary ID data yang ingin diupdate.
     * @param Request $request
     * 
     * @response 200 array{data: object, message: string}
     * @response 404 array{success: boolean, message: string}
     */
    public function update($id = null, Request $request): JsonResponse
    {
        $request->validate($this->model->rules);

        if (!$data_before = $this->model->find($id)) {
            return $this->failNotFound(sprintf(
                'item with id %d not found',
                $id
            ));
        }

        // $data       = $request->getRawInput();		
        // $updateData = array_filter($data);
        $updateData = $request->all();
        $data_before->update($updateData);

        return $this->respond($data_before->refresh(), 200, 'data updated');
    }

    /**
     * Hapus Data (Delete)
     *
     * Menghapus record tertentu berdasarkan identifier dan merekam action "delete" ke sys_logs (jika aktif).
     * 
     * @param int $id Primary ID data yang ingin dihapus.
     * 
     * @response 200 array{data: array{id: int}, message: string}
     * @response 404 array{success: boolean, message: string}
     */
    public function destroy($id = null): JsonResponse
    {
        if (!$data = $model = $this->model->find($id)) {
            return $this->failNotFound(sprintf(
                'item with id %d not found',
                $id
            ));
        }

        if (method_exists($this->model, 'logging')) {
            $this->model->logging(
                array(
                    "action" => "delete",
                    "table_name" => $this->model->table,
                    "activity" => "Menghapus data",
                    "data" => $data->toArray()
                )
            );
        }

        $ret = $model->delete();
        if (!$ret) {
            return $this->failNotFound(sprintf(
                'item with id %d not found or already deleted',
                $id
            ));
        }

        return $this->respondDeleted(['id' => $id], 'data deleted');
    }

    protected function filterArray($array, $filter)
    {
        return array_values(array_filter($array, $filter))[0];
    }
}
