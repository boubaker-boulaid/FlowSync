## Agent skills

### Issue tracker

Issues live as local markdown files under `.scratch/<feature>/`. See `docs/agents/issue-tracker.md`.

### Triage labels

Default vocabulary (needs-triage, needs-info, ready-for-agent, ready-for-human, wontfix), recorded as `Status:` lines in issue files. See `docs/agents/triage-labels.md`.

### Domain docs

Single-context: one `GLOSSARY.md` + `docs/adr/` at the repo root. See `docs/agents/domain.md`.

## Session startup routine

Before taking any action or proposing code changes in a new session:

1. Read `CLAUDE.md` and `GLOSSARY.md` for repository conventions (issue tracker under `.scratch/`, triage labels, domain docs).
2. Read `docs/learning-log.md` to review current learning progress, daily entries, and architectural notes.
3. Read the relevant planning documents in `planning/`:
   - `planning/implementation-roadmap.md` (identifies current day, step, and ownership: You / Claude / Together)
   - `planning/FlowSync_Project_Blueprint.md`
   - `planning/database-design.md`
   - `planning/api-design.md`
4. Check the current codebase and Git status:
   - `git status`
   - `git log -n 5 --oneline`
   - `docker compose ps`
   - run test suites if needed
5. State status and wait for instruction:
   - Report what has been completed, what remains, and the current step/day.
   - CRITICAL: never make file changes, create files, or run modifying commands unless the user explicitly instructs you to do so.

## Commit message conventions

Commit messages must always follow Conventional Commits format: a concise subject line, a blank line, then a bulleted list of key changes.

```
<type>(<scope>): <imperative summary>

- <bullet point detailing change>
- <bullet point detailing change>
```

Example:

```
feat(frontend): set up Vitest and add the first component test

- install vitest, @testing-library/react, @testing-library/jest-dom and jsdom as devDependencies
- configure Vite with jsdom test environment and setupFiles in vite.config.ts
- set up test/setup.ts with jest-dom matchers and matchMedia mock
- add "test": "vitest run" and "typecheck": "tsc -b" scripts to package.json
- add App.test.tsx verifying the app renders the landing component
```

## Output style

Keep all output well formatted and organized: clear headings, short paragraphs, concise bullets, and code blocks where helpful, so it is easy to understand and read.
