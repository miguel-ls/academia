<?php
$base_url = $base_url ?? '';
?>
<?php if (!isset($_SESSION['user_id'])): // Cierre de divs para páginas sin login ?>
                </div> <!-- Cierre de .login-container -->
            </div> <!-- Cierre de .login-wrapper -->
        <?php else: ?>
            </div> <!-- Cierre de .content-body -->
            <footer>
                <p>&copy; <?php echo date('Y'); ?> <?php echo defined('CORP_NAME') ? CORP_NAME : ''; ?>. Todos los derechos reservados.</p>
            </footer>
        <?php endif; ?>
    </div> <!-- Cierre de .main-content -->
</div> <!-- Cierre de .app-wrapper -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="<?php echo $base_url; ?>public/assets/js/main.js"></script>
<script>
    // Script para manejar los dropdowns del menú lateral
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const appWrapper = document.querySelector('.app-wrapper');
        const sidebarStateKey = 'academia.sidebar.collapsed';

        if (sidebarToggle && appWrapper) {
            const isCollapsed = localStorage.getItem(sidebarStateKey) === 'true';
            appWrapper.classList.toggle('sidebar-collapsed', isCollapsed);
            sidebarToggle.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
            sidebarToggle.setAttribute('aria-label', isCollapsed ? 'Mostrar barra lateral' : 'Ocultar barra lateral');

            sidebarToggle.addEventListener('click', function() {
                const collapsed = appWrapper.classList.toggle('sidebar-collapsed');
                localStorage.setItem(sidebarStateKey, collapsed ? 'true' : 'false');
                sidebarToggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                sidebarToggle.setAttribute('aria-label', collapsed ? 'Mostrar barra lateral' : 'Ocultar barra lateral');
            });
        }

        const storageKey = 'academia.sidebar.openGroups';
        const menuGroups = document.querySelectorAll('.sidebar .nav-dropdown');
        let openGroups = [];

        try {
            openGroups = JSON.parse(localStorage.getItem(storageKey)) || [];
        } catch (error) {
            openGroups = [];
        }

        const currentView = new URLSearchParams(window.location.search).get('view') || 'dashboard';
        document.querySelectorAll('.sidebar a[href*="view="]').forEach(function(link) {
            if (new URL(link.href).searchParams.get('view') === currentView) {
                link.classList.add('active');
            }
        });

        menuGroups.forEach(function(group) {
            const toggle = group.querySelector(':scope > a');
            const menuId = group.dataset.menu;

            if (openGroups.includes(menuId) || group.querySelector('.dropdown-content a.active')) {
                group.classList.add('open');
            }

            toggle.setAttribute('role', 'button');
            toggle.setAttribute('aria-expanded', group.classList.contains('open') ? 'true' : 'false');
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                group.classList.toggle('open');
                toggle.setAttribute('aria-expanded', group.classList.contains('open') ? 'true' : 'false');
                openGroups = Array.from(menuGroups)
                    .filter(function(item) { return item.classList.contains('open'); })
                    .map(function(item) { return item.dataset.menu; });
                localStorage.setItem(storageKey, JSON.stringify(openGroups));
            });
        });
    });
</script>
</body>
</html>
