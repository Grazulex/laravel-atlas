<?php

declare(strict_types=1);

namespace LaravelAtlas\Exporters;

use Exception;
use InvalidArgumentException;
use RuntimeException;

class ImageExporter extends BaseExporter
{
    protected string $format = 'png';
    protected int $width = 1200;
    protected int $height = 800;
    
    protected array $colors = [
        'background' => '#ffffff',
        'model' => '#e3f2fd',       // Bleu très clair
        'controller' => '#e8f5e8',  // Vert très clair
        'route' => '#fff3e0',       // Orange très clair
        'job' => '#ffebee',         // Rouge très clair
        'service' => '#f3e5f5',     // Violet très clair
        'policy' => '#fce4ec',      // Rose très clair
        'text' => '#212121',        // Texte foncé bien visible
        'border' => '#424242',      // Bordure foncée
        'line' => '#757575',        // Lignes grises
        'relationship' => '#616161' // Relations en gris foncé
    ];

    public function export(array $data): string
    {
        if (!extension_loaded('gd')) {
            throw new RuntimeException('L\'extension GD est requise pour l\'exportation d\'images');
        }

        try {
            return $this->generateImage($data);
        } catch (Exception $e) {
            throw new RuntimeException('Erreur lors de la génération de l\'image: ' . $e->getMessage(), 0, $e);
        }
    }

    protected function generateImage(array $data): string
    {
        $image = imagecreatetruecolor($this->width, $this->height);
        if ($image === false) {
            throw new RuntimeException('Impossible de créer l\'image');
        }

        $colors = $this->initializeColors($image);
        imagefill($image, 0, 0, $colors['background']);

        $components = $this->extractComponents($data);
        $layout = $this->calculateLayout($components);
        
        $this->drawNodes($image, $layout, $colors);
        $this->drawRelationships($image, $components, $layout, $colors);
        $this->drawLegend($image, $colors);
        $this->drawTitle($image, $colors);

        ob_start();
        switch ($this->format) {
            case 'png':
                imagepng($image);
                break;
            case 'jpg':
            case 'jpeg':
                imagejpeg($image, null, 90);
                break;
            case 'gif':
                imagegif($image);
                break;
            default:
                imagepng($image);
        }
        
        $imageData = ob_get_clean();
        imagedestroy($image);

        if ($imageData === false) {
            throw new RuntimeException('Erreur lors de la génération de l\'image');
        }

        return $imageData;
    }

    protected function initializeColors($image): array
    {
        $allocatedColors = [];
        foreach ($this->colors as $name => $hex) {
            $rgb = $this->hexToRgb($hex);
            $allocatedColors[$name] = imagecolorallocate($image, $rgb['r'], $rgb['g'], $rgb['b']);
        }
        return $allocatedColors;
    }

    protected function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2))
        ];
    }

    protected function extractComponents(array $data): array
    {
        $components = ['nodes' => [], 'relationships' => []];
        
        foreach ($data as $type => $items) {
            if (!is_array($items) || $type === 'relationships' || $type === 'metadata') {
                continue;
            }
            
            foreach ($items as $item) {
                if (!is_array($item)) continue;
                
                $nodeId = $item['class_name'] ?? $item['name'] ?? null;
                if ($nodeId !== null) {
                    $components['nodes'][] = [
                        'id' => $nodeId,
                        'type' => $type,
                        'label' => $this->getShortName($nodeId),
                        'data' => $item
                    ];
                }
            }
        }

        if (isset($data['relationships'])) {
            $components['relationships'] = $data['relationships'];
        }

        return $components;
    }

    protected function getShortName(string $fullName): string
    {
        $parts = explode('\\', $fullName);
        return end($parts) ?: $fullName;
    }

    protected function calculateLayout(array $components): array
    {
        $nodes = $components['nodes'];
        $layout = [];
        
        $nodeWidth = 120;
        $nodeHeight = 50;
        $padding = 20;
        
        $cols = ceil(sqrt(count($nodes)));
        $rows = ceil(count($nodes) / $cols);
        
        $startX = ($this->width - ($cols * ($nodeWidth + $padding))) / 2;
        $startY = 100;
        
        foreach ($nodes as $index => $node) {
            $col = $index % $cols;
            $row = intval($index / $cols);
            
            $layout[$node['id']] = [
                'x' => $startX + ($col * ($nodeWidth + $padding)),
                'y' => $startY + ($row * ($nodeHeight + $padding)),
                'width' => $nodeWidth,
                'height' => $nodeHeight,
                'type' => $node['type']
            ];
        }
        
        return $layout;
    }

    protected function drawNodes($image, array $layout, array $colors): void
    {
        foreach ($layout as $id => $node) {
            $fillColor = $colors[$node['type']] ?? $colors['model'];
            $borderColor = $colors['border'];
            
            // Fond du rectangle
            imagefilledrectangle(
                $image,
                (int) $node['x'],
                (int) $node['y'],
                (int) ($node['x'] + $node['width']),
                (int) ($node['y'] + $node['height']),
                $fillColor
            );
            
            // Bordure du rectangle
            imagerectangle(
                $image,
                (int) $node['x'],
                (int) $node['y'],
                (int) ($node['x'] + $node['width']),
                (int) ($node['y'] + $node['height']),
                $borderColor
            );
            
            // Texte
            $shortName = $this->getShortName($id);
            $this->drawCenteredText($image, $shortName, $node, $colors['text']);
        }
    }

    protected function drawCenteredText($image, string $text, array $node, int $color): void
    {
        $fontSize = 3; // Police GD intégrée (1-5)
        $textWidth = imagefontwidth($fontSize) * strlen($text);
        $textHeight = imagefontheight($fontSize);
        
        $x = (int) ($node['x'] + ($node['width'] - $textWidth) / 2);
        $y = (int) ($node['y'] + ($node['height'] - $textHeight) / 2);
        
        imagestring($image, $fontSize, $x, $y, $text, $color);
    }

    protected function drawRelationships($image, array $components, array $layout, array $colors): void
    {
        if (!isset($components['relationships'])) return;
        
        foreach ($components['relationships'] as $rel) {
            $fromId = $rel['from'] ?? null;
            $toId = $rel['to'] ?? null;
            
            if (!$fromId || !$toId || !isset($layout[$fromId]) || !isset($layout[$toId])) {
                continue;
            }
            
            $from = $layout[$fromId];
            $to = $layout[$toId];
            
            $fromX = (int) ($from['x'] + $from['width'] / 2);
            $fromY = (int) ($from['y'] + $from['height'] / 2);
            $toX = (int) ($to['x'] + $to['width'] / 2);
            $toY = (int) ($to['y'] + $to['height'] / 2);
            
            imageline($image, $fromX, $fromY, $toX, $toY, $colors['line']);
        }
    }

    protected function drawLegend($image, array $colors): void
    {
        $legendX = 20;
        $legendY = 20;
        $fontSize = 2;
        
        imagestring($image, $fontSize, $legendX, $legendY, 'Legende:', $colors['text']);
        
        $y = $legendY + 20;
        $types = [
            'model' => 'Modeles', 
            'controller' => 'Controleurs', 
            'route' => 'Routes', 
            'job' => 'Jobs', 
            'service' => 'Services', 
            'policy' => 'Policies'
        ];
        
        foreach ($types as $type => $label) {
            if (isset($colors[$type])) {
                imagefilledrectangle($image, $legendX, $y, $legendX + 15, $y + 10, $colors[$type]);
                imagerectangle($image, $legendX, $y, $legendX + 15, $y + 10, $colors['border']);
                imagestring($image, $fontSize, $legendX + 20, $y - 2, $label, $colors['text']);
                $y += 15;
            }
        }
    }

    protected function drawTitle($image, array $colors): void
    {
        $title = $this->config('title', 'Laravel Atlas Architecture Map');
        $fontSize = 4;
        $x = (int) (($this->width - imagefontwidth($fontSize) * strlen($title)) / 2);
        $y = 10;
        
        imagestring($image, $fontSize, $x, $y, $title, $colors['text']);
    }

    public function getMimeType(): string
    {
        return match ($this->format) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            default => 'image/png',
        };
    }

    protected function getDefaultConfig(): array
    {
        return [
            'title' => 'Laravel Atlas Architecture Map',
            'format' => 'png',
            'width' => 1200,
            'height' => 800,
        ];
    }

    public function getExtension(): string
    {
        return $this->format;
    }

    public function setFormat(string $format): self
    {
        $validFormats = ['png', 'jpg', 'jpeg', 'gif'];
        
        if (!in_array($format, $validFormats)) {
            throw new InvalidArgumentException("Format d'image non pris en charge: {$format}");
        }
        
        $this->format = $format;
        return $this;
    }

    public function setDimensions(int $width, int $height): self
    {
        if ($width < 200 || $height < 200) {
            throw new InvalidArgumentException('Les dimensions de l\'image doivent être d\'au moins 200x200 pixels');
        }
        
        $this->width = $width;
        $this->height = $height;
        return $this;
    }

    public function setColors(array $colors): self
    {
        $this->colors = array_merge($this->colors, $colors);
        return $this;
    }
}