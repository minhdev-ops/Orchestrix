<?php

namespace Tests\Unit;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Mockery\MockInterface;
use Modules\Portfolio\Models\Project;
use Modules\Portfolio\Repositories\ProjectRepository;
use Modules\Portfolio\Services\ProjectService;
use PHPUnit\Framework\TestCase;

class PortfolioServiceTest extends TestCase
{
    private ProjectService $projectService;
    private ProjectRepository|MockInterface $projectRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->projectRepository = Mockery::mock(ProjectRepository::class);
        $this->projectService = new ProjectService($this->projectRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_visible_projects_calls_repository()
    {
        $mockCollection = Mockery::mock(\Illuminate\Database\Eloquent\Collection::class);
        $this->projectRepository->shouldReceive('getVisible')->once()->andReturn($mockCollection);

        $result = $this->projectService->getVisibleProjects();

        $this->assertSame($mockCollection, $result);
    }

    public function test_create_project_transforms_data_correctly()
    {
        $inputData = [
            'title' => 'My New App',
            'is_visible' => 'on',
            'is_featured' => '',
            'tech_stack_input' => 'PHP, Laravel, Vue.js'
        ];

        $expectedData = [
            'title' => 'My New App',
            'is_visible' => true,
            'is_featured' => false,
            'tech_stack' => ['PHP', 'Laravel', 'Vue.js']
        ];

        $mockProject = Mockery::mock(Project::class);

        $this->projectRepository
            ->shouldReceive('create')
            ->once()
            ->with($expectedData)
            ->andReturn($mockProject);

        $result = $this->projectService->createProject($inputData);

        $this->assertSame($mockProject, $result);
    }
}
