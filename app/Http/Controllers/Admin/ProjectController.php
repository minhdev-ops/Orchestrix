<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ModuleManagerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProjectController extends Controller
{
    protected string $configPath;

    public function __construct()
    {
        $this->configPath = base_path('projects.json');
    }

    /**
     * @OA\Get(
     *     path="/projects",
     *     tags={"System Projects"},
     *     summary="List all system projects",
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index()
    {
        $projects = $this->getProjects();
        return view('admin.projects.index', compact('projects'));
    }

    /**
     * @OA\Get(
     *     path="/projects/create",
     *     tags={"System Projects"},
     *     summary="Show project creation form",
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function create()
    {
        $moduleManager = app(ModuleManagerService::class);
        $allModules = $moduleManager->getAllModules();
        return view('admin.projects.create', compact('allModules'));
    }

    /**
     * @OA\Post(
     *     path="/projects",
     *     tags={"System Projects"},
     *     summary="Create a new system project",
     *     @OA\Response(response=201, description="Project created successfully")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'domain' => 'required|string|unique_project_domain',
            'modules' => 'nullable|array',
        ]);

        $projects = $this->getProjects();
        $projects[$request->domain] = [
            'modules' => $request->modules ?? [],
        ];

        $this->saveProjects($projects);

        return redirect()->route('admin.projects.index')->with('success', 'Project created successfully.');
    }

    /**
     * @OA\Get(
     *     path="/projects/{project}/edit",
     *     tags={"System Projects"},
     *     summary="Show project edit form",
     *     @OA\Parameter(name="project", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function edit($domain)
    {
        $projects = $this->getProjects();
        if (!isset($projects[$domain])) {
            abort(404);
        }

        $project = $projects[$domain];
        $project['domain'] = $domain;

        $moduleManager = app(ModuleManagerService::class);
        $allModules = $moduleManager->getAllModules();

        return view('admin.projects.edit', compact('project', 'allModules'));
    }

    /**
     * @OA\Put(
     *     path="/projects/{project}",
     *     tags={"System Projects"},
     *     summary="Update a system project",
     *     @OA\Parameter(name="project", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Project updated successfully")
     * )
     */
    public function update(Request $request, $domain)
    {
        $request->validate([
            'modules' => 'nullable|array',
        ]);

        $projects = $this->getProjects();
        if (!isset($projects[$domain])) {
            abort(404);
        }

        $projects[$domain]['modules'] = $request->modules ?? [];

        $this->saveProjects($projects);

        return redirect()->route('admin.projects.index')->with('success', 'Project updated successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/projects/{project}",
     *     tags={"System Projects"},
     *     summary="Delete a system project",
     *     @OA\Parameter(name="project", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Project deleted successfully")
     * )
     */
    public function destroy($domain)
    {
        $projects = $this->getProjects();
        if (isset($projects[$domain])) {
            unset($projects[$domain]);
            $this->saveProjects($projects);
        }

        return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully.');
    }

    protected function getProjects(): array
    {
        if (!File::exists($this->configPath)) {
            return [];
        }
        return json_decode(File::get($this->configPath), true) ?? [];
    }

    protected function saveProjects(array $projects): void
    {
        File::put($this->configPath, json_encode($projects, JSON_PRETTY_PRINT));
    }
}
