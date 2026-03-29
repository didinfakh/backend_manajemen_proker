<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Manajemen Proker API',
    description: 'Dokumentasi API untuk Sistem Manajemen Program Kerja. Mencakup Authentication, Menu Management, Role & Group Management, dan Permission Management.',
    contact: new OA\Contact(email: 'admin@manajemenproker.com')
)]
#[OA\Server(url: L5_SWAGGER_CONST_HOST, description: 'API Server')]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'apiKey',
    name: 'Authorization',
    in: 'header',
    description: 'Masukkan token dengan format: Bearer {access_token}'
)]
#[OA\Tag(name: 'Auth', description: 'Endpoint Autentikasi (Register, Login, Logout)')]
#[OA\Tag(name: 'Sys Menu', description: 'CRUD Manajemen Menu Sistem')]
#[OA\Tag(name: 'Sys Groups', description: 'CRUD Manajemen Grup / Role Pengguna')]
#[OA\Tag(name: 'Sys Group Permissions', description: 'Manajemen Hak Akses Role (Mapping, Sync, Add/Remove Action)')]
abstract class Controller
{
    //
}
