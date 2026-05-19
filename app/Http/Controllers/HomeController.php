<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;

class HomeController extends Controller
{
    public function index()
    {
        $featuredEvents = \Illuminate\Support\Facades\Cache::remember('featured_events', 60, function() {
            return Event::with(['category'])
                ->published()
                ->upcoming()
                ->orderBy('start_at')
                ->take(6)
                ->get();
        });

        $categories = \Illuminate\Support\Facades\Cache::remember('active_categories', 60, function() {
            return Category::withCount(['events' => fn($q) => $q->published()])
                ->having('events_count', '>', 0)
                ->orderBy('name')
                ->get();
        });

        $totalEvents = Event::published()->count();

        return view('welcome', compact('featuredEvents', 'categories', 'totalEvents'));
    }
}
