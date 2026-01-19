<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeCrud extends Command
{
    protected $signature = 'make:crud {name} {--table=}';
    protected $description = 'Generate Model & Controller from database table';

    public function handle()
    {
        $name = $this->argument('name');
        $table = $this->option('table') ?? Str::snake(Str::pluralStudly($name));
        $modelName = Str::studly($name);
        $ctrlName = $modelName . 'Controller';

        $this->info("Generating CRUD for table: {$table}");

        $columns = DB::getSchemaBuilder()->getColumnListing($table);

        $this->generateModel($modelName, $table, $columns);
        $this->generateController($modelName, $ctrlName);
        $this->generateRoute(
            $modelName,
            $table
        );


        $this->info('✔ CRUD generated successfully');
    }

    protected function generateModel($modelName, $table, $columns)
    {
        $fillable = collect($columns)
            ->reject(fn($c) => in_array($c, ['id', 'created_at', 'updated_at', 'deleted_at']))
            ->map(fn($c) => "'{$c}'")
            ->implode(",\n        ");

        $template = <<<PHP
<?php

namespace App\Models;

class {$modelName} extends BaseModel
{
    protected \$table = '{$table}';

    protected \$fillable = [
        {$fillable}
    ];

    protected \$orderDefault = 'id desc';

    public function scopeSearch(\$query, \$search)
    {
        if (!\$search) return \$query;

        foreach (\$search as \$field => \$value) {
            if (!empty(\$value)) {
                \$query->where(\$field, 'like', \$value);
            }
        }

        return \$query;
    }
}
PHP;

        File::put(app_path("Models/{$modelName}.php"), $template);
    }

    protected function generateController($modelName, $ctrlName)
    {
        $template = <<<PHP
<?php

namespace App\Http\Controllers;

use App\Models\\{$modelName};
use Illuminate\Http\Request;

class {$ctrlName} extends BaseController
{
    protected \$model;

    public function __construct({$modelName} \$model)
    {
        \$this->model = \$model;
    }
}
PHP;

        File::put(app_path("Http/Controllers/{$ctrlName}.php"), $template);
    }

    protected function generateRoute(string $modelName, string $table)
    {
        $routeFile = base_path('routes/api.php');

        $uri = str_replace('_', '-', $table);
        $controller = "\\App\\Http\\Controllers\\{$modelName}Controller::class";

        $routeBlock = <<<PHP

Route::middleware('auth:sanctum')
    ->group(function () {
        Route::apiResource('{$uri}', {$controller});
    });

PHP;

        $routesContent = file_get_contents($routeFile);

        if (str_contains($routesContent, "Route::apiResource('{$uri}'")) {
            $this->warn("Route '{$uri}' already exists, skipping...");
            return;
        }

        file_put_contents($routeFile, $routeBlock, FILE_APPEND);

        $this->info("✔ Secured API route added (auth:sanctum): /api/{$uri}");
    }


}
