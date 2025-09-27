<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use App\Http\Requests\Address\CreateAddressRequest;
use App\Http\Requests\Address\UpdateAddressRequest;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $query = Address::query();

        if ($request->has('search') && $request->get('search') != '') {
            $search = $request->get('search');
            $query->where('owner_name', 'LIKE', "%{$search}%")
                  ->orWhere('tel', 'LIKE', "%{$search}%")
                  ->orWhere('state', 'LIKE', "%{$search}%")
                  ->orWhere('city', 'LIKE', "%{$search}%")
                  ->orWhere('ward', 'LIKE', "%{$search}%")
                  ->orWhere('room_no', 'LIKE', "%{$search}%");
        }

        $addresses = $query->orderBy('id', 'desc')->paginate(15)->appends(request()->query());

        return view('address.list', compact('addresses'));
    }

    public function create()
    {
        $customers = User::where('role_id', Role::CUSTOMER_ROLE_ID)->get(); // Only customers
        return view('address.add', compact('customers'));
    }

    public function store(CreateAddressRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = Auth::user()->id;
        Address::create($data);

        return redirect()->route('addresses.index')->with('success', __('address.address_added_successfully'));
    }

    public function show(Address $address)
    {
        // disable route
        abort(404);
        return view('address.view', compact('address'));
    }

    public function edit(Address $address)
    {
        $customers = User::where('role_id', Role::CUSTOMER_ROLE_ID)->get(); // Only customers
        return view('address.edit', compact('address', 'customers'));
    }

    public function update(UpdateAddressRequest $request, Address $address)
    {
        $address->update($request->validated());

        return redirect()->route('addresses.index')->with('success', __('address.address_updated_successfully'));
    }

    public function destroy(Address $address)
    {
        $address->delete();

        return redirect()->route('addresses.index')->with('success', __('address.address_deleted_successfully'));
    }

    public function searchByPostalCode(Request $request)
    {
        $postalCode = $request->input('postal_code');

        // Validate postal code format (7 digits for Japan)
        if (!preg_match('/^\d{7}$/', $postalCode)) {
            return response()->json(['error' => 'Invalid postal code format'], 400);
        }

        // Check if Yahoo API key is configured
        $yahooAppId = config('services.yahoo.app_id');
        if (!$yahooAppId || $yahooAppId === '') {
            return response()->json(['error' => 'Yahoo API key not configured'], 500);
        }

        // Yahoo Post Code API endpoint
        $url = "https://map.yahooapis.jp/zip/V1/zipCodeSearch";

        try {
            Log::info('Yahoo API request', [
                'url' => $url,
                'params' => [
                    'appid' => $yahooAppId,
                    'query' => $postalCode,
                    'output' => 'json'
                ]
            ]);

            $response = Http::get($url, [
                'appid' => $yahooAppId,
                'query' => $postalCode,
                'output' => 'json'
            ]);

            Log::info('Yahoo API response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('Yahoo API parsed data', ['data' => $data]);

                if ($data && isset($data['Feature'])) {
                    $features = $data['Feature'];
                    if (!empty($features)) {
                        $firstFeature = $features[0];
                        $properties = $firstFeature['Property'] ?? [];
                        $address = $firstFeature['Name'] ?? '';

                        // Extract address components
                        $addressComponents = $this->extractAddressComponents($address);

                        return response()->json([
                            'state' => $addressComponents['state'],
                            'city' => $addressComponents['city'],
                            'ward' => $addressComponents['ward'],
                            'address' => $address
                        ]);
                    }
                }

                return response()->json(['error' => 'Postal code not found'], 404);
            } else {
                Log::error('Yahoo API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json(['error' => 'Failed to fetch address data'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Yahoo API exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to fetch address data'], 500);
        }
    }

    private function extractAddressComponents($addressString)
    {
        // This is a simplified extraction - in practice, you'd want more robust parsing
        $components = [
            'state' => '',
            'city' => '',
            'ward' => ''
        ];

        // Extract components based on Japanese address format
        // Format: [Prefecture][City][Ward/Suburb][Other]
        $patterns = [
            '/^(.{1,3}県|.{1,3}府|.{1,3}都|.{1,3}道)/u', // Match prefecture (県, 府, 都, 道)
        ];

        $prefecture = '';
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $addressString, $matches)) {
                $prefecture = $matches[1];
                $addressString = trim(str_replace($prefecture, '', $addressString));
                break;
            }
        }

        // Extract city and ward - this is a basic implementation
        // In practice, you'd want more sophisticated parsing
        $remainingParts = explode(' ', $addressString);
        if (count($remainingParts) >= 2) {
            $components['state'] = $prefecture;
            $components['city'] = $remainingParts[0];
            $components['ward'] = $remainingParts[1];
        } else {
            // Fallback to just assign to city if we can't split properly
            $components['state'] = $prefecture;
            $components['city'] = $addressString;
        }

        return $components;
    }
}
