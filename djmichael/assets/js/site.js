/* djmichael.com — small progressive enhancements. No dependencies. */
(function () {
  'use strict';

  var root = document.documentElement;
  var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

  /* ------------------------------------------------------------------------
     Mobile menu: aria-expanded, focus trap, Esc closes
     ------------------------------------------------------------------------ */
  function initMenu() {
    var toggle = document.querySelector('.menu-toggle');
    var menu = document.getElementById('site-menu');
    if (!toggle || !menu) return;

    var header = toggle.closest('.site-header');

    function focusables() {
      return Array.prototype.filter.call(
        header.querySelectorAll('a[href], button:not([disabled])'),
        function (el) { return el.offsetParent !== null; }
      );
    }

    function setOpen(open) {
      toggle.setAttribute('aria-expanded', String(open));
      menu.classList.toggle('is-open', open);
      document.body.classList.toggle('is-menu-open', open);
      if (open) {
        var first = menu.querySelector('a[href]');
        if (first) first.focus();
      }
    }

    toggle.addEventListener('click', function () {
      setOpen(toggle.getAttribute('aria-expanded') !== 'true');
    });

    document.addEventListener('keydown', function (e) {
      if (toggle.getAttribute('aria-expanded') !== 'true') return;
      if (e.key === 'Escape') {
        setOpen(false);
        toggle.focus();
      } else if (e.key === 'Tab') {
        var items = focusables();
        var first = items[0];
        var last = items[items.length - 1];
        if (e.shiftKey && document.activeElement === first) {
          e.preventDefault();
          last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
          e.preventDefault();
          first.focus();
        }
      }
    });

    // Close when a link inside the menu is followed, or the layout grows past mobile.
    menu.addEventListener('click', function (e) {
      if (e.target.closest('a')) setOpen(false);
    });
    window.matchMedia('(min-width: 48em)').addEventListener('change', function (mq) {
      if (mq.matches) setOpen(false);
    });
  }

  /* ------------------------------------------------------------------------
     Hero loop: only when the markup includes it and motion is welcome
     ------------------------------------------------------------------------ */
  function initHeroVideo() {
    var video = document.querySelector('[data-hero-video]');
    if (!video || reducedMotion.matches) return;
    video.querySelectorAll('source[data-src]').forEach(function (source) {
      source.src = source.getAttribute('data-src');
    });
    video.load();
    var playing = video.play();
    if (playing && playing.catch) playing.catch(function () { /* poster stays */ });
  }

  /* ------------------------------------------------------------------------
     Slideshow: native <dialog> over a CSS scroll-snap track. The browser
     handles swipe and momentum; this adds buttons, keys, the counter,
     loading nearby slides and pausing videos that leave the screen.
     ------------------------------------------------------------------------ */
  function initSlideshow() {
    var dialog = document.querySelector('[data-slideshow]');
    var links = Array.prototype.slice.call(document.querySelectorAll('[data-lightbox]'));
    if (!dialog || !links.length || typeof dialog.showModal !== 'function') return;

    var track = dialog.querySelector('[data-slideshow-track]');
    var count = dialog.querySelector('[data-slideshow-count]');
    var slides = [];
    var index = 0;
    var target = null; // slide a button or key is scrolling to
    var opener = null;

    function build() {
      links.forEach(function (link, i) {
        var slide = document.createElement('li');
        slide.className = 'slideshow__slide';
        slide.setAttribute('aria-roledescription', 'slide');
        slide.setAttribute('aria-label', (i + 1) + ' of ' + links.length);

        var thumb = link.querySelector('img');
        var media;
        if (link.getAttribute('data-lightbox') === 'video') {
          media = document.createElement('video');
          media.controls = true;
          media.playsInline = true;
          media.preload = 'none';
          media.poster = link.getAttribute('data-poster') || '';
          media.setAttribute('aria-label', thumb ? thumb.alt : '');
          // Never autoplays: the visitor presses play.
        } else {
          media = document.createElement('img');
          media.alt = thumb ? thumb.alt : '';
          media.decoding = 'async';
        }
        media.className = 'slideshow__media';
        media.width = link.getAttribute('data-width') || (thumb && thumb.width) || 0;
        media.height = link.getAttribute('data-height') || (thumb && thumb.height) || 0;
        media.setAttribute('data-src', link.getAttribute('href'));

        slide.appendChild(media);
        track.appendChild(slide);
        slides.push(media);
      });
    }

    // Load the current slide and its neighbours only.
    function loadAround(i) {
      [i - 1, i, i + 1].forEach(function (n) {
        var media = slides[(n + slides.length) % slides.length];
        var src = media.getAttribute('data-src');
        if (src) {
          media.src = src;
          media.removeAttribute('data-src');
        }
      });
    }

    function setIndex(i) {
      if (i === index && count.textContent) return;
      index = i;
      count.textContent = (index + 1) + ' / ' + links.length;
      loadAround(index);
      slides.forEach(function (media, n) {
        if (n !== index && media.tagName === 'VIDEO' && !media.paused) media.pause();
      });
    }

    function step(by) {
      scrollToIndex((target === null ? index : target) + by, true);
    }

    function scrollToIndex(i, smooth) {
      i = (i + links.length) % links.length;
      var instant = !smooth || reducedMotion.matches || Math.abs(i - index) > 1;
      target = instant ? null : i;
      track.scrollTo({ left: i * track.clientWidth, behavior: instant ? 'instant' : 'smooth' });
      setIndex(i);
    }

    // Swipes update the index; scrolls we started are ignored until they land.
    var ticking = false;
    track.addEventListener('scroll', function () {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(function () {
        ticking = false;
        var at = Math.round(track.scrollLeft / track.clientWidth);
        if (target !== null) {
          if (at === target && track.scrollLeft % track.clientWidth < 2) target = null;
          return;
        }
        setIndex(at);
      });
    }, { passive: true });
    track.addEventListener('scrollend', function () { target = null; });

    function open(i, link) {
      if (!slides.length) build();
      opener = link;
      dialog.showModal();
      count.textContent = '';
      scrollToIndex(i, false);
    }

    links.forEach(function (link, i) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        open(i, link);
      });
    });

    var all = document.querySelector('[data-gallery-all]');
    if (all) {
      all.hidden = false;
      all.querySelector('button').addEventListener('click', function () { open(0, this); });
    }

    dialog.querySelector('[data-slideshow-prev]').addEventListener('click', function () { step(-1); });
    dialog.querySelector('[data-slideshow-next]').addEventListener('click', function () { step(1); });
    dialog.querySelector('[data-slideshow-close]').addEventListener('click', function () { dialog.close(); });

    dialog.addEventListener('keydown', function (e) {
      if (e.target.tagName === 'VIDEO') return; // arrows seek inside a focused video
      if (e.key === 'ArrowLeft') { e.preventDefault(); step(-1); }
      if (e.key === 'ArrowRight') { e.preventDefault(); step(1); }
    });

    // A click on the dark area around the media closes.
    dialog.addEventListener('click', function (e) {
      if (e.target.classList.contains('slideshow__slide')) dialog.close();
    });

    // Keep the current slide in place when the window resizes or rotates.
    window.addEventListener('resize', function () {
      if (dialog.open) track.scrollTo({ left: index * track.clientWidth, behavior: 'instant' });
    });

    dialog.addEventListener('close', function () {
      slides.forEach(function (media) { if (media.tagName === 'VIDEO') media.pause(); });
      if (opener) opener.focus();
    });
  }

  /* ------------------------------------------------------------------------
     Contact form: inline validation, honeypot, prototype success state
     ------------------------------------------------------------------------ */
  var MESSAGES = {
    names: 'Please tell me your names.',
    email: 'Please enter an email address I can reply to.',
    emailFormat: 'That email address doesn’t look complete.',
    event_date: 'Please choose your event date.',
    event_type: 'Please choose an event type.',
    guests: 'Please enter a number, or leave it blank.'
  };

  function initContactForm() {
    var form = document.querySelector('[data-contact-form]');
    if (!form) return;

    var success = document.querySelector('[data-form-success]');
    var started = form.querySelector('[data-started-at]');
    if (started) started.value = String(Date.now());

    form.setAttribute('novalidate', '');

    function errorFor(input) {
      var v = input.validity;
      if (v.valid) return '';
      if (input.name === 'email' && v.typeMismatch) return MESSAGES.emailFormat;
      return MESSAGES[input.name] || 'Please check this field.';
    }

    function show(input, message) {
      var error = document.getElementById(input.id + '-error');
      input.setAttribute('aria-invalid', message ? 'true' : 'false');
      if (!error) return;
      error.textContent = message;
      error.hidden = !message;
    }

    var fields = form.querySelectorAll('.field__input');
    fields.forEach(function (input) {
      // Validate on leave once touched; clear as soon as it's fixed.
      input.addEventListener('blur', function () {
        if (input.value !== '' || input.getAttribute('aria-invalid') === 'true') show(input, errorFor(input));
      });
      input.addEventListener('input', function () {
        if (input.getAttribute('aria-invalid') === 'true') show(input, errorFor(input));
      });
    });

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      var firstInvalid = null;
      fields.forEach(function (input) {
        var message = errorFor(input);
        show(input, message);
        if (message && !firstInvalid) firstInvalid = input;
      });
      if (firstInvalid) {
        firstInvalid.focus();
        return;
      }

      // Spam checks (repeated server-side in Phase 2). Bots still see the success state.
      var honeypot = form.querySelector('[name="website"]');
      var tooFast = started && Date.now() - Number(started.value) < 3000;
      var isSpam = Boolean((honeypot && honeypot.value) || tooFast);

      // TODO (Phase 2): unless isSpam, POST new FormData(form) to the WordPress handler,
      // then show [data-form-success] on success or unhide [data-form-error] on failure.
      void isSpam;
      form.hidden = true;
      success.hidden = false;
      success.focus();
    });
  }

  function initYear() {
    var year = document.querySelector('[data-year]');
    if (year) year.textContent = String(new Date().getFullYear());
  }

  initMenu();
  initHeroVideo();
  initSlideshow();
  initContactForm();
  initYear();
})();
