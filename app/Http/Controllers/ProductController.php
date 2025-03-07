<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        return view('products.index', ['categories' => Category::all()]);
    }

    public function getProducts(Request $req)
    {
        if ($req->ajax()) {
            $products = Product::with('category');
            
            if ($req->category_id != 0 || $req->category_id != '')
            {
                $categoryId = $req->category_id;
                $products->whereHas('category', function ($query) use ($categoryId) {
                    $query->where('id', $categoryId);
                });
            }
        
            $products->select('products.*');
            
            return DataTables::of($products)
                ->addColumn('image', function ($product) {
                    return asset('product_images/' . $product->image_path);
                })
                ->addColumn('category', function ($product) {
                    return $product->category->name;
                })
                ->addColumn('action', function ($product) {
                    
                    return '<a href="' . route("products.edit", $product->id) . '" class="btn btn-warning btn-sm">Edit</a>
                            <button class="btn btn-danger btn-sm delete" data-id="' . $product->id . '">Delete</button>
                            <form action="'. route("products.destroy", $product->id) .'" method="POST" style="display:inline-block;">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                <button type="submit" class="btn btn-danger" onclick="return confirm("Yakin ingin menghapus?")">Hapus</button>
                            </form>';
                })
                ->rawColumns(['image', 'action'])
                ->make(true);
        }
    }

    public function create()
    {
        return view('products.input', ['categories' => Category::all()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:products,name|max:255',
            'category_id' => 'required|exists:categories,id',
            'purchase_price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpeg,png|max:1024',
        ]);

        $product = new Product();
        $product->name = $validated['name'];
        $product->category_id = $validated['category_id'];
        $product->purchase_price = $validated['purchase_price'];
        $product->selling_price = $validated['purchase_price'] * 1.30;
        $product->stock = $validated['stock'];

        if ($request->hasFile('image')) {
            $latestProductId = DB::table('products')
            ->latest('id')
            ->value('id');

            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $fileName = ($latestProductId + 1) . '_' . str_replace(['/', '\\', ' '], '_', strtolower($validated['name'])) . '.' . $extension;
            $image->move(public_path('product_images'), $fileName);
            $product->image_path = $fileName;
        }

        $product->save();

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.input', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|unique:products,name,' . $id . '|max:255',
            'category_id' => 'required|exists:categories,id',
            'purchase_price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png|max:100',
        ]);

        $product = Product::findOrFail($id);
        $product->name = $validated['name'];
        $product->category_id = $validated['category_id'];
        $product->purchase_price = $validated['purchase_price'];
        $product->selling_price = $validated['purchase_price'] * 1.30;
        $product->stock = $validated['stock'];

        if ($request->hasFile('image')) {
            if ($product->image_path && file_exists(public_path('product_images/' . $product->image_path))) {
                unlink(public_path('product_images/' . $product->image_path));
            }

            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $fileName = $validated['category_id'] . '_' . str_replace(['/', '\\', ' '], '_', strtolower($validated['name'])) . '.' . $extension;
            $image->move(public_path('product_images'), $fileName);
            $product->image_path = $fileName;
        }

        $product->save();

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted');
    }

    public function exportExcel(Request $req)
    {
        $products = Product::with('category');
        
        if ($req->category_id != 0 || $req->category_id != '')
        {
            $categoryId = $req->category_id;
            $products->whereHas('category', function ($query) use ($categoryId) {
                $query->where('id', $categoryId);
            });
        }
        
        $products= $products->get();
        
        // Path untuk file Excel
        $filePath = storage_path('app/public/products.xlsx');

        // Buat writer
        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($filePath);

        // Style untuk header
        $headerStyle = (new StyleBuilder())->setFontBold()->build();

        // Header
        $headerRow = WriterEntityFactory::createRowFromArray(
            ['No', 'Nama Produk', 'Kategori', 'Harga Beli', 'Harga Jual', 'Stok'],
            $headerStyle
        );
        $writer->addRow($headerRow);

        // Data produk
        foreach ($products as $index => $product) {
            $row = WriterEntityFactory::createRowFromArray([
                $index + 1,
                // asset('product_images/' . $product->image_path),
                $product->name,
                $product->category->name,
                $product->purchase_price,
                $product->selling_price,
                $product->stock,
            ]);
            $writer->addRow($row);
        }

        $writer->close();

        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}
