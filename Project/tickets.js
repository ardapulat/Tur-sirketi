

const container = document.getElementById('tickets-container');

function biletleriGoster() {
    container.innerHTML = "";
    alinanBiletler.forEach(bilet => {
        container.innerHTML += `
            <div class="tour-card">
                <div class="tour-content">
                    <span class="ticket-id">BİLET #${bilet.id}</span>
                    <h3 class="tour-title">${bilet.title}</h3>
                    <div class="tour-meta">
                        <div class="meta-item"><i class="fas fa-calendar"></i> ${bilet.start_date.split(' ')[0]}</div>
                        <div class="meta-item"><i class="fas fa-route"></i> ${bilet.departure_city} > ${bilet.arrival_city}</div>
                    </div>
                    <div class="tour-footer">
                        <span class="tour-price">${bilet.price} ₺</span>
                        <button class="btn-cancel" onclick="biletIptal(${bilet.id})">İptal Et</button>
                    </div>
                </div>
            </div>`;
    });
}

// Hata almamak için bu ismin yukarıdaki onclick ile aynı olması şart!
function biletIptal(id) {
    alert(id + " ID'li bilet için iptal işlemi başlatıldı.");
}

window.onload = biletleriGoster;