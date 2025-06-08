// ========================================
// USER LOCALSTORAGE MANAGEMENT
// ========================================

/**
 * Store user data in localStorage when user logs in
 * @param {Object} userData - User object with id, name, surnames, email, role
 */
function storeUserData(userData) {
    try {
        localStorage.setItem('mobilestore_user', JSON.stringify({
            id: userData.id,
            name: userData.name,
            surnames: userData.surnames,
            email: userData.email,
            role: userData.role,
            loginTime: new Date().toISOString()
        }));
        console.log('User data stored in localStorage:', userData.name);
    } catch (error) {
        console.error('Error storing user data:', error);
    }
}

/**
 * Get user data from localStorage
 * @returns {Object|null} User data or null if not found
 */
function getUserData() {
    try {
        const userData = localStorage.getItem('mobilestore_user');
        return userData ? JSON.parse(userData) : null;
    } catch (error) {
        console.error('Error getting user data:', error);
        return null;
    }
}

/**
 * Clear user data from localStorage when user logs out
 */
function clearUserData() {
    try {
        localStorage.removeItem('mobilestore_user');
        console.log('User data cleared from localStorage');
    } catch (error) {
        console.error('Error clearing user data:', error);
    }
}

/**
 * Check if user is logged in (both session and localStorage)
 * @returns {boolean} True if user is logged in
 */
function isUserLoggedIn() {
    const userData = getUserData();
    return userData !== null && userData.id;
}

/**
 * Display current user info in console (for debugging)
 */
function showUserInfo() {
    const user = getUserData();
    if (user) {
        console.table({
            'ID': user.id,
            'Nombre': user.name,
            'Apellidos': user.surnames,
            'Email': user.email,
            'Rol': user.role,
            'Login Time': user.loginTime
        });
    } else {
        console.log('No user data found in localStorage');
    }
}

// Make functions available globally
window.userStorage = {
    store: storeUserData,
    get: getUserData,
    clear: clearUserData,
    isLoggedIn: isUserLoggedIn,
    showInfo: showUserInfo
};