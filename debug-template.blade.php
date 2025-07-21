<!DOCTYPE html>
<html>
<head>
    <title>Debug Template</title>
</head>
<body>
    <h1>Debug Variables</h1>
    
    <?php
    // Function to safely display variable types and values
    function debugVar($name, $value, $context = '') {
        echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>";
        echo "<h3>Variable: {$name} {$context}</h3>";
        echo "<p><strong>Type:</strong> " . gettype($value) . "</p>";
        
        if (is_array($value)) {
            echo "<p><strong>Array keys:</strong> " . implode(', ', array_keys($value)) . "</p>";
            echo "<p><strong>Array values (first 3):</strong></p>";
            echo "<pre>";
            $count = 0;
            foreach ($value as $k => $v) {
                if ($count++ >= 3) break;
                echo "[$k] => ";
                if (is_array($v)) {
                    echo "Array(" . count($v) . " items)\n";
                } else {
                    echo gettype($v) . ": " . (is_string($v) ? substr($v, 0, 50) : $v) . "\n";
                }
            }
            echo "</pre>";
        } else {
            echo "<p><strong>Value:</strong> " . (is_string($value) ? substr($value, 0, 100) : $value) . "</p>";
        }
        echo "</div>";
    }
    ?>
    
    <h2>Main Data Structure</h2>
    @if(isset($data))
        <?php debugVar('data', $data); ?>
    @endif
    
    <h2>Routes Analysis</h2>
    @if(isset($data['routes']))
        <?php debugVar('data[routes]', $data['routes'], '(main routes array)'); ?>
        
        @foreach($data['routes'] as $index => $route)
            <h3>Route {{ $index }}</h3>
            <?php debugVar("route[$index]", $route, "(full route data)"); ?>
            
            @if(isset($route['method']))
                <?php debugVar("route[$index]['method']", $route['method'], "(method field)"); ?>
            @endif
            
            @if(isset($route['controller']))
                <?php debugVar("route[$index]['controller']", $route['controller'], "(controller field)"); ?>
            @endif
            
            @if(isset($route['middleware']))
                <?php debugVar("route[$index]['middleware']", $route['middleware'], "(middleware field)"); ?>
                @if(is_array($route['middleware']))
                    @foreach($route['middleware'] as $midIndex => $middleware)
                        <?php debugVar("route[$index]['middleware'][$midIndex]", $middleware, "(individual middleware)"); ?>
                    @endforeach
                @endif
            @endif
        @endforeach
    @endif
    
    <h2>Models Analysis</h2>
    @if(isset($data['models']))
        @foreach($data['models'] as $index => $model)
            <h3>Model {{ $index }}</h3>
            <?php debugVar("model[$index]", $model, "(full model data)"); ?>
            
            @if(isset($model['connected_to']))
                <?php debugVar("model[$index]['connected_to']", $model['connected_to'], "(connected_to field)"); ?>
                @foreach($model['connected_to'] as $type => $components)
                    <?php debugVar("model[$index]['connected_to'] type", $type, "(connection type key)"); ?>
                    <?php debugVar("model[$index]['connected_to'] components", $components, "(components array)"); ?>
                    @if(is_array($components))
                        @foreach($components as $compIndex => $component)
                            <?php debugVar("model[$index]['connected_to'][$type][$compIndex]", $component, "(individual component)"); ?>
                        @endforeach
                    @endif
                @endforeach
            @endif
        @endforeach
    @endif
    
</body>
</html>
