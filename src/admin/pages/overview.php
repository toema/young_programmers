<div class="card">
    <div class="card-body">
        <h5 class="card-title">إحصائيات الموقع</h5>
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6>عدد المستخدمين</h6>
                        <h2><?php echo getUsersCount(); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6>عدد المشاريع</h6>
                        <h2><?php echo getProjectsCount(); ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
