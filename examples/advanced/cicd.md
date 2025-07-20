# CI/CD Integration Examples

Examples demonstrating how to integrate Laravel Atlas into your Continuous Integration and Continuous Deployment pipelines.

## 📋 Prerequisites

- Laravel Atlas installed in your project
- CI/CD platform (GitHub Actions, GitLab CI, Jenkins, etc.)
- Basic understanding of CI/CD concepts

## 🚀 GitHub Actions Integration

### Basic Documentation Generation

```yaml
# .github/workflows/atlas-docs.yml
name: Generate Architecture Documentation

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main]

jobs:
  generate-docs:
    runs-on: ubuntu-latest
    
    steps:
    - name: Checkout code
      uses: actions/checkout@v4
      
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.3'
        extensions: mbstring, xml, ctype, json, fileinfo, tokenizer
        coverage: none
        
    - name: Install dependencies
      run: composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
      
    - name: Create docs directory
      run: mkdir -p docs/atlas
      
    - name: Generate architecture documentation
      run: |
        php artisan atlas:generate --format=markdown --detailed --output=docs/atlas/ARCHITECTURE.md
        php artisan atlas:generate --format=json --output=docs/atlas/architecture.json
        php artisan atlas:generate --format=mermaid --output=docs/atlas/architecture.mmd
        
    - name: Generate component-specific docs
      run: |
        php artisan atlas:generate --type=models --format=markdown --detailed --output=docs/atlas/models.md
        php artisan atlas:generate --type=routes --format=markdown --detailed --output=docs/atlas/routes.md
        php artisan atlas:generate --type=controllers --format=markdown --detailed --output=docs/atlas/controllers.md
        
    - name: Commit documentation
      if: github.ref == 'refs/heads/main'
      run: |
        git config --local user.email "action@github.com"
        git config --local user.name "GitHub Action"
        git add docs/atlas/
        git diff --staged --quiet || git commit -m "📚 Update architecture documentation [skip ci]"
        git push
```

### Advanced Documentation with Deployment

```yaml
# .github/workflows/atlas-advanced.yml
name: Advanced Atlas Documentation

on:
  push:
    branches: [main]
  schedule:
    # Generate docs daily at 2 AM UTC
    - cron: '0 2 * * *'

jobs:
  analyze-and-deploy:
    runs-on: ubuntu-latest
    
    steps:
    - name: Checkout code
      uses: actions/checkout@v4
      
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.3'
        
    - name: Install dependencies
      run: composer install --no-dev --optimize-autoloader
      
    - name: Setup Node.js (for Mermaid CLI)
      uses: actions/setup-node@v4
      with:
        node-version: '18'
        
    - name: Install Mermaid CLI
      run: npm install -g @mermaid-js/mermaid-cli
      
    - name: Create output directories
      run: |
        mkdir -p public/atlas/{docs,diagrams,api}
        mkdir -p docs/architecture
        
    - name: Generate comprehensive documentation
      run: |
        # Generate main documentation
        php artisan atlas:generate --format=html --detailed --output=public/atlas/index.html
        php artisan atlas:generate --format=json --output=public/atlas/api/architecture.json
        php artisan atlas:generate --format=markdown --detailed --output=docs/architecture/README.md
        
        # Generate component documentation
        components=("models" "routes" "controllers" "services" "jobs" "events" "middleware")
        for component in "${components[@]}"; do
          echo "📊 Generating $component documentation..."
          php artisan atlas:generate --type="$component" --format=markdown --detailed --output="docs/architecture/$component.md"
          php artisan atlas:generate --type="$component" --format=mermaid --output="public/atlas/diagrams/$component.mmd"
        done
        
    - name: Convert Mermaid to SVG
      run: |
        for mermaid_file in public/atlas/diagrams/*.mmd; do
          if [[ -f "$mermaid_file" ]]; then
            svg_file="${mermaid_file%.mmd}.svg"
            echo "🎨 Converting $mermaid_file to SVG..."
            mmdc -i "$mermaid_file" -o "$svg_file" -t dark --backgroundColor transparent || true
          fi
        done
        
    - name: Generate architecture report
      run: |
        php -r "
        \$json = file_get_contents('public/atlas/api/architecture.json');
        \$data = json_decode(\$json, true);
        \$report = [
            'generated_at' => \$data['generated_at'],
            'generation_time_ms' => \$data['generation_time_ms'],
            'summary' => []
        ];
        
        foreach (\$data['data'] as \$type => \$typeData) {
            \$count = count(\$typeData['data'] ?? []);
            \$report['summary'][\$type] = \$count;
        }
        
        file_put_contents('public/atlas/api/report.json', json_encode(\$report, JSON_PRETTY_PRINT));
        "
        
    - name: Deploy to GitHub Pages
      if: github.ref == 'refs/heads/main'
      uses: peaceiris/actions-gh-pages@v3
      with:
        github_token: ${{ secrets.GITHUB_TOKEN }}
        publish_dir: ./public/atlas
        destination_dir: atlas
        
    - name: Commit documentation changes
      if: github.ref == 'refs/heads/main'
      run: |
        git config --local user.email "action@github.com"
        git config --local user.name "GitHub Action"
        git add docs/
        git diff --staged --quiet || git commit -m "📚 Update architecture documentation $(date +'%Y-%m-%d')"
        git push
        
    - name: Create architecture summary comment
      if: github.event_name == 'pull_request'
      uses: actions/github-script@v7
      with:
        script: |
          const fs = require('fs');
          const reportData = JSON.parse(fs.readFileSync('public/atlas/api/report.json', 'utf8'));
          
          let summary = '## 🏗️ Architecture Summary\n\n';
          summary += `**Generated:** ${reportData.generated_at}\n`;
          summary += `**Generation Time:** ${reportData.generation_time_ms}ms\n\n`;
          summary += '### Component Counts\n\n';
          
          for (const [type, count] of Object.entries(reportData.summary)) {
            summary += `- **${type}:** ${count}\n`;
          }
          
          summary += `\n**Total Components:** ${Object.values(reportData.summary).reduce((a, b) => a + b, 0)}\n`;
          
          github.rest.issues.createComment({
            issue_number: context.issue.number,
            owner: context.repo.owner,
            repo: context.repo.repo,
            body: summary
          });
```

## 🦊 GitLab CI Integration

### Basic Documentation Pipeline

```yaml
# .gitlab-ci.yml
stages:
  - documentation
  - deploy

variables:
  PHP_VERSION: "8.3"

generate_docs:
  stage: documentation
  image: php:${PHP_VERSION}
  
  before_script:
    - apt-get update -qq && apt-get install -y -qq git curl libmcrypt-dev libjpeg-dev libpng-dev libfreetype6-dev libbz2-dev libzip-dev unzip
    - curl -sS https://getcomposer.org/installer | php
    - php composer.phar install --no-dev --optimize-autoloader
    
  script:
    - mkdir -p docs/atlas public/atlas
    - php artisan atlas:generate --format=markdown --detailed --output=docs/atlas/ARCHITECTURE.md
    - php artisan atlas:generate --format=json --output=public/atlas/architecture.json
    - php artisan atlas:generate --format=html --output=public/atlas/index.html
    - php artisan atlas:generate --format=mermaid --output=docs/atlas/architecture.mmd
    
  artifacts:
    paths:
      - docs/atlas/
      - public/atlas/
    expire_in: 1 week
    
  only:
    - main
    - develop

deploy_docs:
  stage: deploy
  image: alpine:latest
  
  before_script:
    - apk add --no-cache rsync openssh-client
    
  script:
    - echo "Deploying architecture documentation..."
    # Add your deployment commands here
    
  dependencies:
    - generate_docs
    
  only:
    - main
```

### Advanced GitLab Pipeline with Quality Gates

```yaml
# .gitlab-ci.yml
stages:
  - analyze
  - document
  - quality-gate
  - deploy

variables:
  PHP_VERSION: "8.3"
  ATLAS_THRESHOLD_MODELS: 50
  ATLAS_THRESHOLD_ROUTES: 100

.php_template: &php_template
  image: php:${PHP_VERSION}
  before_script:
    - apt-get update -qq && apt-get install -y -qq git curl libzip-dev unzip
    - curl -sS https://getcomposer.org/installer | php
    - php composer.phar install --no-dev --optimize-autoloader

analyze_architecture:
  <<: *php_template
  stage: analyze
  
  script:
    - php artisan atlas:generate --format=json --output=analysis.json
    - |
      php -r "
      \$data = json_decode(file_get_contents('analysis.json'), true);
      \$models = count(\$data['data']['models']['data'] ?? []);
      \$routes = count(\$data['data']['routes']['data'] ?? []);
      
      echo 'Models: ' . \$models . PHP_EOL;
      echo 'Routes: ' . \$routes . PHP_EOL;
      
      file_put_contents('metrics.env', 'MODEL_COUNT=' . \$models . PHP_EOL);
      file_put_contents('metrics.env', 'ROUTE_COUNT=' . \$routes . PHP_EOL, FILE_APPEND);
      "
      
  artifacts:
    paths:
      - analysis.json
      - metrics.env
    expire_in: 1 hour
    reports:
      dotenv: metrics.env

generate_documentation:
  <<: *php_template
  stage: document
  
  dependencies:
    - analyze_architecture
    
  script:
    - mkdir -p docs/atlas diagrams reports
    - |
      # Generate all documentation formats
      formats=("json" "markdown" "mermaid" "html")
      for format in "${formats[@]}"; do
        echo "Generating $format documentation..."
        php artisan atlas:generate --format="$format" --detailed --output="docs/atlas/architecture.$format"
      done
      
    - |
      # Generate component-specific documentation
      components=("models" "routes" "controllers" "services")
      for component in "${components[@]}"; do
        echo "Generating $component documentation..."
        php artisan atlas:generate --type="$component" --format=markdown --detailed --output="docs/atlas/$component.md"
        php artisan atlas:generate --type="$component" --format=mermaid --output="diagrams/$component.mmd"
      done
      
  artifacts:
    paths:
      - docs/atlas/
      - diagrams/
    expire_in: 1 week

architecture_quality_gate:
  <<: *php_template
  stage: quality-gate
  
  dependencies:
    - analyze_architecture
    
  script:
    - |
      echo "Checking architecture quality gates..."
      echo "Model count: $MODEL_COUNT (threshold: $ATLAS_THRESHOLD_MODELS)"
      echo "Route count: $ROUTE_COUNT (threshold: $ATLAS_THRESHOLD_ROUTES)"
      
      if [ "$MODEL_COUNT" -gt "$ATLAS_THRESHOLD_MODELS" ]; then
        echo "⚠️ Warning: Model count ($MODEL_COUNT) exceeds threshold ($ATLAS_THRESHOLD_MODELS)"
      fi
      
      if [ "$ROUTE_COUNT" -gt "$ATLAS_THRESHOLD_ROUTES" ]; then
        echo "⚠️ Warning: Route count ($ROUTE_COUNT) exceeds threshold ($ATLAS_THRESHOLD_ROUTES)"
        exit 1
      fi
      
      echo "✅ Architecture quality gates passed"
      
  allow_failure: true

deploy_documentation:
  stage: deploy
  image: alpine:latest
  
  dependencies:
    - generate_documentation
    
  before_script:
    - apk add --no-cache curl
    
  script:
    - echo "Deploying architecture documentation..."
    - |
      # Example: Upload to documentation server
      curl -X POST \
        -H "Authorization: Bearer $DOCS_API_TOKEN" \
        -F "file=@docs/atlas/architecture.html" \
        "https://docs.example.com/api/upload/atlas"
        
  only:
    - main
```

## 🔨 Jenkins Integration

### Jenkins Pipeline

```groovy
// Jenkinsfile
pipeline {
    agent any
    
    environment {
        PHP_VERSION = '8.3'
        COMPOSER_HOME = "${WORKSPACE}/.composer"
    }
    
    stages {
        stage('Setup') {
            steps {
                sh 'composer install --no-dev --optimize-autoloader'
            }
        }
        
        stage('Generate Architecture Documentation') {
            parallel {
                stage('JSON Export') {
                    steps {
                        sh 'php artisan atlas:generate --format=json --output=atlas.json'
                        archiveArtifacts artifacts: 'atlas.json', fingerprint: true
                    }
                }
                
                stage('Markdown Documentation') {
                    steps {
                        sh 'mkdir -p docs/atlas'
                        sh 'php artisan atlas:generate --format=markdown --detailed --output=docs/atlas/ARCHITECTURE.md'
                        
                        // Generate component-specific docs
                        script {
                            def components = ['models', 'routes', 'controllers', 'services']
                            components.each { component ->
                                sh "php artisan atlas:generate --type=${component} --format=markdown --detailed --output=docs/atlas/${component}.md"
                            }
                        }
                    }
                }
                
                stage('Visual Diagrams') {
                    steps {
                        sh 'mkdir -p diagrams'
                        sh 'php artisan atlas:generate --format=mermaid --output=diagrams/architecture.mmd'
                        
                        script {
                            def components = ['models', 'routes', 'controllers']
                            components.each { component ->
                                sh "php artisan atlas:generate --type=${component} --format=mermaid --output=diagrams/${component}.mmd"
                            }
                        }
                    }
                }
                
                stage('Interactive HTML') {
                    steps {
                        sh 'mkdir -p public/atlas'
                        sh 'php artisan atlas:generate --format=html --detailed --output=public/atlas/index.html'
                    }
                }
            }
        }
        
        stage('Architecture Analysis') {
            steps {
                script {
                    // Analyze the generated JSON
                    sh '''
                        php -r "
                        \\$data = json_decode(file_get_contents('atlas.json'), true);
                        \\$summary = [];
                        
                        foreach (\\$data['data'] as \\$type => \\$typeData) {
                            \\$count = count(\\$typeData['data'] ?? []);
                            \\$summary[\\$type] = \\$count;
                        }
                        
                        echo 'Architecture Summary:' . PHP_EOL;
                        foreach (\\$summary as \\$type => \\$count) {
                            echo '  ' . \\$type . ': ' . \\$count . PHP_EOL;
                        }
                        
                        \\$total = array_sum(\\$summary);
                        echo 'Total Components: ' . \\$total . PHP_EOL;
                        
                        // Save metrics for reporting
                        file_put_contents('architecture-metrics.properties', 
                            'TOTAL_COMPONENTS=' . \\$total . PHP_EOL .
                            'MODEL_COUNT=' . (\\$summary['models'] ?? 0) . PHP_EOL .
                            'ROUTE_COUNT=' . (\\$summary['routes'] ?? 0) . PHP_EOL .
                            'CONTROLLER_COUNT=' . (\\$summary['controllers'] ?? 0) . PHP_EOL
                        );
                        "
                    '''
                    
                    // Load metrics
                    def props = readProperties file: 'architecture-metrics.properties'
                    env.TOTAL_COMPONENTS = props.TOTAL_COMPONENTS
                    env.MODEL_COUNT = props.MODEL_COUNT
                    env.ROUTE_COUNT = props.ROUTE_COUNT
                    env.CONTROLLER_COUNT = props.CONTROLLER_COUNT
                }
            }
        }
        
        stage('Quality Gates') {
            steps {
                script {
                    echo "Architecture Quality Check"
                    echo "Total Components: ${env.TOTAL_COMPONENTS}"
                    echo "Models: ${env.MODEL_COUNT}"
                    echo "Routes: ${env.ROUTE_COUNT}"
                    echo "Controllers: ${env.CONTROLLER_COUNT}"
                    
                    // Define thresholds
                    def maxModels = 50
                    def maxRoutes = 100
                    def maxControllers = 30
                    
                    def warnings = []
                    
                    if (env.MODEL_COUNT as Integer > maxModels) {
                        warnings.add("⚠️ Model count (${env.MODEL_COUNT}) exceeds threshold (${maxModels})")
                    }
                    
                    if (env.ROUTE_COUNT as Integer > maxRoutes) {
                        warnings.add("⚠️ Route count (${env.ROUTE_COUNT}) exceeds threshold (${maxRoutes})")
                    }
                    
                    if (env.CONTROLLER_COUNT as Integer > maxControllers) {
                        warnings.add("⚠️ Controller count (${env.CONTROLLER_COUNT}) exceeds threshold (${maxControllers})")
                    }
                    
                    if (warnings.size() > 0) {
                        echo "Quality Gate Warnings:"
                        warnings.each { warning ->
                            echo warning
                        }
                        // Set build as unstable but don't fail
                        currentBuild.result = 'UNSTABLE'
                    } else {
                        echo "✅ All quality gates passed"
                    }
                }
            }
        }
        
        stage('Deploy Documentation') {
            when {
                branch 'main'
            }
            steps {
                // Archive all documentation artifacts
                archiveArtifacts artifacts: 'docs/**/*', fingerprint: true
                archiveArtifacts artifacts: 'diagrams/**/*', fingerprint: true
                archiveArtifacts artifacts: 'public/atlas/**/*', fingerprint: true
                
                // Publish HTML reports
                publishHTML([
                    allowMissing: false,
                    alwaysLinkToLastBuild: true,
                    keepAll: true,
                    reportDir: 'public/atlas',
                    reportFiles: 'index.html',
                    reportName: 'Architecture Documentation'
                ])
                
                script {
                    // Example: Deploy to documentation server
                    if (env.DOCS_DEPLOY_URL) {
                        sh """
                            curl -X POST \\
                                -H "Authorization: Bearer ${env.DOCS_API_TOKEN}" \\
                                -F "file=@public/atlas/index.html" \\
                                "${env.DOCS_DEPLOY_URL}/atlas"
                        """
                    }
                }
            }
        }
    }
    
    post {
        always {
            // Clean up workspace
            cleanWs()
        }
        
        success {
            echo "✅ Architecture documentation generated successfully"
            
            // Send notification if needed
            script {
                if (env.SLACK_WEBHOOK) {
                    sh """
                        curl -X POST -H 'Content-type: application/json' \\
                            --data '{"text":"📚 Architecture documentation updated for ${env.JOB_NAME} #${env.BUILD_NUMBER}\\nTotal Components: ${env.TOTAL_COMPONENTS}"}' \\
                            ${env.SLACK_WEBHOOK}
                    """
                }
            }
        }
        
        failure {
            echo "❌ Architecture documentation generation failed"
        }
        
        unstable {
            echo "⚠️ Architecture documentation generated with warnings"
        }
    }
}
```

## 🐳 Docker Integration

### Dockerfile for Documentation Generation

```dockerfile
# Dockerfile.atlas
FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Mermaid CLI for diagram generation
RUN npm install -g @mermaid-js/mermaid-cli

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Create output directories
RUN mkdir -p docs/atlas public/atlas diagrams

# Generate documentation
RUN php artisan atlas:generate --format=json --output=public/atlas/architecture.json && \
    php artisan atlas:generate --format=html --detailed --output=public/atlas/index.html && \
    php artisan atlas:generate --format=markdown --detailed --output=docs/atlas/ARCHITECTURE.md && \
    php artisan atlas:generate --format=mermaid --output=diagrams/architecture.mmd

# Expose port for serving documentation
EXPOSE 8000

# Command to serve documentation
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public/atlas"]
```

### Docker Compose for Development

```yaml
# docker-compose.atlas.yml
version: '3.8'

services:
  atlas-docs:
    build:
      context: .
      dockerfile: Dockerfile.atlas
    ports:
      - "8080:8000"
    volumes:
      - ./docs:/var/www/docs
      - ./public/atlas:/var/www/public/atlas
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
    command: >
      sh -c "
        echo 'Generating fresh documentation...' &&
        php artisan atlas:generate --format=html --detailed --output=public/atlas/index.html &&
        php artisan atlas:generate --format=json --output=public/atlas/architecture.json &&
        echo 'Documentation ready at http://localhost:8080' &&
        php -S 0.0.0.0:8000 -t public/atlas
      "
```

## 📊 Monitoring and Metrics

### Architecture Drift Detection

```bash
#!/bin/bash
# scripts/detect-architecture-drift.sh

echo "🔍 Detecting architecture drift..."

# Generate current architecture
php artisan atlas:generate --format=json --output=current-atlas.json

# Compare with baseline if it exists
if [[ -f "baseline-atlas.json" ]]; then
    echo "📊 Comparing with baseline..."
    
    php -r "
    \$current = json_decode(file_get_contents('current-atlas.json'), true);
    \$baseline = json_decode(file_get_contents('baseline-atlas.json'), true);
    
    \$currentCounts = [];
    \$baselineCounts = [];
    
    foreach (\$current['data'] as \$type => \$data) {
        \$currentCounts[\$type] = count(\$data['data'] ?? []);
    }
    
    foreach (\$baseline['data'] as \$type => \$data) {
        \$baselineCounts[\$type] = count(\$data['data'] ?? []);
    }
    
    echo 'Architecture Drift Report:' . PHP_EOL;
    \$driftDetected = false;
    
    foreach (\$currentCounts as \$type => \$current) {
        \$baseline = \$baselineCounts[\$type] ?? 0;
        \$diff = \$current - \$baseline;
        
        if (\$diff != 0) {
            \$driftDetected = true;
            \$symbol = \$diff > 0 ? '📈' : '📉';
            echo \$symbol . ' ' . \$type . ': ' . \$baseline . ' → ' . \$current . ' (' . (\$diff > 0 ? '+' : '') . \$diff . ')' . PHP_EOL;
        }
    }
    
    if (!\$driftDetected) {
        echo '✅ No architecture drift detected' . PHP_EOL;
        exit(0);
    } else {
        echo '⚠️ Architecture drift detected!' . PHP_EOL;
        exit(1);
    }
    "
else
    echo "📝 Creating baseline architecture..."
    cp current-atlas.json baseline-atlas.json
    echo "✅ Baseline created. Run again to detect drift."
fi
```

### Metrics Collection Script

```php
<?php
// scripts/collect-metrics.php

use LaravelAtlas\Facades\Atlas;

class MetricsCollector
{
    public function collectAndSave(): void
    {
        $timestamp = date('Y-m-d H:i:s');
        
        echo "📊 Collecting architecture metrics at {$timestamp}...\n";
        
        $json = Atlas::export('all', 'json');
        $data = json_decode($json, true);
        
        $metrics = [
            'timestamp' => $timestamp,
            'generation_time_ms' => $data['generation_time_ms'] ?? 0,
            'components' => []
        ];
        
        foreach ($data['data'] as $type => $typeData) {
            $count = count($typeData['data'] ?? []);
            $metrics['components'][$type] = $count;
            echo "  {$type}: {$count}\n";
        }
        
        $metrics['total_components'] = array_sum($metrics['components']);
        
        // Save metrics to database or file
        $this->saveMetrics($metrics);
        
        echo "✅ Metrics collected: {$metrics['total_components']} total components\n";
    }
    
    private function saveMetrics(array $metrics): void
    {
        // Option 1: Save to JSON file
        $metricsFile = 'storage/atlas-metrics.json';
        $existingMetrics = [];
        
        if (file_exists($metricsFile)) {
            $existingMetrics = json_decode(file_get_contents($metricsFile), true) ?? [];
        }
        
        $existingMetrics[] = $metrics;
        
        // Keep only last 100 entries
        $existingMetrics = array_slice($existingMetrics, -100);
        
        file_put_contents($metricsFile, json_encode($existingMetrics, JSON_PRETTY_PRINT));
        
        // Option 2: Send to monitoring system (example)
        // $this->sendToMonitoring($metrics);
    }
    
    private function sendToMonitoring(array $metrics): void
    {
        // Example: Send to external monitoring system
        $webhook = env('METRICS_WEBHOOK');
        
        if ($webhook) {
            $payload = [
                'timestamp' => $metrics['timestamp'],
                'service' => 'laravel-atlas',
                'metrics' => $metrics
            ];
            
            $ch = curl_init($webhook);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode !== 200) {
                echo "⚠️ Failed to send metrics to monitoring system (HTTP {$httpCode})\n";
            }
        }
    }
}

// Run the collector
$collector = new MetricsCollector();
$collector->collectAndSave();
```

## 🎯 Best Practices for CI/CD Integration

### 1. **Fail Fast with Quality Gates**
```yaml
# Set thresholds and fail builds that exceed them
script:
  - |
    MODEL_COUNT=$(php -r "echo count(json_decode(file_get_contents('atlas.json'), true)['data']['models']['data'] ?? []);")
    if [ "$MODEL_COUNT" -gt "50" ]; then
      echo "❌ Too many models ($MODEL_COUNT > 50)"
      exit 1
    fi
```

### 2. **Cache Documentation Artifacts**
```yaml
cache:
  key: atlas-docs-$CI_COMMIT_SHA
  paths:
    - docs/atlas/
    - public/atlas/
```

### 3. **Conditional Documentation**
```yaml
# Only generate docs on main branch or when docs change
only:
  refs:
    - main
  changes:
    - app/**/*
    - database/**/*
    - routes/**/*
```

### 4. **Parallel Generation**
```yaml
# Generate different formats in parallel
parallel:
  matrix:
    - FORMAT: json
    - FORMAT: markdown  
    - FORMAT: html
    - FORMAT: mermaid
script:
  - php artisan atlas:generate --format=$FORMAT --output=atlas.$FORMAT
```

### 5. **Environment-Specific Configuration**
```yaml
variables:
  ATLAS_CONFIG_PROD: "production"
  ATLAS_CONFIG_DEV: "development"
  
script:
  - cp config/atlas.${ATLAS_CONFIG_PROD}.php config/atlas.php
  - php artisan atlas:generate
```

## 🔗 Related Examples

- [Basic Usage](../basic-usage.md) - Getting started with Laravel Atlas
- [JSON Export](../exports/json.md) - Working with JSON exports
- [Advanced Analysis](advanced-analysis.md) - Complex architectural analysis
- [Performance Optimization](performance.md) - Optimizing Atlas for large applications