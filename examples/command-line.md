# Command Line Examples

Complete guide to using the `php artisan atlas:generate` command with various options and scenarios.

## 🖥️ Command Syntax

```bash
php artisan atlas:generate [options]
```

### Available Options

| Option | Description | Default | Example |
|--------|-------------|---------|---------|
| `--type` | Component type to analyze | `all` | `--type=models` |
| `--format` | Output format | `json` | `--format=markdown` |
| `--output` | Output file path | Console | `--output=docs/atlas.md` |
| `--detailed` | Include detailed information | `false` | `--detailed` |

## 📊 Type-Specific Examples

### Models
```bash
# Basic model scan
php artisan atlas:generate --type=models

# Detailed model analysis with relationships
php artisan atlas:generate --type=models --detailed --format=markdown --output=docs/models.md

# Model relationship diagram
php artisan atlas:generate --type=models --format=mermaid --output=diagrams/model-relationships.mmd
```

**Use Cases:**
- Understanding database structure
- Documenting model relationships
- Identifying orphaned models
- Planning database migrations

### Routes
```bash
# All application routes
php artisan atlas:generate --type=routes

# API documentation from routes
php artisan atlas:generate --type=routes --format=markdown --detailed --output=docs/API.md

# Route visualization
php artisan atlas:generate --type=routes --format=mermaid --output=diagrams/routes.mmd
```

**Use Cases:**
- API documentation generation
- Route optimization analysis
- Middleware usage review
- Security audit preparation

### Controllers
```bash
# Controller analysis
php artisan atlas:generate --type=controllers --detailed

# Controller documentation
php artisan atlas:generate --type=controllers --format=markdown --output=docs/controllers.md
```

**Use Cases:**
- Code organization review
- Identifying fat controllers
- Method usage analysis
- Refactoring planning

### Services
```bash
# Service layer mapping
php artisan atlas:generate --type=services --detailed --format=json --output=services.json

# Service dependency visualization
php artisan atlas:generate --type=services --format=mermaid --output=diagrams/services.mmd
```

**Use Cases:**
- Dependency injection analysis
- Service layer documentation
- Architecture pattern validation
- Circular dependency detection

### Jobs
```bash
# Queue job analysis
php artisan atlas:generate --type=jobs --detailed

# Job documentation for operations team
php artisan atlas:generate --type=jobs --format=markdown --output=ops/JOBS.md
```

**Use Cases:**
- Queue monitoring setup
- Job dependency mapping
- Performance optimization
- Operations documentation

### Events
```bash
# Event system mapping
php artisan atlas:generate --type=events --format=mermaid --output=diagrams/events.mmd

# Event documentation
php artisan atlas:generate --type=events --detailed --format=markdown --output=docs/events.md
```

**Use Cases:**
- Event flow documentation
- Listener optimization
- Debugging event chains
- System integration planning

### Middleware
```bash
# Middleware analysis
php artisan atlas:generate --type=middleware --detailed

# Security middleware audit
php artisan atlas:generate --type=middleware --format=markdown --output=security/middleware-audit.md
```

**Use Cases:**
- Security auditing
- Performance analysis
- Middleware ordering review
- Request flow documentation

### Policies
```bash
# Authorization policy mapping
php artisan atlas:generate --type=policies --detailed --format=markdown --output=docs/authorization.md

# Policy visualization
php artisan atlas:generate --type=policies --format=mermaid --output=diagrams/policies.mmd
```

**Use Cases:**
- Security documentation
- Permission auditing
- Access control review
- Compliance reporting

### Notifications
```bash
# Notification system analysis
php artisan atlas:generate --type=notifications --detailed

# Communication flow documentation
php artisan atlas:generate --type=notifications --format=markdown --output=docs/notifications.md
```

**Use Cases:**
- Communication audit
- Channel optimization
- Template management
- User experience review

### Requests & Rules
```bash
# Form request validation
php artisan atlas:generate --type=requests --detailed --format=markdown --output=docs/validation.md

# Custom validation rules
php artisan atlas:generate --type=rules --format=json --output=validation-rules.json
```

**Use Cases:**
- Validation documentation
- Security review
- API specification
- Form documentation

## 🎯 Format-Specific Examples

### JSON Format
```bash
# Machine-readable data
php artisan atlas:generate --format=json --output=data/architecture.json

# API endpoint data
php artisan atlas:generate --type=routes --format=json --output=api/endpoints.json

# Compact JSON (no pretty printing)
php artisan atlas:generate --format=json --output=compact.json
```

**Best For:**
- API integration
- Data processing
- Automation scripts
- Third-party tools

### Markdown Format
```bash
# Complete documentation
php artisan atlas:generate --format=markdown --detailed --output=docs/ARCHITECTURE.md

# Component-specific docs
php artisan atlas:generate --type=models --format=markdown --output=docs/DATA_MODEL.md

# README integration
php artisan atlas:generate --type=routes --format=markdown --output=API_ENDPOINTS.md
```

**Best For:**
- Documentation websites
- README files
- Team wikis
- Code reviews

### Mermaid Format
```bash
# Complete architecture diagram
php artisan atlas:generate --format=mermaid --output=diagrams/architecture.mmd

# Model relationships
php artisan atlas:generate --type=models --format=mermaid --output=diagrams/models.mmd

# Service dependencies
php artisan atlas:generate --type=services --format=mermaid --output=diagrams/services.mmd
```

**Best For:**
- Visual presentations
- Architecture reviews
- Documentation sites
- Team onboarding

### HTML Format
```bash
# Interactive documentation
php artisan atlas:generate --format=html --detailed --output=public/architecture.html

# Searchable component browser
php artisan atlas:generate --type=all --format=html --output=public/docs/atlas.html
```

**Best For:**
- Interactive exploration
- Team sharing
- Client presentations
- Self-service documentation

### PDF Format
```bash
# Printable documentation
php artisan atlas:generate --format=pdf --detailed --output=reports/architecture.pdf

# Executive summary
php artisan atlas:generate --type=models --format=pdf --output=reports/data-model.pdf
```

**Best For:**
- Executive reports
- Compliance documentation
- Offline documentation
- Client deliverables

## 🔄 Batch Operations

### Multiple Types, Same Format
```bash
#!/bin/bash
# Generate documentation for key components
types=("models" "routes" "controllers" "services")
format="markdown"

for type in "${types[@]}"; do
    echo "Generating $type documentation..."
    php artisan atlas:generate --type="$type" --format="$format" --detailed --output="docs/$type.md"
done
```

### Same Type, Multiple Formats
```bash
#!/bin/bash
# Generate model documentation in all formats
formats=("json" "markdown" "mermaid" "html")
type="models"

for format in "${formats[@]}"; do
    echo "Generating $type as $format..."
    php artisan atlas:generate --type="$type" --format="$format" --output="output/models.$format"
done
```

### Complete Documentation Generation
```bash
#!/bin/bash
# Generate complete project documentation
echo "🗺️ Generating complete Laravel Atlas documentation..."

# Create output directories
mkdir -p docs/{components,diagrams,reports}
mkdir -p public/docs

# Core documentation
php artisan atlas:generate --format=markdown --detailed --output=docs/ARCHITECTURE.md
php artisan atlas:generate --format=json --output=docs/architecture.json
php artisan atlas:generate --format=html --output=public/docs/architecture.html

# Component-specific documentation
php artisan atlas:generate --type=models --format=markdown --detailed --output=docs/components/models.md
php artisan atlas:generate --type=routes --format=markdown --detailed --output=docs/components/routes.md
php artisan atlas:generate --type=controllers --format=markdown --detailed --output=docs/components/controllers.md

# Visual diagrams
php artisan atlas:generate --type=models --format=mermaid --output=docs/diagrams/models.mmd
php artisan atlas:generate --type=routes --format=mermaid --output=docs/diagrams/routes.mmd
php artisan atlas:generate --format=mermaid --output=docs/diagrams/complete.mmd

# Reports
php artisan atlas:generate --format=pdf --detailed --output=docs/reports/architecture.pdf

echo "✅ Documentation generation complete!"
echo "📁 Check the docs/ and public/docs/ directories"
```

## 🚀 CI/CD Integration Examples

### GitHub Actions
```yaml
name: Generate Architecture Documentation

on:
  push:
    branches: [main]
  pull_request:
    branches: [main]

jobs:
  documentation:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.3'
        
    - name: Install dependencies
      run: composer install --no-dev --optimize-autoloader
      
    - name: Generate documentation
      run: |
        php artisan atlas:generate --format=markdown --output=docs/ARCHITECTURE.md
        php artisan atlas:generate --format=json --output=public/api/architecture.json
        
    - name: Commit documentation
      run: |
        git config --local user.email "action@github.com"
        git config --local user.name "GitHub Action"
        git add docs/ public/api/
        git diff --staged --quiet || git commit -m "Update architecture documentation [skip ci]"
        git push
```

### GitLab CI
```yaml
generate_docs:
  stage: documentation
  image: php:8.3
  before_script:
    - composer install --no-dev --optimize-autoloader
  script:
    - php artisan atlas:generate --format=markdown --output=docs/ARCHITECTURE.md
    - php artisan atlas:generate --format=html --output=public/architecture.html
  artifacts:
    paths:
      - docs/
      - public/architecture.html
    expire_in: 1 week
  only:
    - main
```

### Docker Integration
```dockerfile
# Dockerfile for documentation generation
FROM php:8.3-cli

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader

# Generate documentation during build
RUN php artisan atlas:generate --format=json --output=public/api/architecture.json
RUN php artisan atlas:generate --format=html --output=public/docs/index.html

EXPOSE 8000
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
```

## ⚠️ Troubleshooting Common Issues

### Memory Issues
```bash
# Increase memory limit for large applications
php -d memory_limit=1G artisan atlas:generate --detailed

# Or scan components separately
php artisan atlas:generate --type=models --detailed
php artisan atlas:generate --type=routes --detailed
```

### Permission Issues
```bash
# Ensure output directory exists and is writable
mkdir -p docs/atlas
chmod 755 docs/atlas
php artisan atlas:generate --output=docs/atlas/architecture.json
```

### Performance Optimization
```bash
# Skip detailed scanning for faster results
php artisan atlas:generate --type=models  # Without --detailed

# Use JSON format for fastest processing
php artisan atlas:generate --format=json --type=routes
```

### Output Validation
```bash
# Validate JSON output
php artisan atlas:generate --format=json --output=/tmp/test.json
php -r "json_decode(file_get_contents('/tmp/test.json')); echo 'Valid JSON\n';"

# Check file was created
php artisan atlas:generate --output=test.json && ls -la test.json
```

## 🎯 Pro Tips

1. **Use Descriptive Output Names**: Include timestamps or versions
   ```bash
   php artisan atlas:generate --output="docs/atlas-$(date +%Y%m%d).json"
   ```

2. **Combine with Version Control**: Track architectural changes
   ```bash
   php artisan atlas:generate --format=markdown --output=docs/ARCHITECTURE.md
   git add docs/ARCHITECTURE.md && git commit -m "Update architecture docs"
   ```

3. **Create Aliases for Common Commands**:
   ```bash
   alias atlas-docs="php artisan atlas:generate --format=markdown --detailed"
   alias atlas-diagram="php artisan atlas:generate --format=mermaid"
   alias atlas-json="php artisan atlas:generate --format=json"
   ```

4. **Use Environment-Specific Configuration**:
   ```bash
   # Development environment - detailed analysis
   php artisan atlas:generate --detailed --format=html --output=public/dev-atlas.html
   
   # Production environment - basic overview
   php artisan atlas:generate --format=json --output=public/api/atlas.json
   ```

5. **Combine with Other Laravel Commands**:
   ```bash
   # Generate docs after clearing caches
   php artisan config:clear && php artisan route:clear && php artisan atlas:generate
   ```