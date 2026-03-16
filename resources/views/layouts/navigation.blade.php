<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <div class="flex items-center gap-3 mt-1">
                            <img src="{{ asset('favicons/favicon.ico') }}" alt="会社のロゴ" class="h-10 w-auto">
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>

                <!-- External Links - Icon only (md to lg) -->
                <div class="hidden md:flex lg:hidden items-center space-x-2 sm:ms-6 border-l border-gray-200 pl-4">
                    <a href="https://mail.google.com" target="_blank" rel="noopener noreferrer" 
                       class="text-gray-600 hover:text-gray-900 p-2 rounded hover:bg-gray-50 transition-colors" title="Gmail">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </a>
                    <a href="https://drive.google.com" target="_blank" rel="noopener noreferrer" 
                       class="text-gray-600 hover:text-gray-900 p-2 rounded hover:bg-gray-50 transition-colors" title="ドライブ">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h12a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z" />
                        </svg>
                    </a>
                    <a href="https://meet.google.com" target="_blank" rel="noopener noreferrer" 
                       class="text-gray-600 hover:text-gray-900 p-2 rounded hover:bg-gray-50 transition-colors" title="Meet">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </a>
                    <a href="https://calendar.google.com" target="_blank" rel="noopener noreferrer" 
                       class="text-gray-600 hover:text-gray-900 p-2 rounded hover:bg-gray-50 transition-colors" title="カレンダー">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </a>
                    <a href="https://www.chatwork.com" target="_blank" rel="noopener noreferrer" 
                       class="text-gray-600 hover:text-gray-900 p-2 rounded hover:bg-gray-50 transition-colors" title="チャットワーク">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </a>
                    <a href="https://zoom.us" target="_blank" rel="noopener noreferrer" 
                       class="text-gray-600 hover:text-gray-900 p-2 rounded hover:bg-gray-50 transition-colors" title="Zoom">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </a>
                </div>

                <!-- External Links - Text (lg and above) -->
                <div class="hidden lg:flex items-center space-x-3 sm:ms-6 border-l border-gray-200 pl-6">
                    <a href="https://mail.google.com" target="_blank" rel="noopener noreferrer" 
                       class="text-sm text-gray-600 hover:text-gray-900 px-2 py-1 rounded hover:bg-gray-50 transition-colors" title="Gmail">
                        Gmail
                    </a>
                    <a href="https://drive.google.com" target="_blank" rel="noopener noreferrer" 
                       class="text-sm text-gray-600 hover:text-gray-900 px-2 py-1 rounded hover:bg-gray-50 transition-colors" title="ドライブ">
                        ドライブ
                    </a>
                    <a href="https://meet.google.com" target="_blank" rel="noopener noreferrer" 
                       class="text-sm text-gray-600 hover:text-gray-900 px-2 py-1 rounded hover:bg-gray-50 transition-colors" title="Meet">
                        Meet
                    </a>
                    <a href="https://calendar.google.com" target="_blank" rel="noopener noreferrer" 
                       class="text-sm text-gray-600 hover:text-gray-900 px-2 py-1 rounded hover:bg-gray-50 transition-colors" title="カレンダー">
                        カレンダー
                    </a>
                    <a href="https://www.chatwork.com" target="_blank" rel="noopener noreferrer" 
                       class="text-sm text-gray-600 hover:text-gray-900 px-2 py-1 rounded hover:bg-gray-50 transition-colors" title="チャットワーク">
                        チャットワーク
                    </a>
                    <a href="https://zoom.us" target="_blank" rel="noopener noreferrer" 
                       class="text-sm text-gray-600 hover:text-gray-900 px-2 py-1 rounded hover:bg-gray-50 transition-colors" title="Zoom">
                        Zoom
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-base leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        @php
                            $isBusinessDivision = false;
                            $isManager = Auth::user()->role === 2;
                            $businessDivision = \App\Models\Division::where('name', '業務部')->whereNull('parent_id')->first();
                            if ($businessDivision) {
                                $isBusinessDivision = Auth::user()->division_id === $businessDivision->id || 
                                                       Auth::user()->division_id === $businessDivision->children->pluck('id')->first();
                            }
                        @endphp
                        @if($isBusinessDivision || $isManager)
                            <x-dropdown-link :href="route('approvals.index')">
                                {{ __('承認待ち') }}
                            </x-dropdown-link>
                        @endif

                        @if(Auth::user()->role === 1)
                            <x-dropdown-link :href="route('admin.masters.index')">
                                {{ __('マスタ管理') }}
                            </x-dropdown-link>
                        @endif

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- External Links (Mobile) -->
        <div class="pt-2 pb-3 border-t border-gray-200">
            <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">外部リンク</div>
            <div class="px-4 py-2 grid grid-cols-3 gap-2">
                <a href="https://mail.google.com" target="_blank" rel="noopener noreferrer" class="text-base text-gray-600 hover:text-gray-900">Gmail</a>
                <a href="https://drive.google.com" target="_blank" rel="noopener noreferrer" class="text-base text-gray-600 hover:text-gray-900">ドライブ</a>
                <a href="https://meet.google.com" target="_blank" rel="noopener noreferrer" class="text-base text-gray-600 hover:text-gray-900">Meet</a>
                <a href="https://calendar.google.com" target="_blank" rel="noopener noreferrer" class="text-base text-gray-600 hover:text-gray-900">カレンダー</a>
                <a href="https://www.chatwork.com" target="_blank" rel="noopener noreferrer" class="text-base text-gray-600 hover:text-gray-900">チャットワーク</a>
                <a href="https://zoom.us" target="_blank" rel="noopener noreferrer" class="text-base text-gray-600 hover:text-gray-900">Zoom</a>
                <a href="https://akashi.midworks.net" target="_blank" rel="noopener noreferrer" class="text-base text-gray-600 hover:text-gray-900">AKASHI</a>
                <a href="#" target="_blank" rel="noopener noreferrer" class="text-base text-gray-600 hover:text-gray-900">SS-PAYCIAL</a>
                <a href="https://x.com" target="_blank" rel="noopener noreferrer" class="text-base text-gray-600 hover:text-gray-900">X</a>
            </div>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-base text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                @php
                    $isBusinessDivision = false;
                    $isManager = Auth::user()->role === 2;
                    $businessDivision = \App\Models\Division::where('name', '業務部')->whereNull('parent_id')->first();
                    if ($businessDivision) {
                        $isBusinessDivision = Auth::user()->division_id === $businessDivision->id || 
                                               Auth::user()->division_id === $businessDivision->children->pluck('id')->first();
                    }
                @endphp
                @if($isBusinessDivision || $isManager)
                    <x-responsive-nav-link :href="route('approvals.index')">
                        {{ __('承認待ち') }}
                    </x-responsive-nav-link>
                @endif

                @if(Auth::user()->role === 1)
                    <x-responsive-nav-link :href="route('admin.masters.index')">
                        {{ __('マスタ管理') }}
                    </x-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
