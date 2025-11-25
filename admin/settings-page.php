<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <form method="post" action="options.php">
        <?php
        settings_fields('agri_weather_map_settings');
        do_settings_sections('agri-weather-map');
        submit_button();
        ?>
    </form>
    
    <div class="agri-weather-map-info" style="margin-top: 20px; padding: 20px; background: #fff; border-left: 4px solid #2271b1;">
        <h2>How to Use</h2>
        <p>Add the following shortcode to any page or post to display the weather map:</p>
        <code style="display: block; padding: 10px; background: #f0f0f0; margin: 10px 0;">
            [agri_weather_map width="100%" height="500px"]
        </code>
        
        <h3>Shortcode Parameters</h3>
        <ul>
            <li><strong>width</strong> - Map width (default: 100%)</li>
            <li><strong>height</strong> - Map height (default: 500px)</li>
        </ul>
        
        <h3>Weather Layers Available</h3>
        <ul>
            <li>Temperature Overlay</li>
            <li>Wind Speed Overlay</li>
            <li>Precipitation / Rainfall</li>
            <li>Cloud Cover</li>
            <li>Humidity</li>
        </ul>
        
        <p><em>Weather data provided by OpenWeatherMap</em></p>
    </div>
</div>
