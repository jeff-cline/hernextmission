/* HER NEXT MISSION — site.js  (v1.0)
 *
 * Vanilla JS, no build step. Three jobs:
 *   1. Rocket-launch animation on hero load
 *   2. Mobile nav toggle
 *   3. data-cta="<slug>" → mailto: href with structured intake body
 */

(function () {
    'use strict';

    /* ---------- 1. rocket launch ---------- */

    function launchRocket() {
        var stages = document.querySelectorAll('[data-rocket]');
        stages.forEach(function (s) {
            // force reflow so animation triggers fresh
            // eslint-disable-next-line no-unused-expressions
            s.offsetWidth;
            s.setAttribute('data-launched', 'true');
        });
    }

    /* ---------- 2. mobile nav ---------- */

    function wireMobileNav() {
        var btn = document.querySelector('[data-nav-toggle]');
        var nav = document.querySelector('[data-nav]');
        if (!btn || !nav) return;
        btn.addEventListener('click', function () {
            var open = nav.classList.toggle('is-open');
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    /* ---------- 3. mailto CTA helper ---------- */

    var EMAIL = 'krystalore@thecrewscoach.com';
    var CONTACT_BLOCK = [
        '',
        '— Your contact info —',
        'Name:',
        'Best phone:',
        'Best email:',
        'City / time zone:',
        'Best time to reach you:',
        '',
        'Thank you. — Sent via hernextmission.org'
    ].join('\n');

    var CATALOG = {
        'beneficiary-intake': {
            title: 'For Women in Transition',
            intro: "I'm a woman in (or near) transition out of military service or first responder service, and I'd like to know more about Her Next Mission.",
            questions: [
                'Branch / agency you served (e.g., Air Force, Army, Fire, EMS, Police):',
                'Years of service and approximate transition / retirement date:',
                'What does "transition" look like for you right now — discharged, retiring, medically separating, or already out?',
                'What is the hardest part of this season for you (identity, work, health, family, finances, purpose)?',
                'Have you worked with a transition coach, peer-support program, or Veteran-focused therapist before?',
                'What outcome would make the next 90 days feel like a win?',
                'Are you open to coaching, a retreat, the bootcamp cohort, or all three?'
            ]
        },
        'sponsor-inquiry': {
            title: 'For Sponsors',
            intro: "I'd like to learn more about partnering with Her Next Mission as a sponsor.",
            questions: [
                'Your company / organization name and your role:',
                'Sponsor tier you are exploring (Featured · Lead · Mission Partner · custom):',
                'Decision timeline — when would you want a partnership to launch?',
                'Activation channels of interest (live events, podcast reads, Summit naming, retreat sponsorship, corporate consulting track):',
                'Audience/markets your brand cares most about reaching (women Veterans, women first responders, military spouses, transitioning officers, etc.):',
                'Any DEI, ESG, or Veteran-impact reporting requirements we should know about?',
                'Have you sponsored Veteran or women-led nonprofits before? Anything you loved or would change?'
            ]
        },
        'donor-inquiry': {
            title: 'Donate',
            intro: "I'd like to give to Her Next Mission. Please send me information on how to donate.",
            questions: [
                'Approximate gift size you are considering (one-time or recurring):',
                'Restricted vs. unrestricted — do you want to fund coaching, scholarships, retreats, the Summit, or wherever the need is greatest?',
                'Are you donating personally, on behalf of a family foundation, a DAF (donor-advised fund), or a corporation?',
                'Do you need a tax receipt and our 501(c)(3) status letter?',
                'Would you like to be acknowledged publicly (named gift) or remain anonymous?',
                'Are you open to recurring monthly giving?',
                'Anything else we should know about why this mission matters to you?'
            ]
        },
        'sponsor-deck': {
            title: 'Request Sponsor Deck',
            intro: "Please send me the Her Next Mission sponsor deck.",
            questions: [
                'Your company / organization and your role:',
                'Sponsor tier(s) of interest (Featured · Lead · Mission Partner · custom):',
                'Use of the deck (executive review, board meeting, marketing committee):',
                'Timeline for a sponsorship decision:',
                'Any specific activations you want covered (live events, podcast, Summit, retreats, corporate consulting):',
                'Preferred next step — a 30-min intro call, written proposal, or both?'
            ]
        },
        'explore-programs': {
            title: 'Explore Programs',
            intro: "I'd like to learn more about the programs Her Next Mission offers.",
            questions: [
                'Branch / agency you served and years of service:',
                'Where you are in your transition (still in, recently out, years out and stuck):',
                'Program(s) you\'re curious about (1‑on‑1 coaching, bootcamp, retreat, Summit, podcast, all of it):',
                'What you\'re hoping to get out of working with us — in plain language:',
                'Any logistics that matter (location, time zone, mobility, childcare, scheduling around shift work):',
                'Best way to reach you and best time of day:'
            ]
        },
        'coaching-1on1': {
            title: '1-on-1 Coaching',
            intro: "I'd like to learn more about 1-on-1 coaching with Her Next Mission.",
            questions: [
                'Branch / agency and years of service:',
                'What season of transition are you in?',
                'What\'s the work you want to do — identity, career, leadership, somatic / nervous system, business launch, all of the above?',
                'Have you worked with a coach or therapist before? What worked, what didn\'t?',
                'Frequency you can commit to (weekly, bi-weekly, monthly):',
                'Are you open to homework, journaling, and embodied practices between sessions?',
                'Anything that would make a coaching container feel safe and effective for you?'
            ]
        },
        'bootcamp': {
            title: 'Group Bootcamps',
            intro: "I'm interested in the next Her Next Mission bootcamp cohort.",
            questions: [
                'Branch / agency and years of service:',
                'Which bootcamp interests you (clarity, business launch, leadership, all):',
                'Why now — what\'s pushing you to enroll this season?',
                'Have you done a cohort program before (military school, MBA, mastermind)?',
                'What outcome would make the cohort worth it for you?',
                'Time-zone, schedule constraints, and any travel limitations:'
            ]
        },
        'retreats': {
            title: 'Retreats',
            intro: "I'd like more information on Her Next Mission retreats.",
            questions: [
                'Branch / agency and years of service:',
                'Which retreat are you drawn to (or do you want our recommendation)?',
                'Any physical considerations we should plan around (injuries, mobility, dietary)?',
                'What kind of experience are you looking for — rest, breakthrough, sisterhood, all three?',
                'Are you traveling solo or with a friend / battle buddy?',
                'Any scholarship support needed? (We never want cost to be the gate.)',
                'Preferred dates / blackout dates over the next 12 months:'
            ]
        },
        'summit': {
            title: 'Summit & Events',
            intro: "I'd like to know more about the Her Next Mission Summit and live events.",
            questions: [
                'Are you interested as an attendee, speaker, sponsor, or volunteer?',
                'Branch / agency or the perspective you would bring (if speaking):',
                'Any topics you want to see covered or that you would lead a session on?',
                'Travel city you would fly from (helps us with regional events):',
                'Scholarship or stipend support needed?',
                'Preferred format (in person, virtual, hybrid):'
            ]
        },
        'podcast-guest': {
            title: 'Submit Your Story',
            intro: "I'd like to share my story on the Her Next Mission podcast.",
            questions: [
                'Branch / agency and years of service:',
                'In one sentence — what is the story you want to tell?',
                'Why now? What changed that made you want to share it?',
                'Three to five things you would never want to be asked about on air:',
                'Topics you can speak on with depth (transition, mental health, leadership, business, identity, faith, family):',
                'Have you been on podcasts or done media before? (Not required.)',
                'Links to anything that gives Krystalore a feel for you (LinkedIn, IG, prior interview, blog):'
            ]
        },
        'book-a-call': {
            title: 'Book a Call',
            intro: "I'd like to book a call with Krystalore.",
            questions: [
                'What\'s on your mind? (Coaching, sponsorship, donation, podcast, speaking, press, other.)',
                'Branch / agency / role (if relevant):',
                'Top 1–2 things you\'d want to walk away with from a call:',
                'Best three windows of time over the next two weeks (with your time zone):',
                'Phone or video preference:'
            ]
        },
        'volunteer': {
            title: 'Volunteer',
            intro: "I'd like to volunteer with Her Next Mission.",
            questions: [
                'Branch / agency / background (if relevant):',
                'Skills you would love to use (events, coaching, comms, fundraising, ops, design, web, finance, legal):',
                'Hours per month you can realistically commit:',
                'Are you open to remote, in-person, or both?',
                'Any specific cause-area you care about most (transition, retreats, podcast, Summit)?',
                'Have you volunteered with a nonprofit before? Tell us a sentence about it.'
            ]
        },
        'press-media': {
            title: 'Media & Press',
            intro: "I'm a journalist / producer / podcast host reaching out for a story.",
            questions: [
                'Outlet or show name and your role:',
                'Topic / angle of the story:',
                'Deadline:',
                'Format (print, broadcast, podcast, panel, written quote):',
                'Are you looking for Krystalore specifically, or open to a beneficiary or board member?',
                'Will images or B-roll be needed?'
            ]
        },
        'contact': {
            title: 'Contact',
            intro: "Hi Krystalore — I'd like to get in touch.",
            questions: [
                'What\'s your reason for reaching out?',
                'Any context you want me to know up front:',
                'How soon do you need a reply?'
            ]
        },
        'newsletter': {
            title: 'Join the Newsletter',
            intro: "I'd like to join the Her Next Mission newsletter list.",
            questions: [
                'Are you a transitioning servicemember, a sponsor, a donor, or just a supporter?',
                'Are you OK with monthly mission updates and occasional event invites?'
            ]
        }
    };

    function buildBody(slug) {
        var c = CATALOG[slug] || CATALOG['contact'];
        var body = c.intro + '\n\n' + 'A few questions so we can come back ready:\n\n';
        c.questions.forEach(function (q) { body += '• ' + q + '\n\n'; });
        body += CONTACT_BLOCK;
        return body;
    }

    function buildHref(slug) {
        var c = CATALOG[slug] || CATALOG['contact'];
        var subject = c.title + ' - HER NEXT MISSION';
        var qs = 'subject=' + encodeURIComponent(subject) + '&body=' + encodeURIComponent(buildBody(slug));
        return 'mailto:' + EMAIL + '?' + qs;
    }

    function wireCTAs() {
        document.querySelectorAll('[data-cta]').forEach(function (a) {
            var slug = a.getAttribute('data-cta');
            a.setAttribute('href', buildHref(slug));
        });
    }

    /* ---------- bootstrap ---------- */

    function init() {
        window.requestAnimationFrame(launchRocket);
        wireMobileNav();
        wireCTAs();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
