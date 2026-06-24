        </div>

        <footer class="footer admin-footer">
            <div class="footer-content">
                <p>&copy; 2026 Technological University of the Philippines Visayas. Accounting Office Admin.</p>
                <div class="social-links">
                    <a href="admin_transactions.php" title="Transactions"><i class="ph ph-receipt"></i></a>
                    <a href="admin_students.php" title="Students"><i class="ph ph-student"></i></a>
                    <a href="admin_feedback.php" title="Feedback"><i class="ph ph-chat-centered-text"></i></a>
                </div>
            </div>
        </footer>
    </main>
</div>

<script>
    const themeToggle = document.getElementById('themeToggle');
    const htmlElement = document.documentElement;
    const currentTheme = localStorage.getItem('theme') || 'light';
    htmlElement.setAttribute('data-theme', currentTheme);
    updateIcon(currentTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const newTheme = htmlElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
            htmlElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
        });
    }

    function updateIcon(theme) {
        if(themeToggle) {
            themeToggle.innerHTML = theme === 'light' ? '<i class="ph ph-moon"></i>' : '<i class="ph ph-sun"></i>';
        }
    }

    const profileToggle = document.getElementById('profileToggle');
    const profileDropdown = document.getElementById('profileDropdown');

    if (profileToggle && profileDropdown) {
        profileToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
        });

        document.addEventListener('click', (e) => {
            if (!profileToggle.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.remove('show');
            }
        });
    }
</script>
</body>
</html>
