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
    
    <div class="map-wrapper" style="position: relative; height: 100%;">
        <div id="map"></div>
        
        <div id="loading-overlay" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255, 255, 255, 0.9); z-index: 999999; display: flex; align-items: center; justify-content: center;">
            <svg width="60" height="60" viewBox="0 0 50 50" style="animation: spin 0.8s linear infinite;">
                <circle cx="25" cy="25" r="20" fill="none" stroke="rgba(59, 130, 246, 0.2)" stroke-width="5"></circle>
                <circle cx="25" cy="25" r="20" fill="none" stroke="#3b82f6" stroke-width="5" stroke-dasharray="31.4 31.4" stroke-linecap="round"></circle>
            </svg>
        </div>
    </div>
</div>

<div class="container">
    <div class="equipements-grid mt-4" id="equipements-list">
        <p class="text-muted">Chargement des équipements...</p>
    </div>
</div>

<div id="equipement-modal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title" id="modal-title">Détails de l'équipement</h2>
            <button type="button" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted); line-height: 1;" onclick="document.getElementById('equipement-modal').classList.remove('active'); document.body.style.overflow = '';">&times;</button>
        </div>
        <div class="modal-body" id="modal-body">
        </div>
    </div>
</div>

<div id="reservation-modal" class="modal-overlay">
    <div class="modal reservation-modal">
        <div class="modal-header">
            <h2 class="modal-title">📅 Réserver cet équipement</h2>
            <button type="button" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted); line-height: 1;" onclick="closeReservationModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="reservation-form">
                <div class="reservation-equipement-info" id="reservation-equipement-info">
                </div>
                
                <div class="form-group">
                    <label class="form-label">📅 Date de réservation</label>
                    <input type="date" id="reservation-date" class="form-input" required min="">
                </div>
                
                <div class="form-group">
                    <label class="form-label">⏰ Créneau horaire</label>
                    <div class="creneau-selector">
                        <div class="creneau-card" data-creneau="matin">
                            <div class="creneau-icon">🌅</div>
                            <div class="creneau-label">Matin</div>
                            <div class="creneau-time">8h - 12h</div>
                        </div>
                        <div class="creneau-card" data-creneau="apres-midi">
                            <div class="creneau-icon">☀️</div>
                            <div class="creneau-label">Après-midi</div>
                            <div class="creneau-time">14h - 18h</div>
                        </div>
                        <div class="creneau-card" data-creneau="soiree">
                            <div class="creneau-icon">🌙</div>
                            <div class="creneau-label">Soirée</div>
                            <div class="creneau-time">18h - 22h</div>
                        </div>
                    </div>
                    <input type="hidden" id="reservation-creneau" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">👥 Nombre de participants</label>
                    <input type="number" id="reservation-personnes" class="form-input" value="1" min="1" max="100" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">📝 Commentaire (optionnel)</label>
                    <textarea id="reservation-commentaire" class="form-input" rows="3" placeholder="Précisions sur votre réservation..."></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    ✅ Confirmer la réservation
                </button>
            </form>
        </div>
    </div>
</div>

<div id="confirmation-modal" class="modal-overlay">
    <div class="modal confirmation-modal">
        <div class="modal-body" style="text-align: center; padding: 3rem 2rem;">
            <div class="success-animation">
                <div class="checkmark">✓</div>
            </div>
            <h2 style="color: var(--success); margin: 1.5rem 0 1rem 0;">Réservation confirmée !</h2>
            <p style="color: var(--text-muted); margin-bottom: 2rem;">Votre demande de réservation a été enregistrée.</p>
            <button onclick="closeConfirmationModal()" class="btn btn-primary">Fermer</button>
        </div>
    </div>
</div>

<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 10000;
}

.modal-overlay.active {
    display: flex;
}

.modal {
    background: var(--bg-white);
    border-radius: var(--radius-lg);
    width: 100%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: var(--shadow-lg);
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary);
    margin: 0;
}

.modal-body {
    padding: 1.5rem;
}

.detail-row {
    display: flex;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--border-color);
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    width: 180px;
    flex-shrink: 0;
}

.detail-value {
    color: var(--text-muted);
}}

.modal-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary);
    margin: 0;
}

.modal-body {
    padding: 1.5rem;
}

.detail-row {
    display: flex;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--border-color);
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    width: 180px;
    flex-shrink: 0;
}

.detail-value {
    color: var(--text-muted);
}

.reservation-modal {
    max-width: 600px;
}

.reservation-equipement-info {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(147, 51, 234, 0.1) 100%);
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    border-left: 4px solid var(--primary);
}

.reservation-equipement-info h3 {
    margin: 0 0 0.5rem 0;
    color: var(--primary);
    font-size: 1.1rem;
}

.reservation-equipement-info p {
    margin: 0.25rem 0;
    color: var(--text-muted);
    font-size: 0.9rem;
}

.creneau-selector {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-top: 0.75rem;
}

.creneau-card {
    background: var(--bg-white);
    border: 2px solid var(--border-color);
    border-radius: 12px;
    padding: 1.25rem 0.75rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.creneau-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
    transition: left 0.5s ease;
}

.creneau-card:hover::before {
    left: 100%;
}

.creneau-card:hover {
    border-color: var(--primary);
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(59, 130, 246, 0.2);
}

.creneau-card.selected {
    border-color: var(--primary);
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(147, 51, 234, 0.05) 100%);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.creneau-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.creneau-label {
    font-weight: 600;
    color: var(--text);
    margin-bottom: 0.25rem;
}

.creneau-time {
    font-size: 0.85rem;
    color: var(--text-muted);
}

.confirmation-modal {
    max-width: 450px;
}

.success-animation {
    margin: 0 auto 1.5rem auto;
    width: 80px;
    height: 80px;
}

.checkmark {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--success);
    color: white;
    font-size: 3rem;
    animation: scaleIn 0.5s ease-out;
    box-shadow: 0 4px 20px rgba(34, 197, 94, 0.4);
}

@keyframes scaleIn {
    0% {
        transform: scale(0) rotate(-180deg);
        opacity: 0;
    }
    50% {
        transform: scale(1.2) rotate(0deg);
    }
    100% {
        transform: scale(1) rotate(0deg);
        opacity: 1;
    }
}

.reservation-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-top: 0.5rem;
}
</style>

<script>
const API_URL = 'https://equipements.sports.gouv.fr/api/explore/v2.1/catalog/datasets/data-es/records';

const departementsVoisins = {
    '01': ['01', '39', '69', '71', '73', '74'],
    '03': ['03', '18', '23', '42', '58', '63', '71'],
    '07': ['07', '26', '30', '38', '42', '48', '84'],
    '15': ['15', '19', '43', '46', '48', '63'],
    '26': ['26', '05', '07', '38', '84'],
    '38': ['38', '01', '05', '07', '26', '42', '69', '73'],
    '42': ['42', '03', '07', '38', '43', '63', '69', '71'],
    '43': ['43', '07', '15', '42', '48', '63'],
    '63': ['63', '03', '15', '19', '23', '42', '43', '87'],
    '69': ['69', '01', '38', '42', '71'],
    '73': ['73', '01', '05', '38', '74'],
    '74': ['74', '01', '73'],
    '21': ['21', '10', '39', '52', '58', '70', '71', '89'],
    '25': ['25', '39', '70', '90'],
    '39': ['39', '01', '21', '25', '70', '71'],
    '58': ['58', '03', '18', '21', '45', '71', '89'],
    '70': ['70', '21', '25', '39', '52', '88', '90'],
    '71': ['71', '01', '03', '21', '39', '42', '58', '69', '89'],
    '89': ['89', '10', '21', '45', '58', '71', '77'],
    '90': ['90', '25', '68', '70', '88'],
    '22': ['22', '29', '35', '56'],
    '29': ['29', '22', '56'],
    '35': ['35', '22', '44', '49', '50', '53', '56'],
    '56': ['56', '22', '29', '35', '44'],
    '18': ['18', '03', '23', '36', '41', '45', '58'],
    '28': ['28', '27', '45', '61', '72', '78', '91'],
    '36': ['36', '18', '23', '37', '41', '86', '87'],
    '37': ['37', '36', '41', '49', '85', '86'],
    '41': ['41', '18', '28', '36', '37', '45', '72'],
    '45': ['45', '18', '28', '58', '77', '89', '91'],
    '2A': ['2A', '2B'],
    '2B': ['2B', '2A'],
    '08': ['08', '02', '51', '55'],
    '10': ['10', '21', '51', '52', '77', '89'],
    '51': ['51', '02', '08', '10', '52', '55', '77'],
    '52': ['52', '10', '21', '51', '55', '70', '88'],
    '54': ['54', '55', '57', '67', '88'],
    '55': ['55', '08', '51', '52', '54', '88'],
    '57': ['57', '54', '67'],
    '67': ['67', '54', '57', '68', '88'],
    '68': ['68', '67', '88', '90'],
    '88': ['88', '52', '54', '55', '67', '68', '70', '90'],
    '02': ['02', '08', '51', '59', '60', '77', '80'],
    '59': ['59', '02', '62', '80'],
    '60': ['60', '02', '76', '77', '80', '95'],
    '62': ['62', '59', '80'],
    '80': ['80', '02', '59', '60', '62', '76'],
    '75': ['75', '92', '93', '94'],
    '77': ['77', '02', '10', '45', '51', '60', '89', '91', '93', '94'],
    '78': ['78', '27', '28', '91', '92', '95'],
    '91': ['91', '28', '45', '75', '77', '78', '92', '94'],
    '92': ['92', '75', '78', '91', '93', '94', '95'],
    '93': ['93', '75', '77', '92', '94', '95'],
    '94': ['94', '75', '77', '91', '92', '93'],
    '95': ['95', '60', '78', '92', '93'],
    '14': ['14', '27', '50', '61', '76'],
    '27': ['27', '14', '28', '60', '76', '78', '95'],
    '50': ['50', '14', '35', '53', '61'],
    '61': ['61', '14', '27', '28', '50', '53', '72'],
    '76': ['76', '14', '27', '60', '80'],
    '16': ['16', '17', '24', '79', '86', '87'],
    '17': ['17', '16', '24', '33', '79', '85'],
    '19': ['19', '15', '23', '24', '46', '63', '87'],
    '23': ['23', '03', '18', '19', '36', '63', '87'],
    '24': ['24', '16', '17', '19', '33', '46', '47', '87'],
    '33': ['33', '17', '24', '40', '47'],
    '40': ['40', '33', '47', '64'],
    '47': ['47', '24', '33', '40', '46', '82'],
    '64': ['64', '40', '65'],
    '79': ['79', '16', '17', '85', '86'],
    '86': ['86', '16', '36', '37', '79', '87'],
    '87': ['87', '16', '19', '23', '24', '36', '63', '86'],
    '09': ['09', '11', '31'],
    '11': ['11', '09', '31', '34', '66', '81'],
    '12': ['12', '15', '30', '34', '46', '48', '81', '82'],
    '30': ['30', '07', '12', '34', '48', '84'],
    '31': ['31', '09', '11', '32', '65', '81', '82'],
    '32': ['32', '31', '40', '47', '64', '65', '82'],
    '34': ['34', '11', '12', '30', '81'],
    '46': ['46', '12', '15', '19', '24', '47', '82'],
    '48': ['48', '07', '12', '15', '30', '43'],
    '65': ['65', '31', '32', '64'],
    '66': ['66', '11'],
    '81': ['81', '11', '12', '31', '34', '82'],
    '82': ['82', '12', '31', '32', '46', '47', '81'],
    '44': ['44', '35', '49', '56', '85'],
    '49': ['49', '35', '37', '44', '53', '79', '85', '86'],
    '53': ['53', '35', '49', '50', '61', '72'],
    '72': ['72', '28', '41', '49', '53', '61'],
    '85': ['85', '17', '37', '44', '49', '79'],
    '04': ['04', '05', '06', '13', '83', '84'],
    '05': ['05', '04', '26', '38', '73'],
    '06': ['06', '04', '83'],
    '13': ['13', '04', '30', '83', '84'],
    '83': ['83', '04', '06', '13', '84'],
    '84': ['84', '04', '05', '07', '13', '26', '30', '83']
};

let map;

async function loadTypesEquipements() {
    try {
        const response = await fetch('https://equipements.sports.gouv.fr/api/explore/v2.1/catalog/datasets/data-es/facets');
        const data = await response.json();
        
        const facet = data.facets ? data.facets.find(f => f.name === 'equip_type_name') : null;
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
    
    if (navigator.geolocation) {
        document.getElementById('loading-overlay').style.display = 'flex';
        navigator.geolocation.getCurrentPosition(async function(position) {
            const userLat = position.coords.latitude;
            const userLon = position.coords.longitude;
            
            const blueIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
            
            const userMarker = L.marker([userLat, userLon], { icon: blueIcon }).addTo(map);
            map.setView([userLat, userLon], 13);
            
            try {
                const response = await fetch(`https://api-adresse.data.gouv.fr/reverse/?lon=${userLon}&lat=${userLat}`);
                const data = await response.json();
                if (data.features && data.features.length > 0) {
                    const commune = data.features[0].properties.city;
                    document.getElementById('filter-commune').value = commune;
                    communeCoords = { lat: userLat, lon: userLon };
                    document.getElementById('filter-rayon').value = 5;
                    document.getElementById('rayon-value').textContent = '5 km';
                    document.getElementById('rayon-container').style.display = 'block';
                    loadEquipements();
                }
            } catch (error) {
                console.error('Erreur géocodage inverse:', error);
            }
            
        }, function(error) {
            console.log('Géolocalisation refusée ou indisponible:', error);
        });
    }
}

async function loadEquipements() {

    document.getElementById('loading-overlay').style.display = 'flex';

    const type = document.getElementById('filter-type').value;
    const dimensionMax = document.getElementById('filter-dimension').value;
    const accessibilite = document.getElementById('filter-accessibilite').value;
    
    if (!communeCoords) {
        document.getElementById('total-count').textContent = '0';
        markerClusterGroup.clearLayers();
        document.getElementById('equipements-list').innerHTML = '<p class="text-muted">Veuillez sélectionner une commune pour commencer la recherche.</p>';
        document.getElementById('loading-overlay').style.display = 'none';
        return;
    }
    
    const commune = document.getElementById('filter-commune').value.trim();
    const geoResponse = await fetch(`https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(commune)}&type=municipality&limit=1`);
    const geoData = await geoResponse.json();
    
    const departementPrincipal = geoData.features[0].properties.citycode.substring(0, 2);

    const rayon = parseInt(document.getElementById('filter-rayon').value);

    const departementsAChercher = rayon > 50 && departementsVoisins[departementPrincipal] 
        ? departementsVoisins[departementPrincipal] 
        : [departementPrincipal];

    console.log('Départements à charger:', departementsAChercher);

    let whereConditions = [];

    if (departementsAChercher.length === 1) {
        whereConditions.push(`dep_code="${departementsAChercher[0]}"`);
    } else {
        const depConditions = departementsAChercher.map(d => `dep_code="${d}"`).join(' OR ');
        whereConditions.push(`(${depConditions})`);
    }

    if (type) {
        whereConditions.push(`equip_type_name="${type}"`);
    }
    if (accessibilite) {
        whereConditions.push(`equip_pmr_acc="${accessibilite}"`);
    }
    
    const whereClause = `&where=${encodeURIComponent(whereConditions.join(' AND '))}`;
    
    try {
        const limit = 100;
        let allEquipements = [];
        let offset = 0;
        
        while (true) {
            const url = `${API_URL}?limit=${limit}&offset=${offset}${whereClause}`;
            console.log('Chargement:', offset, 'équipements...');
            const reponse = await fetch(url);
            const data = await reponse.json();

            if (!data.results || data.results.length === 0) break;
            allEquipements = allEquipements.concat(data.results);
            if (data.results.length < limit) break;
            offset += limit;
            
            if (offset >= 10000) break;

            await new Promise(resolve => setTimeout(resolve, 100));
        }

        console.log('Total département API:', allEquipements.length);

        try {
            const localResponse = await fetch('/equipements_sportifs/public/api/equipements.php');
            const localData = await localResponse.json();
            
            if (localData.results && localData.results.length > 0) {
                console.log('Équipements locaux:', localData.results.length);
                allEquipements = allEquipements.concat(localData.results);
                console.log('Total après fusion:', allEquipements.length);
            }
        } catch (error) {
            console.error('Erreur chargement équipements locaux:', error);
        }

        let filteredEquipements = allEquipements
            .map(equip => {
                if (!equip.equip_coordonnees) return null;
                const lat = equip.equip_coordonnees.lat;
                const lon = equip.equip_coordonnees.lon;
                if (!lat || !lon) return null;
                
                const distance = calculateDistance(communeCoords.lat, communeCoords.lon, lat, lon);
                
                if (distance <= rayon) {
                    return {
                        ...equip,
                        distance: distance
                    };
                }
                return null;
            })
            .filter(equip => equip !== null)
            .sort((a, b) => a.distance - b.distance);

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

        document.getElementById('loading-overlay').style.display = 'none';
        
    } catch (error) {
        console.error('Erreur:', error);
        document.getElementById('loading-overlay').style.display = 'none';
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

                marker.bindTooltip(`
                    <strong>${equip.equip_nom || 'Sans nom'}</strong><br>
                    <em>${equip.equip_type_name || 'Type inconnu'}</em>
                `);

                marker.on('click', function() {
                    openEquipementModal(equip.equip_numero || equip.equip_id);
                });

                markerClusterGroup.addLayer(marker);
            }
        }
    });
    
    if (communeCoords && markerClusterGroup.getLayers().length > 0) {
        map.setView([communeCoords.lat, communeCoords.lon], 11);
    }
}

function displayEquipementsList(equipements) {

    allLoadedEquipements = equipements;

    const container = document.getElementById('equipements-list');
    
    if (equipements.length === 0) {
        container.innerHTML = '<p class="text-muted">Aucun équipement trouvé.</p>';
        return;
    }
    
    const cardsHtml = equipements.slice(0, 12).map(equip => `
        <div class="equipement-card" onclick="openEquipementModal('${equip.equip_numero || equip.equip_id}')" style="cursor: pointer;">
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
            <p class="equipement-card-info"><strong>Distance :</strong> ${equip.distance ? equip.distance.toFixed(1) + ' km' : '-'}</p>
            <p class="equipement-card-info"><strong>Surface :</strong> ${equip.equip_surf ? equip.equip_surf + ' m²' : '-'}</p>
        </div>
    `).join('');
    
    const buttonHtml = equipements.length > 12 ? `
        <div style="grid-column: 1 / -1; text-align: center; margin-top: 2rem;">
            <button onclick="goToFullList()" class="btn btn-primary btn-lg">
                Voir tous les ${formatNumber(equipements.length)} équipements
            </button>
        </div>
    ` : '';
    
    container.innerHTML = cardsHtml + buttonHtml;
}

function goToFullList() {
    const commune = document.getElementById('filter-commune').value;
    const rayon = document.getElementById('filter-rayon').value;
    const type = document.getElementById('filter-type').value;
    const accessibilite = document.getElementById('filter-accessibilite').value;
    const dimension = document.getElementById('filter-dimension').value;
    
    const params = new URLSearchParams();
    if (commune) params.append('commune', commune);
    if (rayon) params.append('rayon', rayon);
    if (type) params.append('type', type);
    if (accessibilite) params.append('accessibilite', accessibilite);
    if (dimension) params.append('dimension', dimension);
    
    window.location.href = `/equipements_sportifs/public/equipements?${params.toString()}`;
}

function switchToList() {
    const commune = document.getElementById('filter-commune').value;
    const rayon = document.getElementById('filter-rayon').value;
    const type = document.getElementById('filter-type').value;
    const accessibilite = document.getElementById('filter-accessibilite').value;
    const dimension = document.getElementById('filter-dimension').value;
    
    const params = new URLSearchParams();
    if (commune) params.append('commune', commune);
    if (rayon) params.append('rayon', rayon);
    if (type) params.append('type', type);
    if (accessibilite) params.append('accessibilite', accessibilite);
    if (dimension) params.append('dimension', dimension);
    
    window.location.href = `/equipements_sportifs/public/equipements?${params.toString()}`;
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

let allLoadedEquipements = [];

async function openEquipementModal(equipementId) {
    const equip = allLoadedEquipements.find(e => (e.equip_numero || e.equip_id) === equipementId);
    
    if (!equip) {
        console.error('Équipement non trouvé:', equipementId);
        return;
    }
    
    const modal = document.getElementById('equipement-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalBody = document.getElementById('modal-body');
    
    modalTitle.textContent = equip.inst_nom || equip.equip_nom || 'Sans nom';
    
    let contactHtml = '';

    if ((equip.telephone && equip.telephone.trim()) || (equip.email && equip.email.trim())) {
        contactHtml = `
            <div style="background: rgba(37, 99, 235, 0.05); padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                <h4 style="margin: 0 0 0.75rem 0; font-size: 1rem; color: var(--primary);">📞 Contact direct</h4>
                ${equip.telephone && equip.telephone.trim() ? `
                    <div class="detail-row" style="border: none; padding: 0.25rem 0;">
                        <div class="detail-label">Téléphone</div>
                        <div class="detail-value"><a href="tel:${equip.telephone}" style="color: var(--primary);">${equip.telephone}</a></div>
                    </div>
                ` : ''}
                ${equip.email && equip.email.trim() ? `
                    <div class="detail-row" style="border: none; padding: 0.25rem 0;">
                        <div class="detail-label">Email</div>
                        <div class="detail-value"><a href="mailto:${equip.email}" style="color: var(--primary);">${equip.email}</a></div>
                    </div>
                ` : ''}
            </div>
        `;
    } else {
        try {
            const depCode = equip.dep_code;
            const commune = equip.new_name || equip.arr_name;
            
            const response = await fetch(`/equipements_sportifs/public/api/contacts.php?dep_code=${depCode}&commune=${encodeURIComponent(commune)}`);
            const contact = await response.json();
            
            if (!contact.error) {
                contactHtml = `
                    <div style="background: rgba(37, 99, 235, 0.05); padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                        <h4 style="margin: 0 0 0.75rem 0; font-size: 1rem; color: var(--primary);">📞 Contact de la collectivité</h4>
                        <div class="detail-row" style="border: none; padding: 0.25rem 0;">
                            <div class="detail-label">Collectivité</div>
                            <div class="detail-value">${contact.nom_collectivite}</div>
                        </div>
                        ${contact.telephone ? `
                            <div class="detail-row" style="border: none; padding: 0.25rem 0;">
                                <div class="detail-label">Téléphone</div>
                                <div class="detail-value"><a href="tel:${contact.telephone}" style="color: var(--primary);">${contact.telephone}</a></div>
                            </div>
                        ` : ''}
                        ${contact.email ? `
                            <div class="detail-row" style="border: none; padding: 0.25rem 0;">
                                <div class="detail-label">Email</div>
                                <div class="detail-value"><a href="mailto:${contact.email}" style="color: var(--primary);">${contact.email}</a></div>
                            </div>
                        ` : ''}
                    </div>
                `;
            }
        } catch (error) {
            console.error('Erreur chargement contact:', error);
        }
    }
    
    modalBody.innerHTML = `
    ${contactHtml}
    
    <button onclick="openReservationModal(allLoadedEquipements.find(e => (e.equip_numero || e.equip_id) === '${equipementId}'))" 
            class="btn btn-primary btn-block btn-lg" 
            style="margin-bottom: 1.5rem; background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%); border: none;">
        📅 Réserver cet équipement
    </button>
    
    <div>
        <div>
            <div class="detail-row">
                <div class="detail-label">Numéro d'équipement</div>
                <div class="detail-value">${equip.equip_numero || '-'}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Type</div>
                <div class="detail-value">${equip.equip_type_name || '-'}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Ville</div>
                <div class="detail-value">${equip.arr_name || equip.new_name || '-'}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Code postal</div>
                <div class="detail-value">${equip.inst_cp || '-'}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Adresse</div>
                <div class="detail-value">${equip.inst_adresse || '-'}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Surface</div>
                <div class="detail-value">${equip.equip_surf ? equip.equip_surf + ' m²' : '-'}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Longueur</div>
                <div class="detail-value">${equip.equip_long ? equip.equip_long + ' m' : '-'}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Largeur</div>
                <div class="detail-value">${equip.equip_larg ? equip.equip_larg + ' m' : '-'}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Accessible PMR</div>
                <div class="detail-value">${equip.equip_pmr_acc === 'true' ? '✅ Oui' : '❌ Non'}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Nature du sol</div>
                <div class="detail-value">${equip.equip_nature_sol_lib || '-'}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Installation</div>
                <div class="detail-value">${equip.inst_nom || '-'}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Département</div>
                <div class="detail-value">${equip.dep_nom || equip.dep_code || '-'}</div>
            </div>
        </div>
    `;
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

document.addEventListener('click', function(e) {
    const modal = document.getElementById('equipement-modal');
    if (e.target === modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
});

let currentEquipementForReservation = null;

document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    const dateInput = document.getElementById('reservation-date');
    if (dateInput) {
        dateInput.setAttribute('min', today);
    }
});

function openReservationModal(equipement) {
    currentEquipementForReservation = equipement;
    
    const modal = document.getElementById('reservation-modal');
    const infoDiv = document.getElementById('reservation-equipement-info');
    
    infoDiv.innerHTML = `
        <h3>${equipement.inst_nom || equipement.equip_nom || 'Sans nom'}</h3>
        <p><strong>Type :</strong> ${equipement.equip_type_name || '-'}</p>
        <p><strong>Ville :</strong> ${equipement.arr_name || equipement.new_name || '-'}</p>
    `;
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeReservationModal() {
    const modal = document.getElementById('reservation-modal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
    
    document.getElementById('reservation-form').reset();
    document.querySelectorAll('.creneau-card').forEach(card => {
        card.classList.remove('selected');
    });
}

document.addEventListener('click', function(e) {
    if (e.target.closest('.creneau-card')) {
        const card = e.target.closest('.creneau-card');
        
        document.querySelectorAll('.creneau-card').forEach(c => {
            c.classList.remove('selected');
        });
        
        card.classList.add('selected');
        document.getElementById('reservation-creneau').value = card.dataset.creneau;
    }
});

document.getElementById('reservation-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const creneau = document.getElementById('reservation-creneau').value;
    if (!creneau) {
        alert('Veuillez sélectionner un créneau horaire');
        return;
    }
    
    const reservation = {
        equipement: currentEquipementForReservation,
        date: document.getElementById('reservation-date').value,
        creneau: creneau,
        personnes: document.getElementById('reservation-personnes').value,
        commentaire: document.getElementById('reservation-commentaire').value,
        timestamp: new Date().toISOString()
    };
    
    let reservations = JSON.parse(localStorage.getItem('reservations') || '[]');
    reservations.push(reservation);
    localStorage.setItem('reservations', JSON.stringify(reservations));
    
    closeReservationModal();
    
    document.getElementById('confirmation-modal').classList.add('active');
    document.body.style.overflow = 'hidden';
});

function closeConfirmationModal() {
    document.getElementById('confirmation-modal').classList.remove('active');
    document.body.style.overflow = '';
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>