<?php 
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataType;
use App\Models\DataRow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BreadController extends Controller
{
    public function index($slug)
    {
        $dataType = DataType::where('slug', $slug)->with('rows')->firstOrFail();
        $model = app($dataType->model_name);
        $items = $model::all();

        return Inertia::render('Admin/Bread/Index', [
            'dataType' => $dataType,
            'items' => $items,
        ]);
    }

    public function create($slug)
    {
        $dataType = DataType::where('slug', $slug)->with('rows')->first();

        if (!$dataType) {
            $tableColumns = DB::getSchemaBuilder()->getColumnListing($slug);
            $modelName = 'App\\Models\\' . ucfirst($slug);

            $dataType = DataType::create([
                'name' => ucfirst($slug),
                'slug' => $slug,
                'model_name' => $modelName,
                'controller_name' => null,
                'policy_name' => null,
                'generate_permissions' => false,
                'icon' => 'fas fa-table',
                'scope' => null,
                'order_column' => 'id',
                'order_direction' => 'asc',
            ]);

            foreach ($tableColumns as $column) {
                $type = $this->mapColumnTypeToInput($slug, $column);
                DataRow::create([
                    'data_type_id' => $dataType->id,
                    'field' => $column,
                    'type' => $type,
                    'display_name' => ucfirst(str_replace('_', ' ', $column)),
                    'required' => $column === 'id',
                    'browse' => true,
                    'read' => true,
                    'edit' => $column !== 'id',
                    'add' => $column !== 'id',
                    'delete' => true,
                    'order' => 1,
                ]);
            }

            $dataType = DataType::where('slug', $slug)->with('rows')->first();
        }

        return Inertia::render('Admin/Bread/Create', ['dataType' => $dataType]);
    }

    public function store(Request $request, $slug)
    {
        $dataType = DataType::where('slug', $slug)->with('rows')->firstOrFail();
        $model = app($dataType->model_name);

        // Lấy danh sách các trường được phép thêm từ data_rows
        $allowedFields = $dataType->rows->where('add', true)->pluck('field')->all();
        $rules = $dataType->rows->where('add', true)
            ->pluck('required', 'field')
            ->map(fn($req) => $req ? 'required' : 'nullable')
            ->all();

        // Validate dữ liệu gửi lên
        $validatedData = $request->validate($rules);

        // Lọc dữ liệu chỉ lấy các trường được phép
        $dataToSave = array_intersect_key($validatedData, array_flip($allowedFields));

        // Tạo bản ghi mới
        $item = new $model();
        foreach ($dataToSave as $key => $value) {
            $item->$key = $value; // Gán từng trường bằng tay
        }
        $item->save();

        return redirect()->route('admin.bread.index', $slug)->with('message', 'Item created successfully!');
    }

    public function show($slug, $id)
    {
        $dataType = DataType::where('slug', $slug)->with('rows')->firstOrFail();
        $model = app($dataType->model_name);
        $item = $model::findOrFail($id);

        return Inertia::render('Admin/Bread/Show', ['dataType' => $dataType, 'item' => $item]);
    }

    public function edit($slug, $id)
    {
        $dataType = DataType::where('slug', $slug)->with('rows')->firstOrFail();
        $model = app($dataType->model_name);
        $item = $model::findOrFail($id);

        return Inertia::render('Admin/Bread/Edit', ['dataType' => $dataType, 'item' => $item]);
    }

    public function update(Request $request, $slug, $id)
    {
        $dataType = DataType::where('slug', $slug)->with('rows')->firstOrFail();
        $model = app($dataType->model_name);
        $item = $model::findOrFail($id);

        // Lấy danh sách các trường được phép sửa từ data_rows
        $allowedFields = $dataType->rows->where('edit', true)->pluck('field')->all();
        $rules = $dataType->rows->where('edit', true)
            ->pluck('required', 'field')
            ->map(fn($req) => $req ? 'required' : 'nullable')
            ->all();

        // Validate dữ liệu gửi lên
        $validatedData = $request->validate($rules);

        // Lọc dữ liệu chỉ lấy các trường được phép
        $dataToSave = array_intersect_key($validatedData, array_flip($allowedFields));

        // Cập nhật bản ghi
        foreach ($dataToSave as $key => $value) {
            $item->$key = $value; // Gán từng trường bằng tay
        }
        $item->save();

        return redirect()->route('admin.bread.index', $slug)->with('message', 'Item updated successfully!');
    }

    public function destroy($slug, $id)
    {
        $dataType = DataType::where('slug', $slug)->firstOrFail();
        $model = app($dataType->model_name);
        $item = $model::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.bread.index', $slug)->with('message', 'Item deleted successfully!');
    }

    public function configure($slug)
    {
        $dataType = DataType::where('slug', $slug)->with('rows')->firstOrFail();
        return Inertia::render('Admin/Bread/Configure', [
            'dataType' => $dataType,
        ]);
    }

    public function saveConfiguration(Request $request, $slug)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'model_name' => 'required|string|max:255',
            'controller_name' => 'nullable|string|max:255',
            'policy_name' => 'nullable|string|max:255',
            'generate_permissions' => 'boolean',
            'icon' => 'nullable|string|max:255',
            'scope' => 'nullable|string|max:255',
            'order_column' => 'nullable|string|max:255',
            'order_direction' => 'nullable|in:asc,desc',
            'rows' => 'array',
            'rows.*.field' => 'required|string|max:255',
            'rows.*.type' => 'required|string|max:255',
            'rows.*.display_name' => 'required|string|max:255',
            'rows.*.required' => 'boolean',
            'rows.*.browse' => 'boolean',
            'rows.*.read' => 'boolean',
            'rows.*.edit' => 'boolean',
            'rows.*.add' => 'boolean',
            'rows.*.delete' => 'boolean',
            'rows.*.order' => 'integer',
            'rows.*.details' => 'nullable|string',
        ]);

        $dataType = DataType::where('slug', $slug)->firstOrFail();
        $dataType->update([
            'name' => $request->input('name'),
            'slug' => $request->input('slug'),
            'model_name' => $request->input('model_name'),
            'controller_name' => $request->input('controller_name'),
            'policy_name' => $request->input('policy_name'),
            'generate_permissions' => $request->input('generate_permissions', false),
            'icon' => $request->input('icon'),
            'scope' => $request->input('scope'),
            'order_column' => $request->input('order_column'),
            'order_direction' => $request->input('order_direction', 'asc'),
        ]);

        $existingRows = $dataType->rows->pluck('id')->all();
        $submittedRows = collect($request->input('rows'))->pluck('id')->filter()->all();

        DataRow::where('data_type_id', $dataType->id)->whereNotIn('id', $submittedRows)->delete();

        foreach ($request->input('rows', []) as $rowData) {
            DataRow::updateOrCreate(
                ['id' => $rowData['id'] ?? null, 'data_type_id' => $dataType->id],
                [
                    'field' => $rowData['field'],
                    'type' => $rowData['type'],
                    'display_name' => $rowData['display_name'],
                    'required' => $rowData['required'],
                    'browse' => $rowData['browse'],
                    'read' => $rowData['read'],
                    'edit' => $rowData['edit'],
                    'add' => $rowData['add'],
                    'delete' => $rowData['delete'],
                    'order' => $rowData['order'],
                    'details' => $rowData['details'] ? json_encode($rowData['details']) : null,
                ]
            );
        }

        if ($request->input('generate_permissions')) {
            $permissions = ['browse_' . $request->input('slug'), 'read_' . $request->input('slug'), 'edit_' . $request->input('slug'), 'add_' . $request->input('slug'), 'delete_' . $request->input('slug')];
        }

        return redirect()->route('admin.bread.index', $dataType->slug)->with('message', 'BREAD configuration saved!');
    }

    private function mapColumnTypeToInput($slug, $column)
    {
        $typeMap = [
            'varchar' => 'text',
            'text' => 'textarea',
            'int' => 'number',
            'bigint' => 'number',
            'smallint' => 'number',
            'tinyint' => 'number',
            'float' => 'number',
            'double' => 'number',
            'decimal' => 'number',
            'date' => 'date',
            'datetime' => 'datetime-local',
            'time' => 'time',
            'timestamp' => 'datetime-local',
            'year' => 'number',
            'tinyint(1)' => 'checkbox',
            'json' => 'textarea',
        ];

        $columnType = DB::getSchemaBuilder()->getColumnType($slug, $column);
        return array_key_exists($columnType, $typeMap) ? $typeMap[$columnType] : 'text';
    }
}