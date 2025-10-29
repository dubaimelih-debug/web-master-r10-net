<?php

// BASE URL
$base_url = "https://satilikhesap.com/";

// =============================
// SADECE PLATFORMLAR
// =============================
$platformlar = [
    'instagram' => 'Instagram',
    'tiktok' => 'TikTok',
    'youtube' => 'YouTube',
    'twitter' => 'Twitter (X)',
    'facebook' => 'Facebook',
    'telegram' => 'Telegram',
    'discord' => 'Discord'
];

// Türkiye veri dosyası
include 'turkey.php';

// =======================================
// TÜRKÇE KARAKTER DÖNÜŞTÜRÜCÜ (slug fix)
// =======================================
function slugify($string) {
    $turkish = ['ş','Ş','ı','İ','ç','Ç','ü','Ü','ö','Ö','ğ','Ğ'];
    $english = ['s','s','i','i','c','c','u','u','o','o','g','g'];
    $string = str_replace($turkish, $english, $string);
    $string = preg_replace('/[^a-zA-Z0-9\- ]/', '', $string); // özel karakterleri sil
    $string = strtolower(trim($string));
    $string = preg_replace('/\s+/', '-', $string); // boşlukları tireye çevir
    return $string;
}

// Sitemap limitleri
$limit = 5000; // Google max 50.000
$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;

// ======================
// URL Generator Function
// ======================
function echoUrl($loc, $freq, $priority) {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($loc) . "</loc>\n";
    echo "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
    echo "    <changefreq>$freq</changefreq>\n";
    echo "    <priority>$priority</priority>\n";
    echo "  </url>\n";
}

// ======================
// Count Total URLs
// ======================
function countUrls($turkey_data, $platformlar) {
    $count = 1; // ana sayfa
    foreach ($turkey_data as $il_data) {
        $count += count($platformlar); // il bazlı platformlar
        if (isset($il_data['ilceler'])) {
            foreach ($il_data['ilceler'] as $ilce_data) {
                $count += count($platformlar); // ilçe bazlı platformlar
                if (isset($ilce_data['mahalleler'])) {
                    foreach ($ilce_data['mahalleler'] as $mahalle) {
                        $count += count($platformlar); // mahalle bazlı platformlar
                    }
                }
            }
        }
    }
    return $count;
}

$total_urls = countUrls($turkey_data, $platformlar);
$total_pages = ceil($total_urls / $limit);

// ======================
// SITEMAP INDEX (sitemap.xml)
// ======================
if ($page === 0) {
    header('Content-Type: application/xml; charset=utf-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    for ($i = 1; $i <= $total_pages; $i++) {
        echo "  <sitemap>\n";
        echo "    <loc>" . htmlspecialchars($base_url . "sitemap-$i.xml") . "</loc>\n";
        echo "    <lastmod>" . date('Y-m-d\TH:i:s+00:00') . "</lastmod>\n";
        echo "  </sitemap>\n";
    }
    echo "</sitemapindex>";
    exit;
}

// ======================
// SITEMAP PAGE (sitemap-1.xml, sitemap-2.xml ...)
// ======================
if ($page > $total_pages) {
    header("HTTP/1.0 404 Not Found");
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<error>Sitemap page not found</error>';
    exit;
}

header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Offset
$offset = ($page - 1) * $limit;
$counter = 0;
$current = 0;

// Ana sayfa
if ($offset == 0) {
    if ($counter < $limit) {
        echoUrl($base_url, 'daily', '1.0');
        $counter++;
    }
    $current++;
}

// ======================
// URL üretimi
// ======================
foreach ($turkey_data as $il_slug => $il_data) {
    $il_slug = slugify($il_slug);

    // İl bazlı platform URL’leri
    foreach ($platformlar as $platform_slug => $platform_name) {
        $url = "{$base_url}{$il_slug}-{$platform_slug}-hesap-satisi";
        if ($current++ >= $offset && $counter < $limit) {
            echoUrl($url, 'weekly', '0.9');
            $counter++;
        }
    }

    // İlçeler
    if (isset($il_data['ilceler'])) {
        foreach ($il_data['ilceler'] as $ilce_slug => $ilce_data) {
            $ilce_slug = slugify($ilce_slug);

            foreach ($platformlar as $platform_slug => $platform_name) {
                $url = "{$base_url}{$il_slug}-{$ilce_slug}-{$platform_slug}-hesap-satisi";
                if ($current++ >= $offset && $counter < $limit) {
                    echoUrl($url, 'weekly', '0.8');
                    $counter++;
                }
            }

            // Mahalleler
            if (isset($ilce_data['mahalleler'])) {
                foreach ($ilce_data['mahalleler'] as $mahalle_slug => $mahalle_name) {
                    $mahalle_slug = slugify($mahalle_slug);

                    foreach ($platformlar as $platform_slug => $platform_name) {
                        $url = "{$base_url}{$il_slug}-{$ilce_slug}-{$mahalle_slug}-{$platform_slug}-hesap-satisi";
                        if ($current++ >= $offset && $counter < $limit) {
                            echoUrl($url, 'monthly', '0.7');
                            $counter++;
                        }
                    }
                }
            }
        }
    }
}

echo "</urlset>";
