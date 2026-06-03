<?php

namespace Modules\Portfolio\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Portfolio\Models\Project;
use Modules\Portfolio\Models\Skill;
use Modules\Portfolio\Models\Setting;
use Modules\Portfolio\Services\ProjectService;
use Modules\Portfolio\Services\SkillService;
use Modules\Portfolio\Services\ContactService;
use Modules\Portfolio\Services\AboutService;

class PortfolioController extends Controller
{
    public function __construct(
        private readonly ProjectService $projectService,
        private readonly SkillService $skillService,
        private readonly ContactService $contactService,
        private readonly AboutService $aboutService
    ) {}

    public function index()
    {
        $projects = $this->projectService->getFeaturedProjects(3);
        $skills = $this->skillService->getFeaturedSkills(6);

        $heroTitle = Setting::get('hero_title', 'Architecting the Future');
        $heroSubtitle = Setting::get('hero_subtitle', 'Premium DevOps & Software Engineering');

        $about = $this->aboutService->getAboutInfo();
        $aboutStats = $this->aboutService->getStats();

        return view('portfolio::index', compact('projects', 'skills', 'heroTitle', 'heroSubtitle', 'about', 'aboutStats'));
    }

    public function projects()
    {
        $projects = $this->projectService->paginateVisibleProjects(9);
        return view('portfolio::projects.index', compact('projects'));
    }

    public function showProject(Project $project)
    {
        if (!$project->is_visible) {
            abort(404);
        }
        
        $this->projectService->incrementView($project);
        $relatedProjects = $this->projectService->getRelatedProjects($project, 3);

        return view('portfolio::projects.show', compact('project', 'relatedProjects'));
    }

    public function skills()
    {
        // Nhóm skills theo category để dễ hiển thị public
        $skillsByCategory = $this->skillService->getSkillsGroupedByCategory();
        
        // Để tương thích với view cũ nếu nó loop qua danh sách thẳng
        $skills = $this->skillService->getVisibleSkills();
        
        return view('portfolio::skills.index', compact('skills', 'skillsByCategory'));
    }

    public function showSkill(Skill $skill)
    {
        if (!$skill->is_visible) {
            abort(404);
        }
        return view('portfolio::skills.show', compact('skill'));
    }

    public function about()
    {
        $about = $this->aboutService->getAboutInfo();
        $aboutStats = $this->aboutService->getStats();
        $experiences = $this->aboutService->getExperiences();

        return view('portfolio::about', compact('about', 'aboutStats', 'experiences'));
    }

    public function contact()
    {
        return view('portfolio::contact');
    }

    public function storeContact(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        $data['subject'] = $data['subject'] ?? 'Message from Portfolio';
        $data['ip_address'] = $request->ip();
        $data['user_agent'] = $request->userAgent();
        $data['source']     = 'contact_form';

        $this->contactService->submitContact($data);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Cảm ơn bạn! Thông điệp của bạn đã được gửi thành công.']);
        }

        return back()->with('success', 'Cảm ơn bạn! Thông điệp của bạn đã được gửi thành công.');
    }

    /**
     * @OA\Get(
     *     path="/api/portfolio/projects",
     *     tags={"Portfolio Public"},
     *     summary="Get list of projects for public portfolio",
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function apiProjects()
    {
        $projects = $this->projectService->getVisibleProjects();
        
        return response()->json($projects->map(fn($p) => [
            'id' => $p->id,
            'title' => $p->title,
            'category' => $p->category,
            'image' => $p->image ?? 'https://images.unsplash.com/photo-1639762681485-074b7f938ba0?auto=format&fit=crop&q=80&w=1200',
            'description' => $p->description,
            'tech' => $p->tech_stack ?? [],
            'codeLink' => $p->github_url,
            'demoLink' => $p->link,
        ]));
    }

    /**
     * @OA\Get(
     *     path="/api/portfolio/skills",
     *     tags={"Portfolio Public"},
     *     summary="Get list of skills for public portfolio",
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function apiSkills()
    {
        $skills = $this->skillService->getVisibleSkills();

        return response()->json($skills->map(fn($s) => [
            'name' => $s->name,
            'icon' => $s->icon ?? 'terminal',
            'brandColor' => $this->getBrandColor($s->category),
            'category' => $s->category,
            'size' => $s->is_featured ? 'large' : 'medium',
            'desc' => $s->description,
        ]));
    }

    private function getBrandColor($category)
    {
        $colors = [
            'Backend' => '#ff2d20',
            'Frontend' => '#61dafb',
            'DevOps' => '#2496ed',
            'Cloud' => '#58a6ff',
            'Database' => '#336791',
        ];

        return $colors[$category] ?? '#39c5bb';
    }
}
