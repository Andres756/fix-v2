<?php

namespace App\Http\Controllers\api\Inventario;

use App\Http\Controllers\Controller;
use App\Exports\InventarioExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class InventarioExportController extends Controller
{
    public function exportar(Request $request)
    {
        $data = $request->all();
        $filename = 'inventario_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new InventarioExport($data), $filename);
    }
}
