<?php

namespace App\Http\Controllers\Api;

use App\Model\Product;
use Illuminate\Http\Request;

class ProductController extends BaseController
{

    const PER_PAGE = 20;

    public function products(Request $request)
    {
        $page = (int)$request->query('page');
        $perPage = (int)$request->query('per_page');

        if ($page <= 0) {
            $page = 1;
        }

        if ($perPage <= 0) {
            $perPage = self::PER_PAGE;
        }

        $skip = ($page - 1)*$perPage;
        $data = Product::skip($skip)->take($perPage)->get();

        // Eloquent
        $total = Product::count();

        return $this->success([
            'data' => $data,
            'total' => $total,
        ]);
    }

}
