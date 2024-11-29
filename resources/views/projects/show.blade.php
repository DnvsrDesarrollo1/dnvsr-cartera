<x-app-layout>
    <div class="bg-white shadow-lg rounded-lg p-6 mb-4 h-fit" id="profile_preview">
        <div class="flex items-center justify-between mb-2 px-4">
            <svg width="64px" height="64px" viewBox="-3.5 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg"
                xmlns:xlink="http://www.w3.org/1999/xlink" fill="#000000">
                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                <g id="SVGRepo_iconCarrier">
                    <g id="icomoon-ignore"> </g>
                    <path
                        d="M16 17.333l-3.2-1.813v-6.507c1.813-0.267 3.2-1.813 3.2-3.68 0-2.080-1.653-3.733-3.733-3.733s-3.733 1.653-3.733 3.733c0 1.867 1.387 3.413 3.2 3.68v5.867l-3.2-1.813-8.533 3.467v13.067l8.533-3.467 7.467 4.267 8.533-4.267v-13.067l-8.533 4.267zM7.467 25.44l-6.4 2.56v-10.773l6.4-2.613v10.827zM9.6 5.333c0-1.493 1.173-2.667 2.667-2.667s2.667 1.173 2.667 2.667c0 1.493-1.173 2.667-2.667 2.667s-2.667-1.173-2.667-2.667zM16 29.227l-7.467-4.267v-10.667l3.2 1.813v4.693h1.067v-4.107l3.2 1.813v10.72zM23.467 25.493l-6.4 3.2v-10.667l6.4-3.2v10.667z"
                        fill="#000000"> </path>
                </g>
            </svg>
            <div>
                <p class="text-xl font-semibold text-gray-800">{{ $project->proy_nombre }}</p>
                <p class="text-lg text-center">{{ $project->proy_subprograma }}</p>
            </div>
            <div class="border rounded-md cursor-pointer p-2 shadow">
                <a title="Descargar perfil y plan de pagos" href="">
                    <svg width="45px" height="45px" viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg"
                        fill="#000000">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <defs>
                                <style>
                                    .cls-1 {
                                        fill: #ff402f;
                                    }
                                </style>
                            </defs>
                            <title></title>
                            <g id="xxx-word">
                                <path class="cls-1"
                                    d="M325,105H250a5,5,0,0,1-5-5V25a5,5,0,0,1,10,0V95h70a5,5,0,0,1,0,10Z"></path>
                                <path class="cls-1"
                                    d="M325,154.83a5,5,0,0,1-5-5V102.07L247.93,30H100A20,20,0,0,0,80,50v98.17a5,5,0,0,1-10,0V50a30,30,0,0,1,30-30H250a5,5,0,0,1,3.54,1.46l75,75A5,5,0,0,1,330,100v49.83A5,5,0,0,1,325,154.83Z">
                                </path>
                                <path class="cls-1"
                                    d="M300,380H100a30,30,0,0,1-30-30V275a5,5,0,0,1,10,0v75a20,20,0,0,0,20,20H300a20,20,0,0,0,20-20V275a5,5,0,0,1,10,0v75A30,30,0,0,1,300,380Z">
                                </path>
                                <path class="cls-1" d="M275,280H125a5,5,0,0,1,0-10H275a5,5,0,0,1,0,10Z"></path>
                                <path class="cls-1" d="M200,330H125a5,5,0,0,1,0-10h75a5,5,0,0,1,0,10Z"></path>
                                <path class="cls-1"
                                    d="M325,280H75a30,30,0,0,1-30-30V173.17a30,30,0,0,1,30-30h.2l250,1.66a30.09,30.09,0,0,1,29.81,30V250A30,30,0,0,1,325,280ZM75,153.17a20,20,0,0,0-20,20V250a20,20,0,0,0,20,20H325a20,20,0,0,0,20-20V174.83a20.06,20.06,0,0,0-19.88-20l-250-1.66Z">
                                </path>
                                <path class="cls-1"
                                    d="M145,236h-9.61V182.68h21.84q9.34,0,13.85,4.71a16.37,16.37,0,0,1-.37,22.95,17.49,17.49,0,0,1-12.38,4.53H145Zm0-29.37h11.37q4.45,0,6.8-2.19a7.58,7.58,0,0,0,2.34-5.82,8,8,0,0,0-2.17-5.62q-2.17-2.34-7.83-2.34H145Z">
                                </path>
                                <path class="cls-1"
                                    d="M183,236V182.68H202.7q10.9,0,17.5,7.71t6.6,19q0,11.33-6.8,18.95T200.55,236Zm9.88-7.85h8a14.36,14.36,0,0,0,10.94-4.84q4.49-4.84,4.49-14.41a21.91,21.91,0,0,0-3.93-13.22,12.22,12.22,0,0,0-10.37-5.41h-9.14Z">
                                </path>
                                <path class="cls-1"
                                    d="M245.59,236H235.7V182.68h33.71v8.24H245.59v14.57h18.75v8H245.59Z"></path>
                            </g>
                        </g>
                    </svg>
                </a>
            </div>
        </div>
        <hr>
        <div class="mt-6 grid lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1 gap-2">
            <div class="bg-gray-50 rounded-lg p-4 shadow border">
                <h2 class="text-xl font-semibold text-gray-700 mb-2 text-center">Viviendas con Cartera Activa</h2>
                <p class="font-bold">
                    <img class="mx-auto"
                        src="https://quickchart.io/chart?w=200&h=200&c={type:'pie',data:{labels:['Pendientes', 'Concluidas'],datasets:[{data:[{{ $project->proy_numViviendas - $project->proy_viv_cartera }}, {{ $project->proy_viv_cartera }}]}]}}">
                </p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 shadow border">
                <h2 class="text-xl font-semibold text-gray-700 mb-2">Información Geográfica</h2>
                <table class="min-w-full bg-white border">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="text-left py-2 px-3 uppercase font-semibold text-sm">Nivel</th>
                            <th class="text-left py-2 px-3 uppercase font-semibold text-sm">Ubicación</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <tr>
                            <td class="py-2 px-3">Departamento</td>
                            <td class="py-2 px-3 font-bold">{{ $project->proy_depto }}</td>
                        </tr>
                        <tr class="bg-gray-100">
                            <td class="py-2 px-3">Provincia</td>
                            <td class="py-2 px-3 font-bold">{{ $project->proy_provincia }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3">Municipio</td>
                            <td class="py-2 px-3 font-bold">{{ $project->proy_municipio }}</td>
                        </tr>
                        <tr class="bg-gray-100">
                            <td class="py-2 px-3">Sector</td>
                            <td class="py-2 px-3 font-bold">{{ $project->proy_ubicacion }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 shadow border">
                <h2 class="text-xl font-semibold text-gray-700 mb-2">Información Administrativa</h2>
                <table class="min-w-full bg-white border">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="text-left py-2 px-3 uppercase font-semibold text-sm">Nombre</th>
                            <th class="text-left py-2 px-3 uppercase font-semibold text-sm">Valor</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <tr>
                            <td class="py-2 px-3">Codigo del Proyecto</td>
                            <td class="py-2 px-3 font-bold">{{ $project->proy_cod }} / {{ $project->cod_proy_credito }}
                            </td>
                        </tr>
                        <tr class="bg-gray-100">
                            <td class="py-2 px-3">Codigo de Acta</td>
                            <td class="py-2 px-3 font-bold">{{ $project->proy_numActa }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3">Estado</td>
                            <td class="py-2 px-3 font-bold">{{ $project->proy_estado }}</td>
                        </tr>
                        <tr class="bg-gray-100">
                            <td class="py-2 px-3">Modalidad</td>
                            <td class="py-2 px-3 font-bold">{{ $project->proy_modalidad }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3">Periodos</td>
                            <td class="py-2 px-3 font-bold">Inicio: {{ $project->fecha_ini_obra }} <br> Fin:
                                {{ $project->fecha_fin_obra }}</td>
                        </tr>
                        <tr class="bg-gray-100">
                            <td class="py-2 px-3">Componente</td>
                            <td class="py-2 px-3 font-bold">{{ $project->proy_componente }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3">Valor en Creditos</td>
                            @php
                                $valorEnCreditos = $project->beneficiaries()->sum('total_activado');
                            @endphp
                            <td class="py-2 px-3 font-bold">Bs. {{number_format($valorEnCreditos, 2)}}</td>
                        </tr>
                        <tr class="bg-gray-100">
                            <td class="py-2 px-3">Numero de Beneficiarios</td>
                            <td class="py-2 px-3 font-bold">
                                {{ $project->beneficiaries()->count() }}
                                <button id="dropdownUsersButton" data-dropdown-toggle="dropdownUsers"
                                    data-dropdown-placement="bottom"
                                    class="text-gray-800 text-sm px-5 py-2.5 text-center inline-flex items-center"
                                    type="button"><svg height="12px" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 4 4 4-4" />
                                    </svg>
                                </button>
                                <!-- Dropdown menu -->
                                <div id="dropdownUsers"
                                    class="z-10 hidden bg-white rounded-lg shadow w-70 dark:bg-gray-700">
                                    <ul class="h-40 py-2 overflow-y-auto text-gray-700 dark:text-gray-200"
                                        aria-labelledby="dropdownUsersButton">
                                        @php
                                            $beneficiarios = $project->beneficiaries()->orderBy('nombre')->get();
                                        @endphp
                                        @forelse ($beneficiarios as $b)
                                            <li>
                                                <a href="{{ route('beneficiario.show', ['cedula' => $b->ci]) }}"
                                                    target="_blank"
                                                    class="flex items-center text-xs px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white">
                                                    <svg fill="#FFFFFF" width="12px" height="12px"
                                                        viewBox="-3.6 -3.6 31.20 31.20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <path fill-rule="evenodd"
                                                                d="M5,2 L7,2 C7.55228475,2 8,2.44771525 8,3 C8,3.51283584 7.61395981,3.93550716 7.11662113,3.99327227 L7,4 L5,4 C4.48716416,4 4.06449284,4.38604019 4.00672773,4.88337887 L4,5 L4,19 C4,19.5128358 4.38604019,19.9355072 4.88337887,19.9932723 L5,20 L19,20 C19.5128358,20 19.9355072,19.6139598 19.9932723,19.1166211 L20,19 L20,17 C20,16.4477153 20.4477153,16 21,16 C21.5128358,16 21.9355072,16.3860402 21.9932723,16.8833789 L22,17 L22,19 C22,20.5976809 20.75108,21.9036609 19.1762728,21.9949073 L19,22 L5,22 C3.40231912,22 2.09633912,20.75108 2.00509269,19.1762728 L2,19 L2,5 C2,3.40231912 3.24891996,2.09633912 4.82372721,2.00509269 L5,2 L7,2 L5,2 Z M21,2 L21.081,2.003 L21.2007258,2.02024007 L21.2007258,2.02024007 L21.3121425,2.04973809 L21.3121425,2.04973809 L21.4232215,2.09367336 L21.5207088,2.14599545 L21.5207088,2.14599545 L21.6167501,2.21278596 L21.7071068,2.29289322 L21.7071068,2.29289322 L21.8036654,2.40469339 L21.8036654,2.40469339 L21.8753288,2.5159379 L21.9063462,2.57690085 L21.9063462,2.57690085 L21.9401141,2.65834962 L21.9401141,2.65834962 L21.9641549,2.73400703 L21.9641549,2.73400703 L21.9930928,2.8819045 L21.9930928,2.8819045 L22,3 L22,3 L22,9 C22,9.55228475 21.5522847,10 21,10 C20.4477153,10 20,9.55228475 20,9 L20,5.414 L13.7071068,11.7071068 C13.3466228,12.0675907 12.7793918,12.0953203 12.3871006,11.7902954 L12.2928932,11.7071068 C11.9324093,11.3466228 11.9046797,10.7793918 12.2097046,10.3871006 L12.2928932,10.2928932 L18.584,4 L15,4 C14.4477153,4 14,3.55228475 14,3 C14,2.44771525 14.4477153,2 15,2 L21,2 Z">
                                                            </path>
                                                        </g>
                                                    </svg><span class="ml-2">{{ $b->nombre }}</span>
                                                </a>
                                            </li>
                                        @empty
                                            <li>
                                                Sin Beneciarios
                                            </li>
                                        @endforelse
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr class="mt-4">
    </div>
</x-app-layout>
