<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ExportReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $zipUrl;
    protected $count;

    /**
     * Create a new notification instance.
     *
     * @param string $zipUrl
     */
    public function __construct(string $zipUrl, int $count)
    {
        $this->zipUrl = $zipUrl;
        $this->count = $count;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database']; // Almacena la notificación en la BD
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => '¡Exportación completada!',
            'message' => "Tu archivo ZIP con {$this->count} archivos está listo para ser descargado.",
            'action_url' => $this->zipUrl,
            'icon' => 'fas fa-file-archive',
        ];
    }

}
