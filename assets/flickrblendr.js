//seting up 2 images to load into, don't display until both loaded
//could this be an array?
var imagesloaded = 0;
var image1 = new Image();
var image2 = new Image();
var currentPhotos = new Array();
image1.onload = function() {
	imagesloaded++;
	if (imagesloaded == 2) {
		combo(image1, image2);
		imagesloaded = 0;
	}
};
image2.onload = function() {
	imagesloaded++;
	if (imagesloaded == 2) {
		combo(image1, image2);
		imagesloaded = 0;
	}
};


//what to do when the json arrives from flickr api
//store in global photosObj
var jsonFlickrApi = function(thejson) {
	photosObj = thejson.photos.photo;
	randomBlend();

}

//myRandomImageNumber ensures that random selection from photos has the correct sized image
var myRandomImageNumber = function() {
	var maxPhotos = photosObj.length;
	var returnnumber = false;

	while (!returnnumber) {
		try {
			var ran = Math.floor((Math.random() * maxPhotos));
			var i = photosObj[ran1].url_c;
		} catch (err) {}
		return ran;
	}
}

//get 2 random photos and info
//load the images, global the info proably shoud just save the random numbers?
var randomBlend = function() {
	imagesloaded = 0;
	var maxPhotos = photosObj.length;

	currentPhotos[0] = myRandomImageNumber();
	currentPhotos[1] = myRandomImageNumber();

	image1.src = photosObj[currentPhotos[0]].url_c;
	image2.src = photosObj[currentPhotos[1]].url_c;;


	jQuery('#pic').height(jQuery('#pic').width() * 75 / 100);


}

var combo = function(img1, img2) {
	
	document.getElementById('flickrblenderloader').style.display = "none";
	document.getElementById('pic').style.background = "url(" + img1.src + "),url(" + img2.src + ")";
	document.getElementById('pic').style.backgroundSize = 'cover';
	showCredits();
	setTimeout(randomBlend, 10000)
}

var showCredits = function() {

	license1 = photosObj[currentPhotos[0]].license;
	license2 = photosObj[currentPhotos[1]].license;
	owner1Id = photosObj[currentPhotos[0]].owner;
	owner2Id = photosObj[currentPhotos[1]].owner;
	photo1Id = photosObj[currentPhotos[0]].id;
	photo2Id = photosObj[currentPhotos[1]].id;
	owner1 = photosObj[currentPhotos[0]].ownername;
	owner2 = photosObj[currentPhotos[1]].ownername;

	var photolink1 = "<a target='new' href='https://flickr.com/photos/" + owner1Id + "/" + photo1Id + "'>Flickr Photo: </a>";
	var photolink2 = "<a target='new' href='https://flickr.com/photos/" + owner2Id + "/" + photo2Id + "'>Flickr Photo: </a>";

	var credits1 = "Photo by: " + owner1 + " : " + get_license_text(license1);
	var credits2 = "Photo by " + owner2 + " : " + get_license_text(license2);
	document.getElementById('licenses').innerHTML = photolink1 + credits1 + " " + "<br>" + photolink2 + " " + credits2;


}

var swapmode = function() {
	var e = document.getElementById("mode");
	var mode = e.options[e.selectedIndex].value;
	document.getElementById('pic').style.backgroundBlendMode = mode;
}

document.getElementById('controlwrap').onmouseover = function() {
	show('controls')
};
document.getElementById('controlwrap').onmouseout = function() {
	hide('controls')
};

document.getElementById('info').onclick = function() {
	if (document.getElementById('controls').style.visibility == "hidden") {
		show('controls');
	} else {
		hide('controls');
	}
}




function show(id) {
	document.getElementById(id).style.visibility = "visible";
}

function hide(id) {
	document.getElementById(id).style.visibility = "hidden";
}


function fullscreen() {
	if (jQuery('#flickrblendr').width() !== jQuery(window).width()) {
		jQuery('#flickrblendr').appendTo("body");
		jQuery('#flickrblendr').css({
			position: 'fixed',
			width: jQuery(window).width(),
			height: jQuery(window).height(),
			top: '0',
			left: '0'

		});
		jQuery('#pic').css({
 			width: "800px",
			height: "600px",
			margin:"auto"

		});
window.scrollTo(0, 0);
	} else {
		jQuery('#flickrblendr').prependTo("#flickrblendrholder");
		jQuery('#flickrblendr').css({
			position: 'relative',
			maxWidth: '100%',
			height: "auto",
		});
		jQuery('#pic').css({
 			maxWidth: "100%"
			
 
		});

	}
}

/*
  	licenses stuff from @cogdog
  */
var licenses = new Array("", "BY-NC-SA", "BY-NC", "BY-NC-ND", "BY", "BY-SA", "BY-ND", "", "PD", "CC0", "PD");

function get_license_text(thelicense) {
	switch (thelicense) {
		case "7":
			return 'with no copyright restriction (Flickr Commons)';
			break;
		case "8":
			return 'as s United States Government Work ( PD )';
			break;
		default:
			return 'under a CC (' + licenses[thelicense] + ') license';
	}
}
