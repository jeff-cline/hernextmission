/**
 * Rocket-launch animation.
 *
 * Activates the hero logo's launch animation by adding a data attribute on
 * page ready. Uses a tiny delay so the animation feels intentional. Honors
 * prefers-reduced-motion via the CSS keyframes.
 *
 * The animation is triggered exactly once per page load. We do NOT replay
 * it on scroll or route change to keep it feeling like a launch event.
 */

(function () {
    'use strict';

    function launch() {
        var stages = document.querySelectorAll('[data-hnm-rocket]');
        if (stages.length === 0) {
            return;
        }
        // Force a reflow so the keyframe starts fresh.
        stages.forEach(function (stage) {
            // eslint-disable-next-line no-unused-expressions
            stage.offsetWidth;
            stage.setAttribute('data-hnm-rocket-launched', 'true');
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            window.requestAnimationFrame(launch);
        });
    } else {
        window.requestAnimationFrame(launch);
    }
})();
