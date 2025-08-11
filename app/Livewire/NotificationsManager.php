<?php

namespace App\Livewire;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class NotificationsManager extends Component
{
    // Enable real-time updates with polling
    public $pollingInterval = 5000; // 5 seconds

    protected $listeners = [
        'echo:notifications,NotificationSent' => 'loadNotifications',
        'refresh-notifications' => 'loadNotifications'
    ];
    public $openNotification = false;
    public $notifications;
    public $unreadCount = 0;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->notifications = Auth::user()->notifications()->latest()->get();
        $this->unreadCount = Auth::user()->unreadNotifications->count();
    }

    public function markAsRead($id)
    {
        $notification = DatabaseNotification::find($id);
        if ($notification) {
            $notification->markAsRead();
            $filePath = parse_url($notification->data['action_url'], PHP_URL_PATH);
            $localPath = str_replace('/storage/', '', $filePath);

            if (Storage::exists($localPath)) {
                Storage::delete($localPath);
            }

            $this->loadNotifications();
        }
    }

    public function markAllAsRead()
    {
        $notifications = Auth::user()->unreadNotifications;
        foreach ($notifications as $notification) {
            $notification->markAsRead();

            // Check and delete associated file if exists
            if (isset($notification->data['action_url'])) {
                $filePath = parse_url($notification->data['action_url'], PHP_URL_PATH);
                $localPath = str_replace('/storage/', '', $filePath);

                if (Storage::exists($localPath)) {
                    Storage::delete($localPath);
                }
            }
        }
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notifications-manager');
    }
}
