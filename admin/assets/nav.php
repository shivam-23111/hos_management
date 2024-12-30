<div class="sidebar">
        <button class="btn btn-dark btn-sm" id="toggleSidebar">
            <i class="fas fa-bars"></i>
        </button>
        <div class="nav flex-column">
            <a class="nav-link" href="add_admin.php">
                <i class="fas fa-user-plus"></i> <span class="nav-text">Add New Admin</span>
            </a>
            <a class="nav-link" href="#doctorsSection" data-toggle="collapse">
                <i class="fas fa-user-md"></i> <span class="nav-text">Doctors</span>
            </a>
            <div id="doctorsSection" class="collapse">
                <a class="nav-link" href="add_doctor.php">
                    <i class="fas fa-user-plus"></i> <span class="nav-text">Add Doctor</span>
                </a>
                <a class="nav-link" href="view_doctors.php">
                    <i class="fas fa-list"></i> <span class="nav-text">View Doctors</span>
                </a>
                <a class="nav-link" href="manage_doctors.php">
                    <i class="fas fa-cogs"></i> <span class="nav-text">Manage Doctors</span>
                </a>
            </div>
            <a class="nav-link" href="#patientsSection" data-toggle="collapse">
                <i class="fas fa-procedures"></i> <span class="nav-text">Patients</span>
            </a>
            <div id="patientsSection" class="collapse">
                <a class="nav-link" href="new_patient.php">
                    <i class="fas fa-user-plus"></i> <span class="nav-text">New Patient</span>
                </a>
                <a class="nav-link" href="manage_patients.php">
                    <i class="fas fa-edit"></i> <span class="nav-text">Manage Patient</span>
                </a>
                <a class="nav-link" href="view_patients.php">
                    <i class="fas fa-list"></i> <span class="nav-text">View Patient</span>
                </a>
            </div>
            <a class="nav-link" href="opd.php">
                <i class="fas fa-stethoscope"></i> <span class="nav-text">OPD</span>
            </a>
            <a class="nav-link" href="#bedsSection" data-toggle="collapse">
    <i class="fas fa-bed"></i> <span class="nav-text">Beds</span>
</a>
<div id="bedsSection" class="collapse">
    <a class="nav-link" href="view_beds_status.php">
        <i class="fas fa-eye"></i> <span class="nav-text">View Beds Status</span>
    </a>
    <a class="nav-link" href="manage_beds.php">
        <i class="fas fa-cogs"></i> <span class="nav-text">Manage Beds</span>
    </a>
</div>

            <a class="nav-link" href="#inventorySection" data-toggle="collapse">
                <i class="fas fa-warehouse"></i> <span class="nav-text">Inventory</span>
            </a>
            <div id="inventorySection" class="collapse">
                <a class="nav-link" href="medicines.php">
                    <i class="fas fa-capsules"></i> <span class="nav-text">Medicines</span>
                </a>
                <a class="nav-link" href="consumables.php">
                    <i class="fas fa-clipboard-list"></i> <span class="nav-text">Consumables</span>
                </a>
                <a class="nav-link" href="equipment.php">
                    <i class="fas fa-tools"></i> <span class="nav-text">Equipment</span>
                </a>
            </div>
        </div>
    </div>