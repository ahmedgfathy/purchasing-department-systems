{{-- Compact chrome shared by the vendor form, vendor record and vendor list. --}}
<style>
    /* ---------- Form shell ---------- */
    .vendor-form { max-width: 1240px; margin: 0 auto; }

    .vendor-form__intro {
        display: flex; align-items: center; justify-content: space-between; gap: 16px;
        padding: 12px 16px; margin-bottom: 12px;
        border: 1px solid var(--border); border-radius: 12px;
        background: linear-gradient(135deg, rgba(7, 41, 73, .10), var(--surface));
    }
    .vendor-form__intro h2 { margin: 0; font-size: 16px; letter-spacing: -.01em; }
    .vendor-form__intro p { margin: 2px 0 0; color: var(--muted); font-size: 12px; }
    .vendor-form__intro-badge { display: inline-flex; align-items: center; gap: 6px; color: var(--blue); font-size: 11px; font-weight: 700; white-space: nowrap; }
    .vendor-form__intro-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; box-shadow: 0 0 0 4px rgba(96, 165, 250, .12); }

    .vendor-section {
        padding: 10px 14px 14px; margin-bottom: 10px;
        border: 1px solid var(--border); border-radius: 12px;
        background: var(--panel); box-shadow: none;
    }
    .vendor-section + .vendor-section { margin-top: 0; }

    .vcard { padding: 12px 14px; margin-bottom: 10px; }
    .vcard + .vcard, .vcard + .vendor-section { margin-top: 0; }
    .vcard .vhead { padding-bottom: 6px; }
    .vhead h2 .badge { vertical-align: middle; }

    .vservice-edit > summary { list-style: none; }
    .vservice-edit > summary::-webkit-details-marker { display: none; }
    .vservice-edit > summary::marker { content: ''; }
    .vservice-edit[open] > summary { background: var(--panel-2); }
    .service-edit { min-width: 320px; margin-top: 8px; padding: 10px; border: 1px solid var(--border); border-radius: 10px; background: var(--surface-strong); }
    .service-edit .field { margin-bottom: 8px; }

    /* Sections double as <details> so a long record can be folded. */
    details.vendor-section { padding: 0; }
    details.vendor-section > .vendor-section__header {
        padding: 10px 14px; margin: 0; cursor: pointer; user-select: none;
        display: flex; align-items: center; gap: 9px;
    }
    details.vendor-section > .vendor-section__header::-webkit-details-marker { display: none; }
    details.vendor-section > .vendor-section__header::marker { content: ''; }
    details.vendor-section[open] > .vendor-section__header { border-bottom: 1px solid var(--border); }
    details.vendor-section > .vendor-grid,
    details.vendor-section > .vendor-section__body { padding: 12px 14px 2px; }

    .vendor-section__header { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 20px; }
    .vendor-section__number {
        display: grid; place-items: center; min-width: 24px; height: 24px; padding: 0 5px;
        border-radius: 7px; background: rgba(14, 95, 149, .12); color: var(--pms-navy);
        font-size: 11px; font-weight: 800; flex: 0 0 auto;
    }
    .vendor-section__header h3 { margin: 0; font-size: 13.5px; letter-spacing: -.005em; }
    .vendor-section__header p { margin: 1px 0 0; color: var(--muted); font-size: 11.5px; }
    .vendor-section__titles { flex: 1 1 auto; min-width: 0; }
    .vendor-section__chev { width: 15px; height: 15px; flex: 0 0 15px; color: var(--muted); transition: transform .18s ease; margin-top: 4px; }
    details.vendor-section[open] .vendor-section__chev { transform: rotate(180deg); }
    details.vendor-section > .vendor-section__header:hover h3 { color: var(--pms-sea); }

    /* ---------- Dense grid ---------- */
    .vendor-grid { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 0 10px; }
    .vendor-grid__field { grid-column: span 3; }
    .vendor-grid__field--wide { grid-column: span 4; }
    .vendor-grid__field--xwide { grid-column: span 8; }
    .vendor-grid__field--half { grid-column: span 6; }
    .vendor-grid__field--full { grid-column: 1 / -1; }

    .vendor-grid .field { margin-bottom: 9px; min-width: 0; }
    .vendor-grid .field label,
    .vfacts + .vendor-grid .field label {
        display: flex; align-items: baseline; gap: 4px;
        margin-bottom: 3px; font-size: 11.5px; font-weight: 700; color: #46637c;
    }
    [dir="rtl"] .vendor-grid .field label { letter-spacing: 0; }
    .vendor-grid .field label abbr, .vendor-form__footer abbr { color: var(--danger); text-decoration: none; }

    .vendor-grid input, .vendor-grid select, .vendor-grid textarea {
        min-height: 0; padding: 6px 9px; font-size: 13px; border-radius: 8px;
    }
    .vendor-grid input[type="number"] { font-variant-numeric: tabular-nums; }
    .vendor-grid textarea { resize: vertical; min-height: 54px; line-height: 1.45; }
    .vendor-grid .field .hint { font-size: 10.5px; margin-top: 2px; }
    .vendor-grid .error-text { font-size: 11px; margin-top: 2px; }

    /* Triple inputs (phone 1/2/3) sharing one label — big vertical saving. */
    .vgroup__row { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 5px; }
    .vgroup__row--two { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .vgroup__row input { text-align: start; }

    /* Boolean flags as pills. */
    .vflags { display: flex; flex-wrap: wrap; gap: 6px; padding: 2px 0 6px; }
    .vflag {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 5px 10px; border: 1px solid var(--border); border-radius: 999px;
        background: var(--surface-strong); font-size: 12px; font-weight: 600;
        cursor: pointer; white-space: nowrap;
    }
    .vflag:hover { border-color: var(--border-strong); }
    .vflag input { width: 13px; height: 13px; margin: 0; accent-color: var(--primary); }
    .vflag:has(input:checked) { border-color: rgba(14, 95, 149, .45); background: rgba(14, 95, 149, .09); }

    .vendor-form__footer {
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
        position: sticky; bottom: 10px; z-index: 5;
        padding: 10px 14px; margin-top: 4px;
        border: 1px solid var(--border); border-radius: 12px;
        background: color-mix(in srgb, var(--panel) 92%, transparent);
        backdrop-filter: blur(8px); box-shadow: 0 8px 24px rgba(7, 41, 73, .10);
    }
    .vendor-form__footer-note { color: var(--muted); font-size: 11.5px; }
    .vendor-form__footer .actions { display: flex; gap: 6px; flex-wrap: wrap; }
    .vendor-form__footer .btn { padding: 7px 14px; font-size: 13px; border-radius: 9px; }

    /* ---------- Record facts (detail page) ---------- */
    .vfacts { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0; }
    .vfact { padding: 7px 12px 7px 0; min-width: 0; border-bottom: 1px dashed var(--border); }
    .vfact dt {
        font-size: 10.5px; font-weight: 600; color: var(--muted);
        margin-bottom: 1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .vfact dd { margin: 0; font-size: 13px; line-height: 1.35; word-break: break-word; }
    .vfact dd.is-empty { color: var(--muted); opacity: .55; }
    [dir="rtl"] .vfact { padding: 7px 0 7px 12px; }

    /* ---------- Filter bar ---------- */
    .vfilter { border: 1px solid var(--border); border-radius: 12px; background: var(--panel); box-shadow: var(--shadow); }
    .vfilter__bar { display: flex; align-items: center; gap: 8px; padding: 9px 10px; flex-wrap: wrap; }
    .vfilter__search { display: flex; gap: 6px; flex: 1 1 280px; min-width: 200px; }
    .vfilter__search input {
        min-height: 0; padding: 7px 10px; font-size: 13px; border-radius: 9px;
    }
    .vfilter__search .btn { padding: 7px 13px; font-size: 13px; border-radius: 9px; }
    .vfilter__toggle {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 7px 12px; border-radius: 9px; border: 1px solid var(--border);
        background: var(--panel); color: var(--pms-navy);
        font-family: inherit; font-size: 13px; font-weight: 700; cursor: pointer;
        transition: border-color .15s, background .15s;
    }
    .vfilter__toggle:hover { border-color: var(--border-strong); background: var(--panel-2); }
    .vfilter__toggle svg { width: 14px; height: 14px; }
    .vfilter__count {
        display: inline-grid; place-items: center; min-width: 18px; height: 18px; padding: 0 5px;
        border-radius: 999px; background: var(--primary); color: #fff; font-size: 10.5px; font-weight: 800;
    }
    .vfilter__count[hidden] { display: none; }
    .vfilter__bar .btn-sm { padding: 6px 10px; font-size: 12px; border-radius: 8px; }
    .vfilter__body { padding: 10px; border-top: 1px solid var(--border); }
    .vfilter__body[hidden] { display: none; }
    .vfilter__grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0 10px; }
    .vfilter__grid .field { margin-bottom: 8px; min-width: 0; }
    .vfilter__grid .field label { margin-bottom: 3px; font-size: 11.5px; font-weight: 700; color: #46637c; }
    .vfilter__grid input, .vfilter__grid select { min-height: 0; padding: 6px 9px; font-size: 13px; border-radius: 8px; }
    .vfilter__grid .field--full { grid-column: 1 / -1; }
    .vfilter__grid .hint { font-size: 10.5px; margin-top: 2px; }
    .vfilter__foot { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: 2px; }
    .vfilter__foot .btn { padding: 7px 13px; font-size: 13px; border-radius: 9px; }
    .vfilter__foot .vflags { padding: 0; }

    .vchips { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; padding: 0 10px 9px; }
    .vchips:empty { display: none; }
    .vchip {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 2px 8px; border-radius: 999px;
        background: rgba(14, 95, 149, .08); border: 1px solid rgba(14, 95, 149, .22);
        color: var(--blue); font-size: 11px; font-weight: 600;
    }
    .vchip a { color: inherit; text-decoration: none; font-weight: 800; opacity: .65; line-height: 1; }
    .vchip a:hover { opacity: 1; }

    /* ---------- Compact table ---------- */
    .vtable { width: 100%; border-collapse: collapse; }
    .vtable th {
        padding: 7px 10px; font-size: 10.5px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .05em; white-space: nowrap;
    }
    .vtable td { padding: 7px 10px; font-size: 13px; vertical-align: middle; }
    [dir="rtl"] .vtable th { letter-spacing: 0; }
    .vtable tbody tr:hover { background: #f2f8ff; }
    .vtable .vname { display: block; font-weight: 700; font-size: 13px; line-height: 1.3; }
    .vtable .vsub { display: block; color: var(--muted); font-size: 11.5px; line-height: 1.35; }
    .vtable .vmono { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 11px; color: var(--muted); }
    .vtable .vtrunc { display: block; max-width: 190px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .vtable .badge { padding: 2px 7px; font-size: 10.5px; }
    .vtable td.is-actions { width: 1%; white-space: nowrap; }
    .vtable .actions { gap: 4px; flex-wrap: nowrap; }

    .vicon {
        display: inline-grid; place-items: center; width: 27px; height: 27px;
        border-radius: 7px; border: 1px solid var(--border); background: var(--panel);
        color: var(--pms-navy); cursor: pointer; transition: border-color .15s, background .15s, color .15s;
    }
    .vicon:hover { border-color: var(--border-strong); background: var(--panel-2); }
    .vicon svg { width: 14px; height: 14px; }
    .vicon--danger { color: var(--danger); border-color: rgba(185, 28, 28, .22); background: rgba(185, 28, 28, .06); }
    .vicon--danger:hover { background: rgba(185, 28, 28, .13); border-color: rgba(185, 28, 28, .38); }

    .vtable-wrap { border: 1px solid var(--border); border-radius: 10px; overflow-x: auto; background: var(--panel); }
    .vtable-wrap--flush { border: 0; border-radius: 0; }

    .vhead { display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; padding: 2px 2px 9px; }
    .vhead h2 { margin: 0; font-size: 15px; }
    .vhead p { margin: 2px 0 0; color: var(--muted); font-size: 11.5px; }
    .vhead .vcount { font-size: 11.5px; color: var(--muted); }

    .vservices-add { border-top: 1px dashed var(--border); }

    @media (max-width: 1180px) {
        .vfacts { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .vfilter__grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    @media (max-width: 900px) {
        .vendor-grid__field, .vendor-grid__field--wide, .vendor-grid__field--xwide, .vendor-grid__field--half { grid-column: span 6; }
        .vfacts { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .vfilter__grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 620px) {
        .vendor-form__intro, .vendor-form__footer { align-items: stretch; flex-direction: column; }
        .vendor-grid__field, .vendor-grid__field--wide, .vendor-grid__field--xwide, .vendor-grid__field--half { grid-column: 1 / -1; }
        .vgroup__row { grid-template-columns: 1fr; }
        .vfacts { grid-template-columns: 1fr; }
        .vfilter__grid { grid-template-columns: 1fr; }
        .vendor-form__footer .actions .btn { flex: 1; }
        .vfilter__search { flex: 1 1 100%; }
    }
</style>
