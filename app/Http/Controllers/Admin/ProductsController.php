<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Interfaces\ProductManagerServiceInterface;
use App\Models\Product;
use Database\Factories\ProductFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductsController extends Controller
{
    private $productManager;
    public function __construct()
    {
        $this->productManager = App::make(ProductManagerServiceInterface::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.products.index', [
            "products" => Product::paginate(5)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('admin.products.create');
    }

    /**
     * @param ProductRequest $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function store(ProductRequest $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validated();
            unset($validated['image']);
            try {
                DB::beginTransaction();
                if ($product = $this->productManager->processRequestData(ProductFactory::new($validated)->create(), __('Product was created'))) {
                    DB::commit();
                    return redirect()->action([self::class, 'edit'], ['product' => $product->id]);
                }
            } catch (\Exception $exception) {
                DB::rollback();
                Log::error($exception->getMessage());
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('admin.products.edit', [
            'product' => Product::find($id)
        ]);
    }

    /**
     * @param ProductRequest $request
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function update(ProductRequest $request, Product  $product)
    {
        if ($request->getMethod() == "PATCH") {
            try {
                DB::beginTransaction();
                $product->update($request->validated());
                if ($product = $this->productManager->processRequestData($product, __('Product was updated'))) {
                    DB::commit();
                    return redirect()->action([self::class, 'edit'], ['product' => $product->id]);
                }
            } catch (\Exception $exception) {
                DB::rollback();
                Log::error($exception->getMessage());
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
