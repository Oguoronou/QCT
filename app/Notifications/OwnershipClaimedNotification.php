<?php

namespace App\Notifications;

use App\Models\Item;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OwnershipClaimedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $item;
    public $claimant;

    /**
     * Create a new notification instance.
     */
    public function __construct(Item $item, User $claimant)
    {
        $this->item = $item;
        $this->claimant = $claimant;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->item->category_name === 'personnes'
            ? 'Quelqu\'un a signalé connaître le ' . $this->item->item_name
            : 'Quelqu\'un a signalé que cet objet lui appartient';

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Quelqu\'un a signalé que ' . $this->claimant->item_name . ' lui/leur appartient.')
            ->line('**Détails de la réclamation:**')
            ->line('- Réclamant: ' . $this->claimant->name)
            ->line('- Email: ' . $this->claimant->email)
            ->line('- Téléphone: ' . $this->claimant->mobile_no)
            ->action('Voir la demande et valider', url('/item-detail/' . $this->item->id))
            ->line('Merci d\'avoir utilisé QCT!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'item_id' => $this->item->id,
            'claimant_id' => $this->claimant->id,
            'claimant_name' => $this->claimant->name,
            'message' => 'Quelqu\'un a signalé que cet objet lui appartient',
        ];
    }
}
