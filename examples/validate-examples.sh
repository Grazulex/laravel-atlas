#!/bin/bash

# Simple validation script for Laravel Atlas examples
# This script checks if the basic structure and syntax are correct

echo "=== Laravel Atlas Examples Validation ==="
echo

# Check if all example files exist
echo "1. Checking example files exist:"
examples=(
    "README.md"
    "basic-usage.php"
    "models-example.php"
    "routes-example.php"
    "commands-example.php"
    "services-example.php"
    "notifications-example.php"
    "middlewares-example.php"
    "form-requests-example.php"
    "complete-analysis.php"
)

for example in "${examples[@]}"; do
    if [ -f "$example" ]; then
        echo "✓ $example exists"
    else
        echo "✗ $example missing"
    fi
done

echo

# Check PHP syntax
echo "2. Checking PHP syntax:"
for example in examples/*.php; do
    if [ -f "$example" ]; then
        if php -l "$example" > /dev/null 2>&1; then
            echo "✓ $(basename $example) - syntax OK"
        else
            echo "✗ $(basename $example) - syntax error"
        fi
    fi
done

echo

# Check key implemented classes exist
echo "3. Checking implemented classes:"
key_files=(
    "../src/AtlasManager.php"
    "../src/Mappers/ModelMapper.php"
    "../src/Mappers/RouteMapper.php"
    "../src/Mappers/CommandMapper.php"
    "../src/Mappers/ServiceMapper.php"
    "../src/Mappers/NotificationMapper.php"
    "../src/Mappers/MiddlewareMapper.php"
    "../src/Mappers/FormRequestMapper.php"
    "../src/Facades/Atlas.php"
    "../src/Exporters/Html/HtmlLayoutExporter.php"
)

for file in "${key_files[@]}"; do
    if [ -f "$file" ]; then
        echo "✓ $file exists"
    else
        echo "✗ $file missing"
    fi
done

echo

# Check that unimplemented mapper files are not referenced in examples
echo "4. Checking unimplemented mappers are not referenced:"
unimplemented_mappers=(
    "controllers"
    "events"
    "jobs"
    "policies"
    "resources"
    "rules"
    "observers"
    "listeners"
    "actions"
)

issues_found=0
for mapper in "${unimplemented_mappers[@]}"; do
    if grep -r "scan('${mapper}')" . > /dev/null 2>&1; then
        echo "✗ Examples reference unimplemented mapper: $mapper"
        issues_found=1
    fi
done

if [ $issues_found -eq 0 ]; then
    echo "✓ No references to unimplemented mappers found"
fi

echo

# Check that implemented mappers are properly referenced
echo "5. Checking implemented mappers are referenced:"
implemented_mappers=(
    "models"
    "routes"
    "commands"
    "services"
    "notifications"
    "middlewares"
    "form_requests"
)

for mapper in "${implemented_mappers[@]}"; do
    if grep -r "scan('${mapper}')" . > /dev/null 2>&1; then
        echo "✓ $mapper mapper is referenced in examples"
    else
        echo "✗ $mapper mapper is not referenced in examples"
        issues_found=1
    fi
done

echo
echo "=== Validation completed ==="