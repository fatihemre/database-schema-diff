/**
 * Database Comparison Script - UIKit Version with Localization
 */

// Current language and translations
let currentLang = localStorage.getItem('lang') || 'en';
let translations = {};

document.addEventListener('DOMContentLoaded', function() {
    // Load translations first, then load data
    loadTranslations(currentLang).then(() => {
        loadData();
        initLanguageSelector();
    });
});

/**
 * API'den veri yükle
 */
async function loadData() {
    const loadingEl = document.getElementById('loading');
    const errorEl = document.getElementById('error');
    const errorMessageEl = document.getElementById('error-message');
    const containerEl = document.getElementById('container');

    try {
        loadingEl.style.display = 'block';
        errorEl.style.display = 'none';
        containerEl.style.display = 'none';

        const response = await fetch('api.php');
        const result = await response.json();

        if (!result.success) {
            throw new Error(result.error || t('error_message'));
        }

        loadingEl.style.display = 'none';
        containerEl.style.display = '';

        renderData(result.data);

    } catch (error) {
        loadingEl.style.display = 'none';
        errorEl.style.display = 'block';
        errorMessageEl.textContent = error.message;
    }
}

/**
 * Verileri ekrana render et
 */
function renderData(data) {
    const localColumn = document.getElementById('local-column');
    const remoteColumn = document.getElementById('remote-column');

    // Config bilgileri
    const localHost = data.config.local.host + ':' + data.config.local.port;
    const remoteHost = data.config.remote.host + ':' + data.config.remote.port;

    // Column headers
    localColumn.innerHTML = `<h2 class="column-header"><span uk-icon="icon: laptop; ratio: 1.2"></span> ${t('local_database')} <span class="hostname">(${escapeHtml(localHost)})</span></h2>`;
    remoteColumn.innerHTML = `<h2 class="column-header"><span uk-icon="icon: server; ratio: 1.2"></span> ${t('remote_database')} <span class="hostname">(${escapeHtml(remoteHost)})</span></h2>`;

    // Local schemas
    renderSchemas(
        localColumn,
        data.local,
        data.remote,
        data.schemaStatuses,
        'local'
    );

    // Remote schemas
    renderSchemas(
        remoteColumn,
        data.remote,
        data.local,
        data.schemaStatuses,
        'remote'
    );
}

/**
 * Şemaları render et
 */
function renderSchemas(container, schemas, compareSchemas, schemaStatuses, side) {
    for (const [schemaName, tables] of Object.entries(schemas)) {
        const isOk = schemaStatuses[schemaName];
        const statusIconClass = isOk ? 'status-ok' : 'status-error';

        const schemaDiv = document.createElement('div');
        schemaDiv.className = 'schema-item';
        schemaDiv.setAttribute('data-schema-name', schemaName);
        schemaDiv.setAttribute('data-side', side);

        // Header
        const headerDiv = document.createElement('div');
        headerDiv.className = 'schema-header-custom' + (isOk ? '' : ' schema-diff');
        headerDiv.onclick = function() {
            toggleSchema(schemaDiv, schemaName, side);
        };

        headerDiv.innerHTML = `
            <span class="schema-icon" uk-icon="icon: list"></span>
            <span class="schema-name">${escapeHtml(schemaName)}</span>
            <span class="schema-count">${Object.keys(tables).length} ${t('tables')}</span>
            <span class="schema-status ${statusIconClass}" uk-icon="icon: ${isOk ? 'check' : 'close'}"></span>
        `;

        // Content
        const contentDiv = document.createElement('div');
        contentDiv.className = 'schema-content-custom';

        // Tables
        for (const [tableName, columns] of Object.entries(tables)) {
            const tableCard = renderTable(
                schemaName,
                tableName,
                columns,
                compareSchemas,
                side
            );
            contentDiv.appendChild(tableCard);
        }

        schemaDiv.appendChild(headerDiv);
        schemaDiv.appendChild(contentDiv);
        container.appendChild(schemaDiv);
    }
}

/**
 * Şema toggle fonksiyonu - accordion ve senkronize davranış
 */
function toggleSchema(schemaDiv, schemaName, side) {
    const content = schemaDiv.querySelector('.schema-content-custom');
    const isCurrentlyOpen = content.classList.contains('uk-open');
    const oppositeSide = side === 'local' ? 'remote' : 'local';

    // Aynı taraftaki tüm şemaları kapat
    const sameSideSchemas = document.querySelectorAll(`.schema-item[data-side="${side}"]`);
    sameSideSchemas.forEach(item => {
        const itemContent = item.querySelector('.schema-content-custom');
        itemContent.classList.remove('uk-open');
    });

    // Karşı taraftaki tüm şemaları kapat
    const oppositeSideSchemas = document.querySelectorAll(`.schema-item[data-side="${oppositeSide}"]`);
    oppositeSideSchemas.forEach(item => {
        const itemContent = item.querySelector('.schema-content-custom');
        itemContent.classList.remove('uk-open');
    });

    // Eğer kapalıysa, şimdi aç (hem bu tarafta hem karşı tarafta)
    if (!isCurrentlyOpen) {
        // Bu tarafı aç
        content.classList.add('uk-open');

        // Karşı taraftaki aynı şemayı bul ve aç
        const oppositeSchema = document.querySelector(`.schema-item[data-side="${oppositeSide}"][data-schema-name="${schemaName}"]`);
        if (oppositeSchema) {
            const oppositeContent = oppositeSchema.querySelector('.schema-content-custom');
            oppositeContent.classList.add('uk-open');
        }
    }
}

/**
 * Tablo render et
 */
function renderTable(schemaName, tableName, columns, compareSchemas, side) {
    const tableDiv = document.createElement('div');
    tableDiv.className = 'table-card';

    const compareTable = compareSchemas[schemaName]?.[tableName];
    const tableClass = getTableClass(columns, compareTable, side);

    if (tableClass) {
        tableDiv.classList.add(tableClass);
    }

    // Table header
    const headerDiv = document.createElement('div');
    headerDiv.className = 'table-card-header';
    headerDiv.innerHTML = `
        <span uk-icon="icon: table; ratio: 0.8"></span>
        ${escapeHtml(tableName)}
    `;
    tableDiv.appendChild(headerDiv);

    // Table body
    const bodyDiv = document.createElement('div');
    bodyDiv.className = 'table-card-body';

    for (const column of columns) {
        const columnDiv = document.createElement('div');
        columnDiv.className = 'column-item';

        const columnClass = getColumnClass(column, compareTable, side);
        if (columnClass !== 'normal') {
            columnDiv.classList.add(columnClass);
        }

        columnDiv.innerHTML = `
            <span class="column-name">${escapeHtml(column.name)}</span>
            <span class="column-type">${escapeHtml(column.type)}</span>
        `;

        bodyDiv.appendChild(columnDiv);
    }

    tableDiv.appendChild(bodyDiv);
    return tableDiv;
}

/**
 * Tablo class'ını belirle
 */
function getTableClass(columns, compareTable, side) {
    if (!compareTable) {
        return side === 'local' ? 'missing-table' : 'extra-table';
    }

    // Karşılaştır
    if (JSON.stringify(columns) !== JSON.stringify(compareTable)) {
        return 'diff-table';
    }

    return '';
}

/**
 * Sütun class'ını belirle
 */
function getColumnClass(column, compareTable, side) {
    if (!compareTable) {
        return side === 'local' ? 'missing' : 'extra';
    }

    // Sütunu karşı tarafta ara
    let found = false;
    let typeDiff = false;

    for (const compareCol of compareTable) {
        if (compareCol.name === column.name) {
            found = true;
            if (compareCol.type !== column.type) {
                typeDiff = true;
            }
            break;
        }
    }

    if (!found || typeDiff) {
        return side === 'local' ? 'missing' : 'extra';
    }

    return 'normal';
}

/**
 * HTML escape
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Load translations from API
 */
async function loadTranslations(lang) {
    try {
        const response = await fetch(`lang.php?lang=${lang}`);
        const result = await response.json();

        if (result.success) {
            translations = result.translations;
            currentLang = result.lang;

            // Update HTML lang attribute
            document.documentElement.lang = currentLang;

            // Translate all elements with data-i18n attribute
            translatePage();
        }
    } catch (error) {
        console.error('Failed to load translations:', error);
        // Fallback to English
        translations = {};
    }
}

/**
 * Translate all elements on the page
 */
function translatePage() {
    document.querySelectorAll('[data-i18n]').forEach(element => {
        const key = element.getAttribute('data-i18n');
        if (translations[key]) {
            element.textContent = translations[key];
        }
    });
}

/**
 * Get translation by key
 */
function t(key) {
    return translations[key] || key;
}

/**
 * Initialize language selector
 */
function initLanguageSelector() {
    const selector = document.getElementById('language-selector');

    if (selector) {
        // Set current language
        selector.value = currentLang;

        // Add change event listener
        selector.addEventListener('change', async function() {
            const newLang = this.value;

            // Save to localStorage
            localStorage.setItem('lang', newLang);

            // Load new translations
            await loadTranslations(newLang);

            // Reload data to apply translations
            loadData();
        });
    }
}
