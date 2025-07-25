<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="backdrop-blur-xl bg-white/20 border border-white/30 rounded-2xl shadow-2xl shadow-indigo-500/30 overflow-hidden">
                <div class="p-8 text-gray-900">
                    @if($isNewUser)
                        <h3
                            class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 to-pink-500">
                            Bienvenue à bord, {{ auth()->user()->name }} ! ✨
                        </h3>
                        <p class="mt-2 text-gray-700">
                            Nous sommes ravis que vous fassiez désormais partie de notre communauté. Prêt(e) à explorer ?
                        </p>
                    @else
                        <h3
                            class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-blue-500">
                            Bon retour, {{ auth()->user()->name }} 👋
                        </h3>
                        <p class="mt-2 text-gray-700">
                            Nous espérons que vous passerez un bon moment ici. Que voulez-vous faire aujourd’hui ?
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>