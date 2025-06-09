<?php
namespace Database\Seeders;

use App\Models\EnquirySource;
use App\Models\EnquiryStatus;
use App\Models\PropertyType;
use App\Models\User;
use App\Models\VariableType;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class OtherDataSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        PropertyType::create(['name' => 'Residential']);
        PropertyType::create(['name' => 'Commercial']);
    
        EnquiryStatus::create(['name' => 'New', 'color' => '#3B82F6', 'nature' => 'Normal']);
        EnquiryStatus::create(['name' => 'Contacted', 'color' => '#F59E0B', 'nature' => 'Normal']);
        EnquiryStatus::create(['name' => 'Viewing Scheduled', 'nature' => 'Normal']);
        EnquiryStatus::create(['name' => 'Offer Made', 'nature' => 'Normal']);
        EnquiryStatus::create(['name' => 'Closed Deal', 'nature' => 'Closed']);
        EnquiryStatus::create(['name' => 'Lost Deal', 'color' => '#EF4444', 'nature' => 'Closed']);
        EnquiryStatus::create(['name' => 'Qualified', 'nature' => 'Normal']);
        EnquiryStatus::create(['name' => 'Proposal Sent', 'nature' => 'Normal']);
        EnquiryStatus::create(['name' => 'Negotiation', 'nature' => 'Normal']);
        EnquiryStatus::create(['name' => 'Converted', 'nature' => 'Normal']);
        EnquiryStatus::create(['name' => 'Deleted', 'nature' => 'Normal']); // Or 'Closed' if soft delete
        EnquiryStatus::create(['name' => 'On Hold', 'nature' => 'Normal']);
        EnquiryStatus::create(['name' => 'Junk', 'color' => '#78716C', 'nature' => 'Junk']);
        EnquiryStatus::create(['name' => 'Spam Report', 'color' => '#A855F7', 'nature' => 'Junk']);

        EnquirySource::create(['name' => 'Website']);
        EnquirySource::create(['name' => 'Referral']);
        EnquirySource::create(['name' => 'Walk-in']);
        EnquirySource::create(['name' => 'Social Media']);
        EnquirySource::create(['name' => 'Existing Client']);
        EnquirySource::create(['name' => 'Cold Call']);
        EnquirySource::create(['name' => 'Advertisement']);

        VariableType::create(['name' => 'Runtime']); // For user input at send time
        VariableType::create(['name' => 'Static Text']); // For pre-defined static values

        // System-mappable variables (examples)
        VariableType::create(['name' => 'Static Text']);
        VariableType::create(['name' => 'Runtime']);
        VariableType::create(['name' => 'Client - First Name']);
        VariableType::create(['name' => 'Client - Last Name']);
        VariableType::create(['name' => 'Client - Full Name']);
        VariableType::create(['name' => 'Client - Mobile Number']);
        VariableType::create(['name' => 'Client - Email']);
        VariableType::create(['name' => 'Enquiry - ID']);
        VariableType::create(['name' => 'Enquiry - Project Name']);
        VariableType::create(['name' => 'Enquiry - Property Names']);
        VariableType::create(['name' => 'Enquiry - Assigned Agent Name']);
        VariableType::create(['name' => 'Enquiry - Status']);
        VariableType::create(['name' => 'Enquiry - Rating']);
        VariableType::create(['name' => 'Project - Name']);
        VariableType::create(['name' => 'User - Name']);
        VariableType::create(['name' => 'User - Email']);
        VariableType::create(['name' => 'User - Mobile Number']);

    }
}