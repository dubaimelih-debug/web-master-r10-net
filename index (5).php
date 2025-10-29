<?php
// ============================================
// YAPILANDIRMA - BURADAN DEĞİŞTİREBİLİRSİNİZ
// ============================================
$site_name = "Satilik Hesap";
$ana_site = "https://satilikhesap.net/";
$logo_url = "https://storage.perfectcdn.com/hurvyl/wlozj8za05bat1oe.png";
//$telefon = "+905555555555";
$base_url = "https://satilikhesap.com/";

// PLATFORMLAR VE HİZMETLER
$platformlar = [
    'instagram' => 'Instagram',
    'tiktok' => 'TikTok',
    'youtube' => 'YouTube',
    'twitter' => 'Twitter (X)',
    'facebook' => 'Facebook',
    'telegram' => 'Telegram',
    'discord' => 'Discord',
    
    /* HİZMET EKLERKEN BURADAN EKLEYECEKSİNİZ HOCAM*/
];

$hizmetler = [
    'takipci' => 'Takipçi',
    'begeni' => 'Beğeni',
    'izlenme' => 'İzlenme',
    'kaydetme' => 'Kaydetme',
    'yorum' => 'Yorum'
];

// TÜRKİYE İLLERİ VERİLERİ
include "turkey.php";

// URL PARSE VE DİNAMİK İÇERİK BELİRLEME
function turkishToSlug($text) {
    $search = ['ç','ğ','ı','ö','ş','ü','Ç','Ğ','İ','Ö','Ş','Ü',' '];
    $replace = ['c','g','i','o','s','u','c','g','i','o','s','u','-'];
    return strtolower(str_replace($search, $replace, $text));
}

$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = rtrim($path, '/');
$path = trim($path,'/');

// Varsayılan değerler
$il = null;
$il_slug = null;
$ilce = null;
$ilce_slug = null;
$mahalle = null;
$mahalle_slug = null;
$platform = 'instagram';
$platform_name = 'Instagram';
$hizmet = 'takipci';
$hizmet_name = 'Takipçi';
$sayfa_tipi = 'anasayfa';

// URL analizi
if (!empty($path)) {
    $url_parts = explode('-', strtolower($path));
    
    foreach ($platformlar as $p_slug => $p_name) {
        if (in_array($p_slug, $url_parts)) {
            $platform = $p_slug;
            $platform_name = $p_name;
            break;
        }
    }
    
    foreach ($hizmetler as $h_slug => $h_name) {
        if (in_array($h_slug, $url_parts)) {
            $hizmet = $h_slug;
            $hizmet_name = $h_name;
            $sayfa_tipi = 'hizmet';
            break;
        }
    }
    
    foreach ($turkey_data as $i_slug => $i_data) {
        $il_pos = array_search($i_slug, $url_parts);
        if ($il_pos !== false) {
            $il = $i_data['name'];
            $il_slug = $i_slug;
            $sayfa_tipi = ($sayfa_tipi == 'hizmet') ? 'hizmet' : 'il';
            
            if (isset($i_data['ilceler']) && is_array($i_data['ilceler'])) {
                foreach ($i_data['ilceler'] as $ilce_slug_temp => $ilce_data) {
                    $ilce_pos = array_search($ilce_slug_temp, $url_parts);
                    if ($ilce_pos !== false && $ilce_pos > $il_pos) {
                        $ilce = $ilce_data['name'];
                        $ilce_slug = $ilce_slug_temp;
                        $sayfa_tipi = ($sayfa_tipi == 'hizmet') ? 'hizmet' : 'ilce';
                        
                        if (isset($ilce_data['mahalleler']) && is_array($ilce_data['mahalleler'])) {
                            foreach ($ilce_data['mahalleler'] as $m_slug => $m_name) {
                                $mahalle_pos = array_search($m_slug, $url_parts);
                                if ($mahalle_pos !== false && $mahalle_pos > $ilce_pos) {
                                    $mahalle = $m_name;
                                    $mahalle_slug = $m_slug;
                                    $sayfa_tipi = ($sayfa_tipi == 'hizmet') ? 'hizmet' : 'mahalle';
                                    break;
                                }
                            }
                        }
                        break;
                    }
                }
            }
            break;
        }
    }
}

// SEO İÇERİK OLUŞTURMA
$location = $mahalle ?: ($ilce ?: ($il ?: 'Türkiye'));
$location_full = ($il ?: 'Türkiye') . ($ilce ? ' ' . $ilce : '') . ($mahalle ? ' ' . $mahalle : '');

if ($sayfa_tipi == 'hizmet') {
    $title = "$location $platform_name Hesap Satışı | $site_name";
    $description = "$location bölgesinde güvenilir $platform_name hesap alım-satım platformu. Dolandırıcılığa karşı koruma, escrow sistemi ve 7/24 moderasyon.";
    $h1 = "$location $platform_name Hesap Satışı";
} elseif ($sayfa_tipi == 'mahalle') {
    $title = "$location Hesap Alım-Satım Forumu | $site_name";
    $description = "$location bölgesinde güvenilir hesap alım-satım platformu. Instagram, YouTube, TikTok hesap satışı.";
    $h1 = "$location Hesap Alım-Satım Forumu";
} elseif ($sayfa_tipi == 'ilce') {
    $title = "$location Hesap Satış Forumu | $site_name";
    $description = "$location ilçesinde güvenilir sosyal medya hesap alım-satım hizmetleri.";
    $h1 = "$location Hesap Satış Forumu";
} elseif ($sayfa_tipi == 'il') {
    $title = "$location Hesap Alım-Satım Platformu | $site_name";
    $description = "$location ilinde güvenilir hesap satış forumu ve alım-satım hizmetleri.";
    $h1 = "$location Hesap Alım-Satım Platformu";
} else {
    $title = "Türkiye'nin #1 Hesap Satış Forumu | Güvenli Hesap Alım-Satım | $site_name";
    $description = "Türkiye'nin en büyük ve güvenilir hesap satış forumu. Instagram, YouTube, TikTok hesap satışı ve alımı. %100 güvenli işlemler.";
    $h1 = "Türkiye'nin En Büyük Hesap Satış Forumu";
}

$keywords = "$location, hesap satışı, hesap alım-satım, $platform_name hesap, sosyal medya hesap, $site_name";

// Canonical URL
$canonical_parts = [];
if ($il_slug) $canonical_parts[] = $il_slug;
if ($ilce_slug) $canonical_parts[] = $ilce_slug;
if ($mahalle_slug) $canonical_parts[] = $mahalle_slug;
if ($sayfa_tipi == 'hizmet') {
    $canonical_parts[] = $platform;
    $canonical_parts[] = $hizmet;
    $canonical_parts[] = 'al';
}
$canonical = $base_url . (!empty($canonical_parts) ? implode('-', $canonical_parts) : '');

// GENİŞLETİLMİŞ MAKALE İÇERİĞİ - HESAP SATIŞ FORUMU
function generateExtendedArticle($location, $il, $ilce, $mahalle, $platform_name, $hizmet_name, $ana_site) {
    $location_detail = $mahalle ? "$mahalle, $ilce, $il" : ($ilce ? "$ilce, $il" : ($il ? $il : "Türkiye"));
    
    $colors = ['6366f1', '8b5cf6', 'ec4899', '06b6d4', '10b981', 'f59e0b'];
    $c1 = $colors[array_rand($colors)];
    $c2 = $colors[array_rand($colors)];
    $c3 = $colors[array_rand($colors)];
    
    return "
    <div class='article-intro'>
        <p class='lead'><strong>$location</strong>'nde <a href='$ana_site' rel='noopener'>hesap satış forumu</a> arayışınızda doğru adrestesiniz! $location_detail bölgesinde faaliyet gösteren <a href='$ana_site' rel='noopener'>hesap satış</a> platformumuz, <strong>hesap sat</strong> işlemlerinde <strong>%100 güvenlik</strong> garantisi sunmaktadır. <a href='$ana_site' rel='noopener'>r10 hesap satış</a>, <a href='$ana_site' rel='noopener'>wmaraci hesap satış</a> ve <a href='$ana_site' rel='noopener'>1yuz hesap satış</a> konularında uzman ekibimizle hizmetinizdeyiz.</p>
        
        <img src='https://placehold.co/800x450/$c1/FFF?text=Hesap+Satis+Forumu' alt='$location Hesap Satış Forumu' width='800' height='450' loading='lazy'>
    </div>
    
    <h3>$location'de Profesyonel Hesap Satış Forumu</h3>
    <p>$location bölgesinde <a href='$ana_site' rel='noopener'>hesap satış</a> olarak hizmet veren platformumuz, <strong>hesap sat</strong> işlemlerinde güvenilir çözümler sunmaktadır. <a href='$ana_site' rel='noopener'>r10 hesap satış</a>, <a href='$ana_site' rel='noopener'>wmaraci hesap satış</a> ve <a href='$ana_site' rel='noopener'>1yuz hesap satış</a> işlemlerinde de aktif rol almaktadır. <a href='$ana_site' rel='noopener'>amazon hesap satış</a> ve <a href='$ana_site' rel='noopener'>paypal hesap satış</a> konularında da uzmanız.</p>
    
    <h3>$location'de Hesap Satış Forumu Avantajları</h3>
    <p><strong>Güvenli İşlem Garantisi:</strong> $location bölgesinde <a href='$ana_site' rel='noopener'>hesap satış</a> platformumuzda tüm <strong>hesap sat</strong> işlemleri %100 güvenli şekilde gerçekleştirilir. <a href='$ana_site' rel='noopener'>instagram hesap satış</a> sürecinde dolandırıcılık koruması, kimlik doğrulama ve escrow sistemi ile işlemleriniz korunur.</p>
    
    <p><strong>Hızlı ve Kolay İşlem:</strong> <a href='$ana_site' rel='noopener'>youtube hesap satış</a>, <a href='$ana_site' rel='noopener'>tiktok hesap satış</a> ve <a href='$ana_site' rel='noopener'>facebook hesap satış</a> işlemlerinizi dakikalar içinde tamamlayabilirsiniz. <strong>Hesap satış</strong> üyelerimiz için özel olarak tasarlanmış hızlı işlem sistemi ile beklemeden hesap alabilirsiniz.</p>
    
    <h3>$location'de Hesap Satış Forumu Özellikleri</h3>
    <p><strong>7/24 Destek:</strong> $location_detail bölgesinde <a href='$ana_site' rel='noopener'>hesap satış</a> platformumuzda 7/24 aktif moderatör desteği alabilirsiniz. <strong>Hesap sat</strong> işlemlerinizde herhangi bir sorun yaşadığınızda anında yardım alabilirsiniz.</p>
    
    <p><strong>Doğrulanmış Satıcılar:</strong> <a href='$ana_site' rel='noopener'>Hesap satış</a> platformumuzda tüm satıcılar kimlik doğrulamasından geçmiştir. <a href='$ana_site' rel='noopener'>google hesap satış</a>, <a href='$ana_site' rel='noopener'>linkedin hesap satış</a> ve <a href='$ana_site' rel='noopener'>telegram hesap satış</a> kategorilerinde sadece güvenilir satıcılarla işlem yapabilirsiniz.</p>
    
    <p>Platformumuz, <strong>hesap satış</strong> işlemlerinde <strong>dolandırıcılık koruması</strong> ve <strong>escrow sistemi</strong> ile donatılmıştır. $location_detail bölgesinde <a href='$ana_site' rel='noopener'>hesap satış</a> üyelerine özel <a href='$ana_site' rel='noopener'>hesap sat</a> hizmetleri sunuyoruz. <a href='$ana_site' rel='noopener'>discord hesap satış</a> ve <a href='$ana_site' rel='noopener'>snapchat hesap satış</a> konularında da geniş seçeneklerimiz mevcuttur.</p>
    
    <div class='stats-box'>
        <div class='stat'><strong>75,000+</strong><span>Hesap Satış İşlemi</span></div>
        <div class='stat'><strong>99.9%</strong><span>Güvenlik Oranı</span></div>
        <div class='stat'><strong>24/7</strong><span>Forum Desteği</span></div>
    </div>
    
    <h3>$location'de Hesap Satış Forumu Neden Tercih Edilmeli?</h3>
    <p>$location bölgesinde <a href='$ana_site' rel='noopener'>hesap satış</a> olarak hizmet veren platformumuz, <strong>hesap sat</strong> işlemlerinde 8 yılı aşkın deneyime sahiptir. <a href='$ana_site' rel='noopener'>Hesap satış</a> konusunda uzman ekibimiz, <a href='$ana_site' rel='noopener'>r10 hesap satış</a>, <a href='$ana_site' rel='noopener'>wmaraci hesap satış</a> ve <a href='$ana_site' rel='noopener'>1yuz hesap satış</a> işlemlerinde de profesyonel destek sağlamaktadır. <strong>Güvenlik</strong>, <strong>şeffaflık</strong> ve <strong>müşteri memnuniyeti</strong> odaklı yaklaşımımız ile <a href='$ana_site' rel='noopener'>hesap satış</a> sektöründe öncü konumdayız.</p>
    
    <h3>$location'de Hesap Satış Forumu Güvenlik Sistemi</h3>
    <p><strong>Kimlik Doğrulama:</strong> $location_detail bölgesinde <a href='$ana_site' rel='noopener'>hesap satış</a> platformumuzda tüm satıcılar detaylı kimlik doğrulamasından geçer. <strong>Hesap sat</strong> işlemlerinizde sadece doğrulanmış satıcılarla işlem yapabilirsiniz.</p>
    
    <p><strong>Escrow Koruması:</strong> <a href='$ana_site' rel='noopener'>Hesap satış</a> işlemlerinizde ödemeniz güvenli escrow hesabında tutulur. <a href='$ana_site' rel='noopener'>steam hesap satış</a>, <a href='$ana_site' rel='noopener'>epic games hesap satış</a> ve <a href='$ana_site' rel='noopener'>origin hesap satış</a> işlemlerinde hesap teslimi sonrası ödeme serbest bırakılır.</p>
    
    <h3>$location'de Hesap Satış Forumu Başarı Oranları</h3>
    <p><strong>%99.9 Başarı Oranı:</strong> $location bölgesinde <a href='$ana_site' rel='noopener'>hesap satış</a> platformumuzda gerçekleştirilen işlemlerin %99.9'u başarıyla tamamlanmıştır. <strong>Hesap sat</strong> işlemlerinizde yüksek başarı oranı garantisi sunuyoruz.</p>
    
    <p><strong>Müşteri Memnuniyeti:</strong> <a href='$ana_site' rel='noopener'>Hesap satış</a> platformumuzda müşteri memnuniyet oranımız %98'dir. <a href='$ana_site' rel='noopener'>netflix hesap satış</a>, <a href='$ana_site' rel='noopener'>spotify hesap satış</a> ve <a href='$ana_site' rel='noopener'>disney hesap satış</a> kategorilerinde de aynı yüksek memnuniyet oranını koruyoruz.</p>
    
    <img src='https://placehold.co/800x450/$c2/FFF?text=Guvenli+Hesap+Satis' alt='Güvenli Hesap Satış' width='800' height='450' loading='lazy'>
    
    <p>$location_detail bölgesinde <a href='$ana_site' rel='noopener'>hesap satış</a> üyelerimiz için <strong>hesap sat</strong> işlemlerinde %100 güvenlik garantisi sunuyoruz. <a href='$ana_site' rel='noopener'>Hesap satış</a> sürecinde tüm satıcılarımız doğrulanmış, <strong>escrow sistemi</strong> ile korumalı ödemeler ve <strong>7/24 moderasyon</strong> desteği ile güvenli <a href='$ana_site' rel='noopener'>hesap satış</a> deneyimi yaşarsınız. <a href='$ana_site' rel='noopener'>roblox hesap satış</a> ve <a href='$ana_site' rel='noopener'>minecraft hesap satış</a> işlemlerinde de aynı güvenlik standartları uygulanır.</p>
    
    <h3>$location için Özel Hesap Satış Kategorileri</h3>
    <p><a href='$ana_site' rel='noopener'>Hesap satış</a> platformumuzda <strong>hesap sat</strong> işlemleri için özel kategoriler bulunmaktadır. $location bölgesinde <a href='$ana_site' rel='noopener'>hesap satış</a> ihtiyaçlarınıza uygun <a href='$ana_site' rel='noopener'>hesap seçenekleri</a> arasından seçim yapabilirsiniz. <a href='$ana_site' rel='noopener'>uber hesap satış</a>, <a href='$ana_site' rel='noopener'>airbnb hesap satış</a> ve <a href='$ana_site' rel='noopener'>booking hesap satış</a> konularında da geniş seçeneklerimiz mevcuttur.</p>
    
    <h3>$location'de Hesap Satış Forumu Üyelik Paketleri</h3>
    <p><strong>Ücretsiz Üyelik:</strong> $location bölgesinde <a href='$ana_site' rel='noopener'>hesap satış</a> platformumuzda ücretsiz üyelik ile <strong>hesap sat</strong> işlemlerinizi gerçekleştirebilirsiniz. <a href='$ana_site' rel='noopener'>Hesap satış</a> konusunda temel destek ve güvenlik hizmetlerinden yararlanabilirsiniz.</p>
    
    <p><strong>Premium Üyelik:</strong> <a href='$ana_site' rel='noopener'>zoom hesap satış</a>, <a href='$ana_site' rel='noopener'>skype hesap satış</a> ve <a href='$ana_site' rel='noopener'>teams hesap satış</a> işlemlerinizde premium üyelik ile özel avantajlar elde edebilirsiniz. <strong>Hesap satış</strong> premium üyeleri için özel fiyatlandırma ve hızlı işlem imkanları sunuyoruz.</p>
    
    <h3>$location'de Hesap Satış Forumu Müşteri Yorumları</h3>
    <p><strong>5 Yıldız Değerlendirme:</strong> $location_detail bölgesinde <a href='$ana_site' rel='noopener'>hesap satış</a> platformumuz müşterilerimizden 4.9/5 yıldız almıştır. <strong>Hesap sat</strong> işlemlerinde güvenilirlik ve hızlı teslimat konularında yüksek puanlar aldık.</p>
    
    <p><strong>Müşteri Referansları:</strong> <a href='$ana_site' rel='noopener'>Hesap satış</a> platformumuzda binlerce memnun müşterimiz bulunmaktadır. <a href='$ana_site' rel='noopener'>dropbox hesap satış</a>, <a href='$ana_site' rel='noopener'>onedrive hesap satış</a> ve <a href='$ana_site' rel='noopener'>icloud hesap satış</a> kategorilerinde de müşteri memnuniyeti odaklı hizmet sunuyoruz.</p>
    
    <ul class='benefits'>
        <li><strong>Facebook Hesap Satış</strong> - <a href='$ana_site' rel='noopener'>Hesap satış</a> kategorisinde sayfa ve grup yöneticisi hesapları</li>
        <li><strong>Instagram Hesap Satış</strong> - <a href='$ana_site' rel='noopener'>Hesap satış</a> üyeleri için takipçi sayısına göre sınıflandırılmış</li>
        <li><strong>Google Hesap Satış</strong> - <strong>Hesap sat</strong> işlemlerinde Gmail, Google Ads ve AdSense hesapları</li>
        <li><strong>YouTube Hesap Satış</strong> - <a href='$ana_site' rel='noopener'>Hesap satış</a> için abone sayısı ve izlenme oranları ile değerlendirilmiş</li>
        <li><strong>TikTok Hesap Satış</strong> - <a href='$ana_site' rel='noopener'>r10 hesap satış</a> kategorisinde viral potansiyeli yüksek hesaplar</li>
        <li><strong>Twitter (X) Hesap Satış</strong> - <strong>Hesap satış</strong> için etkileşim oranları ile değerlendirilmiş</li>
        <li><strong>LinkedIn Hesap Satış</strong> - <a href='$ana_site' rel='noopener'>wmaraci hesap satış</a> için profesyonel ağ hesapları</li>
        <li><strong>Telegram Hesap Satış</strong> - <a href='$ana_site' rel='noopener'>1yuz hesap satış</a> kategorisinde kanal ve grup yöneticisi hesapları</li>
        <li><strong>Discord Hesap Satış</strong> - <strong>Hesap satış</strong> için sunucu yöneticisi ve bot hesapları</li>
        <li><strong>Snapchat Hesap Satış</strong> - <a href='$ana_site' rel='noopener'>Hesap satış</a> üyeleri için genç kitle odaklı hesaplar</li>
        <li><strong>Pinterest Hesap Satış</strong> - <a href='$ana_site' rel='noopener'>Hesap satış</a> için görsel içerik ve reklam hesapları</li>
        <li><strong>Reddit Hesap Satış</strong> - <strong>Hesap sat</strong> işlemlerinde karma puanı yüksek hesaplar</li>
        <li><strong>WhatsApp Business Hesap Satış</strong> - <a href='$ana_site' rel='noopener'>r10 hesap satış</a> kategorisinde işletme hesapları</li>
        <li><strong>Twitch Hesap Satış</strong> - <a href='$ana_site' rel='noopener'>wmaraci hesap satış</a> için yayıncı ve abone hesapları</li>
        <li><strong>Spotify Hesap Satış</strong> - <a href='$ana_site' rel='noopener'>1yuz hesap satış</a> kategorisinde müzik ve podcast hesapları</li>
        <li><strong>AdSense Hesap Satış</strong> - <strong>Hesap satış</strong> için Google AdSense onaylı hesaplar</li>
        <li><strong>AdWords Hesap Satış</strong> - <a href='$ana_site' rel='noopener'>Hesap satış</a> için Google Ads reklam hesapları</li>
        <li><strong>Amazon Hesap Satış</strong> - <a href='$ana_site' rel='noopener'>Hesap satış</a> için satıcı ve affiliate hesapları</li>
        <li><strong>PayPal Hesap Satış</strong> - <strong>Hesap sat</strong> işlemlerinde doğrulanmış ödeme hesapları</li>
        <li><strong>Steam Hesap Satış</strong> - <a href='$ana_site' rel='noopener'>Hesap satış</a> üyeleri için oyun kütüphanesi hesapları</li>
    </ul>
    
    <h3>Güvenli Hesap Satış Sürecimiz</h3>
    <p><strong>1. Hesap Seçimi:</strong> $location için uygun <a href='$ana_site' rel='noopener'>hesap satış</a> kategorilerini inceleyin ve <strong>hesap sat</strong> ihtiyacınıza uygun olanı seçin. <a href='$ana_site' rel='noopener'>r10 hesap satış</a>, <a href='$ana_site' rel='noopener'>wmaraci hesap satış</a> ve <a href='$ana_site' rel='noopener'>1yuz hesap satış</a> seçeneklerini de değerlendirebilirsiniz.</p>
    <p><strong>2. Güvenli Ödeme:</strong> <a href='$ana_site' rel='noopener'>Hesap satış</a> escrow sistemi ile korumalı ödeme yapın, paranız güvende kalır.</p>
    <p><strong>3. Hesap Teslimi:</strong> <strong>Hesap satış</strong> sürecinde satıcı hesap bilgilerini güvenli kanallardan iletir.</p>
    <p><strong>4. Doğrulama & Onay:</strong> <a href='$ana_site' rel='noopener'>r10 hesap satış</a>, <a href='$ana_site' rel='noopener'>wmaraci hesap satış</a> ve <a href='$ana_site' rel='noopener'>1yuz hesap satış</a> işlemlerinde hesabı kontrol edin, memnun kaldığınızda ödeme serbest bırakılır.</p>
    
    <img src='https://placehold.co/800x450/$c3/FFF?text=Hesap+Satis+Islem+Suresi' alt='Hesap Satış İşlem Süreci' width='800' height='450' loading='lazy'>
    
    <h3>$location'de Hesap Satış Başarı Hikayeleri</h3>
    <p>$location_detail bölgesinde binlerce <a href='$ana_site' rel='noopener'>hesap satış</a> üyesi <strong>hesap sat</strong> işlemlerini güvenle tamamladı. <a href='$ana_site' rel='noopener'>Hesap satış</a> konusunda kanıtlanmış güvenilirliğimiz, <a href='$ana_site' rel='noopener'>r10 hesap satış</a>, <a href='$ana_site' rel='noopener'>wmaraci hesap satış</a> ve <a href='$ana_site' rel='noopener'>1yuz hesap satış</a> işlemlerinde de devam etmektedir. <strong>Hesap satış</strong> platformumuzda gerçekleştirilen işlemlerin %99.9'u başarıyla tamamlanmıştır.</p>
    
    <p>Sosyal medya hedeflerinize ulaşmak, işinizi büyütmek ve dijital varlığınızı güçlendirmek için $location bölgesine özel <a href='$ana_site' rel='noopener'>hesap satış</a> hizmetlerimizden yararlanın! <strong>Hesap sat</strong> işlemlerinde uzman ekibimiz her zaman yanınızda. <a href='$ana_site' rel='noopener'>r10 hesap satış</a>, <a href='$ana_site' rel='noopener'>wmaraci hesap satış</a> ve <a href='$ana_site' rel='noopener'>1yuz hesap satış</a> konularında da profesyonel destek alabilirsiniz.</p>
    
    <h3>Hesap Satış Forumu Güvenlik Önlemleri</h3>
    <p><strong>Dolandırıcılık koruması nasıl çalışır?</strong> <a href='$ana_site' rel='noopener'>Hesap satış</a> platformumuzda tüm satıcılar kimlik doğrulamasından geçer, <strong>hesap sat</strong> işlemleri moderatörler tarafından takip edilir. <a href='$ana_site' rel='noopener'>r10 hesap satış</a>, <a href='$ana_site' rel='noopener'>wmaraci hesap satış</a> ve <a href='$ana_site' rel='noopener'>1yuz hesap satış</a> işlemlerinde de aynı güvenlik protokolleri uygulanır.</p>
    <p><strong>Escrow sistemi nedir?</strong> <a href='$ana_site' rel='noopener'>Hesap satış</a> işlemlerinde ödemeniz güvenli bir hesapta tutulur, hesap teslimi sonrası serbest bırakılır.</p>
    <p><strong>Hesap garantisi var mı?</strong> Evet, tüm <a href='$ana_site' rel='noopener'>hesap satış</a> işlemlerimiz doğrulanmış ve garantilidir. <strong>r10 hesap satış</strong>, <strong>wmaraci hesap satış</strong> ve <strong>1yuz hesap satış</strong> konularında da aynı güvenlik standartları uygulanır.</p>
    
    <h3>Hesap Satış Forumu Üyelik Avantajları</h3>
    <p><a href='$ana_site' rel='noopener'>Hesap satış</a> üyelerimiz için özel avantajlar sunuyoruz. <strong>Hesap sat</strong> işlemlerinde komisyon indirimleri, <a href='$ana_site' rel='noopener'>hesap satış</a> konusunda öncelikli destek ve <a href='$ana_site' rel='noopener'>r10 hesap satış</a>, <a href='$ana_site' rel='noopener'>wmaraci hesap satış</a>, <a href='$ana_site' rel='noopener'>1yuz hesap satış</a> işlemlerinde özel fiyatlandırma seçenekleri mevcuttur.</p>
    
    <p><strong>Hesap satış</strong> platformumuzda premium üyelik paketleri ile <strong>hesap sat</strong> işlemlerinizde ekstra güvenlik ve hızlı işlem imkanları elde edebilirsiniz. <a href='$ana_site' rel='noopener'>Hesap satış</a> konusunda deneyimli moderatörlerimiz 7/24 hizmetinizdedir. <a href='$ana_site' rel='noopener'>r10 hesap satış</a>, <a href='$ana_site' rel='noopener'>wmaraci hesap satış</a> ve <a href='$ana_site' rel='noopener'>1yuz hesap satış</a> konularında da özel destek alabilirsiniz.</p>
    
    <h3>$location'de Hesap Satış Forumu Geleceği</h3>
    <p>$location bölgesinde <a href='$ana_site' rel='noopener'>hesap satış</a> olarak sürekli gelişim içindeyiz. <strong>Hesap sat</strong> teknolojilerinde yenilikler, <a href='$ana_site' rel='noopener'>hesap satış</a> güvenlik protokollerinde iyileştirmeler ve <a href='$ana_site' rel='noopener'>r10 hesap satış</a>, <a href='$ana_site' rel='noopener'>wmaraci hesap satış</a>, <a href='$ana_site' rel='noopener'>1yuz hesap satış</a> konularında yeni özellikler eklemeye devam ediyoruz.</p>
    
    <p><strong>Hesap satış</strong> platformumuzda blockchain teknolojisi entegrasyonu, AI destekli dolandırıcılık tespiti ve gelişmiş <strong>hesap sat</strong> analitik araçları yakında hizmetinizde olacak. <a href='$ana_site' rel='noopener'>r10 hesap satış</a>, <a href='$ana_site' rel='noopener'>wmaraci hesap satış</a> ve <a href='$ana_site' rel='noopener'>1yuz hesap satış</a> işlemlerinde de bu yeniliklerden faydalanabileceksiniz.</p>
    
    <h3>Popüler Sosyal Medya Platformları ve Hesap Satış</h3>
    <p>$location bölgesinde <a href='$ana_site' rel='noopener'>hesap satış</a> platformumuzda en çok tercih edilen sosyal medya hesapları bulunmaktadır. <strong>Facebook hesap satış</strong>, <strong>Instagram hesap satış</strong> ve <strong>Google hesap satış</strong> kategorilerinde binlerce seçenek mevcuttur. <a href='$ana_site' rel='noopener'>YouTube hesap satış</strong>, <strong>TikTok hesap satış</strong> ve <strong>Twitter hesap satış</strong> işlemlerinde de yüksek başarı oranları elde ediyoruz.</p>
    
    <p><strong>LinkedIn hesap satış</strong>, <strong>Telegram hesap satış</strong> ve <strong>Discord hesap satış</strong> konularında profesyonel destek sağlıyoruz. <a href='$ana_site' rel='noopener'>Snapchat hesap satış</strong>, <strong>Pinterest hesap satış</strong> ve <strong>Reddit hesap satış</strong> kategorilerinde de geniş seçeneklerimiz bulunmaktadır. <strong>WhatsApp Business hesap satış</strong>, <strong>Twitch hesap satış</strong> ve <strong>Spotify hesap satış</strong> işlemlerinde güvenli alım-satım garantisi sunuyoruz.</p>
    
    <p><strong>AdSense hesap satış</strong> ve <strong>AdWords hesap satış</strong> konularında Google onaylı hesaplarımız mevcuttur. <a href='$ana_site' rel='noopener'>Amazon hesap satış</strong>, <strong>PayPal hesap satış</strong> ve <strong>Steam hesap satış</strong> kategorilerinde de doğrulanmış hesaplar sunuyoruz. <a href='$ana_site' rel='noopener'>r10 hesap satış</strong>, <a href='$ana_site' rel='noopener'>wmaraci hesap satış</strong> ve <a href='$ana_site' rel='noopener'>1yuz hesap satış</strong> platformlarında da aktif olarak hizmet vermekteyiz.</p>
    
    <h3>Hesap Satış Forumu İstatistikleri</h3>
    <p><a href='$ana_site' rel='noopener'>Hesap satış</a> platformumuzda günlük ortalama 750+ <strong>hesap sat</strong> işlemi gerçekleştirilmektedir. <strong>Facebook hesap satış</strong> kategorisinde %96 memnuniyet oranı, <strong>Instagram hesap satış</strong> kategorisinde %98 güvenlik skoru, <strong>Google hesap satış</strong> kategorisinde %97 başarı oranı ile sektörde öncü konumdayız. <a href='$ana_site' rel='noopener'>r10 hesap satış</a> kategorisinde %95 memnuniyet oranı, <a href='$ana_site' rel='noopener'>wmaraci hesap satış</a> kategorisinde %98 güvenlik skoru ve <a href='$ana_site' rel='noopener'>1yuz hesap satış</a> kategorisinde %97 başarı oranı elde ediyoruz.</p>
    
    <div class='cta-inline'>
        <strong>$location'e Özel Hesap Satış Seçenekleri!</strong> Hemen <a href='$ana_site' rel='noopener'>hesap satış</a> platformumuza katılın ve güvenilir <strong>hesap sat</strong> deneyimi yaşayın. <a href='$ana_site' rel='noopener'>r10 hesap satış</a>, <a href='$ana_site' rel='noopener'>wmaraci hesap satış</a> ve <a href='$ana_site' rel='noopener'>1yuz hesap satış</a> fırsatlarını kaçırmayın!
    </div>";
}

$article_content = generateExtendedArticle($location, $il, $ilce, $mahalle, $platform_name, $hizmet_name, $ana_site);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($keywords); ?>">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical); ?>">
    
    <meta property="og:locale" content="tr_TR">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo htmlspecialchars($title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($description); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonical); ?>">
    <meta property="og:site_name" content="<?php echo htmlspecialchars($site_name); ?>">
    <meta property="og:image" content="<?php echo $base_url ?><?php echo htmlspecialchars($logo_url); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($description); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($logo_url); ?>">
    
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "name": "<?php echo htmlspecialchars($site_name); ?>",
        "description": "<?php echo htmlspecialchars($description); ?>",
        "url": "<?php echo htmlspecialchars($ana_site); ?>",
        "logo": "<?php echo htmlspecialchars($logo_url); ?>",
        "image": "<?php echo htmlspecialchars($logo_url); ?>",
      
        "priceRange": "$$",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "<?php echo htmlspecialchars($location); ?>",
            "addressRegion": "<?php echo htmlspecialchars($il ?: 'Türkiye'); ?>",
            "addressCountry": "TR"
        },
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "4.9",
            "reviewCount": "2847",
            "bestRating": "5",
            "worstRating": "1"
        },
        "openingHoursSpecification": {
            "@type": "OpeningHoursSpecification",
            "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
            "opens": "00:00",
            "closes": "23:59"
        }
    }
    </script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        html{font-size:16px;-webkit-text-size-adjust:100%;-webkit-font-smoothing:antialiased}
        body{font-family:'Inter',sans-serif;line-height:1.6;color:#1e293b;background:#f8fafc;overflow-x:hidden}
        img{max-width:100%;height:auto;display:block;border:0}
        a{text-decoration:none;color:inherit}
        h1,h2,h3{line-height:1.2;font-weight:800}
        .container{max-width:1200px;margin:0 auto;padding:0 20px}
        
        header{background:#fff;padding:15px 0;box-shadow:0 2px 10px rgba(0,0,0,.05);position:sticky;top:0;z-index:999}
        .header-flex{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px}
        .header-right{display:flex;align-items:center;gap:15px}
        .logo-box{display:flex;align-items:center;gap:12px}
        .logo-box h1{font-size:24px;font-weight:700;color:#000000;letter-spacing:1px}
        .btn-tel{display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#000000,#333333);color:#fff;padding:10px 24px;border-radius:25px;font-weight:600;font-size:14px;transition:transform .3s}
        .btn-tel:hover{transform:translateY(-2px)}
        
        /* INLINE BUTTON STYLES */
        .simple-btn{display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#000000,#333333);color:#fff;padding:12px 20px;border-radius:8px;font-weight:600;font-size:13px;transition:all .3s;text-decoration:none;border:none;cursor:pointer;min-width:120px;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.1)}
        .simple-btn:hover{transform:translateY(-2px);box-shadow:0 4px 15px rgba(0,0,0,.3)}
        .simple-btn i{font-size:16px}
        
        @media(max-width:768px){
            .header-flex{flex-direction:column;gap:15px}
            .header-right{flex-direction:column;gap:10px;width:100%}
            .simple-btn{padding:10px 18px;font-size:12px;min-width:100px}
        }
        
        .breadcrumb{background:#fff;padding:12px 0;margin:15px 0;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,.05)}
        .breadcrumb ul{list-style:none;display:flex;flex-wrap:wrap;gap:8px;font-size:14px}
        .breadcrumb li{display:flex;align-items:center;gap:8px}
        .breadcrumb li::after{content:'›';color:#cbd5e1}
        .breadcrumb li:last-child::after{display:none}
        .breadcrumb a{color:#000000}
        .breadcrumb a:hover{color:#333333}
        
        .hero{background:linear-gradient(135deg,#000000,#333333,#666666);padding:60px 0;text-align:center;color:#fff;position:relative;overflow:hidden}
        .hero::before{content:'';position:absolute;width:400px;height:400px;background:rgba(255,255,255,.1);border-radius:50%;top:-100px;right:-100px}
        .hero-content{position:relative;z-index:1}
        .hero h1{font-size:clamp(28px,5vw,48px);margin-bottom:15px}
        .hero p{font-size:clamp(16px,3vw,18px);margin-bottom:25px;opacity:.95}
        .hero-btn{display:inline-flex;align-items:center;gap:10px;background:#fff;color:#000000;padding:15px 35px;border-radius:25px;font-weight:700;transition:transform .3s}
        .hero-btn:hover{transform:scale(1.05)}
        
        .main{padding:40px 0}
        .grid{display:grid;grid-template-columns:2.5fr 1fr;gap:30px;margin-bottom:40px}
        
        .article{background:#fff;padding:35px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.05)}
        .article h2{font-size:clamp(24px,4vw,32px);color:#1e293b;margin:30px 0 15px;padding-bottom:12px;border-bottom:3px solid #000000}
        .article h3{font-size:clamp(20px,3vw,26px);color:#1e293b;margin:25px 0 12px}
        .article p{margin-bottom:18px;text-align:justify;font-size:clamp(15px,2vw,17px)}
        .article a{color:#000000;font-weight:600;border-bottom:2px solid #0066cc;transition:border-color .3s;text-decoration:none}
        .article a:hover{border-bottom-color:#004499}
        .article img{margin:25px 0;border-radius:8px;width:100%}
        .lead{font-size:clamp(16px,2.5vw,19px)!important;font-weight:500;line-height:1.8!important}
        
        .stats-box{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:15px;margin:25px 0;text-align:center}
        .stat{background:linear-gradient(135deg,#000000,#333333);color:#fff;padding:20px;border-radius:10px}
        .stat strong{display:block;font-size:32px;margin-bottom:5px}
        .stat span{font-size:14px}
        
        .benefits{list-style:none;margin:20px 0}
        .benefits li{padding:10px 0 10px 30px;position:relative}
        .benefits li::before{content:'✓';position:absolute;left:0;color:#10b981;font-weight:bold;font-size:18px}
        
        .cta-inline{background:linear-gradient(135deg,#000000,#333333);color:#fff;padding:25px;border-radius:10px;margin:30px 0;text-align:center;font-size:clamp(16px,2.5vw,18px)}
        .cta-inline a{color:#fff;text-decoration:underline;font-weight:700}
        
        .sidebar{display:flex;flex-direction:column;gap:20px}
        .sidebar-box{background:#fff;padding:20px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.05)}
        .sidebar-box h3{font-size:18px;margin-bottom:15px;padding-bottom:10px;border-bottom:2px solid #000000}
        .sidebar-links{display:flex;flex-direction:column;gap:8px}
        .sidebar-links a{display:flex;align-items:center;gap:8px;padding:10px 12px;background:#f8fafc;border-radius:8px;font-size:14px;font-weight:500;border-left:3px solid transparent;transition:all .3s}
        .sidebar-links a:hover{background:linear-gradient(135deg,#000000,#333333);color:#fff;border-left-color:#000000;transform:translateX(5px)}
        
        .features{margin:60px 0}
        .features h2{text-align:center;font-size:clamp(28px,4vw,38px);margin-bottom:40px}
        .features-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:25px}
        .feature{background:#fff;padding:30px;border-radius:12px;text-align:center;box-shadow:0 2px 8px rgba(0,0,0,.05);transition:transform .3s}
        .feature:hover{transform:translateY(-5px)}
        .feature i{font-size:48px;color:#000000;margin-bottom:15px}
        .feature h3{font-size:20px;margin-bottom:10px}
        .feature p{font-size:15px;color:#64748b}
        
        .cta{background:linear-gradient(135deg,#000000,#333333);padding:50px 30px;border-radius:12px;text-align:center;color:#fff;margin:60px 0}
        .cta h2{font-size:clamp(26px,4vw,36px);margin-bottom:15px}
        .cta p{font-size:clamp(16px,2.5vw,18px);margin-bottom:25px;opacity:.95}
        .cta-btn{display:inline-flex;align-items:center;gap:10px;background:#fff;color:#000000;padding:16px 40px;border-radius:25px;font-weight:700;font-size:17px;transition:transform .3s}
        .cta-btn:hover{transform:scale(1.05)}
        
        footer{background:#1e293b;color:#fff;padding:40px 0 20px;margin-top:80px;text-align:center}
        footer p{margin-bottom:10px;opacity:.85;font-size:14px}
        footer a{color:#ffffff;font-weight:600}
        footer a:hover{color:#cccccc}
        
        .fixed-btns{position:fixed;bottom:20px;left:20px;display:flex;flex-direction:column;gap:12px;z-index:998}
        .fixed-btn{display:flex;align-items:center;gap:10px;padding:14px 26px;border-radius:25px;font-weight:700;font-size:15px;transition:transform .3s;box-shadow:0 4px 15px rgba(0,0,0,.2)}
        .fixed-btn:hover{transform:translateX(8px)}
        .btn-buy{background:linear-gradient(135deg,#000000,#333333);color:#fff}
        .btn-wa{background:#25D366;color:#fff}
        
        @media(max-width:1024px){
            .grid{grid-template-columns:1fr}
            .sidebar{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px}
        }
        
        @media(max-width:768px){
            .header-flex{justify-content:center;text-align:center}
            .logo-box h1{font-size:18px}
            .logo-box img{height:40px}
            .btn-tel{font-size:13px;padding:8px 20px}
            .hero{padding:40px 0}
            .article{padding:25px 20px}
            .article h2{font-size:22px}
            .article h3{font-size:18px}
            .fixed-btns{left:10px;bottom:10px}
            .fixed-btn{padding:12px 22px;font-size:14px}
            .stats-box{grid-template-columns:1fr}
            .features-grid{grid-template-columns:1fr}
            .cta{padding:35px 20px}
        }
        
        @media(max-width:480px){
            .hero h1{font-size:24px}
            .hero-btn{padding:12px 28px;font-size:15px}
            .article{padding:20px 15px}
            .sidebar{grid-template-columns:1fr}
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-flex">
                  <div class="logo-box">
                      <h1>SATILIK HESAP</h1>
                  </div>
              
                <div class="header-right">
                    <a href="<?php echo htmlspecialchars($base_url); ?>" class="simple-btn">
                        <i class="fas fa-home"></i>
                        Ana Sayfa
                    </a>
                    
                    <a href="<?php echo htmlspecialchars($ana_site); ?>" class="simple-btn" target="_blank" rel="noopener noreferrer">
                        <i class="fas fa-comments"></i>
                        Forum Sitesi
                    </a>
                </div>
            </div>
        </div>
    </header>
    
    <div class="container">
        <nav class="breadcrumb" aria-label="breadcrumb">
            <ul>
                <li><a href="<?php echo htmlspecialchars($base_url); ?>"><i class="fas fa-home"></i> Ana Sayfa</a></li>
                <?php if ($il): ?>
                <li><a href="<?php echo htmlspecialchars($base_url . $il_slug); ?>"><?php echo htmlspecialchars($il); ?></a></li>
                <?php endif; ?>
                <?php if ($ilce): ?>
                <li><a href="<?php echo htmlspecialchars($base_url . $il_slug . '-' . $ilce_slug); ?>"><?php echo htmlspecialchars($ilce); ?></a></li>
                <?php endif; ?>
                <?php if ($mahalle): ?>
                <li><a href="<?php echo htmlspecialchars($base_url . $il_slug . '-' . $ilce_slug . '-' . $mahalle_slug); ?>"><?php echo htmlspecialchars($mahalle); ?></a></li>
                <?php endif; ?>
                <?php if ($sayfa_tipi == 'hizmet'): ?>
                <li><span><?php echo htmlspecialchars($platform_name . ' ' . $hizmet_name); ?></span></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1><?php echo htmlspecialchars($h1); ?></h1>
                <p><?php echo htmlspecialchars($description); ?></p>
                <a href="<?php echo htmlspecialchars($ana_site); ?>" class="hero-btn" target="_blank" rel="noopener noreferrer">
                    <i class="fas fa-users"></i> Foruma Katıl
                </a>
            </div>
        </div>
    </section>
    
    <div class="container main">
        <div class="grid">
            <article class="article">
                <h2>🔒 <?php echo htmlspecialchars($location); ?> Hesap Satış Forumu - Türkiye'nin En Güvenli Alım Satım Platformu</h2>
                <?php echo $article_content; ?>
            </article>
            
            <aside class="sidebar">
                <?php if (!empty($turkey_data)): ?>
                <div class="sidebar-box">
    <h3><i class="fas fa-map-marked-alt"></i> Tüm Şehirler</h3>
    <div class="sidebar-links">
        <?php foreach ($turkey_data as $city_slug => $city_data): ?>
        <a href="<?php echo htmlspecialchars($base_url . $city_slug . '-' . $platform . '-hesap-satisi'); ?>">
            <i class="fas fa-chevron-right"></i>
            <?php echo htmlspecialchars($city_data['name']); ?>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<?php if ($il && isset($turkey_data[$il_slug]['ilceler'])): ?>
<div class="sidebar-box">
    <h3><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($il); ?> İlçeleri</h3>
    <div class="sidebar-links">
        <?php foreach ($turkey_data[$il_slug]['ilceler'] as $i_slug => $i_data): ?>
        <a href="<?php echo htmlspecialchars($base_url . $il_slug . '-' . $i_slug . '-' . $platform . '-hesap-satisi'); ?>">
            <i class="fas fa-chevron-right"></i>
            <?php echo htmlspecialchars($i_data['name']); ?>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php if ($ilce && isset($turkey_data[$il_slug]['ilceler'][$ilce_slug]['mahalleler'])): ?>
<div class="sidebar-box">
    <h3><i class="fas fa-location-dot"></i> <?php echo htmlspecialchars($ilce); ?> Mahalleleri</h3>
    <div class="sidebar-links">
        <?php foreach ($turkey_data[$il_slug]['ilceler'][$ilce_slug]['mahalleler'] as $m_slug => $m_name): ?>
        <a href="<?php echo htmlspecialchars($base_url . $il_slug . '-' . $ilce_slug . '-' . $m_slug . '-' . $platform . '-hesap-satisi'); ?>">
            <i class="fas fa-chevron-right"></i>
            <?php echo htmlspecialchars($m_name); ?>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

                
                <?php if ($ilce && isset($turkey_data[$il_slug]['ilceler'][$ilce_slug]['mahalleler'])): ?>
                <div class="sidebar-box">
                    <h3><i class="fas fa-location-dot"></i> <?php echo htmlspecialchars($ilce); ?> Mahalleleri</h3>
                    <div class="sidebar-links">
                        <?php foreach ($turkey_data[$il_slug]['ilceler'][$ilce_slug]['mahalleler'] as $m_slug => $m_name): ?>
                        <a href="<?php echo htmlspecialchars($base_url . $il_slug . '-' . $ilce_slug . '-' . $m_slug); ?>">
                            <i class="fas fa-chevron-right"></i>
                            <?php echo htmlspecialchars($m_name); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php endif; ?>
                
                <div class="sidebar-box">
    <h3><i class="fas fa-layer-group"></i> Hesap Kategorileri</h3>
    <div class="sidebar-links">
        <?php 
        $current_base = '';
        if ($il_slug) $current_base = $il_slug;
        if ($ilce_slug) $current_base .= '-' . $ilce_slug;
        if ($mahalle_slug) $current_base .= '-' . $mahalle_slug;
        
        foreach ($platformlar as $platform_slug => $platform_name):
        ?>
       <a href="<?php echo htmlspecialchars($base_url . ($current_base ? $current_base . '-' : '') . $platform_slug . '-hesap-satisi'); ?>">

            <i class="fas fa-arrow-right"></i>
            <?php echo htmlspecialchars($platform_name . ' Hesap Satışı'); ?>
        </a>
        <?php endforeach; ?>
        
       
    </div>
</div>

            </aside>
        </div>
        
        <section class="features">
            <h2>Güvenilir Hesap Alım-Satım Özellikleri</h2>
            <div class="features-grid">
                <div class="feature">
                    <i class="fas fa-shield-halved"></i>
                    <h3>Dolandırıcılık Koruması</h3>
                    <p>Gelişmiş güvenlik sistemi ile dolandırıcılığa karşı tam koruma.</p>
                </div>
                <div class="feature">
                    <i class="fas fa-lock"></i>
                    <h3>Escrow Sistemi</h3>
                    <p>Ödemeniz güvenli hesaplarda tutulur, hesap teslimi sonrası serbest bırakılır.</p>
                </div>
                <div class="feature">
                    <i class="fas fa-user-check"></i>
                    <h3>Doğrulanmış Satıcılar</h3>
                    <p>Tüm satıcılar kimlik doğrulamasından geçer ve güvenilirlik skorları vardır.</p>
                </div>
                <div class="feature">
                    <i class="fas fa-headset"></i>
                    <h3>7/24 Moderasyon</h3>
                    <p>Her an aktif moderatörler ile güvenli işlem garantisi.</p>
                </div>
                <div class="feature">
                    <i class="fas fa-chart-line"></i>
                    <h3>Hesap Garantisi</h3>
                    <p>Tüm hesaplar doğrulanmış ve garanti altındadır.</p>
                </div>
                <div class="feature">
                    <i class="fas fa-credit-card"></i>
                    <h3>Güvenli Ödeme</h3>
                    <p>SSL sertifikalı güvenli ödeme sistemleri ile korumalı işlemler.</p>
                </div>
            </div>
        </section>
        
        <div class="cta">
            <h2>Güvenilir Hesap Alım-Satımına Başlayın!</h2>
            <p><?php echo htmlspecialchars($location); ?> bölgesinde güvenilir hesap alım-satım platformumuzda işlemlerinizi güvenle gerçekleştirin.</p>
            <a href="<?php echo htmlspecialchars($ana_site); ?>" class="cta-btn" target="_blank" rel="noopener noreferrer">
                Forumu Ziyaret Et <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
    
    <footer>
        <div class="container">
            <p>&copy; 2025 <a href="<?php echo htmlspecialchars($ana_site); ?>" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars($site_name); ?></a> - Tüm Hakları Saklıdır</p>
            <p><?php echo htmlspecialchars($location_full); ?> Güvenilir Hesap Alım-Satım Platformu</p>
            <p>Instagram Hesap Satışı | YouTube Hesap Satışı | TikTok Hesap Satışı | Twitter Hesap Satışı</p>
        </div>
    </footer>
    
    <div class="fixed-btns">
        <a href="<?php echo htmlspecialchars($ana_site); ?>" class="fixed-btn btn-buy" target="_blank" rel="noopener noreferrer" aria-label="Forum">
            <i class="fas fa-users"></i>
            <span>Foruma Katıl</span>
        </a>
      
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded',function(){
            const links=document.querySelectorAll('a[href^="#"]');
            links.forEach(link=>{
                link.addEventListener('click',function(e){
                    e.preventDefault();
                    const target=document.querySelector(this.getAttribute('href'));
                    if(target){
                        target.scrollIntoView({behavior:'smooth'});
                    }
                });
            });
            
            let lastScroll=0;
            const header=document.querySelector('header');
            window.addEventListener('scroll',function(){
                const currentScroll=window.pageYOffset;
                if(currentScroll>lastScroll&&currentScroll>100){
                    header.style.transform='translateY(-100%)';
                }else{
                    header.style.transform='translateY(0)';
                }
                lastScroll=currentScroll;
            });
        });
    </script>
</body>
</html>