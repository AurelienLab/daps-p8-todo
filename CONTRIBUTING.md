# Contributing to Todo & Co

Thank you for considering contributing to **Todo & Co**! Your contributions help improve the project, and we appreciate your efforts.  
Please follow the guidelines below to ensure a smooth collaboration.

## 🚀 How to Contribute
1. **Clone the repository**:
   ```bash
   git clone git@github.com:AurelienLab/daps-p8-todo.git
   cd daps-p8-todo
   ```
2. **Create a new branch** for your feature or fix:
   ```bash
   git checkout -b feature/your-feature-name
   ```
   You can use [git flow](https://git-flow.readthedocs.io/fr/latest/presentation.html) to manage your branches.
3. **Make your changes**, following our [Coding Standards](#coding-standards).
4. **Test your changes** locally (see [Testing Guidelines](#testing-guidelines)).
5. **Commit and push your changes**:
   ```bash
   git add .
   git commit -m "feat: describe your change"
   git push origin feature/your-feature-name
   ```
6. **Submit a Pull Request (PR)**: Open a PR on GitHub with a clear description of the changes.

---

## 🛠 Setting Up the Project

Follow instruction in the [README.md](README.md) to install and set up the project on your local machine.

---

## 🎯 Coding Standards
Follow **PSR-12** for PHP coding style.

---

## 🧪 Testing Guidelines
- Run tests before submitting changes:
  ```bash
  vendor/bin/phpunit
  ```
- Ensure tests pass without errors or failures.
- Write unit and functional tests for new features.
- Maintain a coverage of 80% or higher.

---

## 📝 Commit Message Guidelines
Use clear and structured commit messages following this format:
```
feat: add new feature X
fix: resolve issue Y
refactor: improve existing code Z
docs: update documentation
```

---

## 🔀 Pull Request Process
1. Ensure your code follows the [Coding Standards](#coding-standards).
2. Write tests for any new feature or fix.
3. Push your changes and open a PR.
4. A reviewer will check and approve your PR before merging.

---

## 🐛 Issue Reporting
If you encounter a bug or have a feature request:
1. Check if the issue already exists.
2. Open a **new issue** with:
    - A clear description of the problem.
    - Steps to reproduce.
    - Expected behavior vs. actual behavior.



Happy coding! 🚀
