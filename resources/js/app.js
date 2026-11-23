import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('portalCarousel', (total) => ({
    slide: 0,
    total,
    paused: false,
    focused: false,
    timer: null,
    init() {
        if (this.total < 2) return;
        this.timer = window.setInterval(() => {
            if (!this.paused && !this.focused && !document.hidden) {
                this.slide = (this.slide + 1) % this.total;
            }
        }, 5000);
    },
    destroy() {
        window.clearInterval(this.timer);
    },
}));

Alpine.start();
