<?php

namespace App\Notifications;

use App\Models\Item;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClaimValidatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $item;
    public $owner;

    public function __construct(Item $item, User $owner)
    {
        $this->item = $item;
        $this->owner = $owner;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Remise confirmée pour ' . $this->item->item_name)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line($this->owner->name . ' a confirmé avoir récupéré ' . $this->item->item_name . '.')
            ->line('Merci d\'avoir aidé à retrouver ce bien !')
            ->action('Voir l\'annonce', url('/item-detail/' . $this->item->id))
            ->line('Merci d\'avoir utilisé QCT!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'item_id' => $this->item->id,
            'item_name' => $this->item->item_name,
            'owner_id' => $this->owner->id,
            'owner_name' => $this->owner->name,
            'message' => $this->owner->name . ' a confirmé la récupération de ' . $this->item->item_name,
        ];
    }
}
