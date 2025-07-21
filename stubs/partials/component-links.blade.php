<!-- Component link styles -->
<style>
.component-link {
    color: #0d6efd;
    text-decoration: none;
    border-bottom: 1px dotted #0d6efd;
    transition: all 0.2s ease;
}

.component-link:hover {
    color: #0a58ca;
    text-decoration: none;
    border-bottom: 1px solid #0a58ca;
    background-color: rgba(13, 110, 253, 0.1);
    padding: 0 2px;
    border-radius: 2px;
}

.component-link::before {
    content: "🔗 ";
    opacity: 0.6;
    font-size: 0.8em;
}

.badge .component-link {
    color: inherit;
    border-bottom: 1px dotted rgba(255,255,255,0.6);
}

.badge .component-link:hover {
    background-color: rgba(255,255,255,0.2);
}
</style>
