# Project Instructions & Guidelines

## 1. Git & Deployment Rules
- **NEVER push to GitHub automatically.**
- Always wait for an explicit user prompt/instruction before running `git push`.
- All changes should remain local (staged or unstaged) until the user explicitly requests a commit and push.

## 2. Admin & Local Environment Isolation
- The admin dashboard, local automation scripts, and administrative credentials must remain strictly on the local machine and never be deployed or committed:
  - `admin/`
  - `scripts/`
  - `.env.colormagic.admin`
  - `data/instagram_account.json`
  - `data/pinterest_boards.json`
  - `assets/generated/`
- Ensure `.gitignore` continues to protect these files from being tracked.

## 3. Database-Free Architecture
- Do NOT introduce MySQL, SQLite, or external database connection logic into the public frontend or standard admin workflows.
- Use file-based storage (`data/*.json`) and client-side `localStorage`.

## 4. Credential & Security Practices
- Never hardcode API keys, access tokens, or secrets directly in code files.
- Keep server-safe configuration in `.env.colormagic` and local admin credentials in `.env.colormagic.admin`.

