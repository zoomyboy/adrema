export default {
    main: {
        open: false,
        links: [
            { icon: 'home', href: '/', title: 'Dashboard' },
        ]
    },
    footer: {
        open: false,
        links: [
            { icon: 'user', href: '/user', title: 'Benutzer' },
            { icon: 'users', href: '/group', title: 'Gruppen' },
            { icon: 'cogs', href: '/config', title: 'Konfiguration' },
            { icon: 'sign-out', href: '/logout', title: 'Abmelden' }
        ]
    },
    profileToolbar: {
        open: false,
        links: [
            { icon: 'user', href: '/profile', title: 'Meine Daten' }
        ]
    },
    adminToolbar: {
        open: false,
        links: [
            { icon: 'key', href: '/setting', title: 'Einstellungen' },
            { icon: 'user', href: '/profile', title: 'Meine Einstellungen' }
        ]
    }
};
