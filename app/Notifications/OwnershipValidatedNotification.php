<?php

namespace App\Notifications;

use App\Models\Item;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OwnershipValidatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $item;
    public $poster;

    public function __construct(Item $item, User $poster)
    {
        $this->item = $item;
        $this->poster = $poster;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isPerson = $this->item->category_name === 'personnes';

        $subject = $isPerson
            ? 'Retrouvailles confirmées'
            : 'Remise confirmée pour ' . $this->item->item_name;

        $line = $isPerson
            ? $this->poster->name . ' a confirmé que vous avez bien retrouvé la personne.'
            : $this->poster->name . ' a confirmé vous avoir remis ' . $this->item->item_name . '.';

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line($line)
            ->action('Voir l\'annonce', url('/item-detail/' . $this->item->id))
            ->line('Merci d\'avoir utilisé QCT!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'item_id' => $this->item->id,
            'item_name' => $this->item->item_name,
            'poster_id' => $this->poster->id,
            'poster_name' => $this->poster->name,
            'message' => $this->poster->name . ' a confirmé la remise de ' . $this->item->item_name,
        ];
    }
}
