<script>
    function showPage(pageId) {
        // Hide all pages
        document.querySelectorAll('.page').forEach(page => {
            page.classList.remove('active');
        });
        
        // Remove active class from all nav items
        document.querySelectorAll('.nav-item').forEach(item => {
            item.classList.remove('active');
        });
        
        // Show selected page
        document.getElementById(pageId).classList.add('active');
        
        // Add active class to clicked nav item
        event.target.classList.add('active');
    }
    
    function showRouteDetails(routeName) {
        showPage('routes');
        // Additional logic to highlight specific route
    }
    
    function showCommandDetails(commandName) {
        showPage('commands');
        // Additional logic to highlight specific command
    }
</script>
