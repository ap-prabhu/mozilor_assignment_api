<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Product;


class ProductController extends Controller
{
    
    public function listProducts(Request $request)
    {
        $products = Product::query();

        if ($request->has('search')) {
            $search = $request->search;
            $products->where(function($query) use ($search) {
                $query->where('product_name', 'LIKE', "%{$search}%")
                    ->orWhere('sku', 'LIKE', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 10); 
        $response_data = $products->paginate($perPage);

        $response_data = $response_data->toArray();
        $response_data['status'] = 1; 

        return response()->json($response_data, 201);
    }

    public function importProducts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'imported_file' => 'required|file|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return response()->json([ 'status'=>0, 'error' => $validator->errors()], 400);
        }

        $file = $request->file('imported_file');
        $filePath = $file->getRealPath();

        $fileHandle = fopen($filePath, 'r');
        $header = fgetcsv($fileHandle);  // Get the header row

        $insertData = [];
        $Invalid = [];
        $duplicates = [];
        $rowNumber = 1;
        while (($row = fgetcsv($fileHandle)) !== false) {
            $rowNumber++;

            $data = array_combine($header, $row);

            $productData = [
                'product_name' => $data['Product Name'] ?? null,
                'price' => $data['Price'] ?? null,
                'sku' => $data['SKC'] ?? null,
                'description' => $data['Description'] ?? null,
                'created_by' => $request->user()->id,
                'created_at' => now(),
            ];

            $validation = Validator::make($productData, [
                'product_name' => 'required|string|max:255',
                'price' => 'required|numeric',
                'price' => 'required|',
                'sku' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            if ($validation->fails()) {
                $Invalid[] = [
                    'row' => $rowNumber,
                    'errors' => $validation->errors()->all()
                ];
            } else {
                $existingProduct = Product::where('product_name', $productData['product_name'])->first();
                if ($existingProduct) {
                    $duplicates[] = [
                        'row' => $rowNumber,
                        'product_name' => $productData['product_name'],
                    ];
                } else {
                    $insertData[] = $productData;
                }
            }
        }
        fclose($fileHandle);
        if (!empty($Invalid) || !empty($duplicates)) {
            $errors['Invalid'] = $Invalid;
            $errors['duplicates'] = $duplicates;
            return response()->json(['status'=>0, 'error' => $errors], 400);
        } else {
            Product::insert($insertData);
            return response()->json(['status'=>1, 'message' => 'Products imported successfully'], 201);
        }
    }   
}
