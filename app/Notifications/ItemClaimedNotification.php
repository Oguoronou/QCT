<?php

namespace App\Notifications;

use App\Models\Item;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ItemClaimedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $item;
    public $finder;

    /**
     * Create a new notification instance.
     */
    public function __construct(Item $item, User $finder)
    {
        $this->item = $item;
        $this->finder = $finder;
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
        return (new MailMessage)
            ->subject('Quelqu\'un a peut-être trouvé votre ' . $this->item->item_name)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Bonne nouvelle! Quelqu\'un a signalé avoir trouvé ' . $this->item->item_name)
            ->line('**Détails de l\'objet:**')
            ->line('- Catégorie: ' . $this->item->category_name)
            ->line('- Description: ' . substr($this->item->description, 0, 100) . '...')
            ->line('**Informations du trouveur:**')
            ->line('- Nom: ' . $this->finder->name)
            ->line('- Email: ' . $this->finder->email)
            ->line('- Téléphone: ' . $this->finder->mobile_no)
            ->action('Voir les détails et valider', url('/item-detail/' . $this->item->id))
            ->line('Si c\'est vraiment votre objet, cliquez sur le lien ci-dessus pour confirmer.')
            ->line('Merci d\'avoir utilisé QCT!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'item_id' => $this->item->id,
            'item_name' => $this->item->item_name,
            'finder_id' => $this->finder->id,
            'finder_name' => $this->finder->name,
            'message' => 'Quelqu\'un a trouvé votre ' . $this->item->item_name,
        ];
    }
}
