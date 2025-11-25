<?php
if (!defined('ABSPATH')) {
    exit;
}

$map_id = 'agri-weather-map-' . uniqid();
$farmer_class = $options['farmer_friendly'] ? ' farmer-friendly-mode' : '';
?>

<div class="agri-weather-map-container<?php echo esc_attr($farmer_class); ?>" 
     style="width: <?php echo esc_attr($atts['width']); ?>; height: <?php echo esc_attr($atts['height']); ?>;">
    <div id="<?php echo esc_attr($map_id); ?>" 
         class="agri-weather-map" 
         style="width: 100%; height: 100%;">
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof initAgriWeatherMap === 'function') {
        initAgriWeatherMap('<?php echo esc_js($map_id); ?>');
    }
});
</script>
