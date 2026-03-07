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

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

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
    public function login(Request $request)
    {
        $url_sso = env("APP_SSO_URL");


        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $user->initials = getInitials($user->name);
        $token = $user->createToken('auth_token')->plainTextToken;

        // ===============================
        // SSO
        // ===============================
        $response_sso = Http::withHeaders([
            'id_organization' => '1',
        ])->post($url_sso . '/api/loginbyid', [
            'id_user' => $user->id_organization
        ]);

        // ===========
        // ambil data group
        // ===========
        $group = DB::table('sys_user_groups as sug')
            ->leftJoin('sys_groups as sg', 'sug.id_sys_group', '=', 'sg.id_sys_group')
            ->where('sug.id_user', $user->id_user)
            ->select(
                'sug.id_sys_group',
                'sg.name as group_name'
            )
            ->get();

        if ($group->count() > 1) {
            $group = $group->first();
        } else {

            $id_sys_group = $group[0]->id_sys_group;
            $permissionMap = $this->getmenu($id_sys_group, $user->id_organization);
        }

        // $permissionMap = $this->getmenu($group->id_group, $user->id_organization);
        // foreach ($permissions as $row) {
        //     if ($row->menu) {
        //         $permissionMap[$row->menu] = json_decode($row->actions, true);
        //     }
        // }

        // ===============================
        // SIMPAN KE CACHE
        // ===============================
        Cache::put(
            "perm:user:{$user->id_user}",
            $permissionMap,
            now()->addHours(6) // TTL bebas
        );



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
    select
      sm.id_sys_menu as id_menu,
      sm.name as nama,
      sm.url,
      sm.id_menu_parent,
      sm.icon,
      sp.code as action
    from sys_menu sm
    left join sys_menu_permissions smp
      on sm.id_sys_menu = smp.id_sys_menu
    left join sys_permissions sp
      on smp.id_sys_permission = sp.id_sys_permission
    left join sys_group_permissions sgp
      on sgp.id_sys_menu_permission = smp.id_sys_menu_permission
      and sgp.id_sys_group = ?
    where sm.id_organization = ?
      and sgp.id_sys_group is not null
    order by sm.menu_order, sp.code
", [$id_group, $id_organization]);


        $menuArr = [];
        $actionMenu = [];

        foreach ($rows as $r) {
            $menuArr[$r->id_menu] = [
                'id_menu' => $r->id_menu,
                'nama' => $r->nama,
                'url' => $r->url,
                'icon' => $r->icon,
                'id_menu_parent' => $r->id_menu_parent
            ];

            if ($r->action) {
                $actionMenu[$r->id_menu][] = $r->action;
            }
        }

        // hilangkan duplikat action
        foreach ($actionMenu as &$acts) {
            $acts = array_values(array_unique($acts));
        }

        $menu = $this->_getChild($menuArr, null, $actionMenu);

        return $menu;
    }
    private function _getChild(&$menuArr, $parentId = null, $actionMenu = [])
    {
        $menu = [];

        foreach ($menuArr as $idMenu => $r) {

            if ($r['id_menu_parent'] == $parentId) {

                unset($menuArr[$idMenu]);

                $submenu = $this->_getChild($menuArr, $idMenu, $actionMenu);

                $menu[] = [
                    'url' => $r['url'],
                    'id_menu' => $idMenu,
                    'label' => $r['nama'],
                    'icon' => $r['icon'],
                    'actions' => $actionMenu[$idMenu] ?? [],
                    'children' => $submenu
                ];
            }
        }

        return $menu;
    }
    // public function login(Request $request)
    // {

    //     $url_sso = env("APP_SSO_URL");
    //     if (!Auth::attempt($request->only('email', 'password'))) {
    //         return response()->json([
    //             'message' => 'Unauthorized'
    //         ], 401);
    //     }

    //     // FacadesDB::enableQueryLog();
    //     $user = User::where('email', $request->email)->firstOrFail();
    //     // ddQueryLog();
    //     $token = $user->createToken('auth_token')->plainTextToken;
    //     $response_sso = Http::withHeaders([
    //         'id_organization' => '1',
    //     ])->post($url_sso . '/api/loginbyid', [
    //                 'id_user' => $user->id_organization
    //             ]);


    //     return response()->json([
    //         'message' => 'Login success',
    //         'access_token' => $token,
    //         'user' => $user,
    //         'application' => $response_sso->json(),
    //         'token_type' => 'Bearer'
    //     ]);
    // }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'logout success'
        ]);
    }
}
