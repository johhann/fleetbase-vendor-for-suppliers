<?php

namespace Fleetbase\VendorsForSuppliers\Http\Controllers;

use Fleetbase\Http\Controllers\FleetbaseController;
use Fleetbase\VendorManagement\Models\Vendor;
use Fleetbase\VendorManagement\Http\Requests\CreateVendorRequest;
use Fleetbase\VendorManagement\Http\Requests\UpdateVendorRequest;
use Fleetbase\VendorsForSuppliers\Http\Requests\CreateVendorsForSuppliersRequest;
use Fleetbase\VendorsForSuppliers\Http\Requests\UpdateVendorsForSuppliersRequest;
use Fleetbase\VendorsForSuppliers\Http\Resources\VendorsForSupplierResource;
use Fleetbase\VendorsForSuppliers\Models\VendorsForSupplier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class VendorsForSuppliersResourceController extends FleetbaseController
{
    /**
     * The package namespace used to resolve from.
     */
    public string $namespace = '\Fleetbase\VendorsForSuppliers';

    /**
     * Display a listing of vendors.
     */
    public function index(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);
        $vendors = VendorsForSupplier::where('company_uuid', session('company'))
            ->with(['qrCode'])
            ->paginate($limit);

        return response()->json([
            'vendors' => VendorsForSupplierResource::collection($vendors->items()),
            'meta' => [
                'total' => $vendors->total(),
                'per_page' => $vendors->perPage(),
                'current_page' => $vendors->currentPage(),
                'last_page' => $vendors->lastPage(),
            ]
        ]);
    }

    /**
     * Store a newly created vendor.
     */
    public function store(CreateVendorsForSuppliersRequest $request): JsonResponse
    {
        $vendor = VendorsForSupplier::create([
            'company_uuid' => session('company'),
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => $request->get('status', 'active'),
        ]);

        // Generate QR code
        $this->generateQrCode($vendor);

        return response()->json([
            'vendor' => new VendorsForSupplierResource($vendor->load('qrCode'))
        ], 201);
    }

    /**
     * Display the specified vendor.
     */
    public function show(string $id): JsonResponse
    {
        $vendor = VendorsForSupplier::where('company_uuid', session('company'))
            ->with(['qrCode'])
            ->findOrFail($id);

        return response()->json([
            'vendor' => new VendorsForSupplierResource($vendor)
        ]);
    }

    /**
     * Update the specified vendor.
     */
    public function update(UpdateVendorsForSuppliersRequest $request, string $id): JsonResponse
    {
        $vendor = VendorsForSupplier::where('company_uuid', session('company'))
            ->findOrFail($id);

        $vendor->update($request->validated());

        // Regenerate QR code if needed
        if ($request->has(['name', 'email', 'phone'])) {
            $this->generateQrCode($vendor);
        }

        return response()->json([
            'vendor' => new VendorsForSupplierResource($vendor->load('qrCode'))
        ]);
    }

    /**
     * Remove the specified vendor.
     */
    public function destroy(string $id): JsonResponse
    {
        $vendor = VendorsForSupplier::where('company_uuid', session('company'))
            ->findOrFail($id);

        // Delete QR code file if exists
        if ($vendor->qr_code_path && Storage::exists($vendor->qr_code_path)) {
            Storage::delete($vendor->qr_code_path);
        }

        $vendor->delete();

        return response()->json(['message' => 'Vendor deleted successfully']);
    }

    /**
     * Get vendor details by QR code scan.
     */
    public function scanQrCode(Request $request): JsonResponse
    {
        $request->validate([
            'qr_data' => 'required|string'
        ]);

        $vendor = VendorsForSupplier::where('qr_code_data', $request->qr_data)
            ->where('company_uuid', session('company'))
            ->with(['qrCode'])
            ->first();

        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        return response()->json([
            'vendor' => new VendorsForSupplierResource($vendor)
        ]);
    }

    /**
     * Generate QR code for vendor.
     */
    private function generateQrCode(Vendor $vendor): void
    {
        $qrData = json_encode([
            'vendor_id' => $vendor->uuid,
            'name' => $vendor->name,
            'email' => $vendor->email,
            'phone' => $vendor->phone,
            'scan_url' => url("/vendor-scan/{$vendor->uuid}")
        ]);

        $qrCodePath = "qr-codes/vendors/{$vendor->uuid}.png";
        
        $qrCode = QrCode::format('png')
            ->size(300)
            ->margin(2)
            ->generate($qrData);

        Storage::put($qrCodePath, $qrCode);

        $vendor->update([
            'qr_code_path' => $qrCodePath,
            'qr_code_data' => $qrData,
            'qr_code_url' => Storage::url($qrCodePath)
        ]);
    }
}
