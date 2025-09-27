<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Product;

class WebsiteController extends Controller
{
    public function index()
    {
        // Show only 3 specific services: unlocking, app installation, and account card
        $services = Service::where('is_active', true)
            ->whereIn('name', [
                'Phone Unlocking Service',
                'App Installation & Configuration', 
                'Cloud Setup & Sync'
            ])
            ->get();
            
        // Show only 6 products for cleaner look
        $featuredProducts = Product::where('is_active', true)
            ->where('stock_quantity', '>', 0)
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
        // Show only 3 specific services: unlocking, app installation, and account card
        $services = Service::where('is_active', true)
            ->whereIn('name', [
                'Phone Unlocking Service',
                'App Installation & Configuration', 
                'Cloud Setup & Sync'
            ])
            ->get();
        return view('website.services', compact('services'));
    }
    
    public function contact()
    {
        return view('website.contact');
    }
}
