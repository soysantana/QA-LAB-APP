<!-- Sample Information -->
<div id="product_info">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Sample Information</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="ProjectName" class="form-label">Project Name</label>
                        <input type="text" class="form-control" name="ProjectName" id="ProjectName" value="<?php echo ($Search['Project_Name']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="Client" class="form-label">Client</label>
                        <input type="text" class="form-control" name="Client" id="Client" value="<?php echo ($Search['Client']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="ProjectNumber" class="form-label">Project Number</label>
                        <input type="text" class="form-control" name="ProjectNumber" id="ProjectNumber" value="<?php echo ($Search['Project_Number']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="Structure" class="form-label">Structure</label>
                        <input type="text" class="form-control" name="Structure" id="Structure" value="<?php echo ($Search['Structure']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="Area" class="form-label">Work Area</label>
                        <input type="text" class="form-control" name="Area" id="Area" value="<?php echo ($Search['Area']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="Source" class="form-label">Borrow Source</label>
                        <input type="text" class="form-control" name="Source" id="Source" value="<?php echo ($Search['Source']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="MType" class="form-label">Material Type</label>
                        <input type="text" class="form-control" name="MType" id="MType" value="<?php echo ($Search['Material_Type']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="SType" class="form-label">Sample Type</label>
                        <input type="text" class="form-control" name="SType" id="SType" value="<?php echo ($Search['Sample_Type']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="SampleName" class="form-label">Sample Name</label>
                        <input type="text" class="form-control" name="SampleName" id="SampleName" value="<?php echo ($Search['Sample_ID']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="SampleNumber" class="form-label">Sample Number</label>
                        <input type="text" class="form-control" name="SampleNumber" id="SampleNumber" value="<?php echo ($Search['Sample_Number']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="Sample Date" class="form-label">Sample Date</label>
                        <input type="text" class="form-control" name="CollectionDate" id="Sample Date" value="<?php echo ($Search['Sample_Date']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="SampleBy" class="form-label">Sample By</label>
                        <input type="text" class="form-control" name="SampleBy" id="SampleBy" value="<?php echo ($Search['Sample_By']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="Depth From" class="form-label">Depth From</label>
                        <input type="text" class="form-control" name="DepthFrom" id="Depth From" value="<?php echo ($Search['Depth_From']); ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="Depth To" class="form-label">Depth To</label>
                        <input type="text" class="form-control" name="DepthTo" id="Depth To" value="<?php echo ($Search['Depth_To']); ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="North" class="form-label">North</label>
                        <input type="text" class="form-control" name="North" id="North" value="<?php echo ($Search['North']); ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="East" class="form-label">East</label>
                        <input type="text" class="form-control" name="East" id="East" value="<?php echo ($Search['East']); ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="Elevation" class="form-label">Elevation</label>
                        <input type="text" class="form-control" name="Elev" id="Elevation" value="<?php echo ($Search['Elev']); ?>">
                    </div>
                    <div class="col-md-12">
                        <label for="FieldComment" class="form-label">Field Comment</label>
                        <input type="text" class="form-control" name="FieldComment" id="FieldComment" value="<?php echo ($Search['FieldComment']); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Sample Information -->