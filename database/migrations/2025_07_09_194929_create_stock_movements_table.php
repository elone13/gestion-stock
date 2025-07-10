<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['in', 'out', 'adjustment']); // in = entrée, out = sortie, adjustment = ajustement
            $table->integer('quantity');
            $table->integer('quantity_before'); // Quantité avant le mouvement
            $table->integer('quantity_after'); // Quantité après le mouvement
            $table->text('reason')->nullable(); // Raison du mouvement
            $table->string('reference')->nullable(); // Référence (bon de livraison, facture, etc.)
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Utilisateur qui a fait le mouvement
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_movements');
    }
};
