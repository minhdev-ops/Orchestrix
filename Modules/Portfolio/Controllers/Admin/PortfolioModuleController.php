<?php

namespace Modules\Portfolio\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Portfolio\Models\Project;
use Modules\Portfolio\Models\Skill;
use Modules\Portfolio\Models\Contact;
use Modules\Portfolio\Models\Setting;
use Modules\Portfolio\Services\ProjectService;
use Modules\Portfolio\Services\SkillService;
use Modules\Portfolio\Services\ContactService;
use Modules\Portfolio\Services\AboutService;

class PortfolioModuleController extends Controller
{
    public function __construct(
        private readonly ProjectService $projectService,
        private readonly SkillService $skillService,
        private readonly ContactService $contactService,
        private readonly AboutService $aboutService
    ) {}

    /**
     * @OA\Get(
     *     path="/admin/portfolio",
     *     tags={"Portfolio Admin"},
     *     summary="Portfolio admin dashboard",
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index()
    {
        $projectCount = $this->projectService->countAll();
        $skillCount = $this->skillService->countAll();
        $contactCount = $this->contactService->countUnread();

        return view('portfolio::admin.index', compact('projectCount', 'skillCount', 'contactCount'));
    }

    // =========================================================================
    // Projects
    // =========================================================================

    /**
     * @OA\Get(
     *     path="/admin/portfolio/projects",
     *     tags={"Portfolio Admin"},
     *     summary="List all projects for admin",
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Project")))
     * )
     */
    public function projects()
    {
        $projects = $this->projectService->paginateAllForAdmin(20);
        return view('portfolio::admin.projects', compact('projects'));
    }

    public function createProject()
    {
        $project = new Project();
        return view('portfolio::admin.project-form', compact('project'));
    }

    public function storeProject(Request $request)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'category'         => 'required|string|max:100',
            'description'      => 'nullable|string',
            'content_md'       => 'nullable|string',
            'link'             => 'nullable|url|max:255',
            'github_url'       => 'nullable|url|max:255',
            'image'            => 'nullable|image|max:2048',
            'is_featured'      => 'boolean',
            'is_visible'       => 'boolean',
            'tech_stack_input' => 'nullable|string',
        ]);

        $this->projectService->createProject($data, $request->file('image'));

        return redirect()->route('portfolio.projects')->with('success', 'Dự án đã được tạo thành công.');
    }

    public function showProject(Project $project)
    {
        return view('portfolio::admin.project-show', compact('project'));
    }

    public function editProject(Project $project)
    {
        return view('portfolio::admin.project-form', compact('project'));
    }

    public function updateProject(Request $request, Project $project)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'category'         => 'required|string|max:100',
            'description'      => 'nullable|string',
            'content_md'       => 'nullable|string',
            'link'             => 'nullable|url|max:255',
            'github_url'       => 'nullable|url|max:255',
            'image'            => 'nullable|image|max:2048',
            'is_featured'      => 'boolean',
            'is_visible'       => 'boolean',
            'tech_stack_input' => 'nullable|string',
        ]);

        $this->projectService->updateProject($project, $data, $request->file('image'));

        return redirect()->route('portfolio.projects')->with('success', 'Cập nhật dự án thành công.');
    }

    public function destroyProject(Project $project)
    {
        $this->projectService->deleteProject($project);
        return redirect()->route('portfolio.projects')->with('success', 'Xóa dự án thành công.');
    }

    // =========================================================================
    // Skills
    // =========================================================================

    /**
     * @OA\Get(
     *     path="/admin/portfolio/skills",
     *     tags={"Portfolio Admin"},
     *     summary="List all skills for admin",
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Skill")))
     * )
     */
    public function skills()
    {
        $skills = $this->skillService->paginateAllForAdmin(20);
        return view('portfolio::admin.skills', compact('skills'));
    }

    public function createSkill()
    {
        $skill = new Skill();
        return view('portfolio::admin.skill-form', compact('skill'));
    }

    public function storeSkill(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'required|string|max:100',
            'level'       => 'required|integer|min:0|max:100',
            'icon'        => 'nullable|string|max:50',
            'icon_url'    => 'nullable|image|max:1024',
            'description' => 'nullable|string',
            'is_visible'  => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $this->skillService->createSkill($data, $request->file('icon_url'));

        return redirect()->route('portfolio.skills')->with('success', 'Kỹ năng mới đã được thêm.');
    }

    public function editSkill(Skill $skill)
    {
        return view('portfolio::admin.skill-form', compact('skill'));
    }

    public function updateSkill(Request $request, Skill $skill)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'required|string|max:100',
            'level'       => 'required|integer|min:0|max:100',
            'icon'        => 'nullable|string|max:50',
            'icon_url'    => 'nullable|image|max:1024',
            'description' => 'nullable|string',
            'is_visible'  => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $this->skillService->updateSkill($skill, $data, $request->file('icon_url'));

        return redirect()->route('portfolio.skills')->with('success', 'Cập nhật kỹ năng thành công.');
    }

    public function destroySkill(Skill $skill)
    {
        $this->skillService->deleteSkill($skill);
        return redirect()->route('portfolio.skills')->with('success', 'Xóa kỹ năng thành công.');
    }

    // =========================================================================
    // Contacts
    // =========================================================================

    /**
     * @OA\Get(
     *     path="/admin/portfolio/contacts",
     *     tags={"Portfolio Admin"},
     *     summary="List all contacts for admin",
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Contact")))
     * )
     */
    public function contacts()
    {
        $contacts = $this->contactService->paginateAllForAdmin(20);
        return view('portfolio::admin.contacts', compact('contacts'));
    }

    public function showContact(Contact $contact)
    {
        $contact = $this->contactService->markAsRead($contact);
        return view('portfolio::admin.contact-show', compact('contact'));
    }

    public function destroyContact(Contact $contact)
    {
        $this->contactService->deleteContact($contact);
        return redirect()->route('portfolio.contacts')->with('success', 'Đã xóa tin nhắn.');
    }

    // =========================================================================
    // Settings
    // =========================================================================

    public function editAbout()
    {
        $about = $this->aboutService->getAboutInfo();
        $stats = $this->aboutService->getStats();
        $experiences = $this->aboutService->getExperiences();

        return view('portfolio::admin.settings.about', compact('about', 'stats', 'experiences'));
    }

    public function updateAbout(Request $request)
    {
        $data = $request->validate([
            'full_name'   => 'nullable|string|max:255',
            'title'       => 'nullable|string|max:255',
            'subtitle'    => 'nullable|string|max:255',
            'bio'         => 'nullable|string',
            'description' => 'nullable|string',
            'email'       => 'nullable|email|max:255',
            'phone'       => 'nullable|string|max:20',
            'location'    => 'nullable|string|max:255',
            'github_url'  => 'nullable|url|max:255',
            'linkedin_url'=> 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'website_url' => 'nullable|url|max:255',
            'avatar'      => 'nullable|image|max:2048',
            'resume_url'  => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $this->aboutService->updateAbout($data, $request->file('avatar'), $request->file('resume_url'));

        return back()->with('success', 'Thông tin About đã được cập nhật.');
    }

    public function editHome()
    {
        $heroTitle = Setting::get('hero_title', 'Architecting the Future');
        $heroSubtitle = Setting::get('hero_subtitle', 'Premium DevOps & Software Engineering');

        return view('portfolio::admin.settings.home', compact('heroTitle', 'heroSubtitle'));
    }

    public function updateHome(Request $request)
    {
        $request->validate([
            'hero_title'    => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:255',
        ]);

        Setting::set('hero_title', $request->hero_title, 'home');
        Setting::set('hero_subtitle', $request->hero_subtitle, 'home');

        return back()->with('success', 'Cập nhật thông tin trang chủ thành công.');
    }
}
