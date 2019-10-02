<!-- DataTales Example -->
<!-- Page Heading -->

<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-800">
		Cloud Domain List
		<?php if ($total_count > 0) {echo " (". number_format($total_count, 0) . ")";} ?>
	</h1>
	<a href="<?php echo site_url('/cloud_domain/add') ?>"
	   class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
		<i class="fas fa-download fa-sm text-white-50"></i> Add
	</a>
</div>
<div class="card shadow mb-2">
	<div class="card-body">
		<div class="row">
			<div class="col-12">
				<?php echo form_open('/cloud_domain', array(
					'class' => 'form-inline'
				)) ?>
				<label for="zone" class="mr-sm-2">DNS Zone:</label>
				<?php echo form_dropdown(array(
					'name' => 'zone',
					'id' => 'zone',
					'class' => 'form-control mr-sm-3',
					'onChange' => '$(this).closest(\'form\').submit()',
				), $zones, $selected_zone) ?>
				<!--<label for="zone" class="mr-sm-2">Domain Name:</label>
				<?php /*echo form_input(array(
					'name' => 'zone_search',
					'id' => 'zone_search',
					'class' => 'form-control mr-sm-3'
				)); */?>
				<button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>-->
				<?php echo form_close() ?>
			</div>
		</div>
	</div>
</div>

<div class="card shadow">
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
				<thead>
				<tr>
					<th class="text-center" width="150">No</th>
					<th class="text-center">Cloud Domain</th>
					<th class="text-center" width="150">-</th>
				</tr>
				</thead>
				<tbody>
				<?php
				$ind = 1;
				foreach ($domains as $row) {
					?>
					<tr>
						<td class="text-center"><?php echo $ind ++ ?></td>
						<td class="text-center">
							<a href="<?php echo site_url("cloud_domain") ?>"><?php echo $row ?></a>
						</td>
						<td class="text-center">
							<a href="#"
							   data-href="<?php echo site_url('cloud_domain/change_master/' . $row) ?>"
							   class="btn btn-success btn-circle btn-sm mr-3"
							   title="Change cloud master"
							   data-toggle="modal"
							   data-target="#confirm-change">
								<i class="fas fa-check"></i>
							</a>

							<a href="#"
							   data-href="<?php echo site_url('cloud_domain/delete/' . $row) ?>"
							   class="btn btn-danger btn-circle btn-sm btn-del"
							   data-toggle="modal"
							   data-target="#confirm-delete">
								<i class="fas fa-trash"></i>
							</a>
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
    $(document).ready(function () {
        $('#confirm-delete').on('show.bs.modal', function (e) {
            $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
        });
        $('#confirm-change').on('show.bs.modal', function (e) {
            $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
        });
    });
</script>

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
	 aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				Delete Cloud Domain
			</div>
			<div class="modal-body">
				Are you sure you want to delete?
			</div>
			<div class="modal-footer">
				<a class="btn btn-danger btn-ok">Delete</a>
				<button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="confirm-change" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
	 aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				Change Cloud Master
			</div>
			<div class="modal-body">
				Are you sure you want to change the cloud master?
			</div>
			<div class="modal-footer">
				<a class="btn btn-primary btn-ok">OK</a>
				<button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>
