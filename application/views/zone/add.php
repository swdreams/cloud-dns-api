<div class="d-sm-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-800">Add Cloud Master Domains</h1>
</div>

<div class="card shadow">
	<div class="card-body">
		<?php echo form_open('/zone/add', array('class' => 'user')) ?>
		<div class="form-group row">
			<label for="inputPassword" class="offset-2 col-sm-2 col-form-label">Master Cloud Domains</label>
			<div class="col-sm-6">
				<textarea name="zone_names" class="form-control" rows="6" required></textarea>
			</div>
		</div>
		<div class="form-group row" style="display: none;">
			<label for="inputPassword" class="offset-2 col-sm-2 col-form-label">Zone Type</label>
			<div class="col-sm-6">
				<select name="zone_type" >
					<option value="master">Master Cloud Domain</option>
				</select>
			</div>
		</div>
		<div class="form-group row">
			<label class="offset-4"></label>
			<div class="col-sm-6">
				<button class="btn btn-primary"><i class="fa fa-save"></i> Submit</button>
				<a href="<?php echo site_url('/zone') ?>" class="btn btn-light pull-right">Cancel</a>
			</div>
		</div>
		<?php echo form_close() ?>
	</div>
</div>
