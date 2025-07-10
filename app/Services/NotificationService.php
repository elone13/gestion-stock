<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use App\Models\Notification;

class NotificationService
{
    public static function checkLowStock()
    {
        $lowStockProducts = Product::where('quantity', '<=', 'min_quantity')
            ->where('quantity', '>', 0)
            ->get();

        foreach ($lowStockProducts as $product) {
            self::createLowStockNotification($product);
        }
    }

    public static function checkOutOfStock()
    {
        $outOfStockProducts = Product::where('quantity', 0)->get();

        foreach ($outOfStockProducts as $product) {
            self::createOutOfStockNotification($product);
        }
    }

    public static function createLowStockNotification(Product $product)
    {
        $users = User::all(); // Ou seulement les administrateurs

        foreach ($users as $user) {
            // Vérifier si une notification similaire existe déjà
            $existingNotification = $user->notifications()
                ->where('type', 'low_stock')
                ->where('data->product_id', $product->id)
                ->whereNull('read_at')
                ->first();

            if (!$existingNotification) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'low_stock',
                    'title' => 'Stock Faible',
                    'message' => "Le produit '{$product->name}' a un stock faible ({$product->quantity} unités restantes).",
                    'data' => [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'current_quantity' => $product->quantity,
                        'min_quantity' => $product->min_quantity
                    ]
                ]);
            }
        }
    }

    public static function createOutOfStockNotification(Product $product)
    {
        $users = User::all(); // Ou seulement les administrateurs

        foreach ($users as $user) {
            // Vérifier si une notification similaire existe déjà
            $existingNotification = $user->notifications()
                ->where('type', 'out_of_stock')
                ->where('data->product_id', $product->id)
                ->whereNull('read_at')
                ->first();

            if (!$existingNotification) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'out_of_stock',
                    'title' => 'Rupture de Stock',
                    'message' => "Le produit '{$product->name}' est en rupture de stock.",
                    'data' => [
                        'product_id' => $product->id,
                        'product_name' => $product->name
                    ]
                ]);
            }
        }
    }

    public static function createSystemNotification(User $user, string $title, string $message, array $data = [])
    {
        Notification::create([
            'user_id' => $user->id,
            'type' => 'system',
            'title' => $title,
            'message' => $message,
            'data' => $data
        ]);
    }

    public static function markAllAsRead(User $user)
    {
        $user->unreadNotifications()->update(['read_at' => now()]);
    }

    public static function markAsRead(Notification $notification)
    {
        $notification->markAsRead();
    }
} 