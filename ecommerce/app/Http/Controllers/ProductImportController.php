<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;

class ProductImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,ods,csv'],
        ]);

        try {
            Excel::import(
                new ProductsImport,
                $request->file('file')
            );
        } 
        catch (ValidationException $e) {
            return redirect()
                ->route('admin.products.index')
                ->withErrors($e->errors())
                ->withInput();
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Products imported successfully.');
    }
}
