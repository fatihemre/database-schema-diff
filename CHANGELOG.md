# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Multi-language support (i18n/localization)
- Language switcher in UI (navbar)
- English translations (default)
- Turkish translations
- Language API endpoint (`lang.php`)
- Language preference saved to localStorage
- Translation system for dynamic content

### Planned
- MySQL/MariaDB database support
- Additional language translations (community contributions welcome!)
- Table index comparison
- Foreign key comparison
- Export comparison report (PDF/HTML)
- User authentication system
- Multiple database pair comparison
- Automated schema migration suggestions

## [1.0.0] - 2025-01-14

### Added
- Initial release
- PostgreSQL database schema comparison
- Visual diff display with color coding
- Column name and data type detection
- Synchronized navigation between local and remote schemas
- Accordion-style schema browsing
- Responsive UIKit-based interface
- RESTful JSON API endpoint
- Real-time database comparison
- Support for varchar, char, numeric type detection with precision
- Hostname display in column headers
- Status icons (check/cross) for schema sync status

### Features
- **Schema Comparison**: Compare all schemas between two PostgreSQL databases
- **Table Comparison**: Identify missing, extra, and different tables
- **Column Comparison**: Detect column name and type differences
- **Visual Indicators**:
  - Red border: Missing in remote
  - Green border: Extra in remote
  - Orange border: Column differences
- **Synchronized Navigation**: Click a schema on one side, the same schema opens on both sides
- **Accordion Behavior**: Only one schema open at a time per side
- **Modern UI**: Clean, minimal design with UIKit framework
- **Mobile Responsive**: Works on desktop, tablet, and mobile devices

### Technical Details
- PHP 7.4+ required
- PostgreSQL 9.6+ required
- No external PHP dependencies
- Vanilla JavaScript (no jQuery)
- UIKit 3.17.11 for UI components
- RESTful API architecture

### Security
- Database credentials in separate config file
- Config file excluded from git via .gitignore
- Read-only database access recommended
- No data modification capabilities

## Version History

### Version Numbering

- **Major version (X.0.0)**: Incompatible API changes
- **Minor version (0.X.0)**: New features, backwards compatible
- **Patch version (0.0.X)**: Bug fixes, backwards compatible

---

[Unreleased]: https://github.com/fatihemre/database-schema-diff/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/fatihemre/database-schema-diff/releases/tag/v1.0.0
