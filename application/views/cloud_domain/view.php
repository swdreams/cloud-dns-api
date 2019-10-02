<h1 class="h3 mb-2 text-gray-800">DNS Zone Information</h1>

<div class="card shadow">
	<div class="card-body">
		<div class="row">
			<div class="offset-3 col-3">Name</div>
			<div class="col-3">
				<?php echo $zone_info['name'] ?>
			</div>
		</div>
		<div class="row">
			<div class="offset-3 col-3">Type</div>
			<div class="col-3">
				<?php echo $zone_info['type'] ?>
			</div>
		</div>
		<div class="row">
			<div class="offset-3 col-3">Name</div>
			<div class="col-3">
				<?php echo $zone_info['zone'] ?>
			</div>
		</div>
		<div class="row">
			<div class="offset-3 col-3">Status</div>
			<div class="col-3">
				<?php echo $zone_info['status'] ?>
			</div>
		</div>
	</div>
</div>
