<?php
$videoID = "/venue_locator/video/tour.mp4"; // Replace with the actual YouTube video ID
$contact = [
    "name" => "User",
    "phone" => "*****091429",
    "email" => "******@elretirobaguio.com",
    "website" => "www.elretirobaguio.com",
    "location" => "Get Directions",
    "image" => "/venue_locator/images/venue.png",
    "username" => "user who"
];

$galleryImages = [
    "/venue_locator/images/image1.jpg",
    "/venue_locator/images/image2.jpg",
    "/venue_locator/images/image6.jpg",
    "/venue_locator/images/image4.jpg",
    "/venue_locator/images/image5.jpg",
    "/venue_locator/images/image6.jpg",
    "/venue_locator/images/image1.jpg",
    "/venue_locator/images/image2.jpg",
];

$socialLinks = [
    "facebook" => "#",
    "google" => "#"
];
$venue = [
  "name" => "El Retiro Baguio",
  "location" => "Manila",
  "categories" => "Garden, Outdoors",
  "image" => "/venue_locator/images/header.jpg",
  "logo" => "/venue_locator/images/logo.png",
  "reviews" => 0
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Web Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <style>
       .mx-auto {
    margin-left: auto;
    margin-right: auto;
    margin-top: 20px;

}
.bg-white.p-4.shadow-md.\32 024 {
    height: 25%;
    margin-top: 20%;
}
.bg-white.p-4.mb-4.shadow-md.\32 022 {
    height: 20%;
    margin-top: 20%;
}
.bg-white.p-4.mb-4.shadow-md.text-center.\32 021 {
    height: 20%;
    margin-top: 20%;
}

#lightbox-img {
    width: 50%;
}
.w-12 {
    width: 9rem;
    padding: 5px;
}
.h-12 {
    height: 7%;
}
.space-x-2 > :not([hidden]) ~ :not([hidden]) {
    --tw-space-x-reverse: 0;
    margin-right: calc(0.5rem* var(--tw-space-x-reverse));
    margin-left: calc(0.rem* calc(1 - var(--tw-space-x-reverse)));
}
.flex.flex-wrap.space-x-2 {
    margin-left: 10px;
}
.img.w-12.h-12.cursor-pointer {
    border-radius: 50px;
}

    </style>
</head>
<body class="bg-gray-100 text-white">
    <!-- Header -->
    <header class="bg-gray-800 p-4 flex justify-between items-center">
        <div class="flex items-center">
            <img alt="Ventech" class="h-10 w-10" src="<?= $venue['logo'] ?>" width="40" height="40"/>
            <span class="ml-2 text-xl font-bold">primovenues</span>
        </div>
        <nav class="flex space-x-4 relative z-50">
    <a class="hover:underline" href="#">Submit Venue</a>

    <div class="relative group">
        <a class="hover:underline cursor-pointer flex items-center" href="#">
            Explore <i class="fas fa-chevron-down ml-1"></i>
        </a>

        <!-- Dropdown Menu -->
        <div class="absolute hidden group-hover:block bg-white text-black mt-2 py-2 w-48 shadow-lg border border-gray-200 z-50">
            <a href="index.php" class="block px-4 py-2 hover:bg-gray-200">Home</a>
            <a href="list_venues.php" class="block px-4 py-2 hover:bg-gray-200">List Venues</a>
            <a  href="manage_bookings.php" class="block px-4 py-2 hover:bg-gray-200">Bookings</a>
            <a href="find.php" class="block px-4 py-2 hover:bg-gray-200">Find Venues</a>
        </div>
    </div>

    <a href="#" class="hover:underline">Help</a>
    <a href="signin.php" class="hover:underline">Sign In</a>
</nav>

    </header>
    <!-- Main Content -->
    <main class="relative">
        <img alt="<?= htmlspecialchars($venue['name']) ?>" class="w-full h-96 object-cover" src="<?= $venue['image'] ?>" width="1200" height="400"/>
        <div class="absolute inset-0 bg-black bg-opacity-50 flex flex-col justify-center items-start p-8">
            <h1 class="text-4xl font-bold"><?= htmlspecialchars($venue['name']) ?></h1>
            <p class="text-lg"><?= htmlspecialchars($venue['location']) ?></p>
            <p class="text-sm">Categories: <?= htmlspecialchars($venue['categories']) ?></p>
            <div class="mt-4">
                <button class="bg-transparent border border-white py-2 px-4 text-white hover:bg-white hover:text-black">Login to bookmark this listing</button>
            </div>
            <div class="mt-2 flex items-center">
                <div class="flex space-x-1">
                    <?php for ($i = 0; $i < 5; $i++) : ?>
                        <i class="far fa-star"></i>
                    <?php endfor; ?>
                </div>
                <span class="ml-2"><?= $venue['reviews'] ?> Reviews</span>
            </div>
            <div class="mt-4 flex space-x-2">
                <button class="bg-blue-600 py-2 px-4 text-white hover:bg-blue-700">Contact</button>
                <button class="bg-yellow-500 py-2 px-4 text-white hover:bg-yellow-600">Write a Review</button>
            </div>
        </div>
    </main>


    <div class="container mx-auto">
        <div class="flex flex-col lg:flex-row">
            <!-- Left Column -->
            <div class="w-full lg:w-2/3">
                <!-- Photo Gallery -->
                <div class="bg-white p-4 mb-4 shadow-md">
    <h2 class="text-blue-600 text-lg font-semibold mb-2">
        <i class="fas fa-camera"></i> Photo Gallery
    </h2>
    <div class="mb-4">
        <img id="mainImage" src="<?php echo $galleryImages[0]; ?>" class="w-full h-auto" width="800" height="400" alt="Main gallery image">
    </div>
    <div class="flex space-x-2 overflow-x-auto">
        <?php foreach ($galleryImages as $index => $image) : ?>
            <img src="<?php echo $image; ?>" class="w-20 h-20 cursor-pointer thumbnail" data-index="<?php echo $index; ?>" width="100" height="100" alt="Gallery thumbnail <?php echo $index + 1; ?>">
        <?php endforeach; ?>
    </div>
    <div class="flex justify-between mt-4">
        <button id="prevBtn" class="bg-gray-300 text-black px-4 py-2 rounded">Previous</button>
        <button id="nextBtn" class="bg-gray-300 text-black px-4 py-2 rounded">Next</button>
    </div>
</div>

                <!-- Video Section -->
<div class="bg-white p-4 shadow-md">
    <h2 class="text-gray-700 text-lg font-semibold mb-2">
        <i class="fas fa-video"></i> Video
    </h2>
    <div class="mb-4">
        <video width="100%" height="400" controls>
            <source src="/venue_locator/video/tour.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
</div>

            </div>

            <!-- Right Column -->
            <div class="w-full lg:w-1/3 lg:pl-4">
                <!-- Contact Info -->
                <div class="bg-white p-4 mb-4 shadow-md">
                    <h2 class="text-gray-700 text-lg font-semibold mb-2"><?= $contact["name"] ?></h2>
                    <p class="text-gray-600 mb-2"><i class="fas fa-phone"></i> <?= $contact["phone"] ?></p>
                    <p class="text-gray-600 mb-2"><i class="fas fa-envelope"></i> <?= $contact["email"] ?></p>
                    <p class="text-gray-600 mb-2"><i class="fas fa-globe"></i> <?= $contact["website"] ?></p>
                    <p class="text-gray-600"><i class="fas fa-map-marker-alt"></i> <?= $contact["location"] ?></p>
                </div>

                <!-- Contact Person -->
                <div class="bg-white p-4 mb-4 shadow-md text-center 2021">
                    <img src="<?= $contact["image"] ?>" alt="Contact person" class="w-24 h-24 mx-auto rounded-full mb-2"/>
                    <h2 class="text-gray-700 text-lg font-semibold mb-2"><?= $contact["username"] ?></h2>
                    <button class="bg-yellow-500 text-white px-4 py-2 rounded">Contact</button>
                </div>

                <!-- Social Profiles -->
                <div class="bg-white p-4 mb-4 shadow-md 2022">
                    <h2 class="text-gray-700 text-lg font-semibold mb-2"><i class="fas fa-share-alt"></i> Social Profiles</h2>
                    <div class="flex space-x-4">
                        <?php foreach ($socialLinks as $platform => $link): ?>
                            <a href="<?= $link ?>" class="text-gray-600"><i class="fab fa-<?= $platform ?>"></i></a>
                        <?php endforeach; ?>
                    </div>
                </div>

               <!-- Gallery Thumbnails -->
<div class="bg-white p-4 shadow-md 2024" >
    <h2 class="text-blue-600 text-lg font-semibold mb-2"><i class="fas fa-images"></i> Gallery</h2>
    <div class="flex flex-wrap space-x-2">
        <?php foreach ($galleryImages as $image): ?>
            <img src="<?= $image ?>" alt="Gallery thumbnail" class="w-12 h-12 cursor-pointer" onclick="openLightbox('<?= $image ?>')"/>
        <?php endforeach; ?>
    </div>
</div>

<!-- Lightbox Modal -->
<div id="lightbox" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden">
    <span class="absolute top-5 right-10 text-white text-3xl cursor-pointer" onclick="closeLightbox()">&times;</span>
    <img id="lightbox-img" src="" class="max-w-full max-h-full">
</div>
            </div>
        </div>
    </div>

    <script>
    function openLightbox(imageSrc) {
        document.getElementById('lightbox-img').src = imageSrc;
        document.getElementById('lightbox').classList.remove('hidden');
    }

    function closeLightbox() {
        document.getElementById('lightbox').classList.add('hidden');
    }

    // Close lightbox when clicking outside image
    document.getElementById('lightbox').addEventListener('click', function(event) {
        if (event.target === this) {
            closeLightbox();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            closeLightbox();
        }
    });
</script>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const images = <?php echo json_encode($galleryImages); ?>;
        let currentIndex = 0;
        const mainImage = document.getElementById("mainImage");
        const thumbnails = document.querySelectorAll(".thumbnail");
        const prevBtn = document.getElementById("prevBtn");
        const nextBtn = document.getElementById("nextBtn");

        function updateMainImage(index) {
            if (index >= 0 && index < images.length) {
                mainImage.src = images[index];
                currentIndex = index;
            }
        }

        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener("click", function () {
                updateMainImage(parseInt(this.getAttribute("data-index")));
            });
        });

        prevBtn.addEventListener("click", function () {
            if (currentIndex > 0) {
                updateMainImage(currentIndex - 1);
            }
        });

        nextBtn.addEventListener("click", function () {
            if (currentIndex < images.length - 1) {
                updateMainImage(currentIndex + 1);
            }
        });
    });
</script>
</body>
</html>
