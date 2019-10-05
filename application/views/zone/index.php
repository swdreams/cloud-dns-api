<!-- DataTales Example -->
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-800">
		Cloud Master Domains
		<?php if ($total_count > 0) {
			echo " (" . number_format($total_count, 0) . ")";
		} ?>
	</h1>
	<a href="<?php echo site_url('/zone/add') ?>"
	   class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
		<i class="fas fa-download fa-sm text-white-50"></i> Add
	</a>

</div>

<div class="card shadow">
	<div class="card shadow mb-2">
		<div class="card-body">
			<div class="row">
				<div class="col-12">
					<?php echo form_open('/zone', array(
						'class' => 'form-inline'
					)) ?>
					<label for="zone" class="mr-sm-2">Name:</label>
					<?php echo form_input(array(
						'name' => 'zone_search',
						'id' => 'zone_search',
						'class' => 'form-control mr-sm-3'
					), $zone_search); ?>
					<button type="submit" class="btn btn-primary mr-2"><i class="fa fa-search"></i> Search</button>
					<button type="button" class="btn btn-dark btn-search-reset"><i class="fa fa-res"></i> Reset</button>
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
					<th class="text-center">Name</th>
					<th class="text-center">Label <a href="#" id="labels_save"><i class="fa fa-save ml-2"> </i></a></th>
					<th class="text-center">Type</th>
					<th class="text-center">Zone</th>
					<th class="text-center">Status</th>
					<th class="text-center">-</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($zones as $row) { ?>
					<tr>
						<td class="text-center">
							<a href="<?php echo site_url("zone/view/" . $row['name']) ?>"><?php echo $row['name'] ?></a>
						</td>
						<td class="text-center zone-label" data-zone="<?php echo $row['name'] ?>"
							contenteditable="true">
							<?php echo isset($zone_labels[$row['name']]) ? $zone_labels[$row['name']] : '' ?>
						</td>
						<td class="text-center"><?php echo $row['type'] ?></td>
						<td class="text-center"><?php echo $row['zone'] ?></td>
						<td class="text-center"><?php echo $row['status'] == 0 ? "-" : "OK" ?></td>
						<td class="text-center">
							<a href="#"
							   data-href="<?php echo site_url('zone/delete/' . $row['name']) ?>"
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
<div class="modal fade" id="result-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
	 aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				Status Dialog
			</div>
			<div class="modal-body">
				Saved successfully.
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-light" data-dismiss="modal">OK</button>
			</div>
		</div>
	</div>
</div>
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
<script>
    $(document).ready(function () {
		$('#confirm-delete').on('show.bs.modal', function (e) {
			$(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
		});


        $(".btn-search-reset").click(function (e) {
            var form = $(this).closest('form');
            $('#zone_search').val('');
            form.submit();
        });

        $("#labels_save").click(function (e) {
            e.preventDefault();
            var data = {};
            $("#dataTable td.zone-label").each(function (e) {
                var val = $(this).text();

                val = val.replace(/\r?\n|\r|\t/gm, '');

                data[$(this).data('zone')] = val;

            });
            $.post({
                url: '<?php echo site_url('/zone/ajax_save_labels') ?>',
                data: {
                    json: JSON.stringify(data)
                },
                dataType: 'json',
                success: function (res) {
                    if (res.success) {
                        $('#result-modal').find(".modal-body").html("Labels successfully saved.");
                        $('#result-modal').modal('show');

                    } else {
                        $('#result-modal').find(".modal-body").html("Failed to save labels. Please try again later.");
                        $('#result-modal').modal('show');
					}
                }
            });
        })
    });
</script>
