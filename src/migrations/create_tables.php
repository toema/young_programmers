<?php
require 'bootstrap.php';

use Illuminate\Database\Capsule\Manager as Capsule;

try {
    // Drop existing tables if they exist (optional, be careful in production)
    Capsule::schema()->dropIfExists('parent_child');
    Capsule::schema()->dropIfExists('comments');
    Capsule::schema()->dropIfExists('reactions');
    Capsule::schema()->dropIfExists('posts');
    Capsule::schema()->dropIfExists('users');
    Capsule::schema()->dropIfExists('settings_admin');

    // Create settings_admin table
    Capsule::schema()->create('settings_admin', function ($table) {
        $table->id();
        $table->string('setting_key')->unique();
        $table->text('setting_value');
        $table->timestamps();
    });

    // Create users table
    Capsule::schema()->create('users', function ($table) {
        $table->id();
        $table->string('username');
        $table->string('email')->unique();
        $table->string('password');
        $table->enum('type', ['parent', 'child']);
        $table->string('profile_image')->nullable();
        $table->date('birthdate');
        $table->boolean('active')->default(true);
        $table->timestamps();
    });

    // Create posts table
    Capsule::schema()->create('posts', function ($table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->string('title');
        $table->text('content');
        $table->enum('type', ['question', 'share', 'tip']);
        $table->enum('status', ['active', 'hidden'])->default('active');
        $table->timestamps();
        
        $table->foreign('user_id')
              ->references('id')
              ->on('users')
              ->onDelete('cascade');
    });

    // Create comments table
    Capsule::schema()->create('comments', function ($table) {
        $table->id();
        $table->unsignedBigInteger('post_id');
        $table->unsignedBigInteger('user_id');
        $table->text('content');
        $table->timestamps();
        
        $table->foreign('post_id')
              ->references('id')
              ->on('posts')
              ->onDelete('cascade');
              
        $table->foreign('user_id')
              ->references('id')
              ->on('users')
              ->onDelete('cascade');
    });

    // Create reactions (likes) table
    Capsule::schema()->create('reactions', function ($table) {
        $table->id();
        $table->unsignedBigInteger('post_id');
        $table->unsignedBigInteger('user_id');
        $table->enum('type', ['like'])->default('like');
        $table->timestamps();
        
        $table->foreign('post_id')
              ->references('id')
              ->on('posts')
              ->onDelete('cascade');
              
        $table->foreign('user_id')
              ->references('id')
              ->on('users')
              ->onDelete('cascade');
              
        $table->unique(['post_id', 'user_id', 'type']);
    });

    // Create parent_child table
    Capsule::schema()->create('parent_child', function ($table) {
        $table->id();
        $table->unsignedBigInteger('parent_id');
        $table->unsignedBigInteger('child_id');
        $table->enum('status', ['pending', 'approved'])->default('pending');
        $table->timestamps();
        
        $table->foreign('parent_id')
              ->references('id')
              ->on('users')
              ->onDelete('cascade');
              
        $table->foreign('child_id')
              ->references('id')
              ->on('users')
              ->onDelete('cascade');
              
        $table->unique(['parent_id', 'child_id']);
    });

    echo "Migration completed successfully!\n";
    
    // Insert some default settings
    Capsule::table('settings_admin')->insert([
        [
            'setting_key' => 'site_title',
            'setting_value' => 'مبرمج الصغير|YoungDev',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ],
        [
            'setting_key' => 'site_description',
            'setting_value' => 'منصة تعليمية للمبرمجين الصغار',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ],
        [
            'setting_key' => 'register_button_text',
            'setting_value' => 'سجل الآن',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ],
        [
            'setting_key' => 'video_button_text',
            'setting_value' => 'شاهد الفيديو',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]
    ]);

    echo "Default settings inserted successfully!\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    throw $e;
}
