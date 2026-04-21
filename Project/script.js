// Tur resimleri ve linkleri (dinamik olarak güncellenebilir)
let tourData = [
    { image: "images/Kapadokya.jpg", link: "tours.php#kapadokya-turu" },
    { image: "images/Karadeniz.jpg", link: "tours.php#rize-turu" },
    { image: "images/Ege.jpg", link: "tours.php#izmir-turu" },
    { image: "images/Akdeniz.jpg", link: "tours.php#antalya-turu" },
    { image: "images/GAP.jpg", link: "tours.php#gap-turu" },
    { image: "images/BatiKaradeniz.jpg", link: "tours.php#zonguldak-turu" },
    { image: "images/Likya.jpg", link: "tours.php#likya-yolu-turu" },
    { image: "images/Uludağ.jpg", link: "tours.php#uludag-kayak-turu" },
    { image: "images/Yunan.jpg", link: "tours.php#yunan-adalari-turu" },
    { image: "images/Balkanlar.jpg", link: "tours.php#balkanlar-turu" },
    { image: "images/Marmaris.jpg", link: "tours.php#marmaris-datca-turu" },
    { image: "images/Fırtına.jpg", link: "tours.php#firtina-vadisi-turu" },
    { image: "images/Pamukkale.jpg", link: "tours.php#pamukkale-turu" },
    { image: "images/Cunda.jpg", link: "tours.php#ayvalik-cunda-turu" },
    { image: "images/Assos.jpg", link: "tours.php#assos-bozcaada-turu" }
];

// Şehir-tur eşleştirmesi (dinamik olarak güncellenebilir)
let cityTourMap = {
    'izmir': 'izmir-turu',
    'antalya': 'antalya-turu',
    'erzurum': 'erzurum-turu',
    'zonguldak': 'zonguldak-turu',
    'rize': 'rize-turu',
    'kapadokya': 'kapadokya-turu',
    'nevşehir': 'kapadokya-turu',
    'nevesehir': 'kapadokya-turu',
    'bursa': 'uludag-kayak-turu',
    'uludağ': 'uludag-kayak-turu',
    'uludag': 'uludag-kayak-turu',
    'muğla': 'marmaris-datca-turu',
    'mugla': 'marmaris-datca-turu',
    'marmaris': 'marmaris-datca-turu',
    'denizli': 'pamukkale-turu',
    'pamukkale': 'pamukkale-turu',
    'balıkesir': 'ayvalik-cunda-turu',
    'balikesir': 'ayvalik-cunda-turu',
    'ayvalık': 'ayvalik-cunda-turu',
    'ayvalik': 'ayvalik-cunda-turu',
    'çanakkale': 'assos-bozcaada-turu',
    'canakkale': 'assos-bozcaada-turu',
    'assos': 'assos-bozcaada-turu',
    'trabzon': 'firtina-vadisi-turu',
    'safranbolu': 'safranbolu-turu',
    'karabük': 'safranbolu-turu',
    'karabuk': 'safranbolu-turu'
};

// Mevcut şehirler listesi (dinamik olarak güncellenebilir)
let cities = Object.keys(cityTourMap).sort();

let index = 0;

function showImage() {
    const sliderImage = document.getElementById("sliderImage");
    if (sliderImage) {
        sliderImage.src = tourData[index].image;
    }
}

function nextImage() {
    index = (index + 1) % tourData.length;
    showImage();
}

function prevImage() {
    index = (index - 1 + tourData.length) % tourData.length;
    showImage();
}

// Resme tıklandığında ilgili tura yönlendir
function goToTour() {
    window.location.href = tourData[index].link;
}

// Autocomplete fonksiyonları
function setupAutocomplete() {
    const whereInput = document.getElementById('where');
    const autocompleteList = document.getElementById('autocomplete-list');
    
    if (!whereInput || !autocompleteList) return;
    
    whereInput.addEventListener('input', function(e) {
        const value = e.target.value.toLowerCase().trim();
        
        if (value.length === 0) {
            autocompleteList.style.display = 'none';
            return;
        }
        
        // Eşleşen şehirleri bul
        const matches = cities.filter(city => 
            city.toLowerCase().startsWith(value)
        );
        
        if (matches.length === 0) {
            autocompleteList.style.display = 'none';
            return;
        }
        
        // Autocomplete listesini oluştur
        autocompleteList.innerHTML = '';
        matches.forEach(city => {
            const item = document.createElement('div');
            item.className = 'autocomplete-item';
            item.textContent = city.charAt(0).toUpperCase() + city.slice(1);
            item.addEventListener('click', function() {
                whereInput.value = city.charAt(0).toUpperCase() + city.slice(1);
                autocompleteList.style.display = 'none';
            });
            autocompleteList.appendChild(item);
        });
        
        autocompleteList.style.display = 'block';
    });
    
    // Input dışına tıklandığında listeyi kapat
    document.addEventListener('click', function(e) {
        if (!whereInput.contains(e.target) && !autocompleteList.contains(e.target)) {
            autocompleteList.style.display = 'none';
        }
    });
}

// Arama fonksiyonu
function handleSearch(event) {
    event.preventDefault();
    
    const whereInput = document.getElementById('where');
    if (!whereInput) return;
    
    const city = whereInput.value.toLowerCase().trim();
    // Türkçe karakterleri normalize et
    const cityNormalized = city
        .replace(/ı/g, 'i')
        .replace(/ğ/g, 'g')
        .replace(/ü/g, 'u')
        .replace(/ş/g, 's')
        .replace(/ö/g, 'o')
        .replace(/ç/g, 'c');
    
    // Önce normalize edilmiş haliyle dene, sonra orijinal haliyle
    let tourId = cityTourMap[cityNormalized] || cityTourMap[city];
    
    // Eğer bulunamadıysa, tüm key'lerde ara (kısmi eşleşme)
    if (!tourId) {
        for (const key in cityTourMap) {
            if (key.includes(city) || city.includes(key)) {
                tourId = cityTourMap[key];
                break;
            }
        }
    }
    
    if (tourId) {
        // tours.php kullanıyoruz artık
        window.location.href = `tours.php#${tourId}`;
    } else {
        alert('Lütfen geçerli bir şehir seçin.');
    }
}

// Sayfa yüklendiğinde ilk resmi göster
window.addEventListener('DOMContentLoaded', function() {
    showImage();
    
    // Slider resmini tıklanabilir yap
    const sliderImage = document.getElementById("sliderImage");
    if (sliderImage) {
        sliderImage.style.cursor = "pointer";
        sliderImage.addEventListener('click', goToTour);
    }
    
    // Autocomplete'i ayarla
    setupAutocomplete();
});

// Turlar sayfasında hash varsa ilgili tura scroll yap (sadece tours.html'de çalışır)
if (window.location.pathname.includes('tours.html') && window.location.hash) {
    window.addEventListener('DOMContentLoaded', function() {
        const hash = window.location.hash.substring(1);
        const targetElement = document.getElementById(hash);
        if (targetElement) {
            setTimeout(function() {
                targetElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                // Belirgin vurgulama efekti
                targetElement.classList.add('highlighted');
                
                // 4 saniye sonra efekti kaldır
                setTimeout(function() {
                    targetElement.classList.remove('highlighted');
                    // Smooth geçiş için
                    targetElement.style.transition = 'all 0.5s ease';
                }, 4000);
            }, 100);
        }
    });
}