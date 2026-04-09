<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http as FacadesHttp;
use League\Uri\Http as UriHttp;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/register',
        summary: 'Pendaftaran Pengguna Baru',
        description: 'API untuk registrasi akun user. Mengembalikan detail user beserta Access Token (Bearer).',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'password123'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Registrasi berhasil',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'object'),
                        new OA\Property(property: 'access_token', type: 'string'),
                        new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validasi gagal'),
        ]
    )]
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|string'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    #[OA\Post(
        path: '/api/login',
        summary: 'Login Pengguna (SSO & Sanctum)',
        description: 'API Login terintegrasi SSO. Mengembalikan token, data user, dan mapping hak akses menu beserta permissions.',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@example.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'password123'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login berhasil',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Login success'),
                        new OA\Property(property: 'response_sso', type: 'object'),
                        new OA\Property(property: 'access_token', type: 'string'),
                        new OA\Property(property: 'permission', type: 'array', items: new OA\Items(type: 'object')),
                        new OA\Property(property: 'user', type: 'object'),
                        new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthorized'),
                    ]
                )
            ),
        ]
    )]
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $user->initials = getInitials($user->name);
        $token = $user->createToken('auth_token')->plainTextToken;

        $url_sso = config('services.sso.url');
        $response_sso = Http::withHeaders([
            'id_application' => '1',
        ])->post($url_sso . '/api/loginbyid', [
            'id_user' => $user->id_organization
        ]);

        $group = DB::table('sys_user_groups as sug')
            ->leftJoin('sys_groups as sg', 'sug.id_sys_group', '=', 'sg.id_sys_group')
            ->where('sug.id_user', $user->id_user)
            ->select('sug.id_sys_group', 'sg.name as group_name')
            ->get();

        $permissionMap = [];
        if ($group->count() > 0) {
            $id_sys_group = $group->count() > 1
                ? $group->first()->id_sys_group
                : $group[0]->id_sys_group;
            $permissionMap = $this->getmenu($id_sys_group, $user->id_organization);
        }

        Cache::put("perm:user:{$user->id_user}", $permissionMap, now()->addHours(6));

        // Cache organization context
        if ($user->id_organization) {
            Cache::put("user_org:{$user->id_user}", $user->id_organization, now()->addHours(24));
        }

        return response()->json([
            'message' => 'Login success',
            'response_sso' => $response_sso->json(),
            'access_token' => $token,
            'permission' => $permissionMap,
            'user' => $user,
            'token_type' => 'Bearer'
        ]);
    }

    public function getmenu($id_group, $id_organization)
    {
        $rows = DB::select("
            select sm.id_sys_menu as id_menu, sm.name as nama, sm.url, sm.id_menu_parent, sm.icon, sp.code as action
            from sys_menu sm
            left join sys_menu_permissions smp on sm.id_sys_menu = smp.id_sys_menu
            left join sys_permissions sp on smp.id_sys_permission = sp.id_sys_permission
            left join sys_group_permissions sgp on sgp.id_sys_menu_permission = smp.id_sys_menu_permission and sgp.id_sys_group = ?
            where sm.id_organization = ? and sgp.id_sys_group is not null
            order by sm.menu_order, sp.code
        ", [$id_group, $id_organization]);

        $menuArr = [];
        $actionMenu = [];

        foreach ($rows as $r) {
            $menuArr[$r->id_menu] = [
                'id_menu' => $r->id_menu, 'nama' => $r->nama,
                'url' => $r->url, 'icon' => $r->icon, 'id_menu_parent' => $r->id_menu_parent
            ];
            if ($r->action) {
                $actionMenu[$r->id_menu][] = $r->action;
            }
        }

        foreach ($actionMenu as &$acts) {
            $acts = array_values(array_unique($acts));
        }

        return $this->_getChild($menuArr, null, $actionMenu);
    }

    private function _getChild(&$menuArr, $parentId = null, $actionMenu = [])
    {
        $menu = [];
        foreach ($menuArr as $idMenu => $r) {
            if ($r['id_menu_parent'] == $parentId) {
                unset($menuArr[$idMenu]);
                $submenu = $this->_getChild($menuArr, $idMenu, $actionMenu);
                $menu[] = [
                    'url' => $r['url'], 'id_menu' => $idMenu, 'label' => $r['nama'],
                    'icon' => $r['icon'], 'actions' => $actionMenu[$idMenu] ?? [], 'children' => $submenu
                ];
            }
        }
        return $menu;
    }

    #[OA\Post(
        path: '/api/logout',
        summary: 'Logout / Akhiri Sesi',
        description: 'Menghancurkan token pengguna saat ini.',
        security: [['sanctum' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logout berhasil',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'logout success'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'logout success'
        ]);
    }
}
