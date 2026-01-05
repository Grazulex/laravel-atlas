---
id: 1
title: 'Fix segfault on atlas:export --type=models (Issue #33)'
status: Done
priority: critical
assignees:
  - '@claude'
labels:
  - bug
  - issue-33
subtasks: []
dependencies: []
blocked_by: []
created_date: '2026-01-05T17:47:09.899Z'
updated_date: '2026-01-05T17:54:18.924Z'
closed_date: '2026-01-05T17:54:18.924Z'
changelog:
  - timestamp: '2026-01-05T17:47:09.899Z'
    action: created
    details: Task created
    user: system
  - timestamp: '2026-01-05T17:49:47.826Z'
    action: modified
    details: Task updated
    user: AI
  - timestamp: '2026-01-05T17:49:59.316Z'
    action: updated
    details: 'status: To Do → In Progress'
    user: user
  - timestamp: '2026-01-05T17:53:38.726Z'
    action: modified
    details: Task updated
    user: AI
  - timestamp: '2026-01-05T17:54:11.855Z'
    action: modified
    details: Task updated
    user: AI
  - timestamp: '2026-01-05T17:54:18.924Z'
    action: updated
    details: 'status: In Progress → Done'
    user: user
acceptance_criteria: []
ai_plan: >-
  ## Plan d'implementation


  ### Objectif

  Corriger le segfault dans guessRelations() en remplaçant l'invocation
  dynamique par une analyse statique du code source.


  ### Cause du bug

  La ligne 130 de ModelMapper.php invoque $method->invoke($model) sur chaque
  méthode publique sans paramètres. Cela peut déclencher des références
  circulaires (User -> Posts -> User -> Posts...) causant un stack overflow.


  ### Etapes

  1. Remplacer guessRelations() par une analyse statique regex du code source

  2. Détecter les patterns de relations Laravel dans le code (HasMany,
  BelongsTo, HasOne, etc.)

  3. Extraire le type de relation et le modèle lié sans exécuter le code

  4. Ajouter des tests pour vérifier le bon fonctionnement


  ### Fichiers concernes

  - src/Mappers/ModelMapper.php (modify) - Réécrire guessRelations()


  ### Approche technique

  Utiliser preg_match_all pour détecter les patterns de relations Laravel dans
  le code source :

  - return $this->hasMany(Model::class)

  - return $this->belongsTo(Model::class)

  - return $this->hasOne(Model::class)

  - etc.


  Cette approche est similaire à analyzeFlow() qui utilise déjà des regex sans
  problème.


  ### Defis potentiels

  - Couvrir tous les types de relations Laravel

  - Gérer les différents formats de déclaration (avec/sans paramètres
  additionnels)

  - Maintenir la compatibilité avec l'output existant
ai_notes: >
  **2026-01-05T17:53:38.725Z** - **18:55** - PROGRESS: Implémentation de
  l'analyse statique regex dans guessRelations() terminée

  **18:56** - DECISION: Utiliser des regex pour détecter les patterns de
  relations Laravel au lieu d'invoquer les méthodes

  **18:57** - PROGRESS: Tests Pest exécutés via Docker - 88 passed

  **18:58** - PROGRESS: PHPStan exécuté - No errors

  **18:58** - RESOLVED: Segfault corrigé en évitant l'invocation dynamique des
  méthodes de relation
ai_review: |-
  ## Self-Review

  ### Complete
  - [x] Réécriture de guessRelations() avec analyse statique regex
  - [x] Suppression des imports inutilisés (Relation, Throwable)
  - [x] Ajout de resolveRelatedClass() pour résoudre les FQCN
  - [x] Tests Pest passent (88 passed)
  - [x] PHPStan passe (No errors)
  - [x] Pint passe (64 files)

  ### Tests effectues
  - Pest tests: 88 passed (532 assertions)
  - PHPStan: No errors
  - Pint: 64 files passed

  ### Qualite du code
  - Standards respectes: Oui
  - Documentation: Commentaires dans le code

  ### Changement de comportement
  - foreignKey sera toujours null (ne peut être déterminé sans exécution)
  - Les relations sont détectées via analyse statique du code source

  ### Limitations connues
  - Ne détecte pas les relations définies dynamiquement
  - Ne peut pas déterminer la foreignKey sans exécuter le code
  - Pattern regex peut manquer des formats non-standard

  ### Recommandations
  - Tester sur un projet réel avec des modèles complexes
  - Considérer l'ajout d'un test spécifique pour les relations circulaires
---
Segmentation fault when running php artisan atlas:export --type=models. Works fine with events and listeners.
