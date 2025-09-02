<?php if (!isset($_SESSION['user_id'])): // Cierre de divs para páginas sin login ?>
                </div> <!-- Cierre de .login-container -->
            </div> <!-- Cierre de .login-wrapper -->
        <?php else: ?>
            </div> <!-- Cierre de .content-body -->
            <footer>
                <p>&copy; <?php echo date('Y'); ?> <?php echo defined('SITE_NAME') ? SITE_NAME : ''; ?>. Todos los derechos reservados.</p>
            </footer>
        <?php endif; ?>
    </div> <!-- Cierre de .main-content -->
</div> <!-- Cierre de .app-wrapper -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
    // Script para manejar los dropdowns del menú lateral
    document.addEventListener('DOMContentLoaded', function() {
        const dropdowns = document.querySelectorAll('.sidebar .nav-dropdown > a');
        dropdowns.forEach(function(dropdown) {
            dropdown.addEventListener('click', function(e) {
                e.preventDefault();
                this.parentElement.classList.toggle('open');
            });
        });
    });
</script>
</body>
</html>
