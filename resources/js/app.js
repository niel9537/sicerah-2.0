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

Alpine.data('publicationCarousel', () => ({
    previous: false,
    next: false,
    hovered: false,
    focused: false,
    playing: !window.matchMedia('(prefers-reduced-motion: reduce)').matches,
    timer: null,
    init() {
        this.$nextTick(() => this.update());
        this.timer = window.setInterval(() => {
            if (!this.playing || this.hovered || this.focused || document.hidden) return;
            const track = this.$refs.track;
            if (!track || track.scrollWidth <= track.clientWidth + 2) return;
            this.update();
            if (this.next) this.move(1);
            else track.scrollTo({ left: 0, behavior: 'smooth' });
        }, 3500);
    },
    update() {
        const track = this.$refs.track;
        if (!track) return;
        this.previous = track.scrollLeft > 2;
        this.next = track.scrollLeft + track.clientWidth < track.scrollWidth - 2;
    },
    move(direction) {
        const track = this.$refs.track;
        if (!track) return;
        const cards = track.children;
        const step = cards.length > 1 ? cards[1].offsetLeft - cards[0].offsetLeft : track.clientWidth;
        track.scrollBy({
            left: direction * step,
            behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth',
        });
    },
    destroy() {
        window.clearInterval(this.timer);
    },
}));

Alpine.start();
