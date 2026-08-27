<?php

declare(strict_types=1);

use App\Http\Controllers\AtlasOptionController;
use App\Models\AtlasOptionPost;
use App\Observers\AtlasOptionPostObserver;
use Illuminate\Support\Facades\Route;
use LaravelAtlas\Mappers\CommandMapper;
use LaravelAtlas\Mappers\ControllerMapper;
use LaravelAtlas\Mappers\EventMapper;
use LaravelAtlas\Mappers\FormRequestMapper;
use LaravelAtlas\Mappers\JobMapper;
use LaravelAtlas\Mappers\MiddlewareMapper;
use LaravelAtlas\Mappers\ModelMapper;
use LaravelAtlas\Mappers\NotificationMapper;
use LaravelAtlas\Mappers\RouteMapper;
use LaravelAtlas\Mappers\ServiceMapper;

/**
 * Fixture classes written into the Testbench application skeleton, so the
 * mappers resolve them exactly like they would in a real Laravel project.
 *
 * @var array<string, string>
 */
$fixtures = [
    'Models/AtlasOptionPost.php' => '<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtlasOptionPost extends Model
{
    use HasFactory;

    protected $fillable = [\'title\'];

    public function author()
    {
        return $this->belongsTo(AtlasOptionPost::class);
    }
}
',
    'Observers/AtlasOptionPostObserver.php' => '<?php
namespace App\Observers;

use App\Models\AtlasOptionPost;

class AtlasOptionPostObserver
{
    public function created(AtlasOptionPost $post): void {}
}
',
    'Console/Commands/AtlasOptionCommand.php' => '<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

class AtlasOptionCommand extends Command
{
    protected $signature = \'atlas:option-fixture {name}\';

    protected $description = \'Fixture command for scan options\';

    public function handle(): int
    {
        return 0;
    }
}
',
    'Services/AtlasOptionService.php' => '<?php
namespace App\Services;

class AtlasOptionService
{
    public function __construct(private AtlasOptionRepository $repository) {}

    public function run(): string
    {
        return \'ok\';
    }
}
',
    'Notifications/AtlasOptionNotification.php' => '<?php
namespace App\Notifications;

use Illuminate\Notifications\Notification;

class AtlasOptionNotification extends Notification
{
    public function via($notifiable)
    {
        return [\'mail\'];
    }
}
',
    'Http/Middleware/AtlasOptionMiddleware.php' => '<?php
namespace App\Http\Middleware;

use App\Services\AtlasOptionService;
use Closure;

class AtlasOptionMiddleware
{
    public function __construct(private AtlasOptionService $service) {}

    public function handle($request, Closure $next, string $role = \'admin\')
    {
        return $next($request);
    }
}
',
    'Http/Requests/AtlasOptionRequest.php' => '<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AtlasOptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [\'title\' => \'required|string\'];
    }

    public function attributes(): array
    {
        return [\'title\' => \'Post title\'];
    }
}
',
    'Events/AtlasOptionEvent.php' => '<?php
namespace App\Events;

class AtlasOptionEvent
{
    public string $payload = \'\';
}
',
    'Http/Controllers/AtlasOptionController.php' => '<?php
namespace App\Http\Controllers;

use App\Services\AtlasOptionService;

class AtlasOptionController
{
    public function __construct(private AtlasOptionService $service) {}

    public function index()
    {
        return \'ok\';
    }
}
',
    'Jobs/AtlasOptionJob.php' => '<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AtlasOptionJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): void {}
}
',
];

beforeEach(function () use ($fixtures): void {
    foreach ($fixtures as $relativePath => $code) {
        $path = app_path($relativePath);

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        file_put_contents($path, $code);

        require_once $path;
    }
});

afterEach(function () use ($fixtures): void {
    foreach (array_keys($fixtures) as $relativePath) {
        $path = app_path($relativePath);

        if (is_file($path)) {
            unlink($path);
        }
    }
});

/**
 * @param  array<string, mixed>  $options
 *
 * @return array<string, mixed>
 */
function atlasFixtureComponent(object $mapper, array $paths, array $options, string $name): array
{
    /** @var array<string, mixed> $result */
    $result = $mapper->scan(array_merge($options, ['paths' => $paths]));

    foreach ($result['data'] as $component) {
        if (($component['name'] ?? null) === $name) {
            return $component;
        }
    }

    throw new RuntimeException("Fixture component [{$name}] was not found by the mapper.");
}

describe('model scan options', function (): void {
    test('relations, observers and factories are included by default', function (): void {
        AtlasOptionPost::observe(AtlasOptionPostObserver::class);

        $model = atlasFixtureComponent(new ModelMapper, [app_path('Models')], [], 'AtlasOptionPost');

        expect($model)->toHaveKeys(['relations', 'observers', 'factories'])
            ->and($model['observers'])->toContain(AtlasOptionPostObserver::class)
            ->and($model['factories']['uses_factory'])->toBeTrue()
            ->and($model['factories']['class'])->toBe('Database\\Factories\\AtlasOptionPostFactory');
    });

    test('include_relationships false omits relations', function (): void {
        $model = atlasFixtureComponent(new ModelMapper, [app_path('Models')], ['include_relationships' => false], 'AtlasOptionPost');

        expect($model)->not->toHaveKey('relations');
    });

    test('include_observers false omits observers', function (): void {
        $model = atlasFixtureComponent(new ModelMapper, [app_path('Models')], ['include_observers' => false], 'AtlasOptionPost');

        expect($model)->not->toHaveKey('observers');
    });

    test('include_factories false omits factories', function (): void {
        $model = atlasFixtureComponent(new ModelMapper, [app_path('Models')], ['include_factories' => false], 'AtlasOptionPost');

        expect($model)->not->toHaveKey('factories');
    });
});

describe('route scan options', function (): void {
    beforeEach(function (): void {
        Route::middleware('web')
            ->prefix('atlas')
            ->get('option-fixture', [AtlasOptionController::class, 'index'])
            ->name('atlas.option-fixture');
    });

    test('middleware, controller and grouping are included by default', function (): void {
        $result = (new RouteMapper)->scan();
        $route = collect($result['data'])->firstWhere('name', 'atlas.option-fixture');

        expect($result)->toHaveKey('grouping')
            ->and($route)->toHaveKeys(['middleware', 'controller', 'uses']);
    });

    test('include_middleware false omits middleware', function (): void {
        $result = (new RouteMapper)->scan(['include_middleware' => false]);
        $route = collect($result['data'])->firstWhere('name', 'atlas.option-fixture');

        expect($route)->not->toHaveKey('middleware');
    });

    test('include_controllers false omits controller information', function (): void {
        $result = (new RouteMapper)->scan(['include_controllers' => false]);
        $route = collect($result['data'])->firstWhere('name', 'atlas.option-fixture');

        expect($route)->not->toHaveKey('controller')
            ->and($route)->not->toHaveKey('uses');
    });

    test('group_by_prefix false omits grouping metadata', function (): void {
        $result = (new RouteMapper)->scan(['group_by_prefix' => false]);

        expect($result)->not->toHaveKey('grouping');
    });
});

describe('command scan options', function (): void {
    test('signature and description are included by default', function (): void {
        $command = atlasFixtureComponent(new CommandMapper, [app_path('Console/Commands')], [], 'AtlasOptionCommand');

        expect($command)->toHaveKeys(['signature', 'parsed_signature', 'description']);
    });

    test('include_signatures false omits signature data', function (): void {
        $command = atlasFixtureComponent(new CommandMapper, [app_path('Console/Commands')], ['include_signatures' => false], 'AtlasOptionCommand');

        expect($command)->not->toHaveKey('signature')
            ->and($command)->not->toHaveKey('parsed_signature');
    });

    test('include_descriptions false omits the description', function (): void {
        $command = atlasFixtureComponent(new CommandMapper, [app_path('Console/Commands')], ['include_descriptions' => false], 'AtlasOptionCommand');

        expect($command)->not->toHaveKey('description');
    });
});

describe('service scan options', function (): void {
    test('methods and dependencies are included by default', function (): void {
        $service = atlasFixtureComponent(new ServiceMapper, [app_path('Services')], [], 'AtlasOptionService');

        expect($service)->toHaveKeys(['methods', 'dependencies']);
    });

    test('include_methods false omits methods', function (): void {
        $service = atlasFixtureComponent(new ServiceMapper, [app_path('Services')], ['include_methods' => false], 'AtlasOptionService');

        expect($service)->not->toHaveKey('methods');
    });

    test('include_dependencies false omits dependencies', function (): void {
        $service = atlasFixtureComponent(new ServiceMapper, [app_path('Services')], ['include_dependencies' => false], 'AtlasOptionService');

        expect($service)->not->toHaveKey('dependencies');
    });
});

describe('notification scan options', function (): void {
    test('channels and flow are included by default', function (): void {
        $notification = atlasFixtureComponent(new NotificationMapper, [app_path('Notifications')], [], 'AtlasOptionNotification');

        expect($notification)->toHaveKeys(['channels', 'flow']);
    });

    test('include_channels false omits channels', function (): void {
        $notification = atlasFixtureComponent(new NotificationMapper, [app_path('Notifications')], ['include_channels' => false], 'AtlasOptionNotification');

        expect($notification)->not->toHaveKey('channels');
    });

    test('include_flow false omits the flow', function (): void {
        $notification = atlasFixtureComponent(new NotificationMapper, [app_path('Notifications')], ['include_flow' => false], 'AtlasOptionNotification');

        expect($notification)->not->toHaveKey('flow');
    });
});

describe('middleware scan options', function (): void {
    test('parameters and dependencies are included by default', function (): void {
        $middleware = atlasFixtureComponent(new MiddlewareMapper, [app_path('Http/Middleware')], [], 'AtlasOptionMiddleware');

        expect($middleware)->toHaveKeys(['parameters', 'dependencies']);
    });

    test('include_parameters false omits parameters', function (): void {
        $middleware = atlasFixtureComponent(new MiddlewareMapper, [app_path('Http/Middleware')], ['include_parameters' => false], 'AtlasOptionMiddleware');

        expect($middleware)->not->toHaveKey('parameters');
    });

    test('include_dependencies false omits dependencies', function (): void {
        $middleware = atlasFixtureComponent(new MiddlewareMapper, [app_path('Http/Middleware')], ['include_dependencies' => false], 'AtlasOptionMiddleware');

        expect($middleware)->not->toHaveKey('dependencies');
    });
});

describe('form request scan options', function (): void {
    test('rules, authorization and attributes are included by default', function (): void {
        $formRequest = atlasFixtureComponent(new FormRequestMapper, [app_path('Http/Requests')], [], 'AtlasOptionRequest');

        expect($formRequest)->toHaveKeys(['rules', 'authorization', 'attributes']);
    });

    test('include_rules false omits rules', function (): void {
        $formRequest = atlasFixtureComponent(new FormRequestMapper, [app_path('Http/Requests')], ['include_rules' => false], 'AtlasOptionRequest');

        expect($formRequest)->not->toHaveKey('rules');
    });

    test('include_authorization false omits authorization', function (): void {
        $formRequest = atlasFixtureComponent(new FormRequestMapper, [app_path('Http/Requests')], ['include_authorization' => false], 'AtlasOptionRequest');

        expect($formRequest)->not->toHaveKey('authorization');
    });

    test('include_attributes false omits attributes', function (): void {
        $formRequest = atlasFixtureComponent(new FormRequestMapper, [app_path('Http/Requests')], ['include_attributes' => false], 'AtlasOptionRequest');

        expect($formRequest)->not->toHaveKey('attributes');
    });
});

describe('event scan options', function (): void {
    test('listeners and properties are included by default', function (): void {
        $event = atlasFixtureComponent(new EventMapper, [app_path('Events')], [], 'AtlasOptionEvent');

        expect($event)->toHaveKeys(['listeners', 'properties']);
    });

    test('include_listeners false omits listeners', function (): void {
        $event = atlasFixtureComponent(new EventMapper, [app_path('Events')], ['include_listeners' => false], 'AtlasOptionEvent');

        expect($event)->not->toHaveKey('listeners');
    });

    test('include_properties false omits properties', function (): void {
        $event = atlasFixtureComponent(new EventMapper, [app_path('Events')], ['include_properties' => false], 'AtlasOptionEvent');

        expect($event)->not->toHaveKey('properties');
    });
});

describe('controller scan options', function (): void {
    test('actions and dependencies are included by default', function (): void {
        $controller = atlasFixtureComponent(new ControllerMapper, [app_path('Http/Controllers')], [], 'AtlasOptionController');

        expect($controller)->toHaveKeys(['methods', 'dependencies']);
    });

    test('include_actions false omits methods', function (): void {
        $controller = atlasFixtureComponent(new ControllerMapper, [app_path('Http/Controllers')], ['include_actions' => false], 'AtlasOptionController');

        expect($controller)->not->toHaveKey('methods');
    });

    test('include_dependencies false omits dependencies', function (): void {
        $controller = atlasFixtureComponent(new ControllerMapper, [app_path('Http/Controllers')], ['include_dependencies' => false], 'AtlasOptionController');

        expect($controller)->not->toHaveKey('dependencies');
    });
});

describe('job scan options', function (): void {
    test('methods are included by default and list trait methods', function (): void {
        $job = atlasFixtureComponent(new JobMapper, [app_path('Jobs')], [], 'AtlasOptionJob');

        expect($job)->toHaveKey('methods')
            ->and(array_column($job['methods'], 'name'))->toContain('handle')
            ->and(array_column($job['methods'], 'name'))->toContain('dispatch');
    });

    test('include_methods false omits methods', function (): void {
        $job = atlasFixtureComponent(new JobMapper, [app_path('Jobs')], ['include_methods' => false], 'AtlasOptionJob');

        expect($job)->not->toHaveKey('methods');
    });

    test('include_trait_methods false keeps only the methods declared by the job', function (): void {
        $job = atlasFixtureComponent(new JobMapper, [app_path('Jobs')], ['include_trait_methods' => false], 'AtlasOptionJob');

        $names = array_column($job['methods'], 'name');

        expect($names)->toContain('handle')
            ->and($names)->not->toContain('dispatch')
            ->and($names)->not->toContain('onQueue');
    });
});
