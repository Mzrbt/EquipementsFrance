<div class="map-container">
    <aside class="map-sidebar">
        <h2 class="map-sidebar-title">Rechercher des équipements</h2>
        
        <form id="filters-form">
            <div class="form-group">
                <label class="form-label">Type d'équipement</label>
                <select class="form-select" id="filter-type" name="type">
                    <option value="">Tous les types</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Accessibilité</label>
                <select class="form-select" id="filter-accessibilite" name="accessibilite">
                    <option value="">Toutes les options</option>
                    <option value="true">Accessible PMR</option>
                    <option value="false">Non accessible PMR</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Dimension maximale (m²)</label>
                <input type="number" class="form-input" id="filter-dimension" name="dimension" placeholder="Ex : 500">
            </div>
            
            <div class="form-group">
                <label class="form-label">Commune</label>
                <input type="text" class="form-input" id="filter-commune" name="commune" placeholder="Rechercher une commune...">
            </div>

            <div class="form-group" id="rayon-container" style="display: none;">
                <label class="form-label">Rayon de recherche</label>
                <input type="range" id="filter-rayon" min="5" max="100" value="5" step="5" class="form-range" style="width: 100%;">
                <div style="display: flex; justify-content: space-between; font-size: 0.875rem; color: var(--text-muted); margin-top: 0.5rem;">
                    <span>5km</span>
                    <span id="rayon-value" style="font-weight: 600; color: var(--primary);">5km</span>
                    <span>100km</span>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Appliquer les filtres</button>
            <button type="button" class="btn btn-outline btn-block mt-2" onclick="switchToList()">Basculer vers la liste</button>
        </form>
        
        <div class="mt-4">
            <h3 class="text-muted" style="font-size: 0.875rem;">Aperçu statistique</h3>
            <div class="stat-card mt-2">
                <div class="stat-card-label">Total d'équipements</div>
                <div class="stat-card-value" id="total-count">-</div>
            </div>
        </div>
    </aside>
    
    <div class="map-wrapper">
        <div id="map"></div>
    </div>
</div>

<div class="container">
    <div class="equipements-grid mt-4" id="equipements-list">
        <p class="text-muted">Chargement des équipements...</p>
    </div>
</div>

<script>
const API_URL = 'https://equipements.sports.gouv.fr/api/explore/v2.1/catalog/datasets/data-es/records';

let map;

async function loadTypesEquipements() {
    try {
        const response = await fetch('https://equipements.sports.gouv.fr/api/explore/v2.1/catalog/datasets/data-es/facets');
        const data = await response.json();
        
        const facet = data.facets.find(f => f.name === 'equip_type_name');
        if (facet) {
            const select = document.getElementById('filter-type');
            
            const types = facet.facets.sort((a, b) => a.name.localeCompare(b.name));
            
            types.forEach(type => {
                const option = document.createElement('option');
                option.value = type.name;
                option.textContent = `${type.name} (${type.count})`;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Erreur chargement types:', error);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    initMap();
    loadTypesEquipements();
    loadEquipements();
    
    document.getElementById('filters-form').addEventListener('submit', function(e) {
        e.preventDefault();
        loadEquipements();
    });
});

let markerClusterGroup;

function initMap() {
    map = L.map('map').setView([46.603354, 1.888334], 6);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    markerClusterGroup = L.markerClusterGroup({
        maxClusterRadius: 80,
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: false
    });

    map.addLayer(markerClusterGroup);
}

async function loadEquipements() {

    const type = document.getElementById('filter-type').value;
    const commune = document.getElementById('filter-commune').value;
    const dimensionMax = document.getElementById('filter-dimension').value;
    const accessibilite = document.getElementById('filter-accessibilite').value;
    
    let whereConditions = [];
    if (type) {
        whereConditions.push(`equip_type_name="${type}"`);
    }
    if (accessibilite) {
        whereConditions.push(`equip_pmr_acc="${accessibilite}"`);
    }
    if (commune && commune.length >= 3) {
        whereConditions.push(`new_name LIKE "${commune}%"`);
    }
    
    const whereClause = whereConditions.length > 0 
        ? `&where=${encodeURIComponent(whereConditions.join(' AND '))}` 
        : '';

    let geofilter = '';
    if (communeCoords) {
        const rayon = parseInt(document.getElementById('filter-rayon').value);
        const rayonMetres = rayon * 1000;
        geofilter = `&geofilter.distance=${communeCoords.lat},${communeCoords.lon},${rayonMetres}`;
    }
    
    try {

        const limit = 100;
        const totalToFetch = 1000;
        let allEquipements = [];
        let offset = 0;
        
        while (allEquipements.length < totalToFetch) {
            const url = `${API_URL}?limit=${limit}&offset=${offset}${whereClause}${geofilter}`;
            console.log('URL API:', url);
            const reponse = await fetch(url);
            const data = await reponse.json();

            console.log('Résultats reçus:', data.results.length, '| Total API:', data.total_count);

            if (!data.results || data.results.length === 0) break;

            allEquipements = allEquipements.concat(data.results);

            if (data.results.length < limit) break;

            offset += limit;

            if (allEquipements.length === 100) {
                console.log('Exemple d\'équipement:', allEquipements[2]);
            }

            if (allEquipements.length >= data.total_count) break;
        }

        let filteredEquipements = allEquipements;

        if (dimensionMax) {
            const maxSurface = parseFloat(dimensionMax);
            filteredEquipements = filteredEquipements.filter(equip => {
                const surface = parseFloat(equip.equip_surf) || 
                            (parseFloat(equip.equip_long) || 0) * (parseFloat(equip.equip_larg) || 0);
                
                return surface > 0 && surface <= maxSurface;
            });
        }

        document.getElementById('total-count').textContent = formatNumber(filteredEquipements.length);

        displayMarkers(filteredEquipements);
        displayEquipementsList(filteredEquipements);
        
    } catch (error) {
        console.error('Erreur lors du chargement des équipements:', error);
        document.getElementById('total-count').textContent = '0';
    }
}

function displayMarkers(equipements) {

    markerClusterGroup.clearLayers();

    equipements.forEach(equip => {
        if (equip.equip_coordonnees) {
            const lat = equip.equip_coordonnees.lat;
            const lon = equip.equip_coordonnees.lon;
            
            if (lat && lon) {
                const redIcon = L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                });
                
                const marker = L.marker([lat, lon], { icon: redIcon });
                
                marker.bindPopup(`
                    <strong>${equip.equip_nom || 'Sans nom'}</strong><br>
                    <em>${equip.equip_type_name || 'Type inconnu'}</em><br>
                    ${equip.new_name || ''} ${equip.inst_cp || ''}<br>
                    ${equip.inst_adresse || 'Adresse non renseignée'}
                `);

                markerClusterGroup.addLayer(marker);
            }
        }
    });
    
    if (communeCoords && markerClusterGroup.getLayers().length > 0) {
        map.setView([communeCoords.lat, communeCoords.lon], 11);
    }
}

function displayEquipementsList(equipements) {
    const container = document.getElementById('equipements-list');
    
    if (equipements.length === 0) {
        container.innerHTML = '<p class="text-muted">Aucun équipement trouvé.</p>';
        return;
    }
    
    container.innerHTML = equipements.slice(0, 12).map(equip => `
        <div class="equipement-card">
            <div class="equipement-card-header">
                <div>
                    <h3 class="equipement-card-title">${escapeHtml(equip.inst_nom || equip.equip_nom || 'Sans nom')}</h3>
                    <span class="equipement-card-id">${equip.equip_numero || ''}</span>
                </div>
                <span class="badge badge-success">En service</span>
            </div>
            <p class="equipement-card-info"><strong>Ville :</strong> ${escapeHtml(equip.arr_name || equip.new_name || '-')}</p>
            <p class="equipement-card-info"><strong>Adresse :</strong> ${escapeHtml(equip.inst_adresse || '-')}</p>
            <p class="equipement-card-info"><strong>Type :</strong> ${escapeHtml(equip.equip_type_name || '-')}</p>
            <p class="equipement-card-info"><strong>Surface :</strong> ${equip.equip_surf ? equip.equip_surf + ' m²' : '-'}</p>
        </div>
    `).join('');
}

function switchToList() {
    window.location.href = '/equipements_sportifs/public/equipements';
}

let communeCoords = null;

document.getElementById('filter-rayon').addEventListener('input', function() {
    document.getElementById('rayon-value').textContent = this.value + ' km';
});

document.getElementById('filter-rayon').addEventListener('change', function() {
    loadEquipements();
});

let geocodeTimer = null;

document.getElementById('filter-commune').addEventListener('input', async function() {
    const commune = this.value.trim();
    const rayonContainer = document.getElementById('rayon-container');
    
    clearTimeout(geocodeTimer);
    
    if (commune.length >= 3) {
        geocodeTimer = setTimeout(async () => {
            try {
                const response = await fetch(`https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(commune)}&type=municipality&limit=1`);
                const data = await response.json();
                
                if (data.features && data.features.length > 0) {
                    communeCoords = {
                        lat: data.features[0].geometry.coordinates[1],
                        lon: data.features[0].geometry.coordinates[0]
                    };
                    rayonContainer.style.display = 'block';
                    
                    loadEquipements();
                } else {
                    communeCoords = null;
                    rayonContainer.style.display = 'none';
                }
            } catch (error) {
                console.error('Erreur géocodage:', error);
                communeCoords = null;
                rayonContainer.style.display = 'none';
            }
        }, 500);
    } else {
        communeCoords = null;
        rayonContainer.style.display = 'none';
        loadEquipements();
    }
});

function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

function formatNumber(num) {
    return num.toLocaleString('fr-FR');
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>