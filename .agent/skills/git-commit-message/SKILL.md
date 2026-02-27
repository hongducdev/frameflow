---
name: git-commit-message
description: Generate conventional commit messages with optional emoji prefixes. Use when committing code changes or preparing git commit messages.
---

# Git Commit Message Generator

Generate clear, consistent commit messages following the Conventional Commits specification with optional emoji support.

## Commit Message Structure

```
<emoji> <type>(<scope>): <description>

[optional body]

[optional footer(s)]
```

## Commit Types

| Type       | Description                             | Emoji                   | Semantic Version |
| :--------- | :-------------------------------------- | :---------------------- | :--------------- |
| `feat`     | New feature                             | ✨ `:sparkles:`         | MINOR            |
| `fix`      | Bug fix                                 | 🐛 `:bug:`              | PATCH            |
| `docs`     | Documentation only                      | 📚 `:books:`            | -                |
| `style`    | Code style (formatting, whitespace)     | 💄 `:lipstick:`         | -                |
| `refactor` | Code change that neither fixes nor adds | 🔨 `:hammer:`           | -                |
| `perf`     | Performance improvement                 | 🐎 `:racehorse:`        | -                |
| `test`     | Adding or updating tests                | ✅ `:white_check_mark:` | -                |
| `build`    | Build system or dependencies            | 📦 `:package:`          | -                |
| `ci`       | CI configuration                        | 💚 `:green_heart:`      | -                |
| `chore`    | Other changes (no src/test)             | 🔧 `:wrench:`           | -                |
| `revert`   | Revert previous commit                  | ⏪ `:rewind:`           | -                |

## Extended Emoji Reference

| Commit Type              | Emoji                            |
| :----------------------- | :------------------------------- |
| Initial commit           | 🎉 `:tada:`                      |
| Version tag              | 🔖 `:bookmark:`                  |
| Critical hotfix          | 🚑 `:ambulance:`                 |
| Security fix             | 🔒 `:lock:`                      |
| Deploying                | 🚀 `:rocket:`                    |
| Removing code/files      | 🔥 `:fire:`                      |
| Work in progress         | 🚧 `:construction:`              |
| Adding dependency        | ➕ `:heavy_plus_sign:`           |
| Removing dependency      | ➖ `:heavy_minus_sign:`          |
| Upgrading dependencies   | ⬆️ `:arrow_up:`                  |
| Downgrading dependencies | ⬇️ `:arrow_down:`                |
| Configuration            | 🔧 `:wrench:`                    |
| Improve structure/format | 🎨 `:art:`                       |
| Accessibility            | ♿ `:wheelchair:`                |
| Breaking changes         | 💥 `:boom:`                      |
| Code review changes      | 👌 `:ok_hand:`                   |
| Docker                   | 🐳 `:whale:`                     |
| Merging branches         | 🔀 `:twisted_rightwards_arrows:` |
| Move/rename files        | 🚚 `:truck:`                     |
| Lint fixes               | 👕 `:shirt:`                     |
| Translation              | 👽 `:alien:`                     |
| Text changes             | ✏️ `:pencil:`                    |
| Analytics/tracking       | 📈 `:chart_with_upwards_trend:`  |
| Fixing on MacOS          | 🍎 `:apple:`                     |
| Fixing on Linux          | 🐧 `:penguin:`                   |
| Fixing on Windows        | 🏁 `:checkered_flag:`            |

## Rules

1. **Type is REQUIRED** - Must be one of the defined types
2. **Scope is OPTIONAL** - Noun describing codebase section in parentheses
3. **Description is REQUIRED** - Short summary in imperative mood
4. **Breaking changes** - Use `!` after type/scope OR `BREAKING CHANGE:` footer
5. **Body** - Must have blank line after description
6. **Footer** - Must have blank line after body

## Examples

### Simple commit

```
✨ feat: add user authentication
```

### Commit with scope

```
🐛 fix(parser): handle empty input gracefully
```

### Breaking change with `!`

```
💥 feat(api)!: change response format to JSON

BREAKING CHANGE: API responses are now JSON instead of XML.
```

### Commit with body and footer

```
✨ feat(auth): implement JWT token refresh

Add automatic token refresh when access token expires.
Refresh tokens are stored securely in HTTP-only cookies.

Closes #123
Co-authored-by: Name <email@example.com>
```

### Hotfix

```
🚑 fix: resolve critical login failure

Users were unable to login due to session timeout misconfiguration.
```

## Generating Commit Messages

When asked to generate a commit message:

1. **Analyze the changes** - Review what files changed and how
2. **Identify the type** - Determine if it's a feat, fix, refactor, etc.
3. **Determine scope** - Identify the affected component/module (optional)
4. **Write description** - Clear, concise summary in imperative mood
5. **Add body if needed** - Explain what and why (not how)
6. **Include footers** - Reference issues, breaking changes, co-authors

### Guidelines for Description

- Use imperative mood: "add" not "added" or "adds"
- Don't capitalize first letter
- No period at the end
- Keep under 72 characters
- Be specific but concise

### When to Include Body

- Changes require explanation of motivation
- Complex changes need context
- Breaking changes need migration guidance
- Multiple related changes in one commit