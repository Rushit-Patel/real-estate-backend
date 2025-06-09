<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Users table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('password');
            $table->text('base_password')->nullable();
            $table->string('mobile_no')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Clients table
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('address')->nullable();
            $table->string('area')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Property Types table
        Schema::create('property_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        // Projects table
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('public_id')->unique()->nullable();
            $table->string('name');
            $table->string('status')->default('Active');
            $table->string('map_location_url')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('city')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Project Contacts table
        Schema::create('project_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('contact_no');
            $table->timestamps();
        });

        // Properties table
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->foreignId('property_type_id')->constrained()->onDelete('restrict');
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });

        // Project Files table
        Schema::create('project_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->timestamps();
        });

        // Enquiry Sources table
        Schema::create('enquiry_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        // Enquiry Statuses table
        Schema::create('enquiry_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->nullable();
            $table->string('nature')->default('Normal');
            $table->timestamps();
            $table->softDeletes();
        });

        // Enquiries table
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('restrict');
            $table->foreignId('project_id')->constrained()->onDelete('restrict');
            $table->foreignId('enquiry_status_id')->constrained('enquiry_statuses')->onDelete('restrict');
            $table->string('rating')->nullable();
            $table->text('general_remarks')->nullable();
            $table->foreignId('assign_to_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('source_id')->nullable()->constrained('enquiry_sources')->onDelete('restrict');
            $table->text('source_remarks')->nullable();
            $table->text('followup_notes')->nullable();
            $table->string('closure_reason')->nullable();
            $table->string('junk_status_name')->nullable();
            $table->string('junk_reason')->nullable();
            $table->timestamp('reminder_datetime')->nullable();
            $table->text('reminder_remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Enquiry Property pivot table
        Schema::create('enquiry_property', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enquiry_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('restrict');
            $table->timestamps();
        });

        // Bookings table
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('restrict');
            $table->foreignId('project_id')->constrained()->onDelete('restrict');
            $table->foreignId('property_id')->constrained()->onDelete('restrict');
            $table->timestamp('booking_date');
            $table->string('status')->default('Confirmed');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Email Templates table
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject');
            $table->text('body');
            $table->json('variables')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Variable Types table
        Schema::create('variable_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        // WhatsApp Templates table
        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('content');
            $table->timestamps();
            $table->softDeletes();
        });

        // WhatsApp Template Variables table
        Schema::create('whatsapp_template_variables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('whatsapp_template_id')->constrained()->onDelete('cascade');
            $table->string('variable_name');
            $table->foreignId('variable_type_id')->nullable()->constrained()->onDelete('restrict');
            $table->string('static_value')->nullable();
            $table->timestamps();
        });

        // Triggers table
        Schema::create('triggers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('event_type');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->json('conditions')->nullable();
            $table->json('actions');
            $table->timestamps();
            $table->softDeletes();
        });

        // Notifications table
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('triggers');
        Schema::dropIfExists('whatsapp_template_variables');
        Schema::dropIfExists('whatsapp_templates');
        Schema::dropIfExists('variable_types');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('enquiry_property');
        Schema::dropIfExists('enquiries');
        Schema::dropIfExists('enquiry_statuses');
        Schema::dropIfExists('enquiry_sources');
        Schema::dropIfExists('project_files');
        Schema::dropIfExists('properties');
        Schema::dropIfExists('project_contacts');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('property_types');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('users');
    }
};
