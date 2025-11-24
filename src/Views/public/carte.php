<div class="map-container">
    <!-- Sidebar filtres -->
    <aside class="map-sidebar">
        <h2 class="map-sidebar-title">Rechercher des équipements</h2>
        
        <form id="filters-form">
            <div class="form-group">
                <label class="form-label">Type d'équipement</label>
                <select class="form-select" id="filter-type" name="type">
                    <option value="">Tous les types</option>
                    <?php foreach ($typesEquipements as $type): ?>
                        <option value="<?= e($type['nom']) ?>"><?= e($type['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Accessibilité</label>
                <select class="form-select" id="filter-accessibilite" name="accessibilite">
                    <option value="">Toutes les options</option>
                    <option value="1">Accessible PMR</option>
                    <option value="0">Non accessible PMR</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Statut</label>
                <select class="form-select" id="filter-statut" name="statut">
                    <option value="">Tous les statuts</option>
                    <option value="en_service">En service</option>
                    <option value="en_travaux">En travaux</option>
                    <option value="ferme">Fermé</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Commune</label>
                <input type="text" class="form-input" id="filter-commune" name="commune" placeholder="Rechercher une commune...">
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
    
    <!-- Carte -->
    <div class="map-wrapper">
        <div id="map"></div>
        
        <!-- Légende -->
        <div class="map-legend">
            <strong style="display: block; margin-bottom: 0.5rem;">Légende</strong>
            <div class="legend-item">
                <span class="legend-dot en-service"></span>
                <span>En service</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot en-travaux"></span>
                <span>En travaux</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot ferme"></span>
                <span>Fermé</span>
            </div>
        </div>
    </div>
</div>

<!-- Liste des équipements (en bas) -->
<div class="container">
    <div class="equipements-grid mt-4" id="equipements-list">
        <!-- Les équipements seront chargés ici via JavaScript -->
        <p class="text-muted">Chargement des équipements...</p>
    </div>
</div>

<script>
// Configuration de la carte
const API_URL = 'https://equipements.sports.gouv.fr/api/explore/v2.1/catalog/datasets/data-es/records';

let map;
let markers = [];

// Initialisation de la carte
document.addEventListener('DOMContentLoaded', function() {
    initMap();
    loadEquipements();
    
    // Gestion du formulaire de filtres
    document.getElementById('filters-form').addEventListener('submit', function(e) {
        e.preventDefault();
        loadEquipements();
    });
});

function initMap() {
    // Centrer sur la France
    map = L.map('map').setView([46.603354, 1.888334], 6);
    
    // Ajouter le fond de carte OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
}

async function loadEquipements() {

    const type = document.getElementById('filter-type').value;
    const commune = document.getElementById('filter-commune').value;
    const statut = document.getElementById('filter-statut').value;
    
    let whereConditions = [];
    if (type) {
        whereConditions.push(`equip_type_name="${type}"`);
    }
    if (commune) {
        whereConditions.push(`new_name LIKE "${commune}%"`);
    }
    
    const whereClause = whereConditions.length > 0 
        ? `&where=${encodeURIComponent(whereConditions.join(' AND '))}` 
        : '';
    
    try {

        const limit = 100;
        const totalToFetch = 500;
        let allEquipements = [];
        let offset = 0;
        
        while (allEquipements.length < totalToFetch) {
            console.log('Boucle infinie');
            const url = `${API_URL}?limit=${limit}&offset=${offset}${whereClause}`;
            const reponse = await fetch(url);
            const data = await reponse.json();

            if (!data.results || data.results.length === 0) break;

            allEquipements = allEquipements.concat(data.results);
            offset += limit;

            if (allEquipements.length >= data.total_count) break;
        }

        const finalCountResponse = await fetch(`${API_URL}?limit=0${whereClause}`);
        const finalCountData = await finalCountResponse.json();
        document.getElementById('total-count').textContent = formatNumber(finalCountData.total_count || 0);

        displayMarkers(allEquipements);
        displayEquipementsList(allEquipements);
        
    } catch (error) {
        console.error('Erreur lors du chargement des équipements:', error);
        document.getElementById('total-count').textContent = '0';
    }
}

function displayMarkers(equipements) {
    // Supprimer les anciens marqueurs
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];
    
    // Ajouter les nouveaux marqueurs
    equipements.forEach(equip => {
        if (equip.equip_coordonnees) {
            const lat = equip.equip_coordonnees.lat;
            const lon = equip.equip_coordonnees.lon;
            
            if (lat && lon) {
                // Couleur selon le statut (simulé pour l'instant)
                const color = '#22c55e'; // Vert par défaut (en service)
                
                const redIcon = L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                });

                // Remplace ton code par :
                const marker = L.marker([lat, lon], {
                    icon: redIcon
                });
                
                // Popup avec les infos
                marker.bindPopup(`
                    <strong>${equip.equip_nom || 'Sans nom'}</strong><br>
                    <em>${equip.equip_type_name || 'Type inconnu'}</em><br>
                    ${equip.new_name || ''} ${equip.inst_cp || ''}<br>
                    ${equip.inst_adresse || 'Adresse non renseignée'}
                `);
                
                marker.addTo(map);
                markers.push(marker);
            }
        }
    });
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

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
