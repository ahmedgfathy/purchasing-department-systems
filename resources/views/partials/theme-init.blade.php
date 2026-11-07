<script>
    (function () {
        try {
            if (localStorage.getItem('ppd-theme') === 'light') {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        } catch (e) {
            // Ignore storage errors (private mode, etc.).
        }
    })();
</script>
