<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Portfolio\Models\About;
use Modules\Portfolio\Models\AboutExperience;
use Modules\Portfolio\Models\AboutStat;
use Modules\Portfolio\Models\Project;
use Modules\Portfolio\Models\Skill;
use Modules\Portfolio\Models\Setting;
use Modules\Portfolio\Models\Testimonial;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing test data to ensure clean state
        About::truncate();
        AboutStat::truncate();
        AboutExperience::truncate();
        Skill::truncate();
        Project::truncate();
        Setting::truncate();
        Testimonial::truncate();

        // ════════ ABOUT SINGLETON ════════
        About::create([
            'full_name' => 'Antigravity Architect',
            'title' => 'Senior Software Architect',
            'subtitle' => 'Pioneering Scalable Digital Ecosystems',
            'bio' => 'Specializing in the intersection of high-availability backend systems and cloud-native infrastructure.',
            'description' => "With over a decade of experience, I architect systems that don't just work—they excel under pressure. My approach combines scientific precision with a passion for elegant code, ensuring every project is built on a foundation of reliability and scalability.\n\nI believe in 'Immutable Infrastructure' and 'Clean Code' not as buzzwords, but as fundamental pillars of sustainable software development.",
            'email' => 'architect@antigravity.io',
            'phone' => '+84 900 000 000',
            'location' => 'Ho Chi Minh City, Vietnam',
            'github_url' => 'https://github.com/antigravity',
            'linkedin_url' => 'https://linkedin.com/in/antigravity',
            'is_available' => true,
            'availability_status' => 'Open to high-impact collaborations',
        ]);

        // ════════ STATS ════════
        $stats = [
            ['label' => 'Years Experience', 'value' => '10+', 'icon' => 'history'],
            ['label' => 'Projects Delivered', 'value' => '50+', 'icon' => 'rocket_launch'],
            ['label' => 'Tech Stacks', 'value' => '12', 'icon' => 'hub'],
            ['label' => 'Client Satisfaction', 'value' => '100%', 'icon' => 'verified'],
        ];
        foreach ($stats as $index => $stat) {
            AboutStat::create(array_merge($stat, ['sort_order' => $index]));
        }

        // ════════ EXPERIENCES ════════
        $experiences = [
            [
                'type' => 'work',
                'title' => 'Lead Infrastructure Architect',
                'organization' => 'Nebula Cloud Systems',
                'location' => 'Remote',
                'description' => 'Directed the migration of legacy monolithic architectures to Kubernetes-based microservices, improving deployment frequency by 400%.',
                'start_date' => '2022-01-01',
                'is_current' => true,
            ],
            [
                'type' => 'work',
                'title' => 'Senior Backend Engineer',
                'organization' => 'Quantum Financials',
                'location' => 'Ho Chi Minh City',
                'description' => 'Optimized core transaction processing engine, reducing latency by 150ms for millions of daily active users.',
                'start_date' => '2019-06-01',
                'end_date' => '2021-12-31',
            ],
            [
                'type' => 'education',
                'title' => 'M.S. in Computer Science',
                'organization' => 'Stanford University',
                'location' => 'Palo Alto, CA',
                'description' => 'Focused on Distributed Systems and High-Performance Computing.',
                'start_date' => '2017-09-01',
                'end_date' => '2019-05-30',
            ],
        ];
        foreach ($experiences as $index => $exp) {
            AboutExperience::create(array_merge($exp, ['sort_order' => $index]));
        }

        // ════════ SETTINGS ════════
        Setting::set('hero_title', 'Building <span class="text-primary italic">Scalable</span> Solutions.', 'home');
        Setting::set('hero_subtitle', 'Senior Software Architect specialized in high-availability systems, cloud-native deployments, and the delicate art of DevOps orchestration.', 'home');
        Setting::set('about_title', 'Engineering for Resiliency', 'about');
        Setting::set('about_content', "I don't just write code; I architect systems that survive the storm. My journey began with a curiosity for how data moves, evolving into a career focused on the intersection of robust backend services and seamless deployment pipelines.", 'about');

        // ════════ SKILLS ════════
        $skills = [
            [
                'name' => 'Laravel Framework',
                'category' => 'Backend',
                'description' => 'Advanced application architecture using TDD, DDD, and SOLID principles.',
                'level' => 95,
                'icon' => 'code',
                'is_featured' => true,
            ],
            [
                'name' => 'System Architecture',
                'category' => 'Architectural',
                'description' => 'Designing scalable microservices and distributed modular systems.',
                'level' => 92,
                'icon' => 'hub',
                'is_featured' => true,
            ],
            [
                'name' => 'Docker & Kubernetes',
                'category' => 'Infrastructure',
                'description' => 'Orchestrating containerized environments for maximum uptime.',
                'level' => 88,
                'icon' => 'terminal',
                'is_featured' => true,
            ],
            [
                'name' => 'Tailwind CSS',
                'category' => 'Frontend',
                'description' => 'Crafting premium, responsive design systems from the ground up.',
                'level' => 94,
                'icon' => 'palette',
                'is_featured' => true,
            ],
        ];
        foreach ($skills as $index => $skill) {
            Skill::create(array_merge($skill, ['sort_order' => $index, 'slug' => \Illuminate\Support\Str::slug($skill['name'])]));
        }

        // ════════ PROJECTS ════════
        $projects = [
            [
                'title' => 'Orchestrix Modular ERP',
                'description' => 'A high-performance modular ERP system built with Laravel and Tailwind.',
                'tech_stack' => ['Laravel', 'PostgreSQL', 'Docker', 'Tailwind'],
                'category' => 'Enterprise',
                'is_featured' => true,
                'is_visible' => true,
                'content_md' => '# Orchestrix\n\nA full-scale enterprise resource planning system.'
            ],
            [
                'title' => 'Nexus API Gateway',
                'description' => 'Distributed API gateway with rate limiting and automated documentation.',
                'tech_stack' => ['Go', 'Redis', 'Kubernetes'],
                'category' => 'Infrastructure',
                'is_featured' => true,
                'is_visible' => true,
                'content_md' => '# Nexus Gateway\n\nHigh-throughput gateway for microservices.'
            ],
            [
                'title' => 'Lumina Dashboard',
                'description' => 'Real-time monitoring dashboard with glassmorphism design language.',
                'tech_stack' => ['React', 'Socket.io', 'Framer Motion'],
                'category' => 'Frontend',
                'is_featured' => true,
                'is_visible' => true,
                'content_md' => '# Lumina\n\nVisualizing complex data streams.'
            ]
        ];
        foreach ($projects as $index => $project) {
            Project::create(array_merge($project, ['sort_order' => $index, 'slug' => \Illuminate\Support\Str::slug($project['title'])]));
        }

        // ════════ TESTIMONIALS ════════
        $testimonials = [
            [
                'name' => 'Sarah Jenkins',
                'position' => 'CTO',
                'company' => 'Nebula Cloud',
                'content' => 'One of the most organized and technically proficient architects I have ever worked with.',
                'rating' => 5,
            ],
            [
                'name' => 'Marcus Thorne',
                'position' => 'Product Manager',
                'company' => 'Quantum Fin',
                'content' => 'Transformed our development lifecycle. The modular approach saved us months of work.',
                'rating' => 5,
            ],
        ];
        foreach ($testimonials as $index => $testimonial) {
            Testimonial::create(array_merge($testimonial, ['sort_order' => $index]));
        }
    }
}

