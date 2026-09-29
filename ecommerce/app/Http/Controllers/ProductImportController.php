<?php

namespace App\Http\Controllers;

use App\Imports\ProductsImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class ProductImportController extends Controller
{
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,ods,csv'],
        ]);

        try {
            Excel::import(
                new ProductsImport,
                $request->file('file')
            );
        } catch (ValidationException $e) {
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
