/*
 * Rebuilds template/React/import-modal.js — the generic CSV/Excel import modal that the
 * Tool Creator injects into every generated React brick (placeholder #TCREACTIMPORTMODAL).
 *
 * Source of truth: the DataImport modal shared by the native tools,
 *   module/<Tool>/ui-react/src/shared/ImportModal.tsx  (+ useIsNarrow.ts)
 * Every copy is identical; any of them will do. Re-run this whenever that file changes:
 *
 *   node vendor/melisplatform/melis-tool-creator/template/React/import-modal.build.mjs \
 *        module/Country/ui-react
 *
 * The argument is a brick folder holding src/shared/ImportModal.tsx and a node_modules with
 * vite (>= 8, for transformWithOxc). Generated bricks are no-build ES2019 served as-is and
 * concatenated into ONE bundle, so the output must be a plain expression with no import or
 * export, using the host's React (window.MelisReact) through the classic JSX runtime.
 */
import { readFileSync, writeFileSync } from 'node:fs'
import { createRequire } from 'node:module'
import { resolve, dirname } from 'node:path'
import { fileURLToPath, pathToFileURL } from 'node:url'

const brickDir = resolve(process.argv[2] || 'module/Country/ui-react')
const shared = resolve(brickDir, 'src/shared')
const require = createRequire(resolve(brickDir, 'package.json'))
const { transformWithOxc } = await import(pathToFileURL(require.resolve('vite')).href)

const strip = (src) => src
  .replace(/^import[\s\S]*?from\s+'[^']+'\s*;?\s*$/gm, '') // module imports (react / ./useIsNarrow)
  .replace(/^export\s+(?=(function|const|interface|type)\b)/gm, '')

const source = strip(readFileSync(resolve(shared, 'useIsNarrow.ts'), 'utf8'))
  + '\n' + strip(readFileSync(resolve(shared, 'ImportModal.tsx'), 'utf8'))

const out = await transformWithOxc(source, 'ImportModal.tsx', {
  lang: 'tsx',
  target: 'es2019',
  jsx: { runtime: 'classic', pragma: 'React.createElement', pragmaFrag: 'React.Fragment' },
})

const chunk = `/* ── Import modal ───────────────────────────────────────────────────────────
   * Generated from DataImport's shared ImportModal.tsx by template/React/import-modal.build.mjs.
   * Do not edit here: change the .tsx and rebuild. Posts to /melis/react-api/data-import/<tool>/…
   * ─────────────────────────────────────────────────────────────────────────── */
  var ImportModal = (function () {
    var useState = React.useState, useEffect = React.useEffect, useRef = React.useRef, useMemo = React.useMemo;
${out.code.replace(/^/gm, '    ').replace(/[ ]+$/gm, '')}
    return ImportModal;
  })();
`

const target = resolve(dirname(fileURLToPath(import.meta.url)), 'import-modal.js')
writeFileSync(target, chunk)
console.log('written', target, chunk.length, 'bytes')
