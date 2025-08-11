<div x-data="{ openNotification: @entangle('openNotification') }">
    <div class="relative">
        <span wire:poll.3s="loadNotifications"
            class="absolute top-0 right-0 inline-flex items-center justify-center px-1 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
            {{ $unreadCount }}
        </span>
        <button @click="openNotification = true" @keydown.escape.window="openNotification = false"
            class="text-gray-400 text-lg hover:text-gray-700 focus:outline-none">
            <i class="fa-solid fa-file-pdf"></i>
        </button>
    </div>

    <div x-show="openNotification" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" x-cloak
        class="fixed inset-0 overflow-y-auto z-50 flex items-center justify-center">
        <div class="fixed inset-0 transition-opacity" @click="openNotification = false">
            <div class="absolute inset-0 bg-gray-500 opacity-25"></div>
        </div>
        <div
            class="bg-white rounded-lg shadow-xl transform transition-all sm:max-w-lg sm:w-full m-4 z-50 overflow-hidden border-2 border-gray-500">
            <div class="bg-gray-100 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Cola de exportaciones:</h3>
                <div>
                    <button wire:click="markAllAsRead" class="text-blue-600 hover:text-blue-800 text-sm mr-3">
                        Marcar todas como leídas
                    </button>
                    <button @click="openNotification = false" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                @forelse($notifications as $notification)
                    <div class="px-4 py-3 hover:bg-gray-50 {{ is_null($notification->read_at) ? 'bg-blue-50' : '' }}">
                        <div class="flex justify-between items-center">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $notification->data['title'] ?? 'Nueva notificación' }}
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $notification->data['message'] ?? '' }}
                                </p>
                                <p class="text-xs text-gray-400 mt-2">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>
                            @if (is_null($notification->read_at))
                                <div class="flex items-center">
                                    @if (isset($notification->data['action_url']))
                                        <a href="{{ $notification->data['action_url'] }}" title="Descargar archivo"
                                            class="mr-2 text-blue-600 hover:text-blue-800 p-2 bg-blue-200 rounded">
                                            <i class="fa-solid fa-download"></i>
                                        </a>
                                    @endif
                                    <button wire:click="markAsRead('{{ $notification->id }}')" title="Marcar como leída"
                                        class="text-blue-600 hover:text-blue-800 p-2 bg-blue-200 rounded">
                                        <i class="fa-solid fa-check-double"></i>
                                    </button>
                                </div>
                            @else
                                <i class="text-green-500 fa-solid fa-check-double"></i>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-6 text-center">
                        <p class="text-gray-500">No tienes exportaciones pendendientes.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
{{-- Success is as dangerous as failure. --}}
</div>
