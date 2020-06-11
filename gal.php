<style>
    body {
        background-image: url(background.gif);
        color: white;
    }

    ul {
        list-style: none;
        display: flex;
        flex-wrap: wrap;
    }

    li {
        border: 1px solid white;
        min-width: 300px;
        min-height: 200px;
    }

    ul li a, ul li img {
        display: flex;
        flex-direction: column;
        padding: 5px;
        text-decoration: none;
        color: white;
	cursor: pointer;
    }

    .swipe {
        overflow: hidden;
        visibility: hidden;
        position: relative;
    }

    .swipe-wrap {
        overflow: hidden;
        position: relative;
    }

    .swipe-wrap > div {
        float: left;
        width: 100%;
        position: relative;
        overflow: hidden;
        text-align: center;
        display: flex;
        height: 100%;
        justify-content: center;
        align-items: center;
    }

    .hidden {
        display: none;
    }
    
    .modal {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
    }

    .backdrop {
        background-color: black;
        opacity: 0.8;
        width: 100%;
        height: 100%;
        position: absolute;
    }

    .content {
        width: 75vw;
        height: 75vh;
        margin: auto;
        margin-top: 12.5vh;
    }

    .img-responsive {
        max-height: 75vh;
        max-width: 75vw;
    }

    .exit {
        cursor: pointer;
        position: fixed;
        top: 5vw;
        right: 5vw;
        font-weight: bold;
        border: 1px solid white;
        border-radius: 5vw;
        width: 5vw;
        height: 5vw;
        font-size: 4vw;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(255, 255, 255, 0.5);
    }

    .prev, .next {
        cursor: pointer;
        position: fixed;
        top: 40vh;
        height: 20vh;
        font-size: 20vh;
        max-width: 12.5vw;
        display: flex;
        justify-content: center;
        align-items: center;
        border: 1px solid white;
	font-weight: bold;
        background-color: rgba(255, 255, 255, 0.5);
    }

    .prev {
        left: 0;
    }

    .next {
        right: 0;
    }
</style>
<script src="swipe.js"></script>
<script>
    function showImage(index) {
        document.querySelector('.modal').classList.remove('hidden');
        window.detailSwipe.setup({draggable: true});
	window.detailSwipe.slide(index, 0);
    }
    function closeModal() {
	document.querySelector('.modal').classList.add('hidden');
    }
    document.onkeydown = function(evt) {
	evt = evt || window.event;
	var isEscape = false;
	if ("key" in evt) {
	    isEscape = (evt.key === "Escape" || evt.key === "Esc");
	} else {
	    isEscape = (evt.keyCode === 27);
	}
	if (isEscape) {
	    closeModal(); 
	}
    };
    document.addEventListener("DOMContentLoaded", function() {
        var lazyImages = [].slice.call(document.querySelectorAll("img.lazy"));

        if ("IntersectionObserver" in window) {
            var lazyImageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var lazyImage = entry.target;
                        lazyImage.src = lazyImage.dataset.src;
                        lazyImage.classList.remove("lazy");
                        lazyImageObserver.unobserve(lazyImage);
                    }
                });
	    }, {
                rootMargin: '200px'
	    });

            lazyImages.forEach(function(lazyImage) {
                lazyImageObserver.observe(lazyImage);
            });
	}
    });
</script>
<?php
    if (isset ($_GET['gal'])) {
       $gal = $_GET['gal']; 

       echo "<h1>".$gal."</h1>\n<br>\n";
       echo "<a href='gal.php'>Zur&uuml;ck zur Galerie</a>\n<br>\n";
       $gal = $gal."/";
    } else {
       $gal = "";
    }
    
    $dirname = "img/".$gal;
    $images = scandir($dirname);
    $ignore = Array(".", "..");
    $imagesonly = Array();

    echo "<ul>\n";
    $index = 0;
    foreach ($images as $curimg){
        if (!in_array($curimg, $ignore)) {
	    if (!is_dir($dirname.$curimg)) {
	        if (isImage($dirname.$curimg)) {
                    echo "<li><img class='lazy' data-src='img.php?src=".$dirname.$curimg."&w=300&h=200&zc=1' alt='' onclick='showImage(".$index.")' /></li>\n";
		    $imagesonly[] = $dirname.$curimg;
		    $index += 1;
		}
	    } else {
		if (first_of_folder($dirname.$curimg)) {
		    echo "<li><a href='?gal=".$gal.$curimg."'><span>".$curimg."</span><img class='lazy' data-src='img.php?src=".$dirname.$curimg."/".first_of_folder($dirname.$curimg)."&w=300&h=200' alt='' /></a></li>\n";
		}
	    }
        }
    }
    echo "</ul>\n";
    if ($gal != "") {
        echo "<a href='gal.php'>Zur&uuml;ck zur Galerie</a>\n<br>\n";
    }

    if (!empty($imagesonly)) {
        echo "<div class='modal hidden'><div class='backdrop'></div><div class='prev' onclick='window.detailSwipe.prev();'><</div><div class='next' onclick='window.detailSwipe.next();'>></div><div class='exit' onclick='closeModal();'>X</div><div class='content'><div class='swipe'><div class='swipe-wrap'>";
        foreach ($imagesonly as $image) {
            echo "<div><img data-src='img.php?src=".$image."&w=800&h=600&zc=1' class='lazy img-responsive' /></div>";
        }
        echo "</div></div></div>";
	echo "<script>window.detailSwipe = new Swipe(document.querySelector('.swipe'), {draggable: true});</script>";
    }

    function isImage($img) {
        if (strpos(mime_content_type($img), 'image') !== false) {
	    return True;
	} else {
	    return False;
	}
    }

    function first_of_folder($folder) {
        $images = scandir($folder);
	$ignore = Array(".", "..");
	foreach ($images as $img) {
	    if (!in_array($img, $ignore) && !is_dir($img) && isImage($folder.'/'.$img)) {
		return $img;
	    }
	}
	return False;
    }
?>
