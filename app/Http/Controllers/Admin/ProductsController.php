<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Database\Factories\ProductFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Http\Service\ProductManager;

class ProductsController extends Controller
{
    public function __construct(private ProductManager $productManager)
    {
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $this->productManager->validateRequestData($request);
            try {
                DB::beginTransaction();
                if ($product = $this->productManager->processRequestData($request, ProductFactory::new($validated)->create(), __('Product was created'))) {
                    DB::commit();
                    return redirect()->action([self::class, 'update'], ['id' => $product->id]);
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        if ($product = Product::where('id', $id)->first()) {

            if ($request->getMethod() == "POST") {
                $validated = $this->productManager->validateRequestData($request);
                try {
                    DB::beginTransaction();
                    $product->update($validated);
                    if ($product = $this->productManager->processRequestData($request, $product, __('Product was updated'))) {
                        DB::commit();
                        return redirect()->action([self::class, 'update'], ['id' => $product->id]);
                    }
                } catch (\Exception $exception) {
                    DB::rollback();
                    Log::error($exception->getMessage());
                }
            }

            return view('admin.products.update', [
                'product' => $product
            ]);
        }
        throw new NotFoundHttpException();
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
