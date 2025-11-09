// Toggle Sidebar
const toggleBtn = document.getElementById('toggleBtn');
const sidebar = document.getElementById('sidebar');

toggleBtn.addEventListener('click', function() {
    sidebar.classList.toggle('collapsed');
});

// Menu Navigation (Sidebar)
const menuItems = document.querySelectorAll('.menu-item');

menuItems.forEach(item => {
    item.addEventListener('click', function() {
        // Hapus class active dari semua menu
        menuItems.forEach(menu => menu.classList.remove('active'));
        // Tambahkan class active ke menu yang diklik
        this.classList.add('active');
        // Tidak ada e.preventDefault(), jadi link akan langsung ke halaman
    });
});

// Search Functionality
const searchInput = document.getElementById('searchInput');

searchInput.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    console.log('Searching for:', searchTerm);
    
    // Add your search logic here
    // For example, filter table rows
    filterTable(searchTerm);
});

function filterTable(searchTerm) {
    const table = document.getElementById('ordersTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    Array.from(rows).forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Notification Button
const notificationBtn = document.getElementById('notificationBtn');

notificationBtn.addEventListener('click', function() {
    alert('You have 5 new notifications!');
    // You can replace this with a custom notification panel
});

// User Profile
const userProfile = document.getElementById('userProfile');

userProfile.addEventListener('click', function() {
    console.log('User profile clicked');
    // Add dropdown menu or redirect to profile page
});

// Action Buttons in Table
const actionButtons = document.querySelectorAll('.btn-action');

actionButtons.forEach(button => {
    button.addEventListener('click', function() {
        const row = this.closest('tr');
        const orderId = row.cells[0].textContent;
        console.log('View details for order:', orderId);
        alert('Viewing details for order: ' + orderId);
        // Add your logic to show order details
    });
});

// Animate stats on page load
window.addEventListener('load', function() {
    animateStats();
});

function animateStats() {
    const statValues = document.querySelectorAll('.stat-info .value');
    
    statValues.forEach(stat => {
        const finalValue = stat.textContent;
        const isNumber = !finalValue.includes('$');
        
        if (isNumber) {
            const target = parseInt(finalValue.replace(/,/g, ''));
            animateValue(stat, 0, target, 1000);
        }
    });
}

function animateValue(element, start, end, duration) {
    let startTimestamp = null;
    
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const value = Math.floor(progress * (end - start) + start);
        element.textContent = value.toLocaleString();
        
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    
    window.requestAnimationFrame(step);
}

// Responsive sidebar for mobile
if (window.innerWidth <= 768) {
    sidebar.classList.add('collapsed');
}

window.addEventListener('resize', function() {
    if (window.innerWidth <= 768) {
        sidebar.classList.add('collapsed');
    }
});

// View All Button
const viewAllBtn = document.querySelector('.btn-primary');

if (viewAllBtn) {
    viewAllBtn.addEventListener('click', function() {
        console.log('View all orders clicked');
        alert('Redirecting to all orders page...');
        // Add navigation logic here
    });
}

// Add smooth scrolling
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});

// Update notification badge dynamically
function updateNotificationBadge(count) {
    const badge = document.querySelector('.notification-badge');
    if (badge) {
        badge.textContent = count;
        if (count === 0) {
            badge.style.display = 'none';
        } else {
            badge.style.display = 'flex';
        }
    }
}

// Example: Update stats periodically
setInterval(function() {
    // Simulate real-time data update
    const activeNowValue = document.querySelectorAll('.stat-info .value')[3];
    if (activeNowValue) {
        const currentValue = parseInt(activeNowValue.textContent);
        const newValue = currentValue + Math.floor(Math.random() * 10) - 5;
        activeNowValue.textContent = Math.max(0, newValue);
    }
}, 5000);

console.log('Dashboard loaded successfully!');



document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();
        if (username === '' || password === '') {
            e.preventDefault();
            alert('Username dan password harus diisi!');
        }
    });
});
