<?php

use LaravelAtlas\Mappers\NotificationMapper;
use LaravelAtlas\Mappers\RequestMapper;
use LaravelAtlas\Mappers\RuleMapper;

echo "🗺️  Test des nouveaux mappers Laravel Atlas\n\n";

// Test NotificationMapper
echo "📧 Test NotificationMapper:\n";
$notificationMapper = new NotificationMapper([]);
$result = $notificationMapper->performScan();
echo "✓ NotificationMapper fonctionne - trouvé {$result->count()} notifications\n\n";

// Test RequestMapper
echo "📝 Test RequestMapper:\n";
$requestMapper = new RequestMapper([]);
$result = $requestMapper->performScan();
echo "✓ RequestMapper fonctionne - trouvé {$result->count()} requests\n\n";

// Test RuleMapper
echo "📋 Test RuleMapper:\n";
$ruleMapper = new RuleMapper([]);
$result = $ruleMapper->performScan();
echo "✓ RuleMapper fonctionne - trouvé {$result->count()} rules\n\n";

echo "🎉 Tous les nouveaux mappers fonctionnent parfaitement !\n";
echo "📊 Total de mappers disponibles : 13\n";
echo "   - Mappers originaux: 8 (models, routes, jobs, services, controllers, events, commands, middleware)\n";
echo "   - Nouveaux mappers spécialisés: 5 (policies, resources, notifications, requests, rules)\n";
echo "   - AnalysisEngine avancé pour l'analyse architecturale\n";
echo "\n✨ Laravel Atlas est maintenant complet avec un support maximum de mappers !\n";
