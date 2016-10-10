<!doctype html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Baidu Maps</title>
		<style>
			html { height: 100% }
			body { height: 100%; margin: 0; padding: 0; background-color: #FFF }
		</style>
		<script charset="utf-8" src="http://api.map.baidu.com/api?v=1.3"></script>
		<script>
			var map, geocoder;
			function initialize() {
				map = new BMap.Map('map_canvas');
				var point = new BMap.Point(121.473704, 31.230393);
				map.centerAndZoom(point, 11);
				map.addControl(new BMap.NavigationControl());
				map.enableScrollWheelZoom();

				var gc = new BMap.Geocoder();
				gc.getLocation(point, function(rs){
					var addComp = rs.addressComponents;
					var address = [addComp.city].join('');
					parent.document.getElementById("kindeditor_plugin_map_address").value = address;
				});
			}
			function search(address) {
				if (!map) return;
				var local = new BMap.LocalSearch(map, {
					renderOptions: {
						map: map,
						autoViewport: true,
						selectFirstResult: false
					}
				});
				local.search(address);
			}
		</script>
	</head>
	<body onload="initialize();">
		<div id="map_canvas" style="width:100%; height:100%"></div>
	</body>
</html>
