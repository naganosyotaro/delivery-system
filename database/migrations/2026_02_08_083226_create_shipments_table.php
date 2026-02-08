<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            
            // 発送者情報
            $table->string('sender_name');
            $table->text('sender_address');
            $table->string('sender_phone')->nullable();
            
            // 届け先情報
            $table->string('recipient_name');
            $table->text('recipient_address');
            $table->string('recipient_phone')->nullable();
            
            // 荷物情報
            $table->string('item_name')->nullable();
            $table->enum('size', ['S', 'M', 'L', 'XL'])->default('M');
            $table->decimal('weight', 8, 2)->nullable();
            $table->integer('quantity')->default(1);
            
            // 配送情報
            $table->dateTime('preferred_delivery_at')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'picked_up', 'in_transit', 'delivered', 'undelivered', 'storage'])->default('pending');
            
            // 料金
            $table->decimal('shipping_fee', 10, 2)->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
