<?php 
if (isset($_POST['verisayisi'])) {
	$datamaxvalue = array();
	$dataminvalue = array();
	$data=array();
	$mean=array();
	$standartdeviaton=array();
	$verisayisi =  $_POST['verisayisi'];
	$count =  $_POST['hidden'];
	$korelasyonkatsayisi = $_POST['korelasyonkatsayisi'];
	$features = array();
	
	$classdata = array();
	for ($i=0; $i <= $verisayisi ; $i++) { 
		$classdatarand =rand(0,1);
		if ($classdatarand == 0) {
			$classdata[$i] = 0;
		}
		elseif ($classdatarand == 1 ){
			$classdata[$i] = 1;
		}
	}
	for ($i=0; $i <= $count; $i++) { 
		$name = "Feature".$i;
		$xrange = $_POST['xrange'.$i];
		$yrange = $_POST['yrange'.$i];
		$features[$i] = [$name , $xrange , $yrange ];
	}
	

	for ($i=0; $i <= $count ; $i++) { 
		for ($j=1; $j <= $verisayisi ; $j++) {
			$mean[$i] = 0;
			$standartdeviaton[$i] = 0;
			$rand = rand(1,10)/10;
			if ($j == 1) {
			 	$data[$j][$i] = rand($features[$i][1],$features[$i][2]);
			 }else{
			 	$data[$j][$i] = $data[$j-1][$i] * $rand * $korelasyonkatsayisi;
			 } 
		}
	}

	///Array to Mean Value
	for ($i=0; $i <= $count ; $i++) { 
		for ($j=1; $j <= $verisayisi ; $j++) { 
			$mean[$i] += $data[$j][$i];
		}
		$mean[$i] = $mean[$i] / $verisayisi;
	}



	///Array to Standart Deviation
	for ($i=0; $i <= $count ; $i++) { 
		for ($j=1; $j <= $verisayisi ; $j++) { 
			$standartdeviaton[$i] += sqrt(pow($data[$j][$i] - $mean[$i] , 2) / $verisayisi);
		}
	}
	/// Array to Max-Min 
	for ($i=0; $i <= $count ; $i++) { 
		$datamaxvalue[$i] = 0;
		$dataminvalue[$i] = 999999999999999999;
		for ($j=1; $j <= $verisayisi ; $j++) {
			if ($datamaxvalue[$i] < $data[$j][$i]) {
				$datamaxvalue[$i] = $data[$j][$i];
			}
			if ($dataminvalue[$i] > $data[$j][$i]) {
				$dataminvalue[$i] = $data[$j][$i];
			}  

		}
	}
	if ($_POST['normalizasyon'] == 'normalizezscore') {
	///Change to Normalize Data According to Z-Score
		for ($i=0; $i <= $count ; $i++) { 
			for ($j=1; $j <= $verisayisi ; $j++) { 
				$data[$j][$i] = ($data[$j][$i] - $mean[$i]) / $standartdeviaton[$i];
			}
		}
	}
	elseif ($_POST['normalizasyon'] == 'normalizemaxmin') {
		for ($i=0; $i <= $count ; $i++) { 
			for ($j=1; $j <= $verisayisi ; $j++) { 
				$data[$j][$i] = ($data[$j][$i] - $dataminvalue[$i]) / ($datamaxvalue[$i]-$dataminvalue[$i]);
			}
		}
	}



	$fp = fopen("data.csv", 'w');
//Write the header
	fputcsv($fp, array_keys($data[1]));
//Write fields
	foreach ($data as $fields) {
		fputcsv($fp, $fields);
	}
	fclose($fp);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" >
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" >
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" ></script>
	<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<body>
	<div class="container-fluid">
		<form action="index.php" method="POST">
			<div class="row" style=" margin:20px -15px;">
				<div class="col-md-5">
					<div class="row">	
						<div class="col-md-6" style="margin: 15px;">
							<label for="verisayisi">Veri Sayısı </label>
							<input type="text" class="form-control" placeholder="Veri Sayısı" name="verisayisi" id="verisayisi">
						</div>

						<?php /* ?>
						<div class="col-md-6" style="margin: 15px;">
							
							<input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="linear" checked>
							<label class="form-check-label" for="inlineRadio1">Linear Data</label>

							<input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="nonlinear">
							<label class="form-check-label" for="inlineRadio2">Non-linear Data</label>
							
						</div>

						<?php */ ?>
						<div class="col-md-6" style="margin: 15px;">
							<label for="normalizasyon">Normalizasyon</label>
							<select id="normalizasyon" name="normalizasyon" class="form-control">
								<option value="normalizezscore">Z-Score</option>
								<option value="normalizemaxmin">Min-Max</option>
								
							</select>
						</div>
						<div class="col-md-6" style="margin: 15px;">
							<label for="verisayisi">Korelasyon Katsayısı</label>
							<input type="text" class="form-control" placeholder="Korelasyon Katsayısı" name="korelasyonkatsayisi" id="korelasyonkatsayisi">
						</div>
					</div>
					<div class="row">
						<div class="col-md-6" style="margin:15px;">
							<button type="submit" class="btn btn-primary" onclick="createFeature()" >Oluştur</button>
							<input type="hidden" value="" id="hidden" name="hidden">
						</div>
					</div>
				</div>
				<div class="col-md-7">
					<table id="feature-max-min">
						<thead>
							<tr>
								<th><button type="button" class="btn btn-primary" onclick="addFeature()">Add Feature</button></th>
								<th>Max Value</th>
								<th>Min Value</th>
							</tr>
						</thead>
						<tbody>

						</tbody>

					</table>

				</div>
				<div class="row form-input-wrapper-row">

				</div>

			</div>
			<div class="row">
				<h1 style="text-align: center;">Created Dataset</h1>
				<table>
					
					<?php if (isset($_POST['verisayisi'])) { ?>

						<?php if ($_POST['normalizasyon'] == 'normalizemaxmin'){ ?>
							<tr id="created-feature-row">
								<th></th>
								<?php for ($j=0; $j <= $count ; $j++) {  ?>
									<th>
										<?php echo'Before Normalize '; ?> <br> <?php echo 'Max : '.$datamaxvalue[$j];?> <br> <?php echo 'Min : '. $dataminvalue[$j]; ?>
									</th>
								<?php } ?>
							</tr>
						<?php } else { ?>
							<tr id="created-feature-row">
								<th></th>
								<?php for ($j=0; $j <= $count ; $j++) {  ?>
									<th>
										<?php echo 'Before Normalize' ?> <br> <?php echo 'Standart Dev : '.$standartdeviaton[$j];?><br> <?php echo 'Mean : '. $mean[$j]; ?>
									</th>
								<?php } ?>
							</tr>
						<?php } ?>
						
						
						<tr id="created-feature-row">
							<th>Class</th>
							<?php for ($j=0; $j <= $count ; $j++) {  ?>

								<th>
									<?php echo 'Feature'.$j?>
								</th>
							<?php } ?>

						</tr>

						<tr>

							<?php for ($i=1; $i <= $verisayisi ; $i++) {  ?>
								<tr id="created-feature-row-inner">
									<td><?php echo $classdata[$i]; ?></td>
									<?php for ($j=0; $j <= $count ; $j++) {  ?>
										<td><?php echo $data[$i][$j]; ?></td>
									<?php } ?>
								</tr>
							<?php } }?>
						</table>
					</div>
				</form>

			</div>

			<style>
				.form-input-wrapper-row{margin: 15px;}
				table {font-family: arial, sans-serif; border-collapse: collapse; width: 100%; }
				td, th {border: 1px solid #dddddd; text-align: left; padding: 8px; }
				tr:nth-child(even) {background-color: #dddddd; }
			</style>

			<script>
				function addFeature() {
					var featureCount = $('#feature-max-min > tbody').children("tr").length;
					var html = '<tr id="feature-row"> <td id="feature-name">Feature-'+featureCount+'</td> <td id="feature-max'+featureCount+'"><input type="text" class="form-control" placeholder="Öznitelik Sayısı" name="xrange'+featureCount+'"></td> <td id="feature-min'+featureCount+'"><input type="text" class="form-control" placeholder="Öznitelik Sayısı" name="yrange'+featureCount+'"></td> </tr>'
					$('#feature-max-min').append(html);
			//$('#created-feature-row').append('<th>Feature1</th>');
			//$('#created-feature-row-inner').append('<td id=""></td>');
			$('#hidden').val(featureCount);
		}
	</script>

	


	
</body>
</html>