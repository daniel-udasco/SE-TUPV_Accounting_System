        </div> <!-- End of content-area -->
        
        <!-- Footer -->
        <footer class="footer">
            <div class="footer-content">
                <p>&copy; 2026 Technological University of the Philippines Visayas. All rights reserved.</p>
                <div class="social-links">
                    <a href="https://www.facebook.com/tupvbusinessoffice" target="_blank" title="Facebook"><i class="ph ph-facebook-logo"></i></a>
                    <a href="https://www.messenger.com/tupvbusinessoffice/" target="_blank" title="Messenger"><i class="ph ph-messenger-logo"></i></a>
                    <a href="mailto:accountingtupv@gmail.com" title="Email"><i class="ph ph-envelope-simple"></i></a>
                    <a href="tel:0344452177" title="Phone: (034) 445 2177"><i class="ph ph-phone"></i></a>
                </div>
            </div>
        </footer>
    </main>
</div> <!-- End of wrapper -->

<script>
    // Dark Mode Toggle Logic
    const themeToggle = document.getElementById('themeToggle');
    const htmlElement = document.documentElement;
    
    // Check local storage for theme preference
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
</script>

</body>
</html>
