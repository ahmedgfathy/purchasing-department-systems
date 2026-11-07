<button
    type="button"
    class="icon-btn theme-toggle"
    data-theme-toggle
    aria-label="{{ __('app.theme.toggle') }}"
    title="{{ __('app.theme.toggle') }}"
>
    <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
    </svg>
    <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
        <circle cx="12" cy="12" r="4"></circle>
        <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path>
    </svg>
</button>
<script>
    document.querySelectorAll('[data-theme-toggle]').forEach(function (button) {
        if (button.dataset.bound) {
            return;
        }

        button.dataset.bound = '1';
        button.addEventListener('click', function () {
            var root = document.documentElement;
            var isLight = root.getAttribute('data-theme') === 'light';

            if (isLight) {
                root.removeAttribute('data-theme');
            } else {
                root.setAttribute('data-theme', 'light');
            }

            try {
                localStorage.setItem('ppd-theme', isLight ? 'dark' : 'light');
            } catch (e) {
                // Ignore storage errors.
            }
        });
    });
</script>
