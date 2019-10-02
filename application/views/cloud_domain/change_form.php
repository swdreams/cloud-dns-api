
<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-800">Change Cloud Master</h1>
</div>
<p class="mb-4">
	Please fill the domain name to be change as a master domain.
</p>

<div class="card shadow">
	<div class="card-body">
		<?php echo form_open('/cloud_domain/change_form', array('class'=>'user')) ?>
			<div class="form-group row">
				<label for="inputPassword" class="offset-2 col-sm-2 col-form-label">Domain Name</label>
				<div class="col-sm-6">
					<input name="domain_name" class="form-control" required />
				</div>
			</div>
			<div class="form-group row">
				<label class="offset-4"></label>
				<div class="col-sm-6">
					<button class="btn btn-primary"><i class="fa fa-save"></i> Submit</button>
					<a href="<?php echo site_url('/cloud_domain') ?>" class="btn btn-light pull-right">Cancel</a>
				</div>
			</div>
		<?php echo form_close() ?>
	</div>
</div>
