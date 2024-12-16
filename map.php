<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db_connect.php';

try {
    $sql = "SELECT u.*, 
            COALESCE(AVG(r.rating), 0) as avg_rating,
            COUNT(r.id) as review_count
            FROM database_univ u 
            LEFT JOIN reviews r ON u.id = r.univ_id 
            GROUP BY u.id";
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }

    $markers = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $lat = floatval($row['Latitude']);
            $lng = floatval($row['Longitude']);

            if (is_numeric($lat) && is_numeric($lng)) {
                $markers[] = array(
                    'id' => $row['id'],
                    'nama' => $row['Nama'],
                    'lat' => $lat,
                    'lng' => $lng,
                    'website' => $row['website_url'],
                    'keterangan' => $row['Keterangan'],
                    'avg_rating' => number_format(floatval($row['avg_rating']), 1),
                    'review_count' => intval($row['review_count'])
                );
            }
        }
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Peta Universitas Yogyakarta</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        #map {
            width: 100%;
            height: 100vh;
        }

        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background-color: #50736a;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background-color: #3c5751;
            transform: translateY(-2px);
        }

        .website-link,
        .route-button {
            display: inline-block;
            margin: 5px 0;
            padding: 8px 16px;
            background-color: #50736a;
            color: white !important;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .website-link:hover,
        .route-button:hover {
            background-color: #3c5751;
            transform: translateY(-2px);
        }

        .custom-popup {
            padding: 15px;
            max-width: 300px;
        }

        .popup-header {
            margin: -15px -15px 15px -15px;
            padding: 15px;
            background-color: #50736a;
            color: white;
            border-radius: 4px 4px 0 0;
        }

        .popup-header h3 {
            margin: 0;
            font-size: 1.2em;
        }

        .popup-content {
            margin-bottom: 15px;
        }

        .info-item {
            margin-bottom: 10px;
            padding-left: 25px;
            position: relative;
        }

        .info-item i {
            position: absolute;
            left: 0;
            top: 3px;
            color: #50736a;
        }

        .rating-stars {
            color: #ffd700;
            margin-right: 5px;
        }

        .review-section {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .button-container {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .leaflet-popup-content-wrapper {
            border-radius: 8px;
            box-shadow: 0 3px 14px rgba(0, 0, 0, 0.2);
        }

        .rating-form {
            margin-top: 15px;
        }

        .star-rating {
            display: inline-block;
            font-size: 0;
            position: relative;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            float: right;
            padding: 0 2px;
            font-size: 24px;
            color: #ddd;
            cursor: pointer;
        }

        .star-rating label:before {
            content: '\2605';
        }

        .star-rating input:checked~label {
            color: #ffd700;
        }

        .star-rating:not(:checked) label:hover,
        .star-rating:not(:checked) label:hover~label {
            color: #ffd700;
        }

        .rating-form textarea {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
        }

        .rating-form button {
            background-color: #50736a;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .rating-form button:hover {
            background-color: #3c5751;
        }

        .website-link i,
        .route-button i {
            margin-right: 5px;
        }

        .info-button {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 1000;
            background-color: #50736a;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .info-button:hover {
            background-color: #3c5751;
        }

        .info-modal {
            display: none;
            position: fixed;
            bottom: 80px;
            left: 20px;
            z-index: 1000;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        .info-modal.active {
            display: block;
        }

        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 15px;
            display: block;
            border: 3px solid #50736a;
        }

        .info-content h3, .info-content h4 {
            color: #50736a;
            margin: 0 0 5px 0;
            text-align: center;
        }

        .info-content h4 {
            font-weight: normal;
            margin-bottom: 15px;
            color: #666;
        }

        .info-content p {
            margin: 10px 0;
            line-height: 1.6;
            color: #333;
            text-align: justify;
            padding: 0 10px;
        }

        .social-links {
            margin-top: 15px;
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .social-links a {
            color: #50736a;
            text-decoration: none;
            transition: color 0.3s ease;
            padding: 5px 10px;
            border-radius: 5px;
            background-color: #f5f5f5;
        }

        .social-links a:hover {
            color: #3c5751;
            background-color: #e9e9e9;
        }

        .close-info {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            color: #666;
            font-size: 20px;
        }
    </style>
</head>

<body>
    <a href="index.php" class="back-button">← Kembali</a>
    <div id="map"></div>

    <button class="info-button" onclick="toggleInfo()">
        <i class="fas fa-info-circle"></i> Info Pembuat
    </button>

    <div class="info-modal" id="infoModal">
        <span class="close-info" onclick="toggleInfo()">&times;</span>
        <img src="images/profile.jpg" alt="Profile" class="profile-image">
        <div class="info-content">
            <h3>Adinda Pangestu</h3>
            <h4>NIM : 23/517374/SV/22765</h4>
            <p>Saya adalah seorang mahasiswa prodi Sistem Informasi Geografis, Sekolah Vokasi Universitas Gadjah Mada. Website ini saya buat guna memenuhi tugas responsi praktikum pemrograman geospasial web.</p>
            <div class="social-links">
                <a href="https://instagram.com/adindapngstu_" target="_blank">
                    <i class="fab fa-instagram"></i> adindapngstu_
                </a>
                <a href="https://github.com/adindapngstu" target="_blank">
                    <i class="fab fa-github"></i> GitHub
                </a>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
    <script>
        // Initialize the map
        var map = L.map('map').setView([-7.797068, 110.370529], 13);

        // Add the tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: ' OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Add DIY Boundary GeoJSON
        fetch('./geojson/diy_boundary.geojson')  
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('GeoJSON data:', data); 
                L.geoJSON(data, {
                    style: {
                        color: '#50736a',
                        weight: 2,
                        opacity: 0.8,
                        fillColor: '#50736a',
                        fillOpacity: 0.1,
                        dashArray: '3'
                    }
                }).addTo(map);
            })
            .catch(error => {
                console.error('Error loading GeoJSON:', error);
            });

        // Custom marker icon
        var customIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div style="
                background-color: #50736a;
                width: 30px;
                height: 30px;
                border-radius: 50%;
                position: relative;
                box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            ">
                <div style="
                    position: absolute;
                    bottom: -8px;
                    left: 50%;
                    transform: translateX(-50%);
                    width: 0;
                    height: 0;
                    border-left: 8px solid transparent;
                    border-right: 8px solid transparent;
                    border-top: 8px solid #50736a;
                "></div>
                <i class="fas fa-university" style="
                    color: white;
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    font-size: 14px;
                "></i>
            </div>`,
            iconSize: [30, 42],
            iconAnchor: [15, 42],
            popupAnchor: [0, -42]
        });

        function getStarRating(rating) {
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= rating) {
                    stars += '<i class="fas fa-star"></i>';
                } else if (i - 0.5 <= rating) {
                    stars += '<i class="fas fa-star-half-alt"></i>';
                } else {
                    stars += '<i class="far fa-star"></i>';
                }
            }
            return stars;
        }

        function isValidCoordinate(lat, lng) {
            return lat !== null && lng !== null &&
                !isNaN(lat) && !isNaN(lng) &&
                lat >= -90 && lat <= 90 &&
                lng >= -180 && lng <= 180;
        }

        var routingControl = null;

        var markers = <?php echo json_encode($markers); ?>;
        markers.forEach(function(m) {
            if (isValidCoordinate(m.lat, m.lng)) {
                var marker = L.marker([m.lat, m.lng], {
                    icon: customIcon
                });

                marker.bindTooltip(m.nama, {
                    permanent: false,
                    direction: 'top',
                    className: 'custom-tooltip',
                    offset: [0, -45]
                });

                var popupContent = `
                    <div class="custom-popup">
                        <div class="popup-header">
                            <h3>${m.nama}</h3>
                        </div>
                        <div class="popup-content">
                            <div class="info-item">
                                <i class="fas fa-university"></i>
                                <strong>Jenis:</strong> ${m.keterangan}
                            </div>
                            <div class="info-item">
                                <i class="fas fa-star"></i>
                                <strong>Rating:</strong> 
                                <span class="rating-stars">${getStarRating(m.avg_rating)}</span>
                                <span>(${m.avg_rating}/5 dari ${m.review_count} review)</span>
                            </div>
                            <div class="review-section">
                                <form class="rating-form" onsubmit="submitReview(event, ${m.id})">
                                    <h4 style="margin-top: 0;">Tambah Review</h4>
                                    <div class="star-rating">
                                        <input type="radio" id="star5_${m.id}" name="rating" value="5" required/>
                                        <label for="star5_${m.id}" title="5 stars"></label>
                                        <input type="radio" id="star4_${m.id}" name="rating" value="4"/>
                                        <label for="star4_${m.id}" title="4 stars"></label>
                                        <input type="radio" id="star3_${m.id}" name="rating" value="3"/>
                                        <label for="star3_${m.id}" title="3 stars"></label>
                                        <input type="radio" id="star2_${m.id}" name="rating" value="2"/>
                                        <label for="star2_${m.id}" title="2 stars"></label>
                                        <input type="radio" id="star1_${m.id}" name="rating" value="1"/>
                                        <label for="star1_${m.id}" title="1 star"></label>
                                    </div>
                                    <textarea name="review" rows="3" required placeholder="Tulis review Anda..."></textarea>
                                    <button type="submit">Kirim Review</button>
                                </form>
                            </div>
                            <div class="button-container">
                                <a href="${m.website}" target="_blank" class="website-link">
                                    <i class="fas fa-globe"></i> Website
                                </a>
                                <button onclick='getRoute([${m.lat}, ${m.lng}])' class="route-button">
                                    <i class="fas fa-route"></i> Rute
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                marker.bindPopup(popupContent, {
                    maxWidth: 350,
                    className: 'custom-popup'
                });
                marker.addTo(map);
            }
        });

        function submitReview(event, univId) {
            event.preventDefault();
            const form = event.target;
            const rating = form.rating.value;
            const review = form.review.value;

            fetch('save_review.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `univ_id=${univId}&rating=${rating}&review=${review}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Review berhasil disimpan!');
                        form.reset();
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan review');
                });
        }

        function getRoute(destination) {
            if (!destination || !Array.isArray(destination) || destination.length !== 2 ||
                !isValidCoordinate(destination[0], destination[1])) {
                alert('Invalid destination coordinates');
                return;
            }

            if (routingControl) {
                map.removeControl(routingControl);
            }

            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var userLocation = [position.coords.latitude, position.coords.longitude];

                    if (isValidCoordinate(userLocation[0], userLocation[1])) {
                        routingControl = L.Routing.control({
                            waypoints: [
                                L.latLng(userLocation[0], userLocation[1]),
                                L.latLng(destination[0], destination[1])
                            ],
                            routeWhileDragging: true,
                            show: false
                        }).addTo(map);
                    } else {
                        alert('Invalid user location');
                    }
                }, function(error) {
                    alert('Error getting your location: ' + error.message);
                });
            } else {
                alert("Geolocation is not supported by your browser");
            }
        }

        function toggleInfo() {
            document.getElementById('infoModal').classList.toggle('active');
        }
    </script>
</body>

</html>