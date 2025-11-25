function initAgriWeatherMap(mapId) {
    const settings = window.agriWeatherMapSettings || {
        defaultZoom: 6,
        defaultLat: 39.8283,
        defaultLng: -98.5795,
        farmerFriendly: 0,
        apiKey: ''
    };

    const apiKey = settings.apiKey || '';

    const map = L.map(mapId, {
        center: [settings.defaultLat, settings.defaultLng],
        zoom: settings.defaultZoom,
        zoomControl: true
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);

    if (apiKey && apiKey !== '') {
        const weatherLayers = {
            'Temperature': L.tileLayer('https://tile.openweathermap.org/map/temp_new/{z}/{x}/{y}.png?appid=' + apiKey, {
                opacity: 0.6,
                attribution: 'Weather tiles &copy; OpenWeatherMap'
            }),
            'Wind Speed': L.tileLayer('https://tile.openweathermap.org/map/wind_new/{z}/{x}/{y}.png?appid=' + apiKey, {
                opacity: 0.6,
                attribution: 'Weather tiles &copy; OpenWeatherMap'
            }),
            'Precipitation': L.tileLayer('https://tile.openweathermap.org/map/precipitation_new/{z}/{x}/{y}.png?appid=' + apiKey, {
                opacity: 0.6,
                attribution: 'Weather tiles &copy; OpenWeatherMap'
            }),
            'Clouds': L.tileLayer('https://tile.openweathermap.org/map/clouds_new/{z}/{x}/{y}.png?appid=' + apiKey, {
                opacity: 0.6,
                attribution: 'Weather tiles &copy; OpenWeatherMap'
            }),
            'Humidity': L.tileLayer('https://tile.openweathermap.org/map/humidity_new/{z}/{x}/{y}.png?appid=' + apiKey, {
                opacity: 0.6,
                attribution: 'Weather tiles &copy; OpenWeatherMap'
            })
        };

        L.control.layers(null, weatherLayers, {
            position: 'topright',
            collapsed: false
        }).addTo(map);
    } else {
        addNoApiKeyWarning(map);
    }

    if (settings.farmerFriendly == 1) {
        addFarmerFriendlyLegend(map);
    } else {
        addWeatherLegend(map);
    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                map.setView([lat, lng], settings.defaultZoom);
                
                L.marker([lat, lng])
                    .addTo(map)
                    .bindPopup('Your Location')
                    .openPopup();
            },
            function(error) {
                console.log('Geolocation error:', error.message);
            }
        );
    }

    setTimeout(function() {
        map.invalidateSize();
    }, 100);
    
    return map;
}

function addWeatherLegend(map) {
    const legend = L.control({ position: 'bottomleft' });

    legend.onAdd = function() {
        const div = L.DomUtil.create('div', 'weather-legend');
        div.innerHTML = `
            <h4>Weather Layers</h4>
            <p><strong>Temperature:</strong> Red = Hot, Blue = Cold</p>
            <p><strong>Wind:</strong> Faster winds shown in brighter colors</p>
            <p><strong>Precipitation:</strong> Blue shades indicate rainfall</p>
            <p><strong>Clouds:</strong> White areas show cloud coverage</p>
            <p class="legend-note">Toggle layers using the control panel</p>
        `;
        return div;
    };

    legend.addTo(map);
}

function addNoApiKeyWarning(map) {
    const warning = L.control({ position: 'topright' });

    warning.onAdd = function() {
        const div = L.DomUtil.create('div', 'api-key-warning');
        div.style.background = '#fff3e0';
        div.style.padding = '15px';
        div.style.borderRadius = '8px';
        div.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.2)';
        div.style.maxWidth = '300px';
        div.style.border = '2px solid #ff9800';
        div.innerHTML = `
            <h4 style="margin: 0 0 10px 0; color: #ff9800;">⚠️ API Key Required</h4>
            <p style="margin: 0; font-size: 13px; color: #666;">
                Weather overlays require an OpenWeatherMap API key.
                <br><br>
                <a href="https://openweathermap.org/api" target="_blank" style="color: #2271b1;">Get a free API key</a>
                <br><br>
                Then add it in WordPress Settings → Agri Weather Map.
            </p>
        `;
        return div;
    };

    warning.addTo(map);
}

function addFarmerFriendlyLegend(map) {
    const legend = L.control({ position: 'bottomleft' });

    legend.onAdd = function() {
        const div = L.DomUtil.create('div', 'weather-legend farmer-friendly');
        div.innerHTML = `
            <h4>Weather Guide for Farmers</h4>
            <div class="legend-item">
                <span class="legend-color" style="background: #ff4444;"></span>
                <strong>Red = High Temperature</strong>
                <p>Hot conditions - ensure adequate irrigation</p>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background: #4444ff;"></span>
                <strong>Blue = Rain Expected</strong>
                <p>Precipitation likely - plan field activities accordingly</p>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background: #cccccc;"></span>
                <strong>White Areas = Cloud Cover</strong>
                <p>Cloudy conditions - reduced sun exposure</p>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background: #44ff44;"></span>
                <strong>Green/Yellow = Wind</strong>
                <p>Wind speeds - consider for spraying activities</p>
            </div>
            <p class="legend-note">Use the layer control to switch between different weather conditions</p>
        `;
        return div;
    };

    legend.addTo(map);
}

function addNoApiKeyWarning(map) {
    const warning = L.control({ position: 'topright' });

    warning.onAdd = function() {
        const div = L.DomUtil.create('div', 'api-key-warning');
        div.style.background = '#fff3e0';
        div.style.padding = '15px';
        div.style.borderRadius = '8px';
        div.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.2)';
        div.style.maxWidth = '300px';
        div.style.border = '2px solid #ff9800';
        div.innerHTML = `
            <h4 style="margin: 0 0 10px 0; color: #ff9800;">⚠️ API Key Required</h4>
            <p style="margin: 0; font-size: 13px; color: #666;">
                Weather overlays require an OpenWeatherMap API key.
                <br><br>
                <a href="https://openweathermap.org/api" target="_blank" style="color: #2271b1;">Get a free API key</a>
                <br><br>
                Then add it in WordPress Settings → Agri Weather Map.
            </p>
        `;
        return div;
    };

    warning.addTo(map);
}
