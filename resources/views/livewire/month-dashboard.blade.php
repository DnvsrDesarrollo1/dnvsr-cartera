<div class="flex flex-col">
    <div id="parametros" class="bg-white rounded-lg shadow-md p-6 space-y-6">
        <div class="border-b pb-4">
            <h1 class="text-2xl font-bold text-gray-800">Reporte B.I. de Recaudaciones: {{ $proyecto }}
                ({{ $inicio }} -> {{ $fin }})</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
                <label for="fecha-inicio" class="block text-sm font-medium text-gray-700">Fecha Inicial</label>
                <input type="date" id="fecha-inicio" wire:model.live="inicio"
                    class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
            </div>

            <div class="space-y-2">
                <label for="fecha-fin" class="block text-sm font-medium text-gray-700">Fecha Final</label>
                <input type="date" id="fecha-fin" wire:model.live="fin"
                    class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
            </div>
        </div>

        <div class="space-y-2">
            <label for="busqueda-proyecto" class="block text-sm font-medium text-gray-700">Buscar Proyecto</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="text" id="busqueda-proyecto" wire:model.live="search"
                    placeholder="Buscar por nombre de proyecto"
                    class="block w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />

                @if ($proyectos && $proyectos->count() > 0)
                    <div
                        class="absolute z-50 bg-white rounded-md border shadow-md w-full mt-1 max-h-60 overflow-y-auto">
                        <ul class="py-1">
                            @foreach ($proyectos as $p)
                                <li wire:click="setProject('{{ $p->proyecto }}')"
                                    class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm text-gray-700">
                                    {{ $p->proyecto }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        <div class="pt-2">
            <button type="button" onclick="chart.render();"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Generar Reporte
            </button>
        </div>
    </div>
    <div>
        @if ($chart)
            {!! $chart->container() !!}
        @endif
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
</div>
