<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organization; 
use Illuminate\Support\Facades\Response;

class OrganizationController extends Controller
{
    
    
    public function index(Request $request)
    {
        
        // Get paginated data
        $perPage = $request->input('perPage', 10); // Number of rows per page
        $page = $request->input('page', 1); // Current page

        $organizations = Organization::with('account')->paginate($perPage, ['*'], 'page', $page);

        // Transform data for Handsontable
        $formattedData = $organizations->map(function ($organization) {
            return [
                'id' => $organization->id,
                'name' => $organization->name,
                'account_name' => $organization->account->name,
                'email' => $organization->email,
                'phone' => $organization->phone,
                'address' => $organization->address,
                'city' => $organization->city,
                'region' => $organization->region,
                'country' => $organization->country,
                'postal_code' => $organization->postal_code,
                'created_at' => $organization->created_at,
                'updated_at' => $organization->updated_at,
            ];
        });

        return response()->json([
            'data' => $formattedData,
            'total' => $organizations->total(), // Total number of records
            'perPage' => $organizations->perPage(), // Rows per page
            'currentPage' => $organizations->currentPage(), // Current page
            'lastPage' => $organizations->lastPage(), // Last page
        ]);
    }

    // public function index(Request $request)
    // {
    //     $offset = $request->input('offset', 0);
    //     $limit = $request->input('limit', 30);

    //     $products = Organization::offset($offset)->limit($limit)->get();

    //     // Transform data for Handsontable
    //     $formattedData = $products->map(function ($organization) {
    //         return [
    //             'id' => $organization->id,
    //             'name' => $organization->name,
    //             'email' => $organization->email,
    //             'phone' => $organization->phone,
    //             'address' => $organization->address,
    //             'city' => $organization->city,
    //             'region' => $organization->region,
    //             'country' => $organization->country,
    //             'postal_code' => $organization->postal_code,
    //         ];
    //     });

    //     return response()->json([
    //         'data' => $formattedData,
    //         'offset' => $offset,
    //         'limit' => $limit,
    //     ]);
    // }

    public function all()
    {
        // Set higher memory limit and execution time
        ini_set('memory_limit', '2G');
        ini_set('max_execution_time', 0); // Disable execution time limit

        // Define the headers for the JSON response
        $headers = [
            'Content-Type' => 'application/json',
        ];

        // Create a StreamedResponse to stream the data
        return Response::stream(function () {
            // Open the JSON array
            echo '[';

            // Fetch data in chunks
            $firstChunk = true;
            Organization::chunk(10000, function ($organizations) use (&$firstChunk) {
                foreach ($organizations as $organization) {
                    // Add a comma between JSON objects (except for the first one)
                    if (!$firstChunk) {
                        echo ',';
                    } else {
                        $firstChunk = false;
                    }

                    // Output the JSON object for the current row
                    echo json_encode([
                        'id' => $organization->id,
                        'name' => $organization->name,
                        'email' => $organization->email,
                        'phone' => $organization->phone,
                        'address' => $organization->address,
                        'city' => $organization->city,
                        'region' => $organization->region,
                        'country' => $organization->country,
                        'postal_code' => $organization->postal_code,
                        'created_at' => $organization->created_at,
                        'updated_at' => $organization->updated_at,
                    ]);
                }
            });

            // Close the JSON array
            echo ']';
        }, 200, $headers);
    }
}