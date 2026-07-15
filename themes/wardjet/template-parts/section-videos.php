<?php
$block = $args['block'];
$videos = $block['videos'];
if (!$videos || !is_array($videos) || count($videos) === 0) return;
$total = count($videos);
$is_carousel = ($total > 1);
?>
<section class="video-carousel-section">
    <div class="video-carousel-wrapper">
        <div id="videoCarousel">
            <?php if ($is_carousel) : ?>
            <button class="video-carousel-nav video-carousel-nav--prev" type="button" aria-label="<?php esc_attr_e('Previous', 'axyz'); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <?php endif; ?>

            <div class="video-carousel-viewport">
                <div class="video-carousel-track">
                    <?php foreach ($videos as $i => $video) :
                        $embed = $video['embed'];
                        $title = isset($video['title']) ? $video['title'] : '';
                        $subtitle = isset($video['subtitle']) ? $video['subtitle'] : '';

                        // If WP auto-embed wrapped a raw MP4 URL in a [video] shortcode,
                        // unwrap it so the JS click handler can treat it as a direct URL.
                        if (preg_match('/\[video[^\]]*\bsrc=["\']([^"\']+)["\']/', $embed, $vs_match)) {
                            $embed = $vs_match[1];
                        }

                        $poster_url = '';
                        // 1. Manual placeholder ACF field takes priority (image attachment ID, array, or URL)
                        if (!empty($video['placeholder'])) {
                            $ph = $video['placeholder'];
                            if (is_array($ph) && !empty($ph['url'])) {
                                $poster_url = $ph['url'];
                            } elseif (is_numeric($ph)) {
                                $poster_url = wp_get_attachment_url((int) $ph);
                            } elseif (is_string($ph)) {
                                $poster_url = $ph;
                            }
                        }
                        // 2. Fallback: auto-fetch YouTube thumbnail
                        if (!$poster_url && preg_match('/(?:youtube\.com\/(?:embed\/|watch\?v=)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $embed, $yt_match)) {
                            $poster_url = 'https://img.youtube.com/vi/' . $yt_match[1] . '/maxresdefault.jpg';
                        }
                        // 3. Fallback: poster="..." attribute embedded in the value
                        if (!$poster_url && preg_match('/poster="([^"]+)"/', $embed, $poster_match)) {
                            $poster_url = $poster_match[1];
                        }
                    ?>
                    <div class="video-carousel-slide">
                        <div class="video-carousel-card" data-embed="<?php echo esc_attr($embed); ?>">
                            <?php if ($poster_url) : ?>
                                <div class="video-carousel-poster" style="background-image: url('<?php echo esc_url($poster_url); ?>');">
                            <?php else : ?>
                                <div class="video-carousel-poster">
                            <?php endif; ?>
                                <div class="video-carousel-overlay"></div>
                                <div class="video-carousel-content">
                                    <div class="video-carousel-play">
                                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5 3L19 12L5 21V3Z" fill="#FFFFFF" stroke="#FFFFFF" stroke-width="2" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <?php if ($title) : ?>
                                        <h3 class="video-carousel-title"><?php echo esc_html($title); ?></h3>
                                    <?php endif; ?>
                                    <?php if ($subtitle) : ?>
                                        <p class="video-carousel-subtitle"><?php echo esc_html($subtitle); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if ($is_carousel) : ?>
            <button class="video-carousel-nav video-carousel-nav--next" type="button" aria-label="<?php esc_attr_e('Next', 'axyz'); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>

            <div class="video-carousel-dots">
                <?php for ($d = 0; $d < $total; $d++) : ?>
                    <button type="button" class="video-carousel-dot <?php echo $d === 0 ? 'active' : ''; ?>" data-slide="<?php echo $d; ?>"></button>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
(function() {
    // Video embed on click
    document.querySelectorAll('.video-carousel-card').forEach(function(card) {
        card.addEventListener('click', function() {
            var embed = this.getAttribute('data-embed');
            if (!embed) return;
            var ytMatch = embed.match(/(?:youtube\.com\/(?:embed\/|watch\?v=)|youtu\.be\/)([a-zA-Z0-9_-]+)/);
            if (ytMatch) {
                this.innerHTML = '<iframe src="https://www.youtube.com/embed/' + ytMatch[1] + '?autoplay=1&rel=0" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="width:100%;height:100%;border-radius:16px;"></iframe>';
            } else if (embed.indexOf('<iframe') !== -1 || embed.indexOf('<video') !== -1) {
                this.innerHTML = embed;
            } else {
                this.innerHTML = '<video src="' + embed + '" autoplay controls style="width:100%;height:100%;object-fit:cover;border-radius:16px;"></video>';
            }
        });
    });

    // Custom slider
    var slider = document.getElementById('videoCarousel');
    if (!slider) return;

    var track = slider.querySelector('.video-carousel-track');
    var slides = slider.querySelectorAll('.video-carousel-slide');
    var dots = slider.querySelectorAll('.video-carousel-dot');
    var prevBtn = slider.querySelector('.video-carousel-nav--prev');
    var nextBtn = slider.querySelector('.video-carousel-nav--next');
    var current = 0;
    var total = slides.length;

    if (total <= 1) return;

    function updateArrows() {
        if (prevBtn) prevBtn.classList.toggle('disabled', current <= 0);
        if (nextBtn) nextBtn.classList.toggle('disabled', current >= total - 1);
    }

    function goTo(index) {
        if (index < 0) index = 0;
        if (index >= total) index = total - 1;
        current = index;
        var viewport = slider.querySelector('.video-carousel-viewport');
        var offset = current * viewport.offsetWidth;
        track.style.transform = 'translateX(-' + offset + 'px)';
        dots.forEach(function(d, i) {
            d.classList.toggle('active', i === current);
        });
        updateArrows();
    }

    if (prevBtn) prevBtn.addEventListener('click', function() { goTo(current - 1); });
    if (nextBtn) nextBtn.addEventListener('click', function() { goTo(current + 1); });
    dots.forEach(function(dot) {
        dot.addEventListener('click', function() { goTo(parseInt(this.dataset.slide)); });
    });

    // Touch/swipe
    var startX = 0;
    slider.addEventListener('touchstart', function(e) { startX = e.touches[0].clientX; });
    slider.addEventListener('touchend', function(e) {
        var diff = startX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) {
            if (diff > 0) goTo(current + 1);
            else goTo(current - 1);
        }
    });

    window.addEventListener('resize', function() { goTo(current); });
    updateArrows();
})();
</script>
