function confirmLogout() {
    // Clear both user and cart localStorage before showing modal
    if (window.userStorage) {
        window.userStorage.clear();
    }
    if (window.cartStorage) {
        window.cartStorage.clear();
    }

    document.getElementById('logout-modal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeLogoutModal() {
    document.getElementById('logout-modal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('logout-modal').addEventListener('click', function (e) {
    if (e.target === this) {
        closeLogoutModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeLogoutModal();
    }
});