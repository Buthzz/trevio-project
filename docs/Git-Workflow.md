# 🔀 Git Workflow & Repository Setup

## 📦 Initial Repository Setup

### 1. Create Repository (Hendrik - Project Manager)

```bash
# Di GitHub, create new repository: trevio
# Jangan centang "Initialize with README" (kita buat manual)

# Di local:
mkdir trevio
cd trevio
git init
git branch -M main

# Create initial structure
mkdir -p app/{controllers,models,views} config core public database docs
touch README.md .gitignore

# Add remote
git remote add origin https://github.com/your-team/trevio.git
```

### 2. .gitignore Template

```
# .gitignore untuk PHP Project
/vendor/
/node_modules/
.env
.DS_Store
Thumbs.db

# Config files dengan credentials
config/database.php
config/xendit.php

# IDE files
.vscode/
.idea/
*.sublime-*

# Logs
*.log
/logs/

# Cache
/cache/
/tmp/

# Upload files (optional - tergantung hosting)
/public/uploads/*
!/public/uploads/.gitkeep
```

### 3. First Commit (Hendrik)

```bash
git add .
git commit -m "chore: initial project structure"
git push -u origin main
```

---

## 👥 Team Member Setup

### Setiap Anggota Tim:

```bash
# Clone repository
git clone https://github.com/your-team/trevio.git
cd trevio

# Set git identity (PENTING - untuk track kontribusi)
git config user.name "Nama Lengkap"
git config user.email "email@example.com"

# Verify
git config --list
```

---

## 🌿 Branching Strategy

### Main Branches:
- **`main`** - Production-ready code (protected)
- **`develop`** - Integration branch untuk development

### Feature Branches:
- Setiap fitur dibuat di branch terpisah
- Naming: `feature/nama-fitur`
- Merge ke `develop` setelah review

### Example Branch Names:
```
feature/auth-system          (Fajar)
feature/hotel-booking        (Hendrik)
feature/flight-booking       (Fajar)
feature/payment-xendit       (Hendrik)
feature/admin-dashboard      (Syadat)
feature/ui-landing-page      (Zek + Reno)
feature/ui-search-results    (Reno)
feature/database-setup       (Syadat)
```

---

## 🔄 Git Workflow

### Untuk Setiap Fitur:

```bash
# 1. Update local main
git checkout main
git pull origin main

# 2. Create feature branch
git checkout -b feature/hotel-booking

# 3. Develop & commit regularly
# Edit files...
git add .
git commit -m "feat: add hotel search functionality"

# More changes...
git add .
git commit -m "feat: add hotel detail page"

# 4. Push ke remote
git push origin feature/hotel-booking

# 5. Create Pull Request di GitHub
# Assign reviewer (biasanya Project Manager)

# 6. Setelah di-approve & merge
git checkout main
git pull origin main
git branch -d feature/hotel-booking  # Delete local branch
```

---

## 📝 Commit Message Convention

### Format:
```
<type>: <subject>

[optional body]
```

### Types:
- **feat:** New feature
- **fix:** Bug fix
- **docs:** Documentation only
- **style:** Formatting, CSS changes
- **refactor:** Code restructuring
- **test:** Adding tests
- **chore:** Maintenance (build, config)

### Examples:
```bash
feat: add hotel search with filters
fix: resolve payment callback error
docs: update API documentation
style: improve mobile responsiveness
refactor: optimize database queries
test: add booking validation tests
chore: update dependencies
```

### ❌ Bad Commits:
```bash
update           # Terlalu vague
fix bug          # Tidak spesifik
asdasd           # Tidak informatif
final version    # Meaningless
```

### ✅ Good Commits:
```bash
feat: implement user authentication with session
fix: resolve room availability race condition
docs: add ERD diagram to documentation
style: improve checkout page mobile layout
```

---

## 🚦 Pull Request Process

### 1. Before Creating PR:

```bash
# Update branch dengan main terbaru
git checkout feature/your-feature
git fetch origin
git rebase origin/main

# Resolve conflicts if any
# Test your changes
```

### 2. Create Pull Request:

**Title Format:** `[Feature] Short description`

**Example:**
```
[Feature] Hotel Search & Filter System
[Fix] Payment callback handling
[Docs] Add API endpoints documentation
```

**Description Template:**
```markdown
## 📋 Description
Brief description of changes

## 🎯 Changes Made
- Change 1
- Change 2
- Change 3

## 🧪 Testing
- [ ] Manual testing completed
- [ ] Works on mobile
- [ ] No console errors

## 📸 Screenshots (if UI changes)
[Add screenshots]

## 🔗 Related Issue
Closes #issue-number (if applicable)
```

### 3. Review Process:

- **Reviewer:** Hendrik (Project Manager)
- **Minimum 1 approval** required
- Code review fokus: functionality, code quality, consistency

### 4. Merge:

- **Strategy:** Squash and merge (keep history clean)
- Delete branch after merge

---

## 📅 Daily Workflow

### Morning (Before Development):

```bash
# Update local repository
git checkout main
git pull origin main

# Create/switch to your feature branch
git checkout -b feature/your-feature
# or
git checkout feature/your-feature
git rebase origin/main
```

### During Development:

```bash
# Commit frequently (every logical change)
git add .
git commit -m "feat: implement hotel search API"

# Push regularly (backup & visibility)
git push origin feature/your-feature
```

### Before Leaving:

```bash
# Ensure all changes are pushed
git status
git push origin feature/your-feature
```

---

## 🔍 Useful Git Commands

### Check Status:
```bash
git status                    # Check modified files
git log --oneline            # View commit history
git log --graph --oneline    # Visual commit tree
```

### Undo Changes:
```bash
git checkout -- file.php     # Discard changes in file
git reset HEAD file.php      # Unstage file
git reset --soft HEAD~1      # Undo last commit (keep changes)
git reset --hard HEAD~1      # Undo last commit (discard changes)
```

### Branch Management:
```bash
git branch                   # List local branches
git branch -a                # List all branches (local + remote)
git branch -d feature-name   # Delete local branch
git push origin --delete feature-name  # Delete remote branch
```

### Sync with Remote:
```bash
git fetch origin            # Download remote changes
git pull origin main        # Fetch + merge
git push origin branch      # Push changes
```

### Resolve Conflicts:
```bash
# After git pull or rebase shows conflicts
# Edit conflicted files manually
git add resolved-file.php
git rebase --continue
# or
git merge --continue
```

---

## 🎯 Git Best Practices

### ✅ DO:
- Commit frequently dengan messages yang jelas
- Pull before push
- Review code sebelum merge
- Delete branch setelah merge
- Test sebelum commit
- Write descriptive commit messages

### ❌ DON'T:
- Commit directly ke main
- Commit credentials atau sensitive data
- Force push ke shared branches
- Commit large binary files
- Make generic commit messages
- Mix multiple features in one commit

---

## 🚨 Emergency Commands

### "Saya Salah Branch!":
```bash
# Belum commit
git stash                    # Simpan perubahan
git checkout correct-branch
git stash pop               # Apply perubahan

# Sudah commit di wrong branch
git log                      # Copy commit hash
git checkout correct-branch
git cherry-pick <commit-hash>
```

### "Conflict Terlalu Banyak!":
```bash
git rebase --abort          # Cancel rebase
# atau
git merge --abort           # Cancel merge
```

### "Butuh Revert Commit Public":
```bash
git revert <commit-hash>    # Create new commit that undoes changes
```

---

## 📊 Tracking Contributions

### View Individual Contributions:
```bash
# Total commits per person
git shortlog -sn

# Detailed stats
git log --author="Hendrik" --oneline --shortstat

# Lines changed
git log --author="Fajar" --pretty=tformat: --numstat | \
  awk '{ add += $1; subs += $2; loc += $1 - $2 } END \
  { printf "added lines: %s, removed lines: %s, total lines: %s\n", add, subs, loc }'
```

### GitHub Insights:
- Repository → Insights → Contributors
- Akan otomatis track semua contributions

---

## 🎓 Learning Resources

- [Git Documentation](https://git-scm.com/doc)
- [GitHub Guides](https://guides.github.com/)
- [Oh Shit, Git!?!](https://ohshitgit.com/) - Common mistakes solutions

---

**Important:** Semua anggota tim HARUS commit dengan nama dan email yang benar untuk tracking kontribusi!