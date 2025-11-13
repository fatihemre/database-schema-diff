# Contributing to Database Comparison Tool

First off, thank you for considering contributing to Database Comparison Tool! It's people like you that make this tool better for everyone.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [How Can I Contribute?](#how-can-i-contribute)
- [Development Setup](#development-setup)
- [Pull Request Process](#pull-request-process)
- [Style Guidelines](#style-guidelines)
- [Reporting Bugs](#reporting-bugs)
- [Suggesting Enhancements](#suggesting-enhancements)

## Code of Conduct

This project and everyone participating in it is governed by our commitment to creating a welcoming and inclusive environment. By participating, you are expected to uphold this code.

### Our Standards

- Using welcoming and inclusive language
- Being respectful of differing viewpoints and experiences
- Gracefully accepting constructive criticism
- Focusing on what is best for the community
- Showing empathy towards other community members

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the existing issues to avoid duplicates.

**When submitting a bug report, include:**

- A clear and descriptive title
- Steps to reproduce the behavior
- Expected behavior
- Actual behavior
- Screenshots (if applicable)
- Your environment (OS, PHP version, PostgreSQL version)
- Any error messages or logs

**Example:**

```markdown
**Title:** Schema comparison fails with special characters in table names

**Steps to Reproduce:**
1. Create a table with special characters (e.g., `user#info`)
2. Run the comparison tool
3. Observe the error

**Expected:** Table should be displayed correctly
**Actual:** Error message appears

**Environment:**
- OS: Ubuntu 22.04
- PHP: 8.1
- PostgreSQL: 14.5
```

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion:

- Use a clear and descriptive title
- Provide a detailed description of the suggested enhancement
- Explain why this enhancement would be useful
- List any alternative solutions you've considered

### Your First Code Contribution

Unsure where to begin? Look for issues tagged with:

- `good first issue` - Simple issues for beginners
- `help wanted` - Issues where we need community help
- `documentation` - Improvements to documentation

## Development Setup

### Prerequisites

- PHP 7.4 or higher
- PostgreSQL 9.6 or higher
- Git

### Setup Steps

1. **Fork and clone the repository**
   ```bash
   git clone https://github.com/yourusername/database-compare.git
   cd database-compare
   ```

2. **Create configuration**
   ```bash
   cp config.example.php config.php
   # Edit config.php with your database credentials
   ```

3. **Create a branch**
   ```bash
   git checkout -b feature/my-new-feature
   ```

4. **Start development server**
   ```bash
   php -S localhost:8000
   ```

5. **Make your changes**

6. **Test your changes**
   - Manually test all affected features
   - Ensure no PHP errors in logs
   - Test in different browsers (Chrome, Firefox, Safari)
   - Test responsive design on mobile

## Pull Request Process

1. **Update documentation** - If you've added features, update README.md
2. **Follow code style** - See Style Guidelines below
3. **Test thoroughly** - Make sure everything works
4. **Update CHANGELOG.md** - Add your changes under "Unreleased"
5. **Create Pull Request** with a clear description

### Pull Request Template

```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
- [ ] Tested locally
- [ ] Tested in multiple browsers
- [ ] Tested responsive design

## Checklist
- [ ] Code follows style guidelines
- [ ] Updated documentation
- [ ] Added/updated CHANGELOG.md
- [ ] No new warnings or errors
```

## Style Guidelines

### PHP Code Style

- Follow PSR-12 coding standard
- Use meaningful variable names
- Add comments for complex logic
- Keep functions small and focused

**Example:**

```php
<?php
// Good
function getUserById($userId) {
    // Fetch user from database
    $query = "SELECT * FROM users WHERE id = $1";
    return pg_query_params($query, [$userId]);
}

// Bad
function get($id) {
    return pg_query("SELECT * FROM users WHERE id = " . $id);
}
```

### JavaScript Code Style

- Use ES6+ features
- Use const/let instead of var
- Use meaningful variable names
- Add JSDoc comments for functions

**Example:**

```javascript
// Good
/**
 * Toggle schema visibility
 * @param {HTMLElement} schemaDiv - Schema DOM element
 * @param {string} schemaName - Name of the schema
 * @param {string} side - 'local' or 'remote'
 */
function toggleSchema(schemaDiv, schemaName, side) {
    // Implementation
}

// Bad
function t(d, n, s) {
    // What does this do?
}
```

### CSS Code Style

- Use meaningful class names
- Group related properties
- Add comments for sections
- Keep specificity low

**Example:**

```css
/* Good */
.table-card {
    margin-bottom: 8px;
    border-radius: 3px;
    border: 1px solid #e5e5e5;
}

/* Bad */
div.table > div { margin: 8px; border: 1px solid #e5e5e5; border-radius: 3px; }
```

### Commit Messages

- Use present tense ("Add feature" not "Added feature")
- Use imperative mood ("Move cursor to..." not "Moves cursor to...")
- Limit first line to 72 characters
- Reference issues and pull requests

**Example:**

```
Add MySQL database support

- Implement MySQL connection class
- Add MySQL-specific column type detection
- Update documentation for MySQL setup

Fixes #123
```

## Development Workflow

### Branch Naming

- `feature/` - New features (e.g., `feature/mysql-support`)
- `fix/` - Bug fixes (e.g., `fix/schema-detection`)
- `docs/` - Documentation (e.g., `docs/update-readme`)
- `refactor/` - Code refactoring (e.g., `refactor/database-class`)

### Testing Checklist

Before submitting a PR, test:

- [ ] Schema comparison works correctly
- [ ] Synchronized navigation between local/remote
- [ ] Column type detection is accurate
- [ ] Responsive design on mobile
- [ ] No JavaScript console errors
- [ ] No PHP errors or warnings
- [ ] Cross-browser compatibility (Chrome, Firefox, Safari)

## Questions?

Feel free to:

- Open an issue with the `question` label
- Start a discussion in GitHub Discussions
- Contact maintainers directly

## Recognition

Contributors will be recognized in:

- README.md contributors section
- Release notes
- GitHub contributors page

Thank you for contributing! 🎉
