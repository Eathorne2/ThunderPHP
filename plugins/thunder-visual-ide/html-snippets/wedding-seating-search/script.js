const assignments = {
    'chanda bwalya': 'Table 04 · Garden Room',
    'daniel cole': 'Table 02 · Garden Room',
    'lena mwansa': 'Table 01 · Garden Room',
    'michael grant': 'Table 06 · Garden Room'
};

const input = root.querySelector('[data-input]');
const result = root.querySelector('[data-result]');

function findTable() {
    const query = input.value.trim().toLowerCase();
    const match = Object.entries(assignments).find(([name]) => name.includes(query) && query.length > 1);

    result.textContent = match
        ? `${match[0].replace(/\b\w/g, letter => letter.toUpperCase())}: ${match[1]}`
        : 'We could not find that name. Please check the spelling or ask the welcome team.';
}

root.querySelector('[data-search]')?.addEventListener('click', findTable);
input?.addEventListener('keydown', event => {
    if (event.key === 'Enter') {
        findTable();
    }
});
