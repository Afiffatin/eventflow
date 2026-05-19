<x-app-layout title="Platform Tiket Event #1">
    {{-- Hero --}}
    <section class="pt-20 pb-16 text-center px-4">
        <div class="inline-flex items-center gap-2 bg-white/5 border border-white/10 rounded-full px-5 py-2 mb-8 text-sm text-gray-400">
            ✨ Platform Tiket Event #1 di Indonesia
        </div>
        <h1 class="text-5xl md:text-7xl font-extrabold leading-tight mb-6">
            Temukan Event<br><span class="gradient-text">Terbaik Untukmu</span>
        </h1>
        <p class="text-gray-400 text-lg max-w-2xl mx-auto mb-10">
            Jelajahi ribuan event menarik, beli tiket dengan mudah, dan nikmati pengalaman tak terlupakan bersama Eventify.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('events.index') }}" class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 px-8 py-3.5 rounded-xl font-semibold transition flex items-center justify-center gap-2">
                <i data-lucide="search" class="w-5 h-5"></i> Jelajahi Event
            </a>
            @guest
                <a href="{{ route('register') }}" class="border border-white/20 hover:bg-white/5 px-8 py-3.5 rounded-xl font-semibold transition flex items-center justify-center">Daftar Gratis</a>
            @endguest
        </div>

        <div class="flex justify-center gap-8 mt-16 text-center">
            <div class="border-r border-white/10 pr-8">
                <p class="text-3xl font-bold text-purple-400">{{ $totalEvents }}+</p>
                <p class="text-gray-500 text-sm">Event Aktif</p>
            </div>
            <div class="border-r border-white/10 pr-8">
                <p class="text-3xl font-bold text-purple-400">1K+</p>
                <p class="text-gray-500 text-sm">Tiket Terjual</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-purple-400">100%</p>
                <p class="text-gray-500 text-sm">Aman & Terpercaya</p>
            </div>
        </div>
    </section>

    {{-- Categories --}}
    @if($categories->isNotEmpty())
    <section class="max-w-7xl mx-auto px-4 mb-16">
        <h2 class="text-2xl font-bold mb-6">Kategori</h2>
        <div class="flex flex-wrap gap-3">
            @foreach($categories as $cat)
                <a href="{{ route('events.index', ['category' => $cat->id]) }}"
                   class="bg-white/5 hover:bg-purple-600/20 border border-white/10 px-5 py-2 rounded-full text-sm transition">
                    {{ $cat->name }} <span class="text-gray-500">({{ $cat->events_count }})</span>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Featured Events --}}
    @if($featuredEvents->isNotEmpty())
    <section class="max-w-7xl mx-auto px-4 pb-20">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold">Event Mendatang</h2>
            <a href="{{ route('events.index') }}" class="text-purple-400 hover:text-purple-300 text-sm font-medium">Lihat Semua →</a>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($featuredEvents as $event)
                <a href="{{ route('events.show', $event->slug) }}"
                   class="group bg-white/5 border border-white/10 rounded-2xl overflow-hidden hover:border-purple-500/50 transition-all duration-300">
                    <div class="aspect-video bg-gradient-to-br from-purple-900/50 to-indigo-900/50 flex items-center justify-center">
                        @if($event->banner)
                            <img src="{{ Storage::url($event->banner) }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                        @else
                            <i data-lucide="calendar" class="w-12 h-12 text-purple-400/50"></i>
                        @endif
                    </div>
                    <div class="p-5">
                        @if($event->category)
                            <span class="text-xs font-medium text-purple-400 bg-purple-500/10 px-2.5 py-1 rounded-full">{{ $event->category->name }}</span>
                        @endif
                        <h3 class="text-lg font-semibold mt-3 group-hover:text-purple-400 transition">{{ $event->title }}</h3>
                        <div class="mt-3 space-y-1 text-sm text-gray-400">
                            <p class="flex items-center gap-2"><i data-lucide="calendar" class="w-4 h-4"></i> {{ $event->start_at->format('d M Y, H:i') }}</p>
                            <p class="flex items-center gap-2"><i data-lucide="map-pin" class="w-4 h-4"></i> {{ $event->city ?? ($event->is_online ? 'Online' : '-') }}</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-white/5 flex items-center justify-between">
                            <span class="text-purple-400 font-bold">
                                @if($event->getMinPrice() == 0) Gratis @else Rp {{ number_format($event->getMinPrice(), 0, ',', '.') }} @endif
                            </span>
                            <span class="text-xs text-gray-500">{{ $event->getAvailableStock() }} tiket tersisa</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
    @endif
</x-app-layout>
<!-- PRK42-45: Fixed responsive layout for payment button and disabled state -->
