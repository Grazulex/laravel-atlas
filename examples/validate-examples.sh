#!/bin/bash

# Simple validation script for Laravel Atlas examples
# This script checks if the basic structure and syntax are correct

echo "=== Laravel Atlas Examples Validation ==="
echo

# Check if all example files exist
echo "1. Checking example files exist:"
examples=(
    "examples/README.md"
    "examples/basic-usage.php"
    "examples/models-example.php"
    "examples/routes-example.php"
    "examples/commands-example.php"
    "examples/complete-analysis.php"
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
    "src/AtlasManager.php"
    "src/Mappers/ModelMapper.php"
    "src/Mappers/RouteMapper.php"
    "src/Mappers/CommandMapper.php"
    "src/Facades/Atlas.php"
)

for file in "${key_files[@]}"; do
    if [ -f "$file" ]; then
        echo "✓ $file exists"
    else
        echo "✗ $file missing"
    fi
done

echo

# Check that empty mapper files are not referenced in examples
echo "4. Checking empty mappers are not referenced:"
empty_mappers=(
    "services"
    "controllers"
    "events"
    "jobs"
    "middleware"
    "notifications"
    "policies"
    "requests"
    "resources"
    "rules"
    "observers"
    "listeners"
    "actions"
)

issues_found=0
for mapper in "${empty_mappers[@]}"; do
    if grep -r "scan('${mapper}')" examples/ > /dev/null 2>&1; then
        echo "✗ Examples reference unimplemented mapper: $mapper"
        issues_found=1
    fi
done

if [ $issues_found -eq 0 ]; then
    echo "✓ No references to unimplemented mappers found"
fi

echo
echo "=== Validation completed ==="