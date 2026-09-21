const officeButtons = Array.from(root.querySelectorAll('[data-gocf-office]'));
const officePanels = Array.from(root.querySelectorAll('[data-gocf-panel]'));

function selectOffice(office) {
    officeButtons.forEach(function (button) {
        button.classList.toggle('is-active', button.dataset.gocfOffice === office);
    });

    officePanels.forEach(function (panel) {
        panel.hidden = panel.dataset.gocfPanel !== office;
    });
}

officeButtons.forEach(function (button) {
    button.addEventListener('click', function () {
        selectOffice(button.dataset.gocfOffice);
    });
});

root.querySelector('form')?.addEventListener('submit', function (event) {
    event.preventDefault();
});
