document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('dependent-form');
    document.querySelectorAll('.dropdown-toggle-manual').forEach(btn => {
        btn.addEventListener('click', function (e) {
            const id = this.dataset.dropdownId;
            const menu = document.getElementById(`dropdown-${id}`);
            menu.classList.toggle('show');
        });
    });

    document.addEventListener('click', function (e) {
        document.querySelectorAll('.manual-dropdown').forEach(menu => {
            if (!menu.contains(e.target) && !menu.previousElementSibling.contains(e.target)) {
                menu.classList.remove('show');
            }
        });
    });

    document.querySelectorAll('.keep-open').forEach(cb => {
        cb.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    });

    document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
        cb.addEventListener('change', function () {
            const currentLevel = parseInt(this.dataset.levelIndex);
            document.querySelectorAll('input[type="checkbox"]').forEach(otherCb => {
                if (parseInt(otherCb.dataset.levelIndex) > currentLevel) {
                    otherCb.checked = false;
                }
            });
        });
    });

    document.querySelectorAll('.select-all').forEach(btn => {
        btn.addEventListener('click', function () {
            const levelCode = this.dataset.level;
            document.querySelectorAll(`input[name="${levelCode}[]"]`).forEach(cb => cb.checked = true);
        });
    });

    document.querySelectorAll('.clear-all').forEach(btn => {
        btn.addEventListener('click', function () {
            const currentLevelIndex = parseInt(this.dataset.levelIndex);
            const levelCode = this.dataset.level;
            document.querySelectorAll(`input[name="${levelCode}[]"]`).forEach(cb => cb.checked = false);
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                if (parseInt(cb.dataset.levelIndex) > currentLevelIndex) {
                    cb.checked = false;
                }
            });
        });
    });
});