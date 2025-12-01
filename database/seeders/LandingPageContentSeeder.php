<?php

namespace Database\Seeders;

use App\Models\LandingPageContent;
use Illuminate\Database\Seeder;

class LandingPageContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contents = [
            // Hero Section
            [
                'key' => 'hero_title_line1',
                'section' => 'hero',
                'type' => 'text',
                'value' => ['en' => 'Your Gateway to', 'ar' => null],
                'description' => 'Hero section main title - first line',
            ],
            [
                'key' => 'hero_title_line2',
                'section' => 'hero',
                'type' => 'text',
                'value' => ['en' => 'Premium Cloud Services', 'ar' => null],
                'description' => 'Hero section main title - second line (gradient)',
            ],
            [
                'key' => 'hero_subtitle',
                'section' => 'hero',
                'type' => 'textarea',
                'value' => ['en' => 'We manage the complexity of the cloud so you can focus on your business. Hosting, Development, and Marketing—all in one place.', 'ar' => null],
                'description' => 'Hero section subtitle/description',
            ],
            [
                'key' => 'hero_btn_primary',
                'section' => 'hero',
                'type' => 'text',
                'value' => ['en' => 'Get Started', 'ar' => null],
                'description' => 'Primary CTA button text',
            ],
            [
                'key' => 'hero_btn_secondary',
                'section' => 'hero',
                'type' => 'text',
                'value' => ['en' => 'Our Services', 'ar' => null],
                'description' => 'Secondary CTA button text',
            ],

            // Services Section
            [
                'key' => 'services_heading',
                'section' => 'services',
                'type' => 'text',
                'value' => ['en' => 'Our Services', 'ar' => null],
                'description' => 'Services section main heading',
            ],
            [
                'key' => 'services_subheading',
                'section' => 'services',
                'type' => 'text',
                'value' => ['en' => 'Comprehensive digital solutions tailored for your success.', 'ar' => null],
                'description' => 'Services section subheading',
            ],
            [
                'key' => 'service1_title',
                'section' => 'services',
                'type' => 'text',
                'value' => ['en' => 'Managed Hosting', 'ar' => null],
                'description' => 'Service card 1 - title',
            ],
            [
                'key' => 'service1_description',
                'section' => 'services',
                'type' => 'textarea',
                'value' => ['en' => 'Premium cloud hosting managed by experts. We handle DNS, security, and updates.', 'ar' => null],
                'description' => 'Service card 1 - description',
            ],
            [
                'key' => 'service2_title',
                'section' => 'services',
                'type' => 'text',
                'value' => ['en' => 'Web Development', 'ar' => null],
                'description' => 'Service card 2 - title',
            ],
            [
                'key' => 'service2_description',
                'section' => 'services',
                'type' => 'textarea',
                'value' => ['en' => 'Custom websites and applications built with modern technologies like Laravel and React.', 'ar' => null],
                'description' => 'Service card 2 - description',
            ],
            [
                'key' => 'service3_title',
                'section' => 'services',
                'type' => 'text',
                'value' => ['en' => 'SEO & Marketing', 'ar' => null],
                'description' => 'Service card 3 - title',
            ],
            [
                'key' => 'service3_description',
                'section' => 'services',
                'type' => 'textarea',
                'value' => ['en' => 'Boost your visibility with our data-driven SEO strategies and digital marketing campaigns.', 'ar' => null],
                'description' => 'Service card 3 - description',
            ],

            // Pricing Section
            [
                'key' => 'pricing_heading',
                'section' => 'pricing',
                'type' => 'text',
                'value' => ['en' => 'Simple Pricing', 'ar' => null],
                'description' => 'Pricing section heading',
            ],
            [
                'key' => 'pricing_subheading',
                'section' => 'pricing',
                'type' => 'text',
                'value' => ['en' => 'Choose the plan that fits your needs. No hidden fees.', 'ar' => null],
                'description' => 'Pricing section subheading',
            ],

            // Contact Section
            [
                'key' => 'contact_heading',
                'section' => 'contact',
                'type' => 'text',
                'value' => ['en' => 'Get In Touch', 'ar' => null],
                'description' => 'Contact section heading',
            ],
            [
                'key' => 'contact_subheading',
                'section' => 'contact',
                'type' => 'text',
                'value' => ['en' => 'Have a question or ready to start your project? Drop us a message and we\'ll get back to you soon.', 'ar' => null],
                'description' => 'Contact section subheading',
            ],
            [
                'key' => 'contact_name_label',
                'section' => 'contact',
                'type' => 'text',
                'value' => ['en' => 'Name', 'ar' => null],
                'description' => 'Contact form - Name field label',
            ],
            [
                'key' => 'contact_email_label',
                'section' => 'contact',
                'type' => 'text',
                'value' => ['en' => 'Email', 'ar' => null],
                'description' => 'Contact form - Email field label',
            ],
            [
                'key' => 'contact_subject_label',
                'section' => 'contact',
                'type' => 'text',
                'value' => ['en' => 'Subject', 'ar' => null],
                'description' => 'Contact form - Subject field label',
            ],
            [
                'key' => 'contact_message_label',
                'section' => 'contact',
                'type' => 'text',
                'value' => ['en' => 'Message', 'ar' => null],
                'description' => 'Contact form - Message field label',
            ],
            [
                'key' => 'contact_submit_btn',
                'section' => 'contact',
                'type' => 'text',
                'value' => ['en' => 'Send Message', 'ar' => null],
                'description' => 'Contact form - Submit button text',
            ],

            // About Section
            [
                'key' => 'about_heading',
                'section' => 'about',
                'type' => 'text',
                'value' => ['en' => 'About inClouding', 'ar' => null],
                'description' => 'About section heading',
            ],

            // Features Section
            [
                'key' => 'features_heading',
                'section' => 'features',
                'type' => 'text',
                'value' => ['en' => 'Why Choose inClouding', 'ar' => null],
                'description' => 'Features section heading',
            ],
            [
                'key' => 'features_subheading',
                'section' => 'features',
                'type' => 'text',
                'value' => ['en' => 'Everything you need to build, manage, and scale your digital presence', 'ar' => null],
                'description' => 'Features section subheading',
            ],
        ];

        foreach ($contents as $content) {
            LandingPageContent::create($content);
        }
    }
}
