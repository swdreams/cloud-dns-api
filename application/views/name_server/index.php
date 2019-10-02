<!-- DataTales Example -->
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">DNS Servers List</h1>
<div class="card shadow">
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
				<thead>
				<tr>
					<th>Type</th>
					<th>Name</th>
					<th>ip4</th>
					<th>ip6</th>
					<th>Location</th>
					<th>Location CC</th>
					<th>DDos Protected?</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($dns_list as $row) { ?>
					<tr>
						<td class="text-center"><?php echo $row['type'] ?></td>
						<td class="text-center"><?php echo $row['name'] ?></td>
						<td><?php echo $row['ip4'] ?></td>
						<td><?php echo $row['ip6'] ?></td>
						<td><?php echo $row['location'] ?></td>
						<td class="text-center"><?php echo $row['location_cc'] ?></td>
						<td class="text-center"><?php echo $row['ddos_protected'] == 0 ? "No" : "Yes" ?></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
