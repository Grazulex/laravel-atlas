# Laravel Atlas Facade - Démonstration

Voici comment utiliser la nouvelle façade Atlas dans votre code Laravel :

## Installation

Aucune installation supplémentaire n'est nécessaire. La façade est automatiquement enregistrée avec le package.

## Exemples d'utilisation

### 1. Accès aux informations disponibles

```php
use Grazulex\LaravelAtlas\Facades\Atlas;

// Obtenir les types disponibles
$types = Atlas::getAvailableTypes();
// Retourne: ['models', 'routes', 'jobs']

// Obtenir les formats d'export disponibles  
$formats = Atlas::getAvailableFormats();
// Retourne: ['json', 'html', 'markdown', 'mermaid', 'pdf']
```

### 2. Scanner des données

```php
// Scanner tous les modèles
$modelsData = Atlas::scan('models');

// Scanner avec des options spécifiques
$routesData = Atlas::scan('routes', [
    'include_middleware' => true,
    'group_by_prefix' => true
]);
```

### 3. Exporter vers différents formats

```php
// Exporter les modèles en JSON
$json = Atlas::export('models', 'json');

// Exporter les routes en Markdown
$markdown = Atlas::export('routes', 'markdown', ['detailed' => true]);

// Exporter les jobs en HTML
$html = Atlas::export('jobs', 'html');
```

### 4. Générer des exports multi-types

```php
// Générer un rapport complet en HTML
$completeReport = Atlas::generate(['models', 'routes', 'jobs'], 'html');

// Générer uniquement models et routes en PDF
$pdf = Atlas::generate(['models', 'routes'], 'pdf', [
    'detailed' => true,
    'include_relationships' => true
]);
```

### 5. Accès direct aux composants

```php
// Obtenir un mapper spécifique
$modelMapper = Atlas::mapper('models');
$data = $modelMapper->scan(['scan_path' => app_path('Domain/Models')]);

// Obtenir un exporter spécifique
$pdfExporter = Atlas::exporter('pdf');
$content = $pdfExporter->export($data);
```

## Avantages de la façade

- **Syntaxe plus simple** : `Atlas::scan('models')` vs `app(ModelMapper::class)->scan()`
- **API unifiée** : Une seule interface pour tous les mappers et exporters
- **Typage complet** : Support PHPDoc pour l'autocomplétion IDE
- **Extensibilité** : Possibilité d'enregistrer des mappers/exporters personnalisés

## Utilisation dans les contrôleurs

```php
<?php

namespace App\Http\Controllers;

use Grazulex\LaravelAtlas\Facades\Atlas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AtlasController extends Controller
{
    public function models(): JsonResponse
    {
        $data = Atlas::scan('models');
        return response()->json($data);
    }
    
    public function exportModels(string $format): Response
    {
        $content = Atlas::export('models', $format);
        $extension = Atlas::exporter($format)->getExtension();
        
        return response($content)
            ->header('Content-Type', $this->getContentType($format))
            ->header('Content-Disposition', "attachment; filename=models.{$extension}");
    }
    
    public function completeReport(): Response
    {
        $html = Atlas::generate(['models', 'routes', 'jobs'], 'html', [
            'detailed' => true
        ]);
        
        return response($html)->header('Content-Type', 'text/html');
    }
}
```

La façade Atlas rend Laravel Atlas beaucoup plus facile à utiliser dans vos applications ! 🚀
