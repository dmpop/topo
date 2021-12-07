<?php
$photo_dir = '../photos/';
$gpx_dir = 'gpx';
$ext = '*.{JPEG,jpeg,JPG,jpg}';
?>

<!DOCTYPE html>
<html lang="en" data-theme="dark">
<!-- Author: Dmitri Popov, dmpop@linux.com
         License: GPLv3 https://www.gnu.org/licenses/gpl-3.0.txt -->

<head>
	<meta charset="utf-8">
	<title>Topo</title>
	<link rel="shortcut icon" href="favicon.png" />
	<link rel="stylesheet" href="css/classless.css">
	<link rel="stylesheet" href="css/themes.css">
	<script type="text/javascript" src="GM_Utils/GPX2GM.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>
		#map {
			width: 100%;
			height: 35em;
			margin: 0;
			padding: 0;
		}

		#map_img {
			display: none
		}
	</style>
</head>

<body>
	<div style="text-align: center; margin-bottom: 2em;">
		<img style="display: inline; height: 2.5em; vertical-align: middle;" src="favicon.svg" alt="logo" />
		<h1 class="text-center" style="display: inline; margin-left: 0.19em; vertical-align: middle; letter-spacing: 3px; margin-top: 0em; color: #66ff33;">TOPO</h1>
	</div>
	<?php
	if (!file_exists($photo_dir)) {
		exit("<h3 class='text-center'>The directory <code>$photo_dir</code> doesn't exist.</h3>");
	}
	if (!file_exists($gpx_dir)) {
		mkdir($gpx_dir, 0777, true);
	}
	$files = glob($gpx_dir . "/*.gpx");
	$list = implode(",", $files);

	?>
	<div class="card">
		<div id="map" class="gpxview:<?php echo $list ?>:Karte"><noscript>
				<p>Enable JavaScript to view the map</p>
			</noscript></div>
		<div id="map_img">
			<?php

			function read_gps_location($file)
			{
				if (is_file($file)) {
					$info = exif_read_data($file);
					if (
						isset($info['GPSLatitude']) && isset($info['GPSLongitude']) &&
						isset($info['GPSLatitudeRef']) && isset($info['GPSLongitudeRef']) &&
						in_array($info['GPSLatitudeRef'], array('E', 'W', 'N', 'S')) && in_array($info['GPSLongitudeRef'], array('E', 'W', 'N', 'S'))
					) {

						$GPSLatitudeRef	 = strtolower(trim($info['GPSLatitudeRef']));
						$GPSLongitudeRef = strtolower(trim($info['GPSLongitudeRef']));

						$lat_degrees_a = explode('/', $info['GPSLatitude'][0]);
						$lat_minutes_a = explode('/', $info['GPSLatitude'][1]);
						$lat_seconds_a = explode('/', $info['GPSLatitude'][2]);
						$lon_degrees_a = explode('/', $info['GPSLongitude'][0]);
						$lon_minutes_a = explode('/', $info['GPSLongitude'][1]);
						$lon_seconds_a = explode('/', $info['GPSLongitude'][2]);

						$lat_degrees = $lat_degrees_a[0] / $lat_degrees_a[1];
						$lat_minutes = $lat_minutes_a[0] / $lat_minutes_a[1];
						$lat_seconds = $lat_seconds_a[0] / $lat_seconds_a[1];
						$lon_degrees = $lon_degrees_a[0] / $lon_degrees_a[1];
						$lon_minutes = $lon_minutes_a[0] / $lon_minutes_a[1];
						$lon_seconds = $lon_seconds_a[0] / $lon_seconds_a[1];

						$lat = (float) $lat_degrees + ((($lat_minutes * 60) + ($lat_seconds)) / 3600);
						$lon = (float) $lon_degrees + ((($lon_minutes * 60) + ($lon_seconds)) / 3600);

						// If the latitude is South, make it negative
						// If the longitude is west, make it negative
						$GPSLatitudeRef	 == 's' ? $lat *= -1 : '';
						$GPSLongitudeRef == 'w' ? $lon *= -1 : '';

						return array(
							'lat' => $lat,
							'lon' => $lon
						);
					}
				}
				return false;
			}

			foreach (glob($photo_dir . $ext, GLOB_BRACE) as $file) {
				$gps = read_gps_location($file);
				echo '<a href="' . $file . '" data-geo="lat:' . $gps['lat'] . ',lon:' . $gps['lon'] . '">' . $gps['lat'] . ', ' . $gps['lon'] . '</a>';
			}
			?>
			<script>
				var Bestaetigung = false;
				var Shwpname = false;
				var Legende_fnm = false;
				var Fullscreenbutton = true;
				var Shwpshadow = false;
				var Wpcluster = false;
			</script>
		</div>
		<div style="text-align: center; margin-top:1em; margin-bottom: 1em;">
			<p style="font-size: 85%">This is <a href="https://github.com/dmpop/topo">Topo</a></p>
		</div>
	</div>
</body>

</html>