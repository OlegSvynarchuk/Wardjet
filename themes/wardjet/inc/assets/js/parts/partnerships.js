/* Partnerships carousel - ported from blueprint (standalone file). */
jQuery(document).ready(function($) {
    (function() {
        var $slider = $('#partnerships-slider');
        var $track = $slider.find('.partnerships__track');
        var $allSlides = $track.find('.partnerships__slide');
        var $dotsContainer = $('.partnerships__pagination');
        var $dots = $('.partnerships__dot');
        var currentSlide = 0;
        var totalSlides = $dots.length;
        var slideWidth = 0;
        var autoplayInterval;
        var isMobile = window.innerWidth <= 767;
        var isTablet = window.innerWidth > 767 && window.innerWidth <= 1023;

        // On mobile/tablet: rebuild dots based on slides per view
        if ((isMobile || isTablet) && $allSlides.length > 0) {
            var perView = isMobile ? 1 : 2;
            $dotsContainer.empty();
            totalSlides = Math.ceil($allSlides.length / perView);
            for (var d = 0; d < totalSlides; d++) {
                var $dot = $('<button class="partnerships__dot" data-slide="' + d + '"></button>');
                if (d === 0) $dot.addClass('active');
                $dotsContainer.append($dot);
            }
            $dots = $dotsContainer.find('.partnerships__dot');
        }

        function getSlidesPerView() {
            if (isMobile) return 1;
            if (isTablet) return 2;
            return 4;
        }

        function calculateSlideWidth() {
            var containerWidth = $slider.width();
            if (isMobile) {
                slideWidth = containerWidth + 32; // 100% + gap
            } else {
                var slidesPerView = getSlidesPerView();
                var gap = 32;
                slideWidth = (containerWidth + gap) / slidesPerView;
            }
        }

        function goToSlide(index) {
            if (index < 0) index = 0;
            if (index >= totalSlides) index = totalSlides - 1;

            currentSlide = index;
            calculateSlideWidth();
            var perView = getSlidesPerView();
            var offset = currentSlide * slideWidth * perView;
            $track.css('transform', 'translateX(-' + offset + 'px)');

            $dots.removeClass('active');
            $dots.eq(currentSlide).addClass('active');
        }

        function nextSlide() {
            var next = currentSlide + 1;
            if (next >= totalSlides) next = 0;
            goToSlide(next);
        }

        function startAutoplay() {
            autoplayInterval = setInterval(nextSlide, 4000);
        }

        function stopAutoplay() {
            clearInterval(autoplayInterval);
        }

        if ($slider.length && totalSlides > 1) {
            $dotsContainer.on('click', '.partnerships__dot', function() {
                var slideIndex = $(this).data('slide');
                goToSlide(slideIndex);
                stopAutoplay();
                startAutoplay();
            });

            calculateSlideWidth();
            startAutoplay();

            $slider.on('mouseenter', stopAutoplay);
            $slider.on('mouseleave', startAutoplay);

            $(window).on('resize', function() {
                isMobile = window.innerWidth <= 767;
                isTablet = window.innerWidth > 767 && window.innerWidth <= 1023;
                calculateSlideWidth();
                goToSlide(currentSlide);
            });
        }
    })();
});
