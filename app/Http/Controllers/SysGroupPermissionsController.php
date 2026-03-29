<?php

namespace App\Http\Controllers;

use App\Models\SysGroupPermission;
use App\Models\SysMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class SysGroupPermissionsController extends BaseController
{
    protected $model;

    public function __construct(SysGroupPermission $model)
    {
        $this->model = $model;
    }

    #[OA\Get(
        path: '/api/sys-group-permissions/{groupId}/mapping',
        summary: 'Mapping Hak Akses Role (Tree)',
        description: 'Mengembalikan keseluruhan struktur menu bersarang (Tree), lengkap dengan setiap opsi akses beserta status checked true/false untuk grup tertentu.',
        security: [['sanctum' => []]],
        tags: ['Sys Group Permissions'],
        parameters: [
            new OA\Parameter(name: 'groupId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'ID Grup / Role'),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Berhasil',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'id_sys_menu', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Dashboard'),
                                new OA\Property(property: 'url', type: 'string', example: '/dashboard'),
                                new OA\Property(property: 'icon', type: 'string', example: '🏠'),
                                new OA\Property(property: 'permissions', type: 'array', items: new OA\Items(
                                    properties: [
                                        new OA\Property(property: 'id_sys_menu_permission', type: 'integer', example: 1),
                                        new OA\Property(property: 'code', type: 'string', example: 'index'),
                                        new OA\Property(property: 'name', type: 'string', example: 'Index Access'),
                                        new OA\Property(property: 'checked', type: 'boolean', example: true),
                                    ]
                                )),
                                new OA\Property(property: 'children', type: 'array', items: new OA\Items(type: 'object')),
                            ]
                        )),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function mapping($groupId)
    {
        $menus = SysMenu::orderBy('menu_order', 'asc')->get();
        $menuPermissions = DB::table('sys_menu_permissions')
            ->join('sys_permissions', 'sys_menu_permissions.id_sys_permission', '=', 'sys_permissions.id_sys_permission')
            ->select('sys_menu_permissions.*', 'sys_permissions.code', 'sys_permissions.description')
            ->get();
        $groupPermissions = SysGroupPermission::where('id_sys_group', $groupId)->pluck('id_sys_menu_permission')->toArray();
        $tree = $this->buildMenuTree($menus, $menuPermissions, $groupPermissions, null);

        return response()->json(['success' => true, 'data' => $tree]);
    }

    private function buildMenuTree($menus, $menuPermissions, $groupPermissions, $parentId = null)
    {
        $branch = [];
        foreach ($menus as $menu) {
            if ($menu->id_menu_parent == $parentId) {
                $item = [
                    'id_sys_menu' => $menu->id_sys_menu,
                    'name' => $menu->name,
                    'url' => $menu->url,
                    'icon' => $menu->icon,
                ];
                $perms = $menuPermissions->where('id_sys_menu', $menu->id_sys_menu);
                $item['permissions'] = [];
                foreach ($perms as $p) {
                    $item['permissions'][] = [
                        'id_sys_menu_permission' => $p->id_sys_menu_permission,
                        'code' => $p->code,
                        'name' => $p->description,
                        'checked' => in_array($p->id_sys_menu_permission, $groupPermissions)
                    ];
                }
                $children = $this->buildMenuTree($menus, $menuPermissions, $groupPermissions, $menu->id_sys_menu);
                if (count($children) > 0) {
                    $item['children'] = $children;
                }
                $branch[] = $item;
            }
        }
        return $branch;
    }

    #[OA\Post(
        path: '/api/sys-group-permissions/{groupId}/sync',
        summary: 'Simpan / Sinkronisasi Hak Akses Role',
        description: 'Menyimpan perubahan hak akses. Semua permission lama untuk grup tersebut akan ditimpa dengan array permissions baru.',
        security: [['sanctum' => []]],
        tags: ['Sys Group Permissions'],
        parameters: [
            new OA\Parameter(name: 'groupId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'ID Grup / Role'),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['permissions'],
                properties: [
                    new OA\Property(property: 'permissions', type: 'array', items: new OA\Items(type: 'integer'), example: [1, 2, 5, 8]),
                    new OA\Property(property: 'id_organization', type: 'integer', example: 17),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Hak akses berhasil diperbarui', content: new OA\JsonContent(properties: [new OA\Property(property: 'success', type: 'boolean', example: true), new OA\Property(property: 'message', type: 'string', example: 'Hak akses berhasil diperbarui')])),
            new OA\Response(response: 500, description: 'Gagal memperbarui', content: new OA\JsonContent(properties: [new OA\Property(property: 'success', type: 'boolean', example: false), new OA\Property(property: 'message', type: 'string')])),
        ]
    )]
    public function sync(Request $request, $groupId)
    {
        $permissions = $request->input('permissions', []);
        $organizationId = $request->input('id_organization', 17);

        DB::beginTransaction();
        try {
            SysGroupPermission::where('id_sys_group', $groupId)->delete();
            $insertData = [];
            foreach ($permissions as $permId) {
                $insertData[] = [
                    'id_sys_group' => $groupId,
                    'id_sys_menu_permission' => $permId,
                    'id_organization' => $organizationId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (!empty($insertData)) {
                SysGroupPermission::insert($insertData);
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Hak akses berhasil diperbarui']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui hak akses: ' . $e->getMessage()], 500);
        }
    }

    #[OA\Post(
        path: '/api/sys-group-permissions/add-action',
        summary: 'Tambah Custom Action ke Menu',
        description: 'Mendaftarkan aksi kustom baru (misal: export_pdf) ke sys_permissions, sys_menu_permissions, dan sys_group_permissions sekaligus.',
        security: [['sanctum' => []]],
        tags: ['Sys Group Permissions'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['id_sys_group', 'id_sys_menu', 'code', 'description'],
                properties: [
                    new OA\Property(property: 'id_sys_group', type: 'integer', example: 1, description: 'ID Group / Role'),
                    new OA\Property(property: 'id_sys_menu', type: 'integer', example: 15, description: 'ID Menu tujuan'),
                    new OA\Property(property: 'code', type: 'string', example: 'export_pdf', description: 'Kode aksi unik'),
                    new OA\Property(property: 'description', type: 'string', example: 'Export Data ke PDF', description: 'Label aksi'),
                    new OA\Property(property: 'id_organization', type: 'integer', example: 17, description: 'ID Organisasi (opsional)'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Action berhasil ditambahkan',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Action berhasil ditambahkan'),
                        new OA\Property(property: 'data', type: 'object',
                            properties: [
                                new OA\Property(property: 'id_sys_menu_permission', type: 'integer', example: 25),
                                new OA\Property(property: 'code', type: 'string', example: 'export_pdf'),
                                new OA\Property(property: 'name', type: 'string', example: 'Export Data ke PDF'),
                                new OA\Property(property: 'checked', type: 'boolean', example: true),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 500, description: 'Gagal menambahkan action', content: new OA\JsonContent(properties: [new OA\Property(property: 'success', type: 'boolean', example: false), new OA\Property(property: 'message', type: 'string')])),
        ]
    )]
    public function addAction(Request $request)
    {
        $request->validate([
            'id_sys_group' => 'required|integer',
            'id_sys_menu' => 'required|integer',
            'code' => 'required|string',
            'description' => 'required|string'
        ]);

        $code = $request->input('code');
        $description = $request->input('description');
        $idMenu = $request->input('id_sys_menu');
        $idGroup = $request->input('id_sys_group');
        $organizationId = $request->input('id_organization', 17);

        DB::beginTransaction();
        try {
            $permission = DB::table('sys_permissions')->where('code', $code)->first();
            $idPermission = null;
            if (!$permission) {
                $idPermission = DB::table('sys_permissions')->insertGetId([
                    'code' => $code, 'description' => $description, 'id_organization' => $organizationId,
                    'created_at' => now(), 'updated_at' => now(),
                ], 'id_sys_permission');
            } else {
                $idPermission = $permission->id_sys_permission;
            }

            $menuPermission = DB::table('sys_menu_permissions')
                ->where('id_sys_menu', $idMenu)->where('id_sys_permission', $idPermission)->first();
            $idMenuPermission = null;
            if (!$menuPermission) {
                $idMenuPermission = DB::table('sys_menu_permissions')->insertGetId([
                    'id_sys_menu' => $idMenu, 'id_sys_permission' => $idPermission, 'id_organization' => $organizationId,
                    'created_at' => now(), 'updated_at' => now(),
                ], 'id_sys_menu_permission');
            } else {
                $idMenuPermission = $menuPermission->id_sys_menu_permission;
            }

            $groupPermission = DB::table('sys_group_permissions')
                ->where('id_sys_group', $idGroup)->where('id_sys_menu_permission', $idMenuPermission)->first();
            if (!$groupPermission) {
                DB::table('sys_group_permissions')->insert([
                    'id_sys_group' => $idGroup, 'id_sys_menu_permission' => $idMenuPermission, 'id_organization' => $organizationId,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true, 'message' => 'Action berhasil ditambahkan',
                'data' => ['id_sys_menu_permission' => $idMenuPermission, 'code' => $code, 'name' => $description, 'checked' => true]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menambahkan action: ' . $e->getMessage()], 500);
        }
    }

    #[OA\Post(
        path: '/api/sys-group-permissions/remove-action',
        summary: 'Hapus Custom Action dari Menu',
        description: 'Mencabut aksi dari keterikatannya dengan menu. Menghapus dari sys_group_permissions dan sys_menu_permissions. Data di sys_permissions tetap utuh.',
        security: [['sanctum' => []]],
        tags: ['Sys Group Permissions'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['id_sys_menu_permission'],
                properties: [
                    new OA\Property(property: 'id_sys_menu_permission', type: 'integer', example: 32, description: 'ID Menu Permission yang ingin dihapus'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Action berhasil dihapus', content: new OA\JsonContent(properties: [new OA\Property(property: 'success', type: 'boolean', example: true), new OA\Property(property: 'message', type: 'string', example: 'Action berhasil dihapus dari menu')])),
            new OA\Response(response: 404, description: 'Action tidak ditemukan', content: new OA\JsonContent(properties: [new OA\Property(property: 'success', type: 'boolean', example: false), new OA\Property(property: 'message', type: 'string', example: 'Action tidak ditemukan pada menu ini')])),
            new OA\Response(response: 500, description: 'Gagal menghapus', content: new OA\JsonContent(properties: [new OA\Property(property: 'success', type: 'boolean', example: false), new OA\Property(property: 'message', type: 'string')])),
        ]
    )]
    public function removeAction(Request $request)
    {
        $request->validate(['id_sys_menu_permission' => 'required|integer']);
        $idMenuPermission = $request->input('id_sys_menu_permission');

        DB::beginTransaction();
        try {
            DB::table('sys_group_permissions')->where('id_sys_menu_permission', $idMenuPermission)->delete();
            $deleted = DB::table('sys_menu_permissions')->where('id_sys_menu_permission', $idMenuPermission)->delete();

            if (!$deleted) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Action tidak ditemukan pada menu ini'], 404);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Action berhasil dihapus dari menu']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menghapus action: ' . $e->getMessage()], 500);
        }
    }
}
