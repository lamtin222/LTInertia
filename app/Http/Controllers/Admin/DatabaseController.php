<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class DatabaseController extends Controller
{
    public function index()
    {
        $tables = DB::select('SHOW TABLES');
        $tableNames = array_map(fn($table) => array_values((array)$table)[0], $tables);
        // Danh sách bảng cần ẩn
        $hiddenTables = [
            'cache',
            'cache_locks',
            'failed_jobs',
            'job_batches',
            'jobs',
            'migrations',
            'password_reset_tokens',
            'sessions',
        ];

        // Lọc bỏ các bảng không mong muốn
        $filteredTables = array_filter($tableNames, fn($table) => !in_array($table, $hiddenTables));

        return Inertia::render('Admin/Database/Index', [
            'tables' => array_values($filteredTables), // Đảm bảo mảng được đánh số lại
        ]);
    }

    public function show($table)
    {
        $columns = DB::select("SHOW COLUMNS FROM {$table}");
        return Inertia::render('Admin/Database/Show', [
            'table' => $table,
            'columns' => $columns,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Database/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|regex:/^[a-z_]+$/',
            'columns' => 'required|array|min:1',
            'columns.*.name' => 'required|string|regex:/^[a-z_]+$/',
            'columns.*.type' => 'required|in:string,text,char,integer,bigInteger,smallInteger,tinyInteger,float,double,decimal,date,dateTime,time,timestamp,year,boolean,json,timestamps',
            'columns.*.length' => 'nullable|integer|min:1',
            'columns.*.required' => 'boolean',
            'columns.*.default' => 'nullable|string',
            'columns.*.comment' => 'nullable|string',
        ]);

        $tableName = $request->input('name');
        $columns = $request->input('columns');

        Schema::create($tableName, function ($table) use ($columns) {
            $table->id(); // Cột id mặc định
            foreach ($columns as $column) {
                $method = $column['type'];
                $columnName = $column['name'];

                if ($method === 'timestamps') {
                    $table->$method();
                } else {
                    // Áp dụng length nếu có (cho string, char, v.v.)
                    $col = in_array($method, ['string', 'char']) && !empty($column['length'])
                        ? $table->$method($columnName, $column['length'])
                        : $table->$method($columnName);

                    // Required (NOT NULL)
                    if ($column['required']) {
                        $col->nullable(false);
                    } else {
                        $col->nullable();
                    }

                    // Default value
                    if (!empty($column['default'])) {
                        $col->default($column['default']);
                    }

                    // Comment
                    if (!empty($column['comment'])) {
                        $col->comment($column['comment']);
                    }
                }
            }
        });

        // Thêm vào data_types cho BREAD
        \App\Models\DataType::create([
            'name' => ucfirst($tableName),
            'slug' => $tableName,
            'model_name' => 'App\\Models\\' . ucfirst($tableName),
        ]);

        return redirect()->route('admin.database.index')->with('message', 'Table created successfully!');
    }
    public function edit($table)
    {
        $columns = DB::select("SHOW COLUMNS FROM {$table}");
        return Inertia::render('Admin/Database/Edit', [
            'table' => $table,
            'columns' => $columns,
        ]);
    }

    public function update(Request $request, $table)
    {
        $request->validate([
            'name' => 'required|string|regex:/^[a-z_]+$/',
            'columns' => 'required|array|min:1',
            'columns.*.name' => 'required|string|regex:/^[a-z_]+$/',
            'columns.*.type' => 'required|in:string,text,char,integer,bigInteger,smallInteger,tinyInteger,float,double,decimal,date,dateTime,time,timestamp,year,boolean,json',
            'columns.*.length' => 'nullable|integer|min:1',
            'columns.*.required' => 'boolean',
            'columns.*.unsigned' => 'boolean',
            'columns.*.auto_increment' => 'boolean', // Thêm validate cho auto_increment
            'columns.*.default' => 'nullable|string',
            'columns.*.comment' => 'nullable|string',
            'columns.*.old_name' => 'nullable|string',
            'columns.*.action' => 'nullable|in:modify,drop',
        ]);

        $newTableName = $request->input('name');
        $columns = $request->input('columns');

        if ($table !== $newTableName) {
            Schema::rename($table, $newTableName);
            $table = $newTableName;

            $dataType = \App\Models\DataType::where('slug', $table)->first();
            if ($dataType) {
                $dataType->update([
                    'name' => ucfirst($newTableName),
                    'slug' => $newTableName,
                    'model_name' => 'App\\Models\\' . ucfirst($newTableName),
                ]);
            }
        }

        Schema::table($table, function ($t) use ($columns) {
            foreach ($columns as $column) {
                $action = $column['action'] ?? 'modify';
                $oldName = $column['old_name'] ?? $column['name'];
                $newName = $column['name'];

                if ($action === 'drop') {
                    $t->dropColumn($oldName);
                } else {
                    $method = $column['type'];
                    if (in_array($method, ['string', 'char']) && !empty($column['length'])) {
                        $col = $t->$method($newName, $column['length']);
                    } else {
                        $col = $t->$method($newName);
                    }

                    if ($column['required']) {
                        $col->nullable(false);
                    } else {
                        $col->nullable();
                    }

                    if ($column['unsigned'] && in_array($method, ['integer', 'bigInteger', 'smallInteger', 'tinyInteger'])) {
                        $col->unsigned();
                    }

                    if ($column['auto_increment'] && in_array($method, ['integer', 'bigInteger', 'smallInteger', 'tinyInteger'])) {
                        $col->autoIncrement();
                    }

                    if (!empty($column['default'])) {
                        $col->default($column['default']);
                    } else {
                        $col->default(null);
                    }

                    if (!empty($column['comment'])) {
                        $col->comment($column['comment']);
                    } else {
                        $col->comment(null);
                    }

                    if ($oldName !== $newName) {
                        $t->renameColumn($oldName, $newName);
                    }

                    $col->change(); // Áp dụng thay đổi
                }
            }
        });

        return redirect()->route('admin.database.index')->with('message', 'Table updated successfully!');
    }

    public function destroy($table)
    {
        Schema::dropIfExists($table);

        // Xóa data_types liên quan
        \App\Models\DataType::where('slug', $table)->delete();

        return redirect()->route('admin.database.index')->with('message', 'Table deleted successfully!');
    }
    public function columns($table)
    {
        $columns = DB::select("SHOW COLUMNS FROM {$table}");
        return response()->json($columns);
    }
}