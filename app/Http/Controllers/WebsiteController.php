<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function index()
    {
        $services = Service::query()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('id')
            ->take(3)
            ->get();

        $featuredProducts = Product::query()
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->orderByDesc('id')
            ->take(6)
            ->get();

        return view('website.index', compact('services', 'featuredProducts'));
    }

    public function about()
    {
        return view('website.about');
    }

    public function services()
    {
        $services = Service::query()
            ->where('is_active', true)
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->get();

        return view('website.services', compact('services'));
    }

    public function contact()
    {
        return view('website.contact');
    }

    public function submitContact(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:32'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        // v1: no mail backend yet. Flash and redirect.
        return redirect()
            ->route('contact')
            ->with('status', "Thanks {$data['name']}, we'll be in touch shortly.");
    }
}
