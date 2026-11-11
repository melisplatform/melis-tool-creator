/* ── Import modal ───────────────────────────────────────────────────────────
   * Generated from DataImport's shared ImportModal.tsx by template/React/import-modal.build.mjs.
   * Do not edit here: change the .tsx and rebuild. Posts to /melis/react-api/data-import/<tool>/…
   * ─────────────────────────────────────────────────────────────────────────── */
  var ImportModal = (function () {
    var useState = React.useState, useEffect = React.useEffect, useRef = React.useRef, useMemo = React.useMemo;
    /**
    * True when the viewport is narrower than `breakpoint`. Drives every responsive decision on
    * this brick as a JS ternary (inline styles) instead of a CSS media query — see the
    * `melis-react-mobile-responsive` skill for why.
    */
    function useIsNarrow(breakpoint = 640) {
    	const [narrow, setNarrow] = useState(() => window.innerWidth < breakpoint);
    	useEffect(() => {
    		const onResize = () => setNarrow(window.innerWidth < breakpoint);
    		window.addEventListener("resize", onResize);
    		return () => window.removeEventListener("resize", onResize);
    	}, [breakpoint]);
    	return narrow;
    }
    /**
    * Largeur courante du viewport. `useIsNarrow` ne re-rend que quand le booléen BASCULE — pour une
    * décision qui dépend des pixels disponibles (ex. un libellé tient-il dans un demi-bouton ?), il
    * faut la largeur elle-même, réactive à chaque redimensionnement.
    */
    function useViewportWidth() {
    	const [w, setW] = useState(() => window.innerWidth);
    	useEffect(() => {
    		const onResize = () => setW(window.innerWidth);
    		window.addEventListener("resize", onResize);
    		return () => window.removeEventListener("resize", onResize);
    	}, []);
    	return w;
    }
    /* ─────────────────────────────────────────────────────────────────────────────
    * Generic CSV / Excel import, identical copy in each module's shared/ folder
    * (bricks cannot import host or cross-module code).
    *
    * The file is parsed HERE, in the browser, with the SheetJS instance the host exposes
    * (`window.MelisXLSX`, posed in MelisCore's main.tsx — the same one ExportModal uses); only rows
    * of raw strings are posted to /melis/react-api/data-import/<tool>/…, so no upload limit applies
    * and the server needs no spreadsheet library.
    *
    * Four steps: file → mapping → unknown codes → run.
    *   - the mapping comes from the tool's CONFIG (DataImport/config/data-import.tools.php); the
    *     admin can correct it and save it back, which rewrites the generated config server-side,
    *   - a reference value the server cannot resolve (a code absent from the reference data) is
    *     remapped by hand and can be remembered for later imports,
    *   - a row whose identifying column matches an existing record is an UPDATE, otherwise an INSERT.
    *
    *   <ImportModal toolKey="establishment_tool" onClose={…} onDone={refreshList} />
    * ───────────────────────────────────────────────────────────────────────────── */
    const XHR_HEADER = { "X-Requested-With": "XMLHttpRequest" };
    const BASE = "/melis/react-api/data-import";
    /** Rows posted per request. The server caps a chunk at 500. */
    const CHUNK = 200;
    /** Above this many rows the import is announced as slow; above the hard cap the file is refused. */
    const ROWS_WARN = 2e4;
    const ROWS_MAX = 1e5;
    function getXLSX() {
    	var _MelisXLSX;
    	return (_MelisXLSX = window.MelisXLSX) !== null && _MelisXLSX !== void 0 ? _MelisXLSX : null;
    }
    function currentLang() {
    	return (document.documentElement.lang || "en").toLowerCase().startsWith("fr") ? "fr" : "en";
    }
    const DICT = {
    	fr: {
    		import: "Importer",
    		title: "Importer des données",
    		cancel: "Annuler",
    		close: "Fermer",
    		back: "Retour",
    		next: "Suivant",
    		step_file: "Fichier",
    		step_map: "Correspondance",
    		step_codes: "Valeurs inconnues",
    		step_run: "Import",
    		drop: "Glissez un fichier ici, ou cliquez pour le choisir",
    		formats: "CSV, Excel (.xlsx, .xls)",
    		sheet: "Feuille",
    		rows_found: "{n} lignes détectées",
    		xls_hint: "Convertir en .xlsx donne le résultat le plus fiable.",
    		too_many: "Fichier trop volumineux ({n} lignes). Découpez-le en plusieurs fichiers (maximum {max}).",
    		slow: "Import long ({n} lignes) : ne fermez pas cette fenêtre avant la fin.",
    		parse_error: "Fichier illisible",
    		empty_file: "Le fichier ne contient aucune ligne",
    		csv_only: "Excel indisponible ici : seuls les fichiers CSV peuvent être importés.",
    		panel_file: "Colonnes du fichier",
    		panel_fields: "Champs de l’outil",
    		drag_hint: "Glissez un champ (à gauche) sur la colonne du fichier qui le contient, ou choisissez-le dans la liste de la colonne. Une colonne sans champ n’est pas importée.",
    		all_mapped: "Tous les champs sont associés.",
    		match_badge: "identifiant",
    		ignore: "— ne pas importer —",
    		already_used: "déjà prise par {c}",
    		target_label: "Champ de l’outil",
    		left_note: "Ces champs ne seront pas importés tant qu’ils ne sont pas associés à une colonne.",
    		match_on: "Colonne d’identification",
    		match_hint: "Si la valeur existe déjà, la ligne met à jour l’enregistrement ; sinon elle le crée.",
    		missing_required: "Champ obligatoire non associé : {f}",
    		match_unmapped: "Colonne d’identification non associée : chaque ligne créera un nouvel enregistrement.",
    		save_mapping: "Enregistrer cette correspondance",
    		mapping_saved: "Correspondance enregistrée",
    		preview: "Aperçu des 5 premières lignes",
    		codes_intro: "Ces valeurs du fichier ne correspondent à aucun enregistrement. Choisissez à quoi elles correspondent.",
    		leave_empty: "Laisser vide",
    		skip_rows: "Ignorer ces lignes",
    		remember: "Mémoriser ce choix",
    		no_codes: "Toutes les valeurs ont été reconnues.",
    		start: "Lancer l’import",
    		running: "Import en cours…",
    		done: "Import terminé",
    		inserted: "Créés",
    		updated: "Mis à jour",
    		skipped: "Ignorés",
    		errors: "Erreurs",
    		report: "Télécharger le rapport (CSV)",
    		line: "Ligne",
    		field: "Champ",
    		message: "Message",
    		overwrite_note: "Les champs associés écrasent les valeurs existantes ; les autres ne sont pas modifiés."
    	},
    	en: {
    		import: "Import",
    		title: "Import data",
    		cancel: "Cancel",
    		close: "Close",
    		back: "Back",
    		next: "Next",
    		step_file: "File",
    		step_map: "Mapping",
    		step_codes: "Unknown values",
    		step_run: "Import",
    		drop: "Drop a file here, or click to choose one",
    		formats: "CSV, Excel (.xlsx, .xls)",
    		sheet: "Sheet",
    		rows_found: "{n} rows found",
    		xls_hint: "Converting to .xlsx gives the most reliable result.",
    		too_many: "File too large ({n} rows). Split it into several files (maximum {max}).",
    		slow: "Long import ({n} rows): do not close this window before it finishes.",
    		parse_error: "The file could not be read",
    		empty_file: "The file has no rows",
    		csv_only: "Excel is unavailable here: only CSV files can be imported.",
    		panel_file: "File columns",
    		panel_fields: "Tool fields",
    		drag_hint: "Drag a field (left) onto the file column that holds it, or pick it in the column’s list. A column without a field is not imported.",
    		all_mapped: "Every field is mapped.",
    		match_badge: "identifier",
    		ignore: "— do not import —",
    		already_used: "taken by {c}",
    		target_label: "Tool field",
    		left_note: "These fields will not be imported until they are mapped to a column.",
    		match_on: "Identifying column",
    		match_hint: "If the value already exists the row updates that record, otherwise it creates one.",
    		missing_required: "Required field not mapped: {f}",
    		match_unmapped: "Identifying column not mapped: every row will create a new record.",
    		save_mapping: "Save this mapping",
    		mapping_saved: "Mapping saved",
    		preview: "Preview of the first 5 rows",
    		codes_intro: "These file values match no record. Choose what they stand for.",
    		leave_empty: "Leave empty",
    		skip_rows: "Skip these rows",
    		remember: "Remember this choice",
    		no_codes: "Every value was recognised.",
    		start: "Start the import",
    		running: "Importing…",
    		done: "Import finished",
    		inserted: "Created",
    		updated: "Updated",
    		skipped: "Skipped",
    		errors: "Errors",
    		report: "Download the report (CSV)",
    		line: "Line",
    		field: "Field",
    		message: "Message",
    		overwrite_note: "Mapped fields overwrite the stored values; the other fields are left untouched."
    	}
    };
    function tr(key, vars) {
    	var _DICT$currentLang$key;
    	let s = (_DICT$currentLang$key = DICT[currentLang()][key]) !== null && _DICT$currentLang$key !== void 0 ? _DICT$currentLang$key : key;
    	if (vars) for (const [k, v] of Object.entries(vars)) s = s.replaceAll(`{${k}}`, String(v));
    	return s;
    }
    async function api(url, body) {
    	var _payload$error;
    	const res = await fetch(url, {
    		method: body === undefined ? "GET" : "POST",
    		headers: {
    			...XHR_HEADER,
    			...body === undefined ? {} : { "Content-Type": "application/json" }
    		},
    		credentials: "include",
    		body: body === undefined ? undefined : JSON.stringify(body)
    	});
    	let payload = null;
    	try {
    		payload = await res.json();
    	} catch {}
    	if (!res.ok || !(payload === null || payload === void 0 ? void 0 : payload.success)) throw new Error((_payload$error = payload === null || payload === void 0 ? void 0 : payload.error) !== null && _payload$error !== void 0 ? _payload$error : `HTTP ${res.status}`);
    	return payload.data;
    }
    const card = {
    	border: "1px solid var(--color-border)",
    	background: "var(--color-card)",
    	borderRadius: 12,
    	boxShadow: "0 1px 2px rgba(0,0,0,.04)"
    };
    const btnGhost = {
    	display: "inline-flex",
    	alignItems: "center",
    	gap: 6,
    	height: 34,
    	padding: "0 12px",
    	borderRadius: 8,
    	border: "1px solid var(--color-border)",
    	background: "var(--color-card)",
    	color: "var(--color-foreground)",
    	fontSize: 14,
    	cursor: "pointer"
    };
    const btnPrimary = {
    	display: "inline-flex",
    	alignItems: "center",
    	gap: 6,
    	height: 34,
    	padding: "0 14px",
    	borderRadius: 8,
    	border: 0,
    	background: "var(--color-primary)",
    	color: "var(--color-primary-foreground,#fff)",
    	fontSize: 14,
    	fontWeight: 500,
    	cursor: "pointer"
    };
    const inputCss = {
    	height: 32,
    	borderRadius: 8,
    	border: "1px solid var(--color-border)",
    	background: "var(--color-card)",
    	color: "var(--color-foreground)",
    	fontSize: 13,
    	padding: "0 8px"
    };
    const panelTitle = {
    	padding: "0 6px 4px",
    	fontSize: 10,
    	fontWeight: 600,
    	textTransform: "uppercase",
    	letterSpacing: ".06em",
    	color: "var(--color-muted-foreground)"
    };
    const muted = {
    	fontSize: 12,
    	color: "var(--color-muted-foreground)"
    };
    const panelCss = {
    	border: "1px solid var(--color-border)",
    	borderRadius: 10,
    	padding: 8,
    	maxHeight: 440,
    	overflowY: "auto"
    };
    const chipCss = {
    	display: "flex",
    	alignItems: "baseline",
    	gap: 8,
    	padding: "5px 8px",
    	borderRadius: 8,
    	border: "1px solid var(--color-border)",
    	background: "var(--color-card)",
    	cursor: "grab",
    	fontSize: 13,
    	minWidth: 0
    };
    const UploadIcon = () => /* @__PURE__ */ React.createElement("svg", {
    	style: {
    		width: 15,
    		height: 15,
    		flexShrink: 0
    	},
    	viewBox: "0 0 24 24",
    	fill: "none",
    	stroke: "currentColor",
    	strokeWidth: "2",
    	strokeLinecap: "round",
    	strokeLinejoin: "round"
    }, /* @__PURE__ */ React.createElement("path", { d: "M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 9l5-5 5 5M12 4v12" }));
    function ImportModal({ toolKey, onClose, onDone }) {
    	const narrow = useIsNarrow();
    	const [step, setStep] = useState("file");
    	const [error, setError] = useState(null);
    	const [notice, setNotice] = useState(null);
    	const [fields, setFields] = useState([]);
    	const [matchKeys, setMatchKeys] = useState([]);
    	const [parsed, setParsed] = useState(null);
    	const [buffer, setBuffer] = useState(null);
    	const [mapping, setMapping] = useState({});
    	const [dragKey, setDragKey] = useState(null);
    	const [matchKey, setMatchKey] = useState("");
    	const [unresolved, setUnresolved] = useState({});
    	const [overrides, setOverrides] = useState({});
    	const [remember, setRemember] = useState({});
    	const [progress, setProgress] = useState(0);
    	const [running, setRunning] = useState(false);
    	const [mapSaved, setMapSaved] = useState(false);
    	const [counts, setCounts] = useState({
    		inserted: 0,
    		updated: 0,
    		skipped: 0,
    		error: 0
    	});
    	const [failures, setFailures] = useState([]);
    	const byKey = useMemo(() => Object.fromEntries(fields.map((f) => [f.key, f])), [fields]);
    	const fieldOf = useMemo(() => {
    		const out = {};
    		for (const [index, key] of Object.entries(mapping)) if (key) out[key] = Number(index);
    		return out;
    	}, [mapping]);
    	useEffect(() => {
    		api(`${BASE}/${toolKey}/fields`).then((d) => {
    			var _d$matchKeys$0$key, _d$matchKeys$;
    			setFields(d.fields);
    			setMatchKeys(d.matchKeys);
    			setMatchKey((_d$matchKeys$0$key = (_d$matchKeys$ = d.matchKeys[0]) === null || _d$matchKeys$ === void 0 ? void 0 : _d$matchKeys$.key) !== null && _d$matchKeys$0$key !== void 0 ? _d$matchKeys$0$key : "");
    		}).catch((e) => setError(e instanceof Error ? e.message : String(e)));
    	}, [toolKey]);
    	// ── Step 1: the file ──────────────────────────────────────────────────────
    	function readSheet(buf, sheetName, filename) {
    		const xlsx = getXLSX();
    		const isCsv = /\.csv$/i.test(filename);
    		if (!xlsx && !isCsv) {
    			throw new Error(tr("csv_only"));
    		}
    		let wb;
    		if (xlsx && !isCsv) {
    			wb = xlsx.read(buf, {
    				type: "array",
    				cellDates: false,
    				raw: false
    			});
    		} else {
    			const text = decodeText(buf);
    			if (xlsx) wb = xlsx.read(text, {
    				type: "string",
    				FS: sniffDelimiter(text),
    				raw: false
    			});
    			else return parseCsvManually(text, filename);
    		}
    		const names = wb.SheetNames;
    		const name = sheetName && names.includes(sheetName) ? sheetName : names[0];
    		const aoa = xlsx.utils.sheet_to_json(wb.Sheets[name], {
    			header: 1,
    			raw: false,
    			defval: "",
    			blankrows: false
    		});
    		// Excel shows a long number as "3.12611E+13" and that DISPLAYED text is what raw:false
    		// yields — a truncated SIRET. The cell still holds the exact number, so read the sheet a
    		// second time raw and swap the digits back in wherever the display went scientific.
    		const aoaRaw = xlsx.utils.sheet_to_json(wb.Sheets[name], {
    			header: 1,
    			raw: true,
    			defval: "",
    			blankrows: false
    		});
    		const sci = /^-?\d+(\.\d+)?E[+-]?\d+$/i;
    		const dateish = /^\d{1,2}[\/\-.]\d{1,2}[\/\-.]\d{2,4}(\s|$)/;
    		for (let r = 0; r < aoa.length; r++) {
    			var _aoa$r;
    			const row = (_aoa$r = aoa[r]) !== null && _aoa$r !== void 0 ? _aoa$r : [];
    			for (let c = 0; c < row.length; c++) {
    				var _aoaRaw$r;
    				const shown = row[c];
    				const exact = (_aoaRaw$r = aoaRaw[r]) === null || _aoaRaw$r === void 0 ? void 0 : _aoaRaw$r[c];
    				if (typeof shown === "string" && typeof exact === "number" && Number.isFinite(exact) && sci.test(shown.trim())) {
    					row[c] = String(exact);
    				} else if (typeof shown === "string" && typeof exact === "number" && Number.isFinite(exact) && exact >= 1 && exact < 2958466 && dateish.test(shown.trim())) {
    					// Date cell: Excel's display text follows the cell's number format and may carry a
    					// 2-digit year ("1/3/27"); rebuild the value from the exact serial number instead.
    					row[c] = serialToIso(exact);
    				}
    			}
    		}
    		return toParsed(aoa, names, name, filename);
    	}
    	// The first row of the sheet IS the column names — the import has no other template.
    	function toParsed(aoa, names, sheet, filename) {
    		const rows = aoa.map((r) => (r !== null && r !== void 0 ? r : []).map((c) => c === null || c === undefined ? "" : String(c)));
    		if (rows.length === 0) return null;
    		const width = rows.reduce((w, r) => Math.max(w, r.length), 0);
    		const pad = (r) => Array.from({ length: width }, (_, i) => {
    			var _r$i;
    			return (_r$i = r[i]) !== null && _r$i !== void 0 ? _r$i : "";
    		});
    		return {
    			headers: pad(rows[0]),
    			rows: rows.slice(1).map(pad),
    			sheetNames: names,
    			sheet,
    			filename,
    			isXls: /\.xls$/i.test(filename)
    		};
    	}
    	function parseCsvManually(text, filename) {
    		const fs = sniffDelimiter(text);
    		const aoa = text.split(/\r\n|\n|\r/).filter((l) => l.trim() !== "").map((line) => splitCsvLine(line, fs));
    		return toParsed(aoa, ["CSV"], "CSV", filename);
    	}
    	async function onFile(file) {
    		setError(null);
    		setNotice(null);
    		try {
    			const buf = await file.arrayBuffer();
    			const p = readSheet(buf, null, file.name);
    			if (!p) {
    				setError(tr("empty_file"));
    				return;
    			}
    			if (p.rows.length > ROWS_MAX) {
    				setError(tr("too_many", {
    					n: p.rows.length,
    					max: ROWS_MAX
    				}));
    				return;
    			}
    			if (p.rows.length > ROWS_WARN) setNotice(tr("slow", { n: p.rows.length }));
    			setBuffer(buf);
    			setParsed(p);
    			await loadMapping(p);
    		} catch (e) {
    			setError(e instanceof Error ? e.message : tr("parse_error"));
    		}
    	}
    	function changeSheet(sheet) {
    		if (!buffer || !parsed) return;
    		const p = readSheet(buffer, sheet, parsed.filename);
    		if (p) {
    			setParsed(p);
    			void loadMapping(p);
    		}
    	}
    	/** Ask the server which field each header feeds — the mapping lives in the tool's config. */
    	async function loadMapping(p) {
    		const res = await api(`${BASE}/${toolKey}/map`, { headers: p.headers });
    		const next = {};
    		p.headers.forEach((_, i) => {
    			var _res$mapping$String;
    			next[i] = (_res$mapping$String = res.mapping[String(i)]) !== null && _res$mapping$String !== void 0 ? _res$mapping$String : null;
    		});
    		setMapping(next);
    		setStep("map");
    	}
    	// ── Step 2: mapping ───────────────────────────────────────────────────────
    	function assign(index, key) {
    		setMapping((cur) => {
    			const next = { ...cur };
    			if (key) {
    				for (const i of Object.keys(next)) if (next[Number(i)] === key) next[Number(i)] = null;
    			}
    			next[index] = key;
    			return next;
    		});
    	}
    	// What a file column may be imported into: the writable fields, plus — only when it is the
    	// chosen identifying column — the primary key (it can match a record but is never written).
    	// Otherwise the ID is not offered at all: dragging it around would target arbitrary records.
    	const importable = fields.filter((f) => f.writable || f.key === matchKey);
    	const mapIssues = [];
    	for (const f of fields) if (f.required && f.writable && fieldOf[f.key] === undefined) mapIssues.push(tr("missing_required", { f: f.label }));
    	// Not blocking: a file without the identifying column simply inserts every row.
    	const matchWarning = matchKey && fieldOf[matchKey] === undefined ? tr("match_unmapped") : null;
    	async function saveMapping() {
    		if (!parsed) return;
    		const headers = {};
    		for (const f of fields) {
    			const index = fieldOf[f.key];
    			headers[f.key] = index === undefined ? [] : [parsed.headers[index]];
    		}
    		try {
    			await api(`${BASE}/${toolKey}/mapping`, { headers });
    			// Unmistakable feedback, right where the click happened: the button itself
    			// turns into a green "saved" confirmation for a few seconds.
    			setMapSaved(true);
    			window.setTimeout(() => setMapSaved(false), 3e3);
    		} catch (e) {
    			setError(e instanceof Error ? e.message : String(e));
    		}
    	}
    	// ── Step 3: unknown reference values ──────────────────────────────────────
    	async function goToCodes() {
    		if (!parsed || mapIssues.length) return;
    		setError(null);
    		setNotice(null);
    		const values = {};
    		for (const [index, key] of Object.entries(mapping)) {
    			const f = key ? byKey[key] : null;
    			if (!f || f.type !== "ref") continue;
    			const seen = new Set();
    			for (const row of parsed.rows) {
    				var _row$Number;
    				const raw = ((_row$Number = row[Number(index)]) !== null && _row$Number !== void 0 ? _row$Number : "").trim();
    				if (raw !== "") seen.add(raw);
    			}
    			values[key] = [...seen];
    		}
    		try {
    			var _res$unresolved;
    			const res = await api(`${BASE}/${toolKey}/resolve`, { values });
    			setUnresolved((_res$unresolved = res.unresolved) !== null && _res$unresolved !== void 0 ? _res$unresolved : {});
    			setStep("codes");
    		} catch (e) {
    			setError(e instanceof Error ? e.message : String(e));
    		}
    	}
    	function setOverride(field, raw, value) {
    		setOverrides((cur) => {
    			var _cur$field;
    			return {
    				...cur,
    				[field]: {
    					...(_cur$field = cur[field]) !== null && _cur$field !== void 0 ? _cur$field : {},
    					[raw]: value
    				}
    			};
    		});
    	}
    	// ── Step 4: run ───────────────────────────────────────────────────────────
    	async function run() {
    		if (!parsed) return;
    		setStep("run");
    		setRunning(true);
    		setError(null);
    		setCounts({
    			inserted: 0,
    			updated: 0,
    			skipped: 0,
    			error: 0
    		});
    		setFailures([]);
    		setProgress(0);
    		// Remember the remapping choices the admin ticked, so later imports resolve them alone.
    		for (const [field, values] of Object.entries(overrides)) {
    			for (const [raw, value] of Object.entries(values)) {
    				if (!remember[`${field}|${raw}`] || value === "__skip__") continue;
    				try {
    					await api(`${BASE}/${toolKey}/alias`, {
    						field,
    						raw,
    						targetId: value === "" ? null : Number(value)
    					});
    				} catch {}
    			}
    		}
    		const total = parsed.rows.length;
    		const headerOffset = 2;
    		const acc = {
    			inserted: 0,
    			updated: 0,
    			skipped: 0,
    			error: 0
    		};
    		const fails = [];
    		try {
    			for (let start = 0; start < total; start += CHUNK) {
    				var _res$counts$k;
    				const rows = parsed.rows.slice(start, start + CHUNK).map((cells, i) => ({
    					l: start + i + headerOffset,
    					c: cells
    				}));
    				const res = await api(`${BASE}/${toolKey}/run`, {
    					rows,
    					mapping,
    					matchKey,
    					overrides
    				});
    				for (const k of Object.keys(acc)) acc[k] += (_res$counts$k = res.counts[k]) !== null && _res$counts$k !== void 0 ? _res$counts$k : 0;
    				for (const r of res.results) if (r.status === "error") fails.push(r);
    				setCounts({ ...acc });
    				setFailures([...fails]);
    				setProgress(Math.min(100, Math.round((start + rows.length) / total * 100)));
    			}
    			onDone === null || onDone === void 0 ? void 0 : onDone();
    		} catch (e) {
    			setError(e instanceof Error ? e.message : String(e));
    		} finally {
    			setRunning(false);
    		}
    	}
    	function downloadReport() {
    		const rows = [[
    			tr("line"),
    			tr("field"),
    			tr("message")
    		]];
    		for (const f of failures) {
    			var _f$errors;
    			for (const [field, message] of Object.entries((_f$errors = f.errors) !== null && _f$errors !== void 0 ? _f$errors : {})) rows.push([
    				String(f.line),
    				field,
    				message
    			]);
    		}
    		const csv = rows.map((r) => r.map((v) => `"${String(v !== null && v !== void 0 ? v : "").replace(/"/g, "\"\"")}"`).join(";")).join("\n");
    		const blob = new Blob(["﻿" + csv], { type: "text/csv;charset=utf-8;" });
    		const url = URL.createObjectURL(blob);
    		const a = Object.assign(document.createElement("a"), {
    			href: url,
    			download: `import-${new Date().toISOString().slice(0, 10)}.csv`
    		});
    		document.body.appendChild(a);
    		a.click();
    		document.body.removeChild(a);
    		URL.revokeObjectURL(url);
    	}
    	// ── Render ────────────────────────────────────────────────────────────────
    	const steps = [
    		{
    			key: "file",
    			label: tr("step_file")
    		},
    		{
    			key: "map",
    			label: tr("step_map")
    		},
    		{
    			key: "codes",
    			label: tr("step_codes")
    		},
    		{
    			key: "run",
    			label: tr("step_run")
    		}
    	];
    	return /* @__PURE__ */ React.createElement("div", {
    		style: {
    			position: "fixed",
    			inset: 0,
    			zIndex: 60,
    			display: "flex",
    			alignItems: narrow ? "flex-start" : "center",
    			justifyContent: "center",
    			overflow: "auto",
    			padding: narrow ? "16px 12px" : 16,
    			background: "rgba(0,0,0,.5)"
    		},
    		onClick: (e) => {
    			if (e.target === e.currentTarget && !running) onClose();
    		}
    	}, /* @__PURE__ */ React.createElement("div", { style: {
    		...card,
    		width: "100%",
    		maxWidth: 980,
    		display: "flex",
    		flexDirection: "column",
    		maxHeight: narrow ? undefined : "calc(100vh - 48px)"
    	} }, /* @__PURE__ */ React.createElement("div", { style: {
    		display: "flex",
    		alignItems: "flex-start",
    		justifyContent: "space-between",
    		padding: "16px 20px",
    		borderBottom: "1px solid var(--color-border)",
    		flexShrink: 0
    	} }, /* @__PURE__ */ React.createElement("div", null, /* @__PURE__ */ React.createElement("h2", { style: {
    		fontSize: 14,
    		fontWeight: 600,
    		margin: 0
    	} }, tr("title")), /* @__PURE__ */ React.createElement("div", { style: {
    		display: "flex",
    		gap: 8,
    		marginTop: 6
    	} }, steps.map((s, i) => /* @__PURE__ */ React.createElement("span", {
    		key: s.key,
    		style: {
    			...muted,
    			fontWeight: step === s.key ? 600 : 400,
    			color: step === s.key ? "var(--color-primary)" : "var(--color-muted-foreground)"
    		}
    	}, i + 1, ". ", s.label)))), /* @__PURE__ */ React.createElement("button", {
    		style: {
    			border: 0,
    			background: "transparent",
    			cursor: running ? "not-allowed" : "pointer",
    			color: "var(--color-muted-foreground)",
    			fontSize: 16
    		},
    		disabled: running,
    		onClick: onClose
    	}, "✕")), /* @__PURE__ */ React.createElement("div", { style: {
    		padding: 16,
    		display: "flex",
    		flexDirection: "column",
    		gap: 12,
    		overflowY: "auto",
    		flex: "1 1 auto"
    	} }, error && /* @__PURE__ */ React.createElement("div", { style: {
    		...muted,
    		color: "var(--color-destructive,#dc2626)"
    	} }, error), notice && /* @__PURE__ */ React.createElement("div", { style: {
    		...muted,
    		color: "var(--color-primary)"
    	} }, notice), step === "file" && /* @__PURE__ */ React.createElement(FileStep, { onFile }), step === "map" && parsed && /* @__PURE__ */ React.createElement(React.Fragment, null, mapIssues.length > 0 && /* @__PURE__ */ React.createElement("div", { style: {
    		padding: "10px 12px",
    		borderRadius: 8,
    		fontSize: 13,
    		fontWeight: 500,
    		border: "1px solid var(--color-destructive,#dc2626)",
    		color: "var(--color-destructive,#dc2626)",
    		background: "color-mix(in srgb, var(--color-destructive,#dc2626) 7%, transparent)"
    	} }, mapIssues.map((m) => /* @__PURE__ */ React.createElement("div", { key: m }, "⚠ ", m))), matchWarning && /* @__PURE__ */ React.createElement("div", { style: {
    		padding: "10px 12px",
    		borderRadius: 8,
    		fontSize: 13,
    		fontWeight: 500,
    		border: "1px solid #d97706",
    		color: "#b45309",
    		background: "color-mix(in srgb, #d97706 8%, transparent)"
    	} }, "⚠ ", matchWarning), /* @__PURE__ */ React.createElement("div", { style: {
    		display: "flex",
    		flexWrap: "wrap",
    		alignItems: "center",
    		gap: 8
    	} }, /* @__PURE__ */ React.createElement("span", { style: {
    		fontSize: 13,
    		fontWeight: 600
    	} }, tr("rows_found", { n: parsed.rows.length })), parsed.sheetNames.length > 1 && /* @__PURE__ */ React.createElement(React.Fragment, null, /* @__PURE__ */ React.createElement("span", { style: muted }, "· ", tr("sheet")), /* @__PURE__ */ React.createElement("select", {
    		style: inputCss,
    		value: parsed.sheet,
    		onChange: (e) => changeSheet(e.target.value)
    	}, parsed.sheetNames.map((n) => /* @__PURE__ */ React.createElement("option", {
    		key: n,
    		value: n
    	}, n)))), parsed.isXls && /* @__PURE__ */ React.createElement("span", { style: muted }, "· ", tr("xls_hint"))), /* @__PURE__ */ React.createElement("div", { style: {
    		display: "flex",
    		flexWrap: "wrap",
    		alignItems: "center",
    		gap: 8
    	} }, /* @__PURE__ */ React.createElement("label", { style: {
    		...muted,
    		fontWeight: 600
    	} }, tr("match_on")), /* @__PURE__ */ React.createElement("select", {
    		style: inputCss,
    		value: matchKey,
    		onChange: (e) => {
    			const next = e.target.value;
    			setMatchKey(next);
    			// The pk is only offered while it is the identifier: unmap it when it stops being one.
    			setMapping((cur) => {
    				const out = { ...cur };
    				for (const i of Object.keys(out)) {
    					var _byKey$k;
    					const k = out[Number(i)];
    					if (k && !((_byKey$k = byKey[k]) === null || _byKey$k === void 0 ? void 0 : _byKey$k.writable) && k !== next) out[Number(i)] = null;
    				}
    				return out;
    			});
    		}
    	}, matchKeys.map((m) => /* @__PURE__ */ React.createElement("option", {
    		key: m.key,
    		value: m.key
    	}, m.label))), /* @__PURE__ */ React.createElement("span", { style: muted }, tr("match_hint"))), /* @__PURE__ */ React.createElement("div", { style: muted }, tr("drag_hint")), /* @__PURE__ */ React.createElement("div", { style: {
    		display: "flex",
    		flexDirection: narrow ? "column" : "row",
    		gap: 10
    	} }, /* @__PURE__ */ React.createElement("div", {
    		style: {
    			...panelCss,
    			flex: 1,
    			minWidth: 0
    		},
    		onDragOver: (e) => e.preventDefault(),
    		onDrop: () => {
    			if (dragKey) {
    				const i = fieldOf[dragKey];
    				if (i !== undefined) assign(i, null);
    			}
    			setDragKey(null);
    		}
    	}, /* @__PURE__ */ React.createElement("p", { style: {
    		...panelTitle,
    		fontSize: 11,
    		fontWeight: 700,
    		color: "var(--color-foreground)"
    	} }, tr("panel_fields"), " (", importable.filter((f) => fieldOf[f.key] === undefined).length, ")"), /* @__PURE__ */ React.createElement("div", { style: {
    		...muted,
    		fontSize: 11,
    		padding: "0 6px 6px"
    	} }, tr("left_note")), /* @__PURE__ */ React.createElement("div", { style: {
    		display: "flex",
    		flexDirection: "column",
    		gap: 6
    	} }, importable.filter((f) => fieldOf[f.key] === undefined).map((f) => /* @__PURE__ */ React.createElement("div", {
    		key: f.key,
    		draggable: true,
    		style: chipCss,
    		onDragStart: () => setDragKey(f.key),
    		onDragEnd: () => setDragKey(null)
    	}, /* @__PURE__ */ React.createElement("span", { style: {
    		fontWeight: 500,
    		overflow: "hidden",
    		textOverflow: "ellipsis",
    		whiteSpace: "nowrap"
    	} }, f.label, f.required && /* @__PURE__ */ React.createElement("span", { style: { color: "var(--color-destructive,#dc2626)" } }, " *")), f.key === matchKey && /* @__PURE__ */ React.createElement("span", { style: {
    		...muted,
    		fontSize: 11,
    		padding: "1px 6px",
    		borderRadius: 999,
    		border: "1px solid var(--color-border)",
    		flexShrink: 0
    	} }, tr("match_badge")))), importable.every((f) => fieldOf[f.key] !== undefined) && /* @__PURE__ */ React.createElement("div", { style: muted }, tr("all_mapped")))), /* @__PURE__ */ React.createElement("div", { style: {
    		...panelCss,
    		flex: 1.4,
    		minWidth: 0
    	} }, /* @__PURE__ */ React.createElement("div", { style: {
    		display: "flex",
    		alignItems: "baseline",
    		gap: 8,
    		padding: "0 6px"
    	} }, /* @__PURE__ */ React.createElement("p", { style: {
    		...panelTitle,
    		flex: "0 0 45%",
    		padding: 0,
    		fontWeight: 700,
    		color: "var(--color-foreground)"
    	} }, tr("panel_file")), /* @__PURE__ */ React.createElement("p", { style: {
    		...panelTitle,
    		flex: 1,
    		padding: 0,
    		fontWeight: 700,
    		color: "var(--color-foreground)"
    	} }, tr("target_label"))), /* @__PURE__ */ React.createElement("div", { style: {
    		display: "flex",
    		flexDirection: "column"
    	} }, parsed.headers.map((header, i) => {
    		var _byKey$key$label, _byKey$key;
    		const key = mapping[i];
    		const mapped = !!key;
    		return /* @__PURE__ */ React.createElement("div", {
    			key: i,
    			onDragOver: (e) => e.preventDefault(),
    			onDrop: (e) => {
    				e.stopPropagation();
    				if (dragKey) assign(i, dragKey);
    				setDragKey(null);
    			},
    			style: {
    				display: "flex",
    				alignItems: "center",
    				gap: 8,
    				padding: "8px 6px",
    				opacity: mapped || dragKey ? 1 : .6,
    				borderTop: i > 0 ? "1px solid var(--color-border)" : "none",
    				background: dragKey && !mapped ? "color-mix(in srgb, var(--color-primary) 8%, transparent)" : "transparent"
    			}
    		}, /* @__PURE__ */ React.createElement("span", { style: {
    			flex: "0 0 45%",
    			minWidth: 0,
    			fontSize: 13,
    			display: "flex",
    			alignItems: "baseline",
    			gap: 6
    		} }, /* @__PURE__ */ React.createElement("span", { style: {
    			...muted,
    			flexShrink: 0,
    			fontSize: 10,
    			fontWeight: 600,
    			width: 20,
    			textAlign: "center",
    			padding: "1px 0",
    			borderRadius: 4,
    			background: "var(--color-muted,rgba(0,0,0,.06))"
    		} }, colLetter(i)), /* @__PURE__ */ React.createElement("span", {
    			style: {
    				fontWeight: 500,
    				flexShrink: 0,
    				whiteSpace: "nowrap"
    			},
    			title: header || `#${i + 1}`
    		}, header || `#${i + 1}`)), mapped && /* @__PURE__ */ React.createElement("span", {
    			draggable: true,
    			onDragStart: () => setDragKey(key),
    			onDragEnd: () => setDragKey(null),
    			title: (_byKey$key$label = (_byKey$key = byKey[key]) === null || _byKey$key === void 0 ? void 0 : _byKey$key.label) !== null && _byKey$key$label !== void 0 ? _byKey$key$label : key,
    			style: {
    				cursor: "grab",
    				color: "var(--color-muted-foreground)",
    				fontSize: 13,
    				lineHeight: 1,
    				padding: "4px 2px",
    				userSelect: "none",
    				flexShrink: 0
    			}
    		}, "⠿"), /* @__PURE__ */ React.createElement("select", {
    			style: {
    				...inputCss,
    				flex: 1,
    				minWidth: 120,
    				fontWeight: mapped ? 500 : 400,
    				opacity: mapped ? 1 : .7
    			},
    			value: key !== null && key !== void 0 ? key : "",
    			onChange: (e) => assign(i, e.target.value === "" ? null : e.target.value)
    		}, /* @__PURE__ */ React.createElement("option", { value: "" }, tr("ignore")), importable.map((f) => {
    			const usedBy = fieldOf[f.key];
    			const elsewhere = usedBy !== undefined && usedBy !== i;
    			return /* @__PURE__ */ React.createElement("option", {
    				key: f.key,
    				value: f.key
    			}, f.label, f.required ? " *" : "", elsewhere ? ` — ${tr("already_used", { c: parsed.headers[usedBy] || `#${usedBy + 1}` })}` : "");
    		})), key === matchKey && /* @__PURE__ */ React.createElement("span", { style: {
    			...muted,
    			fontSize: 11,
    			padding: "1px 6px",
    			borderRadius: 999,
    			border: "1px solid var(--color-border)",
    			flexShrink: 0
    		} }, tr("match_badge")));
    	})))), /* @__PURE__ */ React.createElement(Preview, {
    		parsed,
    		mapping,
    		byKey
    	}), /* @__PURE__ */ React.createElement("div", { style: muted }, tr("overwrite_note"))), step === "codes" && /* @__PURE__ */ React.createElement(CodesStep, {
    		unresolved,
    		byKey,
    		overrides,
    		remember,
    		onChange: setOverride,
    		onRemember: (k, v) => setRemember((cur) => ({
    			...cur,
    			[k]: v
    		}))
    	}), step === "run" && /* @__PURE__ */ React.createElement(RunStep, {
    		running,
    		progress,
    		counts,
    		failures,
    		onReport: downloadReport
    	})), /* @__PURE__ */ React.createElement("div", { style: {
    		display: "flex",
    		justifyContent: "space-between",
    		gap: 8,
    		padding: "12px 16px",
    		borderTop: "1px solid var(--color-border)",
    		flexShrink: 0
    	} }, /* @__PURE__ */ React.createElement("div", null, step === "map" && /* @__PURE__ */ React.createElement("button", {
    		style: mapSaved ? {
    			...btnGhost,
    			borderColor: "var(--color-success,#16a34a)",
    			color: "var(--color-success,#16a34a)",
    			fontWeight: 600
    		} : btnGhost,
    		onClick: saveMapping
    	}, mapSaved ? `✓ ${tr("mapping_saved")}` : tr("save_mapping"))), /* @__PURE__ */ React.createElement("div", { style: {
    		display: "flex",
    		gap: 8
    	} }, step !== "file" && step !== "run" && /* @__PURE__ */ React.createElement("button", {
    		style: btnGhost,
    		onClick: () => setStep(step === "codes" ? "map" : "file")
    	}, tr("back")), /* @__PURE__ */ React.createElement("button", {
    		style: btnGhost,
    		disabled: running,
    		onClick: onClose
    	}, step === "run" && !running ? tr("close") : tr("cancel")), step === "map" && /* @__PURE__ */ React.createElement("button", {
    		style: {
    			...btnPrimary,
    			opacity: mapIssues.length ? .6 : 1
    		},
    		disabled: mapIssues.length > 0,
    		onClick: goToCodes
    	}, tr("next")), step === "codes" && /* @__PURE__ */ React.createElement("button", {
    		style: btnPrimary,
    		onClick: run
    	}, tr("start"))))));
    }
    /* ── Step components ──────────────────────────────────────────────────────── */
    function FileStep({ onFile }) {
    	const inputRef = useRef(null);
    	const [over, setOver] = useState(false);
    	return /* @__PURE__ */ React.createElement("div", {
    		onClick: () => {
    			var _inputRef$current;
    			return (_inputRef$current = inputRef.current) === null || _inputRef$current === void 0 ? void 0 : _inputRef$current.click();
    		},
    		onDragOver: (e) => {
    			e.preventDefault();
    			setOver(true);
    		},
    		onDragLeave: () => setOver(false),
    		onDrop: (e) => {
    			var _e$dataTransfer$files;
    			e.preventDefault();
    			setOver(false);
    			const f = (_e$dataTransfer$files = e.dataTransfer.files) === null || _e$dataTransfer$files === void 0 ? void 0 : _e$dataTransfer$files[0];
    			if (f) onFile(f);
    		},
    		style: {
    			display: "flex",
    			flexDirection: "column",
    			alignItems: "center",
    			justifyContent: "center",
    			gap: 6,
    			padding: "36px 16px",
    			borderRadius: 10,
    			cursor: "pointer",
    			textAlign: "center",
    			border: "1px dashed var(--color-border)",
    			background: over ? "color-mix(in srgb, var(--color-primary) 6%, transparent)" : "transparent"
    		}
    	}, /* @__PURE__ */ React.createElement(UploadIcon, null), /* @__PURE__ */ React.createElement("div", { style: { fontSize: 14 } }, tr("drop")), /* @__PURE__ */ React.createElement("div", { style: muted }, tr("formats")), /* @__PURE__ */ React.createElement("input", {
    		ref: inputRef,
    		type: "file",
    		accept: ".csv,.xlsx,.xls",
    		style: { display: "none" },
    		onChange: (e) => {
    			var _e$target$files;
    			const f = (_e$target$files = e.target.files) === null || _e$target$files === void 0 ? void 0 : _e$target$files[0];
    			if (f) onFile(f);
    			e.target.value = "";
    		}
    	}));
    }
    function Preview({ parsed, mapping, byKey }) {
    	const cols = Object.entries(mapping).filter(([, k]) => k).map(([i, k]) => ({
    		index: Number(i),
    		key: k
    	}));
    	if (cols.length === 0) return null;
    	return /* @__PURE__ */ React.createElement("div", null, /* @__PURE__ */ React.createElement("p", { style: panelTitle }, tr("preview")), /* @__PURE__ */ React.createElement("div", { style: {
    		overflowX: "auto",
    		border: "1px solid var(--color-border)",
    		borderRadius: 8
    	} }, /* @__PURE__ */ React.createElement("table", { style: {
    		width: "100%",
    		borderCollapse: "collapse",
    		fontSize: 12
    	} }, /* @__PURE__ */ React.createElement("thead", { style: { background: "var(--color-muted,rgba(0,0,0,.03))" } }, /* @__PURE__ */ React.createElement("tr", null, cols.map((c) => /* @__PURE__ */ React.createElement("th", {
    		key: c.key,
    		style: {
    			padding: "6px 8px",
    			textAlign: "left",
    			whiteSpace: "nowrap"
    		}
    	}, humanLabel(c.key, byKey[c.key]))))), /* @__PURE__ */ React.createElement("tbody", null, parsed.rows.slice(0, 5).map((row, i) => /* @__PURE__ */ React.createElement("tr", {
    		key: i,
    		style: { borderTop: "1px solid var(--color-border)" }
    	}, cols.map((c) => {
    		var _row$c$index;
    		return /* @__PURE__ */ React.createElement("td", {
    			key: c.key,
    			style: {
    				padding: "6px 8px",
    				whiteSpace: "nowrap",
    				maxWidth: 220,
    				overflow: "hidden",
    				textOverflow: "ellipsis"
    			}
    		}, (_row$c$index = row[c.index]) !== null && _row$c$index !== void 0 ? _row$c$index : "");
    	})))))));
    }
    function CodesStep({ unresolved, byKey, overrides, remember, onChange, onRemember }) {
    	const entries = Object.entries(unresolved).filter(([, values]) => values.length > 0);
    	if (entries.length === 0) return /* @__PURE__ */ React.createElement("div", { style: muted }, tr("no_codes"));
    	return /* @__PURE__ */ React.createElement("div", { style: {
    		display: "flex",
    		flexDirection: "column",
    		gap: 12
    	} }, /* @__PURE__ */ React.createElement("div", { style: muted }, tr("codes_intro")), entries.map(([field, values]) => /* @__PURE__ */ React.createElement("div", { key: field }, /* @__PURE__ */ React.createElement("p", { style: panelTitle }, humanLabel(field, byKey[field])), /* @__PURE__ */ React.createElement("div", { style: {
    		display: "flex",
    		flexDirection: "column",
    		gap: 6
    	} }, values.map((raw) => {
    		var _overrides$field$raw, _overrides$field, _byKey$field$refOptio, _byKey$field;
    		const value = (_overrides$field$raw = (_overrides$field = overrides[field]) === null || _overrides$field === void 0 ? void 0 : _overrides$field[raw]) !== null && _overrides$field$raw !== void 0 ? _overrides$field$raw : "";
    		return /* @__PURE__ */ React.createElement("div", {
    			key: raw,
    			style: {
    				display: "flex",
    				flexWrap: "wrap",
    				alignItems: "center",
    				gap: 8
    			}
    		}, /* @__PURE__ */ React.createElement("code", { style: {
    			flex: "0 0 170px",
    			fontSize: 12,
    			padding: "4px 6px",
    			borderRadius: 6,
    			background: "var(--color-muted,rgba(0,0,0,.05))",
    			overflow: "hidden",
    			textOverflow: "ellipsis",
    			whiteSpace: "nowrap"
    		} }, raw), /* @__PURE__ */ React.createElement("select", {
    			style: {
    				...inputCss,
    				flex: 1,
    				minWidth: 180
    			},
    			value,
    			onChange: (e) => onChange(field, raw, e.target.value)
    		}, /* @__PURE__ */ React.createElement("option", { value: "" }, tr("leave_empty")), /* @__PURE__ */ React.createElement("option", { value: "__skip__" }, tr("skip_rows")), ((_byKey$field$refOptio = (_byKey$field = byKey[field]) === null || _byKey$field === void 0 ? void 0 : _byKey$field.refOptions) !== null && _byKey$field$refOptio !== void 0 ? _byKey$field$refOptio : []).map((o) => /* @__PURE__ */ React.createElement("option", {
    			key: o.value,
    			value: o.value
    		}, o.code ? `${o.code} — ${o.label}` : o.label))), /* @__PURE__ */ React.createElement("label", { style: {
    			...muted,
    			display: "flex",
    			alignItems: "center",
    			gap: 4,
    			cursor: "pointer"
    		} }, /* @__PURE__ */ React.createElement("input", {
    			type: "checkbox",
    			disabled: value === "" || value === "__skip__",
    			checked: !!remember[`${field}|${raw}`],
    			onChange: (e) => onRemember(`${field}|${raw}`, e.target.checked)
    		}), tr("remember")));
    	})))));
    }
    function RunStep({ running, progress, counts, failures, onReport }) {
    	const tile = (label, value, color) => /* @__PURE__ */ React.createElement("div", { style: {
    		...card,
    		flex: 1,
    		minWidth: 100,
    		padding: "8px 10px"
    	} }, /* @__PURE__ */ React.createElement("div", { style: muted }, label), /* @__PURE__ */ React.createElement("div", { style: {
    		fontSize: 18,
    		fontWeight: 600,
    		color
    	} }, value));
    	return /* @__PURE__ */ React.createElement("div", { style: {
    		display: "flex",
    		flexDirection: "column",
    		gap: 12
    	} }, /* @__PURE__ */ React.createElement("div", null, /* @__PURE__ */ React.createElement("div", { style: {
    		...muted,
    		marginBottom: 4
    	} }, running ? tr("running") : tr("done"), " — ", progress, "%"), /* @__PURE__ */ React.createElement("div", { style: {
    		height: 8,
    		borderRadius: 999,
    		background: "var(--color-muted,rgba(0,0,0,.08))",
    		overflow: "hidden"
    	} }, /* @__PURE__ */ React.createElement("div", { style: {
    		width: `${progress}%`,
    		height: "100%",
    		background: "var(--color-primary)",
    		transition: "width .2s"
    	} }))), /* @__PURE__ */ React.createElement("div", { style: {
    		display: "flex",
    		flexWrap: "wrap",
    		gap: 8
    	} }, tile(tr("inserted"), counts.inserted), tile(tr("updated"), counts.updated), tile(tr("skipped"), counts.skipped), tile(tr("errors"), counts.error, counts.error ? "var(--color-destructive,#dc2626)" : undefined)), failures.length > 0 && /* @__PURE__ */ React.createElement(React.Fragment, null, /* @__PURE__ */ React.createElement("div", { style: {
    		maxHeight: 220,
    		overflowY: "auto",
    		border: "1px solid var(--color-border)",
    		borderRadius: 8
    	} }, /* @__PURE__ */ React.createElement("table", { style: {
    		width: "100%",
    		borderCollapse: "collapse",
    		fontSize: 12
    	} }, /* @__PURE__ */ React.createElement("thead", { style: { background: "var(--color-muted,rgba(0,0,0,.03))" } }, /* @__PURE__ */ React.createElement("tr", null, /* @__PURE__ */ React.createElement("th", { style: {
    		padding: "6px 8px",
    		textAlign: "left",
    		width: 70
    	} }, tr("line")), /* @__PURE__ */ React.createElement("th", { style: {
    		padding: "6px 8px",
    		textAlign: "left"
    	} }, tr("field")), /* @__PURE__ */ React.createElement("th", { style: {
    		padding: "6px 8px",
    		textAlign: "left"
    	} }, tr("message")))), /* @__PURE__ */ React.createElement("tbody", null, failures.flatMap((f) => {
    		var _f$errors2;
    		return Object.entries((_f$errors2 = f.errors) !== null && _f$errors2 !== void 0 ? _f$errors2 : {}).map(([field, message]) => /* @__PURE__ */ React.createElement("tr", {
    			key: `${f.line}-${field}`,
    			style: { borderTop: "1px solid var(--color-border)" }
    		}, /* @__PURE__ */ React.createElement("td", { style: { padding: "6px 8px" } }, f.line), /* @__PURE__ */ React.createElement("td", { style: { padding: "6px 8px" } }, field), /* @__PURE__ */ React.createElement("td", { style: { padding: "6px 8px" } }, message)));
    	})))), /* @__PURE__ */ React.createElement("div", null, /* @__PURE__ */ React.createElement("button", {
    		style: btnGhost,
    		onClick: onReport
    	}, tr("report")))));
    }
    /** 0-based index to the spreadsheet column letter (A, B, … Z, AA), to say "file column" at a glance. */
    function serialToIso(serial) {
    	// Excel serial day 0 = 1899-12-30. Pure arithmetic on UTC, so no timezone drift.
    	const d = new Date(Date.UTC(1899, 11, 30) + Math.round(serial * 864e5));
    	const pad = (n) => String(n).padStart(2, "0");
    	const date = `${d.getUTCFullYear()}-${pad(d.getUTCMonth() + 1)}-${pad(d.getUTCDate())}`;
    	const h = d.getUTCHours(), mi = d.getUTCMinutes(), sec = d.getUTCSeconds();
    	return h || mi || sec ? `${date} ${pad(h)}:${pad(mi)}:${pad(sec)}` : date;
    }
    function colLetter(index) {
    	let s = "";
    	let i = index + 1;
    	while (i > 0) {
    		i--;
    		s = String.fromCharCode(65 + i % 26) + s;
    		i = Math.floor(i / 26);
    	}
    	return s;
    }
    /**
    * A field is ALWAYS shown by its human label — never by its DB column ("Forme juridique", not
    * oe_fja_id). This is the last-resort fallback for a field the server sent without one: turn
    * the column into something readable rather than printing the raw key.
    */
    function humanLabel(key, field) {
    	if (field === null || field === void 0 ? void 0 : field.label) return field.label;
    	return key.replace(/_custom$/, "").replace(/^o[a-z]*_/, "").replace(/_id$/, "").replace(/_/g, " ").replace(/^./, (c) => c.toUpperCase());
    }
    /* ── CSV helpers ──────────────────────────────────────────────────────────── */
    /** UTF-8, falling back to windows-1252 when the decode produces replacement characters. */
    function decodeText(buf) {
    	const utf8 = new TextDecoder("utf-8").decode(buf);
    	if (!utf8.includes("�")) return utf8.replace(/^﻿/, "");
    	try {
    		return new TextDecoder("windows-1252").decode(buf).replace(/^﻿/, "");
    	} catch {
    		return utf8.replace(/^﻿/, "");
    	}
    }
    /** Delimiter of a CSV, sniffed over its first lines — same rule as the legacy importer. */
    function sniffDelimiter(text) {
    	const lines = text.split(/\r\n|\n|\r/).filter((l) => l.trim() !== "").slice(0, 3);
    	let best = ";";
    	let bestCount = 0;
    	for (const sep of [
    		";",
    		",",
    		"	"
    	]) {
    		const counts = lines.map((l) => l.split(sep).length - 1);
    		const total = counts.reduce((a, b) => a + b, 0);
    		if (total > bestCount) {
    			best = sep;
    			bestCount = total;
    		}
    	}
    	return best;
    }
    /** One CSV line → cells, honouring "" quoting. */
    function splitCsvLine(line, sep) {
    	const out = [];
    	let cur = "";
    	let quoted = false;
    	for (let i = 0; i < line.length; i++) {
    		const c = line[i];
    		if (quoted) {
    			if (c === "\"") {
    				if (line[i + 1] === "\"") {
    					cur += "\"";
    					i++;
    				} else quoted = false;
    			} else cur += c;
    		} else if (c === "\"") quoted = true;
    		else if (c === sep) {
    			out.push(cur);
    			cur = "";
    		} else cur += c;
    	}
    	out.push(cur);
    	return out;
    }

    return ImportModal;
  })();
