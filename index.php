<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connect.php';

$featuredEvents = [];
try {
    $stmt = $pdo->query('SELECT * FROM events ORDER BY event_date ASC, event_time ASC LIMIT 6');
    $featuredEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $featuredEvents = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLIATE Event Management</title>
    <link rel="stylesheet" href="CSS/global.css">
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <main class="container" style="padding: 40px 0;">
        <section class="hero"
            style="background-color: #2b0e5e; padding: 80px 20px; border-radius: 24px; margin-bottom: 40px; text-align: center; color: white;">
            <h1 style="font-size: 3rem; margin-bottom: 16px;">Welcome to SLIATE Event Management</h1>
            <p style="font-size: 1.1rem; max-width: 760px; margin: 0 auto 24px; color: #dcd2ff;">Browse campus events,
                manage event listings, and stay connected with the latest student activities.</p>
            <a class="btn btn-primary" href="event.php">Browse Events</a>
        </section>

        <section style="margin-bottom: 40px;">
            <div class="section-title">Featured Events</div>
            <?php if (empty($featuredEvents)): ?>
                <p style="text-align:center; margin-top: 24px; color: #d3c2ef;">No events are available right now. Check
                    back later or login to create one.</p>
            <?php else: ?>
                <div class="events-grid" style="margin-top: 24px;">
                    <?php foreach ($featuredEvents as $event): ?>
                        <article class="card event-card" style="padding: 20px;">
                            <h2 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h2>
                            <p style="margin-bottom: 12px; color: #d3c2ef;">
                                <?php echo htmlspecialchars(substr($event['description'], 0, 140)); ?>
                                <?php echo strlen($event['description']) > 140 ? '...' : ''; ?>
                            </p>
                            <div class="event-info"
                                style="display:flex; gap: 16px; flex-wrap: wrap; color:#b9a4df; margin-bottom: 16px;">
                                <div><strong>Date:</strong> <?php echo htmlspecialchars($event['event_date']); ?></div>
                                <div><strong>Time:</strong> <?php echo htmlspecialchars($event['event_time']); ?></div>
                                <div><strong>Category:</strong> <?php echo htmlspecialchars($event['category']); ?></div>
                            </div>
                            <div style="display:flex; gap: 12px; flex-wrap: wrap;">
                                <a class="btn btn-secondary"
                                    href="event_details.php?id=<?php echo htmlspecialchars($event['id']); ?>">View Details</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                <div style="text-align: center; margin-top: 32px;">
                    <a class="btn btn-primary" href="event.php">Browse All Events</a>
                </div>
            <?php endif; ?>
        </section>

        <section class="about" style="background: rgba(255,255,255,0.04); padding: 40px; border-radius: 24px;">
            <h2 class="section-title">Why Use This Portal?</h2>
            <p style="margin: 0 auto; color: #d3c2ef; line-height: 1.8;">SLIATE Event Management makes
                it easy for students, organizers, and administrators to share important events, manage participation,
                and keep campus activity information centralized. Create events, update details, and remove old listings
                in one place.</p>
        </section>
        <section id="about" class="about section section--events">
            <h2 class="section-title">About Us</h2>
            <div class="about-content">
                <div class="about-text">
                    <h3>Excellence in Technological Education</h3>
                    <p>The Sri Lanka Institute of Advanced Technological Education (SLIATE) is a premier institution
                        dedicated to providing advanced technological education to meet the growing demands of the
                        industry and business sectors.</p>
                    <p>Established under the Sri Lanka Institute of Advanced Technological Education Act No. 29 of
                        1995, we have been at the forefront of technological education in Sri Lanka for over two
                        decades, offering Higher National Diplomas and other advanced technological courses.</p>
                    <p>Our mission is to produce globally competitive, technically proficient professionals equipped
                        with both theoretical knowledge and practical skills necessary to excel in their chosen
                        fields.</p>
                </div>
                <div class="about-image">
                    <img src="https://images.unsplash.com/photo-1562774053-701939374585?w=800&q=80"
                        alt="University Campus">
                </div>
            </div>
        </section>

        <section id="programs" class="programs section section--events">
            <h2 class="section-title">Our Courses</h2>
            <div class="program-cards">
                <div class="program-card">
                    <img src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=600&q=80" alt="English">
                    <div class="program-details">
                        <h3>Higher National Diploma in English</h3>
                        <p>A comprehensive program designed to enhance proficiency in English language and
                            literature, focusing on communication skills, linguistics, and critical analysis. This
                            program prepares students for careers in teaching, media, and other professional fields
                            requiring advanced English skills.</p>
                    </div>
                </div>
                <div class="program-card">
                    <img src="https://images.unsplash.com/photo-1518770660439-4636190af475?w=600&q=80"
                        alt="Information Technology">
                    <div class="program-details">
                        <h3>Higher National Diploma in IT</h3>
                        <p>This program equips students with advanced knowledge in software development, networking,
                            database management, and cybersecurity. It focuses on practical skills and theoretical
                            foundations to prepare students for careers in IT-related fields such as software
                            engineering, system administration, and IT consultancy.</p>
                    </div>
                </div>
                <div class="program-card">
                    <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=600&q=80" alt="Accountancy">
                    <div class="program-details">
                        <h3>Higher National Diploma in Accountancy</h3>
                        <p>This program provides a strong foundation in financial accounting, management accounting,
                            taxation, and auditing. It prepares students for careers in accounting, finance, and
                            business management, with a focus on analytical and problem-solving skills essential for
                            the corporate world.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="students" class="programs section section--events ">
            <h2 class="section-title">Student Life</h2>
            <div class="program-cards">
                <div class="program-card">
                    <div class="program-details">
                        <h3>Student Clubs & Societies</h3>
                        <p>Explore our diverse range of student clubs focused on technology, culture, sports, and
                            community service.</p>
                    </div>
                </div>
                <div class="program-card">
                    <div class="program-details">
                        <h3>Campus Facilities</h3>
                        <p>State-of-the-art laboratories, library resources, sports facilities, and recreational
                            areas.</p>
                    </div>
                </div>
                <div class="program-card">
                    <div class="program-details">
                        <h3>Career Development</h3>
                        <p>Career guidance, internship opportunities, job placement assistance, and industry
                            connections.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="contact" class="contact section section--events">
            <h2 class="section-title">Contact Us</h2>
            <div class="contact-container">
                <div class="contact-info">
                    <h3>Get in Touch</h3>
                    <div class="contact-item">
                        <strong>Address:</strong>&nbsp;
                        <div>Akkara 111, Anula MW, Pandulagama, Anuradhapura, Sri Lanka</div>
                    </div>
                    <div class="contact-item">
                        <strong>Phone:</strong>&nbsp;
                        <div>025-2234417</div>
                    </div>
                    <div class="contact-item">
                        <strong>Office Hours:</strong>&nbsp;
                        <div>Monday - Friday: 8:30 AM - 4:30 PM</div>
                    </div>
                    <iframe class="map-frame"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3958.257692477762!2d80.3652263!3d8.3253079!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3afcf5d50f19537d%3A0x3d135827470d27bc!2sATI%20Anuradhapura!5e0!3m2!1sen!2slk!4v1680000000000!5m2!1sen!2slk"
                        allowfullscreen="" loading="lazy">
                    </iframe>
                </div>
                <div class="contact-form">
                    <h3>Send us a Message</h3>
                    <form onsubmit="return false;">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" name="subject" required>
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" required></textarea>
                        </div>
                        <button type="submit" class="btn">Send Message</button>
                    </form>
                </div>
            </div>
        </section>

    </main>
</body>

</html>



<!-- <footer class="footer">
  <p>&copy; 2025 Sri Lanka Institute of Advanced Technological Education. All Rights Reserved.</p>
  <p>Akkara 111,Anula MW,Pandulagama,Anuradhapura, Sri Lanka</p>
  <div class="social-links">
      <a href="https://www.facebook.com/share/1BfCjEYSdK/" target="_blank" aria-label="Facebook">
          <img src="images/fb.jpg" alt="Facebook" style="width: 24px; height: 24px;">
      </a>
      <a href="https://youtube.com/@atianuradhapura?si=FaXm5PbIKHmXVxMr" target="_blank" aria-label="YouTube">
          <img src="images/yt.jpg" alt="YouTube" style="width: 24px; height: 24px;">
      </a>
  </div>
</footer> -->

<!-- <script>
  // Smooth scrolling for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
          e.preventDefault();
          const target = document.querySelector(this.getAttribute('href'));
          if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
          }
          
          // Update active nav link
          document.querySelectorAll('.nav-links a').forEach(link => {
              link.classList.remove('active');
          });
          this.classList.add('active');

          // Close mobile menu after clicking a link
          document.getElementById('navLinks').classList.remove('open');
      });
  });

  // Hamburger menu toggle
  const hamburger = document.getElementById('hamburger');
  const navLinks = document.getElementById('navLinks');

  if (hamburger && navLinks) {
    hamburger.addEventListener('click', () => {
      navLinks.classList.toggle('open');
    });
  }

  // Update active nav link on scroll
  window.addEventListener('scroll', function() {
      let scrollPosition = window.scrollY;
      
      document.querySelectorAll('section').forEach(section => {
          const sectionTop = section.offsetTop - 100;
          const sectionBottom = sectionTop + section.offsetHeight;
          
          if (scrollPosition >= sectionTop && scrollPosition < sectionBottom) {
              document.querySelectorAll('.nav-links a').forEach(link => {
                  link.classList.remove('active');
                  if (link.getAttribute('href') === '#' + section.getAttribute('id')) {
                      link.classList.add('active');
                  }
              });
          }
      });
  });
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    function updateDashboardCounter() {
        const addedEvents = JSON.parse(localStorage.getItem('addedEvents')) || [];
        const counters = document.querySelectorAll('.dashboard-counter');
        counters.forEach(counter => {
            if (addedEvents.length > 0) {
                counter.textContent = addedEvents.length;
                counter.style.display = 'inline-block';
            } else {
                counter.style.display = 'none';
            }
        });
    }
    updateDashboardCounter();
    window.addEventListener('storage', function(e) {
        if (e.key === 'addedEvents') {
            updateDashboardCounter();
        }
    });
});
</script> -->