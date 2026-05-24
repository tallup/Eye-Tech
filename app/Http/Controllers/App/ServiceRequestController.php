<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequestRequest;
use App\Http\Requests\UpdateServiceRequestRequest;
use App\Http\Resources\ServiceRequestResource;
use App\Models\Service;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceRequestController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', ServiceRequest::class);

        $requests = ServiceRequest::with('service')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('ServiceRequests/Index', [
            'serviceRequests' => $requests->through(fn ($r) => ServiceRequestResource::make($r)->resolve()),
        ]);
    }

    public function show(ServiceRequest $serviceRequest): Response
    {
        $this->authorize('view', $serviceRequest);

        $serviceRequest->load('service');

        return Inertia::render('ServiceRequests/Show', [
            'serviceRequest' => ServiceRequestResource::make($serviceRequest)->resolve(),
        ]);
    }

    public function store(StoreServiceRequestRequest $request): RedirectResponse
    {
        ServiceRequest::create($request->validated());

        return redirect()->route('service-requests.index')->with('success', 'Service request created');
    }

    public function update(UpdateServiceRequestRequest $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $data = $request->validated();

        if ($data['status'] === 'completed' && $serviceRequest->status !== 'completed') {
            $data['completed_at'] = now();
        }

        $serviceRequest->update($data);

        return redirect()
            ->route('service-requests.show', $serviceRequest)
            ->with('success', 'Service request updated');
    }

    public function destroy(ServiceRequest $serviceRequest): RedirectResponse
    {
        $this->authorize('delete', $serviceRequest);
        $serviceRequest->delete();

        return redirect()->route('service-requests.index')->with('success', 'Service request deleted');
    }

    public function services(): array
    {
        return Service::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'price'])
            ->map(fn ($s) => ['id' => $s->id, 'name' => $s->name, 'price' => (float) $s->price])
            ->toArray();
    }
}
