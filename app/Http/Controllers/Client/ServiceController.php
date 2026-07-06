<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $services = Service::where('business_id', $user->business_id)
            ->orderByDesc('created_at')
            ->get();

        return view('client.services.index', compact('user', 'services'));
    }

    public function create()
    {
        $user = Auth::user();
        return view('client.services.create', compact('user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'unit_price'  => 'required|numeric|min:0',
            'unit'        => 'required|string|max:50',
            'currency'    => 'nullable|string|max:3',
            'is_active'   => 'boolean',
        ]);

        $data['business_id'] = $user->business_id;
        $data['currency'] = $data['currency'] ?? ($user->business->currency ?? 'XAF');
        $data['is_active'] = $request->boolean('is_active', true);

        Service::create($data);

        return redirect(url('client/services'))->with('success', __('app.client.flash.service_created'));
    }

    public function edit(Service $service)
    {
        $user = Auth::user();
        abort_unless($service->business_id === $user->business_id, 403);
        return view('client.services.edit', compact('user', 'service'));
    }

    public function update(Request $request, Service $service)
    {
        $user = Auth::user();
        abort_unless($service->business_id === $user->business_id, 403);

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'unit_price'  => 'required|numeric|min:0',
            'unit'        => 'required|string|max:50',
            'is_active'   => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $service->update($data);

        return redirect(url('client/services'))->with('success', __('app.client.flash.service_updated'));
    }

    public function destroy(Service $service)
    {
        $user = Auth::user();
        abort_unless($service->business_id === $user->business_id, 403);
        $service->delete();
        return redirect(url('client/services'))->with('success', __('app.client.flash.service_deleted'));
    }
}
