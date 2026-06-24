<?php
require_once 'php/includes/admin_auth.php';

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $officeName = trim($_POST['office_name'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $operatingHours = trim($_POST['operating_hours'] ?? '');
    $services = trim($_POST['services'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $facebookUrl = trim($_POST['facebook_url'] ?? '');
    $messengerUrl = trim($_POST['messenger_url'] ?? '');

    if ($officeName && $location && $operatingHours && $services && $email && $phone) {
        try {
            $stmt = $pdo->prepare("
                UPDATE office_info
                SET office_name = ?, location = ?, operating_hours = ?, services = ?, email = ?, phone = ?, facebook_url = ?, messenger_url = ?
                WHERE id = 1
            ");
            $stmt->execute([$officeName, $location, $operatingHours, $services, $email, $phone, $facebookUrl, $messengerUrl]);
            $successMsg = "Information board updated.";
        } catch (\PDOException $e) {
            $errorMsg = "Unable to update information board.";
        }
    } else {
        $errorMsg = "Please complete all required office information.";
    }
}

$info = [
    'office_name' => 'TUPV Accounting Office',
    'location' => 'Admin Building, Ground Floor, Room 101, TUP Visayas, Capt. Sabi St., Brgy. Zone 12, Talisay City.',
    'operating_hours' => 'Monday to Friday, 8:00 AM to 5:00 PM. Closed during weekends and declared holidays.',
    'services' => 'Fee assessment, payment confirmation, student receipts, summer class payments, and university materials.',
    'email' => 'accountingtupv@gmail.com',
    'phone' => '(034) 445 2177',
    'facebook_url' => 'https://www.facebook.com/tupvbusinessoffice',
    'messenger_url' => 'https://www.messenger.com/tupvbusinessoffice/',
];

try {
    $row = $pdo->query("SELECT * FROM office_info WHERE id = 1")->fetch();
    if ($row) {
        $info = array_merge($info, $row);
    }
} catch (\PDOException $e) {
    $errorMsg = $errorMsg ?: "Unable to load information board settings.";
}

require_once 'php/includes/admin_header.php';
?>

<div class="page-header">
    <div>
        <span class="eyebrow admin-eyebrow"><i class="ph ph-info"></i> Public board</span>
        <h1>Edit Information Board</h1>
        <p class="text-muted">Update public office details shown to students and visitors.</p>
    </div>
    <a href="info.php" class="btn btn-outline"><i class="ph ph-arrow-square-out"></i> Preview Public Page</a>
</div>

<?php if ($successMsg): ?><div class="alert alert-success"><i class="ph-fill ph-check-circle"></i> <?php echo htmlspecialchars($successMsg); ?></div><?php endif; ?>
<?php if ($errorMsg): ?><div class="alert alert-danger"><i class="ph-fill ph-warning-circle"></i> <?php echo htmlspecialchars($errorMsg); ?></div><?php endif; ?>

<div class="admin-two-column">
    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 style="font-size: 1.05rem;">Office Details</h2>
                <p class="text-muted" style="font-size: 0.86rem;">These values power the student information board.</p>
            </div>
        </div>
        <div class="panel-body">
            <form action="admin_info.php" method="POST" class="admin-info-form">
                <div class="form-group">
                    <label class="form-label" for="office_name">Office Name</label>
                    <input class="form-control" id="office_name" name="office_name" value="<?php echo htmlspecialchars($info['office_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="location">Location</label>
                    <textarea class="form-control" id="location" name="location" required><?php echo htmlspecialchars($info['location']); ?></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label" for="operating_hours">Operating Hours</label>
                    <input class="form-control" id="operating_hours" name="operating_hours" value="<?php echo htmlspecialchars($info['operating_hours']); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="services">Services</label>
                    <textarea class="form-control" id="services" name="services" required><?php echo htmlspecialchars($info['services']); ?></textarea>
                </div>
                <div class="admin-form-grid two">
                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($info['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone</label>
                        <input class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($info['phone']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="facebook_url">Facebook URL</label>
                        <input class="form-control" id="facebook_url" name="facebook_url" value="<?php echo htmlspecialchars($info['facebook_url']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="messenger_url">Messenger URL</label>
                        <input class="form-control" id="messenger_url" name="messenger_url" value="<?php echo htmlspecialchars($info['messenger_url']); ?>">
                    </div>
                </div>
                <button class="btn btn-primary admin-btn" type="submit"><i class="ph ph-floppy-disk"></i> Save Board Details</button>
            </form>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 style="font-size: 1.05rem;">Current Public Preview</h2>
                <p class="text-muted" style="font-size: 0.86rem;">A compact view of what visitors will see.</p>
            </div>
        </div>
        <div class="panel-body">
            <div class="info-card admin-preview-card">
                <i class="ph ph-buildings"></i>
                <h3><?php echo htmlspecialchars($info['office_name']); ?></h3>
                <p><?php echo htmlspecialchars($info['location']); ?></p>
            </div>
            <div class="info-card admin-preview-card">
                <i class="ph ph-clock"></i>
                <h3>Operating Hours</h3>
                <p><?php echo htmlspecialchars($info['operating_hours']); ?></p>
            </div>
            <div class="contact-list">
                <a href="mailto:<?php echo htmlspecialchars($info['email']); ?>"><i class="ph-fill ph-envelope-simple"></i> <?php echo htmlspecialchars($info['email']); ?></a>
                <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $info['phone']); ?>"><i class="ph-fill ph-phone"></i> <?php echo htmlspecialchars($info['phone']); ?></a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'php/includes/admin_footer.php'; ?>
