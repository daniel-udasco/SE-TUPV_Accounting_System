<?php
require_once 'php/includes/auth.php';
require_once 'php/db.php';
require_once 'php/includes/header.php';
?>

<div class="page-header">
    <div>
        <span class="eyebrow"><i class="ph ph-map-pin"></i> Locate Us</span>
        <h1>Visit the Accounting Office</h1>
        <p class="text-muted">Find our physical counter, coordinates, operating schedules, and campus photos below.</p>
    </div>
</div>

<div class="form-shell" style="grid-template-columns: minmax(0, 1.2fr) 350px;">
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Embedded Google Map -->
        <div class="panel" style="padding: 0.5rem; overflow: hidden;">
            <iframe src="https://maps.google.com/maps?q=10.739997919736169,122.97016491613583&t=&z=17&ie=UTF8&iwloc=&output=embed" 
                    width="100%" height="450" style="border: 0; border-radius: var(--radius-lg);" 
                    allowfullscreen="" loading="lazy"></iframe>
        </div>
        
        <!-- Sliding Gallery -->
        <div>
            <span class="eyebrow"><i class="ph ph-images"></i> Campus & Office Gallery</span>
            <style>
                .slider-wrapper {
                    width: 1000% !important;
                    animation: slideShowTen 40s infinite !important;
                }
                .slide {
                    width: 10% !important;
                }
                @keyframes slideShowTen {
                    0%, 8% { transform: translateX(0); }
                    10%, 18% { transform: translateX(-10%); }
                    20%, 28% { transform: translateX(-20%); }
                    30%, 38% { transform: translateX(-30%); }
                    40%, 48% { transform: translateX(-40%); }
                    50%, 58% { transform: translateX(-50%); }
                    60%, 68% { transform: translateX(-60%); }
                    70%, 78% { transform: translateX(-70%); }
                    80%, 88% { transform: translateX(-80%); }
                    90%, 98% { transform: translateX(-90%); }
                    100% { transform: translateX(0); }
                }
            </style>
            <div class="slider-container" style="margin-top: 0.5rem;">
                <div class="slider-wrapper">
                    <div class="slide"><img src="assets/visit1.jpg" alt="Campus Entrance & Guardhouse"></div>
                    <div class="slide"><img src="assets/visit2.jpg" alt="TUPV Walkway & Gardens"></div>
                    <div class="slide"><img src="assets/visit3.jpg" alt="Admin Building Lobby"></div>
                    <div class="slide"><img src="assets/visit4.jpg" alt="Accounting Office Exterior"></div>
                    <div class="slide"><img src="assets/visit5.jpg" alt="Accounting Counter Detail"></div>
                    <div class="slide"><img src="assets/visit6.jpg" alt="Campus Courtyard & Benches"></div>
                    <div class="slide"><img src="assets/visit7.jpg" alt="Student Lounge & Discussion Area"></div>
                    <div class="slide"><img src="assets/visit8.jpg" alt="Library Entrance & Study Hall"></div>
                    <div class="slide"><img src="assets/visit9.jpg" alt="Main Academic Building Corridor"></div>
                    <div class="slide"><img src="assets/visit9a.jpg" alt="Campus Sports Field & Activity Center"></div>
                </div>
            </div>
        </div>
    </div>
    
    <aside style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Operating Hours Card -->
        <div class="panel">
            <div class="panel-header">
                <h3><i class="ph ph-clock"></i> Operating Hours</h3>
            </div>
            <div class="panel-body" style="display: flex; flex-direction: column; gap: 1rem;">
                <div class="schedule-row">
                    <span>Monday - Friday</span>
                    <strong>8:00 AM - 5:00 PM</strong>
                </div>
                <div class="schedule-row">
                    <span>Noon Break</span>
                    <strong style="color: var(--green-700);">Open (Continuous Service)</strong>
                </div>
                <div class="schedule-row">
                    <span>Saturday - Sunday</span>
                    <strong style="color: var(--tup-maroon);">Closed</strong>
                </div>
                <div class="schedule-row">
                    <span>Declared Holidays</span>
                    <strong style="color: var(--tup-maroon);">Closed</strong>
                </div>
                
                <hr style="border: 0; border-top: 1px solid var(--line); margin: 0.5rem 0;">
                
                <div>
                    <h4 style="font-size: 0.88rem; margin-bottom: 0.25rem;"><i class="ph ph-info" style="color: var(--tup-maroon);"></i> Peak Hours Notice</h4>
                    <p style="font-size: 0.8rem; line-height: 1.4;">Expect longer queues during enrollment weeks and mid-term / final examination periods. Settle payments early through the portal to skip physical lines!</p>
                </div>
            </div>
        </div>
        
    </aside>
</div>

<?php
require_once 'php/includes/footer.php';
?>
