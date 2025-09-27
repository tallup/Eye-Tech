<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Software Installation & Updates',
                'slug' => 'software-installation-updates',
                'description' => 'Professional software installation and system updates for all mobile devices. We ensure your device runs the latest software with optimal performance.',
                'price' => 25.00,
                'estimated_duration' => 60, // 1 hour
                'category' => 'Software Services',
                'is_active' => true,
            ],
            [
                'name' => 'Virus & Malware Removal',
                'slug' => 'virus-malware-removal',
                'description' => 'Complete virus and malware removal service. We clean your device thoroughly and install security software to protect against future threats.',
                'price' => 40.00,
                'estimated_duration' => 90, // 1.5 hours
                'category' => 'Security Services',
                'is_active' => true,
            ],
            [
                'name' => 'Data Recovery & Backup',
                'slug' => 'data-recovery-backup',
                'description' => 'Professional data recovery service for lost photos, contacts, messages, and files. We also set up automatic backup solutions.',
                'price' => 60.00,
                'estimated_duration' => 120, // 2 hours
                'category' => 'Data Services',
                'is_active' => true,
            ],
            [
                'name' => 'Phone Unlocking Service',
                'slug' => 'phone-unlocking-service',
                'description' => 'Unlock your phone from any carrier. We support all major networks and phone models with permanent unlocking solutions.',
                'price' => 35.00,
                'estimated_duration' => 30, // 30 minutes
                'category' => 'Software Services',
                'is_active' => true,
            ],
            [
                'name' => 'Performance Optimization',
                'slug' => 'performance-optimization',
                'description' => 'Optimize your device performance by cleaning cache, removing bloatware, and configuring settings for maximum speed and battery life.',
                'price' => 30.00,
                'estimated_duration' => 75, // 1.25 hours
                'category' => 'Software Services',
                'is_active' => true,
            ],
            [
                'name' => 'App Installation & Configuration',
                'slug' => 'app-installation-configuration',
                'description' => 'Professional app installation and configuration service. We help you set up productivity apps, social media, and custom configurations.',
                'price' => 20.00,
                'estimated_duration' => 45, // 45 minutes
                'category' => 'Software Services',
                'is_active' => true,
            ],
            [
                'name' => 'Cloud Setup & Sync',
                'slug' => 'cloud-setup-sync',
                'description' => 'Set up cloud services like Google Drive, iCloud, or OneDrive. We configure automatic sync and backup for your important data.',
                'price' => 25.00,
                'estimated_duration' => 60, // 1 hour
                'category' => 'Cloud Services',
                'is_active' => true,
            ],
            [
                'name' => 'Network & Connectivity Setup',
                'slug' => 'network-connectivity-setup',
                'description' => 'Configure WiFi, Bluetooth, and mobile data settings. We optimize network connections and troubleshoot connectivity issues.',
                'price' => 20.00,
                'estimated_duration' => 45, // 45 minutes
                'category' => 'Network Services',
                'is_active' => true,
            ],
        ];

        foreach ($services as $serviceData) {
            Service::create($serviceData);
        }
    }
}