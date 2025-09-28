# 🚀 AI-Assisted Development Guidelines

## 📋 Overview

This document provides guidelines for effective AI-assisted development based on real-world experience working on the Laravel Cycle ORM Adapter project. These practices help maintain code quality, consistency, and productivity when working with AI coding assistants.

## 🤖 Working with AI Assistants

### 🎯 Effective Prompting

#### ✅ **Do This:**

- **Be Specific**: Provide exact file paths, error messages, and context
- **Show Examples**: Include code snippets, logs, or configuration files
- **State Intent**: Clearly explain what you want to achieve
- **Provide Context**: Share relevant background information about the project

#### ❌ **Avoid This:**

- Vague requests like "fix this" or "make it better"
- Assuming the AI knows your project structure
- Omitting error messages or logs
- Making multiple unrelated requests in one prompt

### 📁 Context Management

```bash
# Always provide file paths when discussing issues
# ✅ Good: "In file `src/Bridge/Laravel/Providers/CycleServiceProvider.php` line 48..."
# ❌ Bad: "In the service provider..."

# Share relevant configuration
# ✅ Include: psalm.xml, composer.json, workflow files
# ❌ Don't assume: AI knows your specific setup
```

### 🔍 Debugging Approach

1. **Start with Error Analysis** 🐛
   - Share complete error messages
   - Include stack traces
   - Provide relevant log outputs

2. **Incremental Problem Solving** 🔧
   - Break complex issues into smaller parts
   - Test one fix at a time
   - Verify each solution before moving to the next

3. **Root Cause Investigation** 🔬
   - Don't just fix symptoms
   - Understand why issues occur
   - Research best practices and proven solutions

## 💻 Code Quality Standards

### 🧪 Testing Philosophy

```bash
# Run tests before and after changes
make test                    # Unit tests
make test-pgsql             # PostgreSQL tests
make test-mysql             # MySQL tests
make test-sqlserver         # SQL Server tests

# Use specific test commands when debugging
composer test               # Direct composer command
vendor/bin/pest            # Direct test runner
```

### 📊 Static Analysis

```bash
# Always run static analysis tools
make lint-stan              # PHPStan analysis
make lint-psalm             # Psalm analysis
make lint-php               # PHP CS Fixer

# Handle tool-specific issues
# Example: Suppress package-related false positives in psalm.xml
```

### 🔧 Configuration Management

#### **Tool Configuration Best Practices:**

1. **Psalm Configuration** (`psalm.xml`):
   - Suppress unused code warnings for package development
   - Configure appropriate error levels
   - Exclude problematic files (like `rector.php` that import vendor configs)

2. **GitHub Actions** (`.github/workflows/`):
   - Use proven patterns from successful projects
   - Handle environment-specific requirements (like ODBC drivers)
   - Implement proper health checks and retries

3. **Docker Services**:
   - Use appropriate SQL Server editions (`Express` vs `Developer`)
   - Configure memory limits and feature flags
   - Implement comprehensive health checks

## 📝 Documentation Practices

### 📖 Keep Documentation Current

```markdown
# ✅ Good compatibility matrix format
| Laravel              | Cycle ORM | Adapter  |
|----------------------|-----------|----------|
| `^10.28`             | `2.x`     | `<4.9.0` |
| `^10.28, 11.x`       | `2.x`     | `≥4.9.0` |
| `^10.28, 11.x, 12.x` | `2.x`     | `≥5.0.0` |
```

### 🎨 Documentation Style

- Use emojis for visual organization 🎯
- Provide clear examples and code snippets
- Update README.md and docs simultaneously
- Include rationale for decisions in commit messages

## 🔄 Git Workflow

### 📦 Conventional Commits

We follow [Conventional Commits](https://www.conventionalcommits.org/) specification:

```bash
# Format: <type>(<scope>): <description>
feat: add Laravel 12 support in v5.0.0
fix: resolve SQL Server container initialization issues
docs: update compatibility matrix for Laravel 12 support
ci: improve ODBC driver installation for Ubuntu 24.04
test: add comprehensive SQL Server connection verification
```

#### **Commit Types:**

- `feat` 🆕 — New features
- `fix` 🐛 — Bug fixes
- `docs` 📖 — Documentation changes
- `ci` 🔧 — CI/CD improvements
- `test` 🧪 — Testing improvements
- `refactor` ♻️ — Code refactoring
- `perf` ⚡ — Performance improvements
- `chore` 🧹 — Maintenance tasks

### 📋 Commit Best Practices

#### ✅ **Good Commit Messages:**

```bash
fix: exclude rector.php from psalm analysis to prevent crashes

The rector.php file imports external vendor configurations which causes
psalm to crash with exit code 255, especially on PHP 8.4. By removing
rector.php from psalm's projectFiles configuration, psalm can complete
successfully.
```

#### ❌ **Poor Commit Messages:**

```bash
fix stuff
update config
wip
```

## 🛠️ Tool-Specific Guidelines

### 🔍 Psalm Configuration

```xml
<!-- Suppress package-related unused code warnings -->
<issueHandlers>
    <UnusedClass>
        <errorLevel type="suppress">
            <directory name="src/"/>
        </errorLevel>
    </UnusedClass>
    <PossiblyUnusedMethod>
        <errorLevel type="suppress">
            <directory name="src/"/>
        </errorLevel>
    </PossiblyUnusedMethod>
</issueHandlers>
```

### 🐳 Docker & CI/CD

#### **SQL Server in GitHub Actions:**

```yaml
# Use dynamic Ubuntu version detection
UBUNTU_VERSION=$(lsb_release -rs 2>/dev/null || echo "24.04")

# Install required ODBC drivers
sudo ACCEPT_EULA=Y apt-get install -y msodbcsql18 mssql-tools18 unixodbc-dev

# Use appropriate SQL Server configuration
MSSQL_PID: 'Express'              # More stable than Developer in CI
MSSQL_MEMORY_LIMIT_MB: '2048'     # Explicit memory limit
MSSQL_ENABLE_HADR: '0'            # Disable unnecessary features
```

### 📊 Testing Strategy

```bash
# Layer testing approach
make test           # Fast unit tests
make test-pgsql     # Integration with PostgreSQL
make test-mysql     # Integration with MySQL
make test-sqlserver # Integration with SQL Server

# Database-specific testing
DB_CONNECTION=sqlserver make test    # Environment-specific
composer test:sqlserver              # Direct composer command
```

## 🚨 Common Issues & Solutions

### 🐛 **Psalm Exit Code 255**

**Problem**: Psalm crashes when analyzing rector.php
**Solution**: Exclude rector.php from psalm.xml projectFiles

### 🔌 **SQL Server Connection Issues**

**Problem**: PDO requires Microsoft ODBC Driver
**Solution**: Install msodbcsql18, mssql-tools18, unixodbc-dev

### 📦 **Package Unused Code Warnings**

**Problem**: Psalm reports false positives for unused classes/methods
**Solution**: Suppress warnings for src/ directory in package development

### 🐧 **Ubuntu Version Mismatch**

**Problem**: Wrong package repository for ubuntu-latest (24.04)
**Solution**: Use `$(lsb_release -rs)` for dynamic detection

## 🎯 Best Practices Summary

### 🏗️ **Development Workflow**

1. **Plan First** 📋 — Break complex tasks into smaller steps
2. **Test Early** 🧪 — Run tests before making changes
3. **Commit Often** 💾 — Small, focused commits with clear messages
4. **Document Changes** 📖 — Update docs alongside code changes
5. **Verify Quality** ✅ — Run all linting and static analysis tools

### 🤝 **AI Collaboration**

1. **Provide Context** 🎯 — Share relevant files and error messages
2. **Be Specific** 📝 — Clear, actionable requests
3. **Iterate Incrementally** 🔄 — One problem at a time
4. **Verify Solutions** ✅ — Test AI suggestions before applying
5. **Document Learnings** 📚 — Update guidelines based on experience

### 🔧 **Tool Configuration**

1. **Research Proven Patterns** 🔍 — Study successful projects
2. **Handle Edge Cases** ⚠️ — Environment-specific requirements
3. **Maintain Compatibility** 🔄 — Support multiple versions when possible
4. **Document Decisions** 📝 — Explain complex configurations
5. **Regular Updates** 🔄 — Keep tools and dependencies current

---

## 📞 Need Help?

- 📖 Check [Contributing Guidelines](../docs/pages/contributing.mdx)
- 🐛 [Report Issues](https://github.com/wayofdev/laravel-cycle-orm-adapter/issues)
- 💬 [Discord Community](https://discord.gg/CE3TcCC5vr)
- 📧 [Email Support](mailto:the@wayof.dev)

---

*Generated from real development experience on the Laravel Cycle ORM Adapter project* ✨
