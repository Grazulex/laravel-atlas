<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use LaravelAtlas\Mappers\ModelMapper;

// Créons une classe de test qui étend Model pour nos tests
class TestModel extends Model
{
    protected $table = 'test_models';

    protected $fillable = ['name', 'email'];

    protected $guarded = ['id'];

    protected $casts = ['created_at' => 'datetime'];

    public function testRelation()
    {
        return $this->hasMany(TestModel::class);
    }
}

it('has correct type', function (): void {
    $mapper = new ModelMapper;

    expect($mapper->type())->toBe('models');
});

it('scans for models in given paths', function (): void {
    $mapper = new ModelMapper;

    // Test avec des chemins inexistants
    $result = $mapper->scan(['paths' => ['/non/existent/path']]);

    expect($result)->toHaveKey('type');
    expect($result)->toHaveKey('count');
    expect($result)->toHaveKey('data');
    expect($result['type'])->toBe('models');
    expect($result['count'])->toBe(0);
    expect($result['data'])->toBeArray();
});

it('uses default paths when none provided', function (): void {
    $mapper = new ModelMapper;

    $result = $mapper->scan();

    expect($result)->toHaveKey('type');
    expect($result)->toHaveKey('count');
    expect($result)->toHaveKey('data');
    expect($result['type'])->toBe('models');
});

it('analyzes model correctly', function (): void {
    $mapper = new ModelMapper;
    $model = new TestModel;

    // Utiliser la réflection pour accéder à la méthode protégée
    $reflection = new ReflectionClass($mapper);
    $method = $reflection->getMethod('analyzeModel');
    $method->setAccessible(true);

    $result = $method->invoke($mapper, $model);

    expect($result)->toHaveKey('class');
    expect($result)->toHaveKey('table');
    expect($result)->toHaveKey('fillable');
    expect($result)->toHaveKey('guarded');
    expect($result)->toHaveKey('casts');
    expect($result)->toHaveKey('relations');

    expect($result['class'])->toBe(TestModel::class);
    expect($result['table'])->toBe('test_models');
    expect($result['fillable'])->toBe(['name', 'email']);
    expect($result['guarded'])->toBe(['id']);
    expect($result['relations'])->toBeArray();
});

it('resolves class from file path', function (): void {
    $mapper = new ModelMapper;

    // Utiliser la réflection pour accéder à la méthode protégée
    $reflection = new ReflectionClass($mapper);
    $method = $reflection->getMethod('resolveClassFromFile');
    $method->setAccessible(true);

    // Test avec un chemin inexistant
    $result = $method->invoke($mapper, '/non/existent/file.php');

    expect($result)->toBeNull();
});

it('guesses relations from model methods', function (): void {
    $mapper = new ModelMapper;
    $model = new TestModel;

    // Utiliser la réflection pour accéder à la méthode protégée
    $reflection = new ReflectionClass($mapper);
    $method = $reflection->getMethod('guessRelations');
    $method->setAccessible(true);

    $result = $method->invoke($mapper, $model);

    expect($result)->toBeArray();
    // Le résultat peut être vide si aucune relation n'est trouvée, ce qui est normal
});

it('handles different method types in relations guessing', function (): void {
    // Créons une classe avec différents types de méthodes
    $model = new class extends Model
    {
        protected $table = 'test_table';

        // Méthode avec paramètres - devrait être ignorée
        public function methodWithParams($param): void {}

        // Méthode statique - devrait être ignorée
        public static function staticMethod(): void {}

        // Méthode qui lève une exception - devrait être gérée silencieusement
        public function problematicMethod(): never
        {
            throw new Exception('Test exception');
        }
    };

    $mapper = new ModelMapper;
    $reflection = new ReflectionClass($mapper);
    $method = $reflection->getMethod('guessRelations');
    $method->setAccessible(true);

    $result = $method->invoke($mapper, $model);

    expect($result)->toBeArray();
});
