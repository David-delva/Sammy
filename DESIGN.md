Guide de style — E.T.P.

But: Fournir une base visuelle cohérente pour la refonte (palette bleue, typographie, composants).

Palette
- Couleurs principales :
  - --inst-blue-800: #3f51b5 (primaire)
  - --inst-blue-700: #5c6bc0
  - --inst-blue-soft: rgba(92, 107, 192, 0.18)
  - --inst-bg: #eef4ff (fond)
  - Accents verts: #66bb6a (succès)
  - Danger: #ef476f

Typographie
- Titres: `Fraunces` (serif variable) pour `page-title`.
- Texte courant: `Sora` pour lisibilité moderne.
- Chargement dans `resources/css/app.css` via Google Fonts déjà présent.

Principaux composants (classes utilitaires)
- Boutons: `.btn-primary`, `.btn-secondary`, `.btn-danger`, `.btn-success`.
- Cartes: `.card`, `.surface-card`, `.page-hero`.
- Formulaires: `.form-field`, `.form-label`, `.form-input`, `.form-select`, `.form-textarea`.
- Badges: `.badge-*` (utiliser `.badge-blue`, `.badge-green`, etc.).
- Tables: `.data-table` pour tableaux responsifs et stylés.
- Sidebar: `.pf-left` / `.pf-right` containers.

Accessibilité & Contraste
- Respecter un ratio de contraste minimal 4.5:1 pour textes normaux.
- Focus visibles : règle `*:focus-visible` dans `app.css`.

Assets
- Logo : placer `logo.svg` dans `public/images/` puis intégrer dans la navbar.

Comment utiliser
- Préserver les variables CSS dans `resources/css/app.css`.
- Favoriser les classes utilitaires définies ci‑dessus plutôt que styles inline.
- Pour nouveaux composants, ajouter une section courte dans `DESIGN.md` et une règle dans `app.css`.

Prochaine étape recommandée
- Intégrer un `logo.svg` et appliquer le header/navbar sur toutes les vues.
- Refactoriser la page `dashboard` et les listes principales pour utiliser les composants ci‑dessous.
