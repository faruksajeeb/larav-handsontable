<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Assuming you have a `Product` model

class ProductController extends Controller
{
    public function index()
    {
        // Fetch data from the database
        $products = Product::all(); // Adjust this query as needed

        // Return data as JSON
        return response()->json($products);
    }
}