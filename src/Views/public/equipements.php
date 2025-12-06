<div class="container">
    <div class="page-header">
        <h1 class="page-title">Liste des Équipements</h1>
        <p class="page-subtitle">Consultez les équipements sportifs de votre région.</p>
    </div>

    <?php
    $commune = $_GET['commune'] ?? null;
    $rayon = $_GET['rayon'] ?? null;
    $type = $_GET['type'] ?? null;
    $accessibilite = $_GET['accessibilite'] ?? null;
    $dimension = $_GET['dimension'] ?? null;
    $equipementId = $_GET['id'] ?? null;
    ?>

    <?php if ($commune || $type || $accessibilite || $dimension): ?>
    <div class="card" style="margin-bottom: 2rem; background: rgba(37, 99, 235, 0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <strong>Recherche active :</strong>
                <?php if ($commune): ?>
                    <span class="badge badge-primary">📍 <?= e($commune) ?></span>
                <?php endif; ?>
                <?php if ($rayon): ?>
                    <span class="badge badge-primary">🎯 <?= e($rayon) ?> km</span>
                <?php endif; ?>
                <?php if ($type): ?>
                    <span class="badge badge-primary">🏃 <?= e($type) ?></span>
                <?php endif; ?>
                <?php if ($accessibilite): ?>
                    <span class="badge badge-primary">♿ Accessible PMR</span>
                <?php endif; ?>
            </div>
            <a href="/equipements_sportifs/public/carte" class="btn btn-outline btn-sm">← Retour à la carte</a>
        </div>
    </div>
    <?php endif; ?>

    <div id="equipements-container">
        <div style="text-align: center; padding: 3rem;">
            <div class="spinner" style="width: 60px; height: 60px; border: 6px solid rgba(59, 130, 246, 0.2); border-top: 6px solid #3b82f6; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto;"></div>
            <p style="margin-top: 1rem; color: var(--text-muted);">Chargement des équipements...</p>
        </div>
    </div>

    <div id="equipement-modal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title" id="modal-title">Détails de l'équipement</h2>
                <button type="button" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted); line-height: 1;" onclick="document.getElementById('equipement-modal').classList.remove('active'); document.body.style.overflow = '';">&times;</button>
            </div>
            <div class="modal-body" id="modal-body"></div>
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
    z-index: 2000;
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
}
</style>

<script>
const searchParams = {
    commune: <?= json_encode($commune) ?>,
    rayon: <?= json_encode($rayon) ?>,
    type: <?= json_encode($type) ?>,
    accessibilite: <?= json_encode($accessibilite) ?>,
    dimension: <?= json_encode($dimension) ?>,
    equipementId: <?= json_encode($equipementId) ?>
};

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

async function loadEquipements() {
    const container = document.getElementById('equipements-container');
    
    // Si pas de commune, afficher message
    if (!searchParams.commune) {
        container.innerHTML = '<div class="card text-center" style="padding: 3rem;"><p style="font-size: 1.125rem; color: var(--text-muted);">Utilisez la carte pour rechercher des équipements.</p><a href="/equipements_sportifs/public/carte" class="btn btn-primary mt-3">Aller à la carte</a></div>';
        return;
    }
    
    try {
        // Géocodage de la commune
        const geoResponse = await fetch(`https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(searchParams.commune)}&type=municipality&limit=1`);
        const geoData = await geoResponse.json();
        
        if (!geoData.features || geoData.features.length === 0) {
            container.innerHTML = '<div class="card text-center" style="padding: 3rem;"><p style="color: var(--danger);">Commune introuvable.</p></div>';
            return;
        }
        
        const communeCoords = {
            lat: geoData.features[0].geometry.coordinates[1],
            lon: geoData.features[0].geometry.coordinates[0]
        };
        
        const departementPrincipal = geoData.features[0].properties.citycode.substring(0, 2);
        const rayon = parseInt(searchParams.rayon) || 5;
        
        // Départements à chercher
        const departementsAChercher = rayon > 50 && departementsVoisins[departementPrincipal] 
            ? departementsVoisins[departementPrincipal] 
            : [departementPrincipal];
        
        // Construire les conditions WHERE
        let whereConditions = [];
        
        if (departementsAChercher.length === 1) {
            whereConditions.push(`dep_code="${departementsAChercher[0]}"`);
        } else {
            const depConditions = departementsAChercher.map(d => `dep_code="${d}"`).join(' OR ');
            whereConditions.push(`(${depConditions})`);
        }
        
        if (searchParams.type) {
            whereConditions.push(`equip_type_name="${searchParams.type}"`);
        }
        if (searchParams.accessibilite) {
            whereConditions.push(`equip_pmr_acc="${searchParams.accessibilite}"`);
        }
        
        const whereClause = `&where=${encodeURIComponent(whereConditions.join(' AND '))}`;
        
        // Charger tous les équipements
        const limit = 100;
        let allEquipements = [];
        let offset = 0;
        
        while (true) {
            const url = `${API_URL}?limit=${limit}&offset=${offset}${whereClause}`;
            const response = await fetch(url);
            const data = await response.json();
            
            if (!data.results || data.results.length === 0) break;
            allEquipements = allEquipements.concat(data.results);
            if (data.results.length < limit) break;
            offset += limit;
            if (offset >= 10000) break;
            
            await new Promise(resolve => setTimeout(resolve, 100));
        }
        
        // Filtrer par rayon et dimension
        let filteredEquipements = allEquipements.filter(equip => {
            if (!equip.equip_coordonnees) return false;
            const lat = equip.equip_coordonnees.lat;
            const lon = equip.equip_coordonnees.lon;
            if (!lat || !lon) return false;
            
            const distance = calculateDistance(communeCoords.lat, communeCoords.lon, lat, lon);
            if (distance > rayon) return false;
            
            if (searchParams.dimension) {
                const maxSurface = parseFloat(searchParams.dimension);
                const surface = parseFloat(equip.equip_surf) || 
                    (parseFloat(equip.equip_long) || 0) * (parseFloat(equip.equip_larg) || 0);
                if (surface <= 0 || surface > maxSurface) return false;
            }
            
            return true;
        });
        
        displayEquipements(filteredEquipements);
        
    } catch (error) {
        console.error('Erreur:', error);
        container.innerHTML = '<div class="card text-center" style="padding: 3rem;"><p style="color: var(--danger);">Erreur lors du chargement des équipements.</p></div>';
    }
}

function displayEquipements(equipements) {
    const container = document.getElementById('equipements-container');
    
    // Stocker les équipements globalement
    allLoadedEquipements = equipements;
    
    if (equipements.length === 0) {
        container.innerHTML = '<div class="card text-center" style="padding: 3rem;"><p style="font-size: 1.125rem; color: var(--text-muted);">Aucun équipement trouvé avec ces critères.</p></div>';
        return;
    }
    
    container.innerHTML = `
        <div style="margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.25rem; font-weight: 600;">${formatNumber(equipements.length)} équipement${equipements.length > 1 ? 's' : ''} trouvé${equipements.length > 1 ? 's' : ''}</h3>
        </div>
        <div class="equipements-grid">
            ${equipements.map(equip => `
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
                    <p class="equipement-card-info"><strong>Surface :</strong> ${equip.equip_surf ? equip.equip_surf + ' m²' : '-'}</p>
                </div>
            `).join('')}
        </div>
    `;
}

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

// Stocker tous les équipements chargés
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

document.addEventListener('DOMContentLoaded', loadEquipements);
</script>