function waitForImages(slider) {
    const images = slider.querySelectorAll('img');
    const promises = [];

    images.forEach(img => {
        if (img.complete) return;

        promises.push(new Promise(resolve => {
            img.addEventListener('load', resolve, { once: true });
            img.addEventListener('error', resolve, { once: true });
        }));
    });

    return Promise.all(promises);
}

function setSliderHeight(slider) {
    const slides = slider.querySelectorAll('.opiner-me-slide');
    let maxHeight = 0;

    slider.style.height = 'auto';

    slides.forEach(slide => {
        slide.style.height = 'auto';
    });

    slides.forEach(slide => {
        const inner = slide.querySelector('.opiner-me-slide-inner');
        const h = inner ? inner.offsetHeight : slide.offsetHeight;

        if (h > maxHeight) maxHeight = h;
    });

    slides.forEach(slide => {
        slide.style.height = maxHeight + 'px';
    });

    slider.style.height = (maxHeight + 40) + 'px';
}

function goToSlide(slider, index) {
    const slides = slider.querySelectorAll('.opiner-me-slide');
    const dots = slider.querySelectorAll('.opiner-me-slider-dot');
    const track = slider.querySelector('.opiner-me-slider-track');
    const total = slides.length;

    if (!total) return;

    const safeIndex = ((index % total) + total) % total;

    slides.forEach(s => s.classList.remove('active'));
    slides[safeIndex].classList.add('active');

    if (track && !slider.classList.contains('opiner-me-fade-mode')) {
        track.style.transform = `translateX(-${safeIndex * 100}%)`;
    }

    dots.forEach(d => d.classList.remove('active'));
    if (dots[safeIndex]) dots[safeIndex].classList.add('active');

    slider.dataset.current = String(safeIndex);
}

function initArrows(slider, controlAutoplay) {
    const prev = slider.querySelector('.opiner-me-slider-arrow.prev');
    const next = slider.querySelector('.opiner-me-slider-arrow.next');
    const slides = slider.querySelectorAll('.opiner-me-slide');

    if (!prev && !next) return;

    const getCurrent = () => parseInt(slider.dataset.current || '0', 10) || 0;

    prev?.addEventListener('click', () => {
        controlAutoplay.stopAndResume();

        const current = getCurrent();
        const target = (current - 1 + slides.length) % slides.length;

        goToSlide(slider, target);
    });

    next?.addEventListener('click', () => {
        controlAutoplay.stopAndResume();

        const current = getCurrent();
        const target = (current + 1) % slides.length;

        goToSlide(slider, target);
    });
}

function initDots(slider, controlAutoplay) {
    const dotsContainer = slider.querySelector('.opiner-me-slider-dots');
    const slides = slider.querySelectorAll('.opiner-me-slide');

    if (!dotsContainer) return;

    dotsContainer.innerHTML = '';

    slides.forEach((_, i) => {
        const dot = document.createElement('span');
        dot.className = 'opiner-me-slider-dot';
        dot.dataset.index = String(i);

        if (i === 0) dot.classList.add('active');

        dot.addEventListener('click', () => {
            controlAutoplay.stopAndResume();
            goToSlide(slider, i);
        });

        dotsContainer.appendChild(dot);
    });
}

function initSwipe(slider, controlAutoplay) {
    let startX = 0;
    let currentX = 0;
    let isDown = false;
    const threshold = 40;

    const slides = slider.querySelectorAll('.opiner-me-slide');

    const getCurrent = () => parseInt(slider.dataset.current || '0', 10) || 0;

    function onStart(e) {
        if (e.target.closest('.opiner-me-slider-arrow') ||
            e.target.closest('.opiner-me-slider-dot')) {
            return;
        }

        isDown = true;
        controlAutoplay.stop();

        startX = e.touches ? e.touches[0].clientX : e.clientX;
        currentX = startX;
    }

    function onMove(e) {
        if (!isDown) return;

        currentX = e.touches ? e.touches[0].clientX : e.clientX;
    }

    function onEnd() {
        if (!isDown) return;

        isDown = false;
        const diff = currentX - startX;
        startX = 0;
        currentX = 0;

        if (Math.abs(diff) < threshold) {
            controlAutoplay.start();

            return;
        }

        let current = getCurrent();

        if (diff < 0) {
            current = (current + 1) % slides.length;
        } else {
            current = (current - 1 + slides.length) % slides.length;
        }

        goToSlide(slider, current);
        controlAutoplay.stopAndResume();
    }

    slider.addEventListener('touchstart', onStart, { passive: true });
    slider.addEventListener('touchmove', onMove, { passive: true });
    slider.addEventListener('touchend', onEnd);

    slider.addEventListener('mousedown', onStart);
    slider.addEventListener('mousemove', onMove);
    slider.addEventListener('mouseup', onEnd);
    slider.addEventListener('mouseleave', () => {
        if (isDown) {
            isDown = false;
            controlAutoplay.start();
        }
    });
}

function createAutoplayController(slider) {
    const interval = parseInt(slider.dataset.autoplay || '0', 10);

    if (!interval || interval < 500) {
        return {
            start: () => {},
            stop: () => {},
            stopAndResume: () => {}
        };
    }

    const slides = slider.querySelectorAll('.opiner-me-slide');
    let timer = null;
    let resumeTimer = null;

    const getCurrent = () => parseInt(slider.dataset.current || '0', 10) || 0;

    function nextSlide() {
        let current = getCurrent();
        current = (current + 1) % slides.length;

        goToSlide(slider, current);
    }

    function start() {
        if (timer) return;

        timer = setInterval(nextSlide, interval);
    }

    function stop() {
        if (timer) {
            clearInterval(timer);
            timer = null;
        }

        if (resumeTimer) {
            clearTimeout(resumeTimer);
            resumeTimer = null;
        }
    }

    function stopAndResume() {
        stop();

        resumeTimer = setTimeout(start, 3000);
    }

    slider.addEventListener('pointerdown', stopAndResume);
    slider.addEventListener('touchstart', stopAndResume, { passive: true });
    slider.addEventListener('mousedown', stopAndResume);
    slider.addEventListener('click', stopAndResume);

    slider.addEventListener('mouseenter', stop);
    slider.addEventListener('mouseleave', start);

    start();

    return { start, stop, stopAndResume };
}

function initOpinerSliders() {
    const sliders = document.querySelectorAll('.opiner-me-slider');
    let resizeTimer = null;

    sliders.forEach(slider => {
        const slides = slider.querySelectorAll('.opiner-me-slide');

        if (!slides.length) return;

        slider.dataset.current = '0';
        slides[0].classList.add('active');

        const autoplay = createAutoplayController(slider);

        initArrows(slider, autoplay);
        initDots(slider, autoplay);
        initSwipe(slider, autoplay);

        waitForImages(slider).then(() => {
            setSliderHeight(slider);
            slider.classList.add('ready');
        });
    });

    function recalcAllSliders() {
        document.querySelectorAll('.opiner-me-slider').forEach(slider => {
            setSliderHeight(slider);
        });
    }

    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(recalcAllSliders, 150);
    });

    window.addEventListener('load', recalcAllSliders);
}

document.addEventListener('DOMContentLoaded', initOpinerSliders);
