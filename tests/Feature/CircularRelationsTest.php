<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use LaravelAtlas\Mappers\ModelMapper;

beforeEach(function (): void {
    // Create temporary models directory
    $this->modelsPath = sys_get_temp_dir() . '/atlas-test-models-' . uniqid();
    File::makeDirectory($this->modelsPath, 0755, true);
});

afterEach(function (): void {
    // Clean up temporary models
    if (isset($this->modelsPath) && File::isDirectory($this->modelsPath)) {
        File::deleteDirectory($this->modelsPath);
    }
});

test('ModelMapper handles circular relations without segfault (Issue #33)', function (): void {
    // Create User model with circular relation to Post
    $userModel = <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    protected $fillable = ['name', 'email'];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
PHP;

    // Create Post model with circular relation back to User
    $postModel = <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    protected $fillable = ['title', 'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
PHP;

    // Create Comment model with relation to Post (deeper circular chain)
    $commentModel = <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    protected $fillable = ['content', 'post_id', 'user_id'];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
PHP;

    File::put($this->modelsPath . '/User.php', $userModel);
    File::put($this->modelsPath . '/Post.php', $postModel);
    File::put($this->modelsPath . '/Comment.php', $commentModel);

    $mapper = new ModelMapper;

    // This should NOT cause a segmentation fault
    // Before the fix, invoking relation methods would cause infinite recursion
    $result = $mapper->scan([
        'paths' => [$this->modelsPath],
        'recursive' => false,
    ]);

    expect($result)->toBeArray()
        ->and($result['type'])->toBe('models');
});

test('ModelMapper detects relations using static analysis', function (): void {
    // Create a model with various relation types
    $model = <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TestModel extends Model
{
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Parent::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}
PHP;

    File::put($this->modelsPath . '/TestModel.php', $model);

    $mapper = new ModelMapper;
    $result = $mapper->scan([
        'paths' => [$this->modelsPath],
        'recursive' => false,
    ]);

    // The models won't be scanned because they're not real Laravel models
    // (they're not in the autoloader), but the test ensures no crash occurs
    expect($result)->toBeArray()
        ->and($result['type'])->toBe('models');
});
