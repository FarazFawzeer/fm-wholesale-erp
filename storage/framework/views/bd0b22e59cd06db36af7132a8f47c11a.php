<?php $__env->startSection('body-attribuet'); ?>
    class="authentication-bg"
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="account-pages py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-sm-10 col-md-7 col-lg-5 col-xl-4">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-4 p-sm-5">
                            <div class="text-center">
                                <div class="mx-auto mb-4 text-center auth-logo">
                                    <a href="#" class="logo-dark">
                                        <img src="<?php echo e(asset('images/logo-dark.png')); ?>" height="72" alt="FM Shoes">
                                    </a>

                                    <a href="#" class="logo-light">
                                        <img src="<?php echo e(asset('images/logo-light.png')); ?>" height="72" alt="FM Shoes">
                                    </a>
                                </div>
                                <h4 class="fw-bold mb-1">Welcome back</h4>
                                <p class="text-muted mb-0">Sign in to continue to FM Wholesale ERP</p>
                            </div>
                            <form method="POST" action="<?php echo e(route('login.post')); ?>" class="mt-4">
                                <?php echo csrf_field(); ?>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="<?php echo e(old('email')); ?>" placeholder="Enter your email" autocomplete="username" required autofocus>
                                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <small class="text-danger"><?php echo e($message); ?></small>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="password" class="form-label">Password</label>
                                    </div>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Enter your password" autocomplete="current-password" required>
                                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <small class="text-danger"><?php echo e($message); ?></small>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>

                                <div class="d-grid">
                                    <button class="btn btn-primary btn-lg fw-medium" type="submit">Sign In</button>
                                </div>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.base', ['subtitle' => 'Login'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/auth/signin.blade.php ENDPATH**/ ?>