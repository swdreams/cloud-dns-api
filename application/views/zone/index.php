<!-- DataTales Example -->
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">DNS Zones List</h1>
<div class="card shadow">
	<div class="card shadow mb-2">
		<div class="card-body">
			<div class="row">
				<div class="col-12">
					<?php echo form_open('/zone', array(
						'class' => 'form-inline'
					)) ?>
					<label for="zone" class="mr-sm-2">Zone Name:</label>
					<?php echo form_input(array(
						'name' => 'zone_search',
						'id' => 'zone_search',
						'class' => 'form-control mr-sm-3'
					)); ?>
					<button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
					<?php echo form_close() ?>
				</div>
			</div>
		</div>
	</div>

	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
				<thead>
				<tr>
					<th>Name</th>
					<th>Type</th>
					<th>Zone</th>
					<th>Status</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($zones as $row) { ?>
					<tr>
						<td class="text-center">
							<a href="<?php echo site_url("zone/view/" . $row['name']) ?>"><?php echo $row['name'] ?></a>
						</td>
						<td class="text-center"><?php echo $row['type'] ?></td>
						<td class="text-center"><?php echo $row['zone'] ?></td>
						<td class="text-center"><?php echo $row['status'] == 0 ? "-" : "OK" ?></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
