<div class="row">
	<div class="offset-3 col-md-6">
		<div class="login-body">
			<?php echo form_open("auth/login", array('class' => 'user')); ?>
			<div class="row justify-content-center">

				<div class="col-xl-10 col-lg-12 col-md-9">

					<div class="card o-hidden border-0 shadow-lg my-5">
						<div class="card-body p-0">
							<!-- Nested Row within Card Body -->
							<div class="p-5">
								<div class="text-center">
									<h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
								</div>
								<div class="form-group">
									<input type="text" class="form-control form-control-user"
										   id="<?php echo $user_id['id'] ?>"
										   name="<?php echo $user_id['name'] ?>"
										   placeholder="<?php echo $user_id['placeholder'] ?>">
								</div>
								<div class="form-group">
									<input type="password" class="form-control form-control-user"
										   id="<?php echo $password['id'] ?>"
										   name="<?php echo $password['name'] ?>"
										   placeholder="<?php echo $password['placeholder'] ?>">
								</div>
								<button type="submit" class="btn btn-primary btn-user btn-block">Login</button>

							</div>
						</div>
					</div>
				</div>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>
