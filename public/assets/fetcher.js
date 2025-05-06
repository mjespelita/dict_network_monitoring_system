let lastFetchedData = null;  // Store last fetched data to compare and prevent unnecessary requests
let isRequestInProgress = false;  // Prevent concurrent requests
let retryCount = 0;  // Track how many retries we've made
const maxRetries = 5;  // Maximum number of retries for failed requests
let cachedDataVariable = 'usersData';
let load = 0;

// Utility function to compare two objects (shallow comparison for simplicity)
function isEqual(a, b) {
    return JSON.stringify(a) === JSON.stringify(b);
}

// Get the current connection type to adjust polling frequency (e.g., '4g', '3g', '2g', 'slow-2g')
function getConnectionType() {
    if (navigator.connection) {
        return navigator.connection.effectiveType;  // Returns connection type
    }
    return 'unknown';  // Fallback for browsers without Network Information API
}

// Function to fetch data from the server
function fetchData() {

    load++;

    if (isRequestInProgress) {
        console.log('Skipping fetch as another request is still in progress.');
        return;  // Prevent new fetch if a request is in progress
    }

    isRequestInProgress = true;  // Mark that a request is in progress

    fetch('/database-sync')  // Replace with your actual API endpoint
        .then(response => response.json())
        .then(data => {
            if (!isEqual(data, lastFetchedData)) {
                // console.log('Fetched new data:', data);
                lastFetchedData = data;  // Update last fetched data

                if (load != 1) {
                    // Check if the browser supports notifications
                    if ("Notification" in window) {
                        // Function to request notification permission
                        function requestNotificationPermission() {
                            Notification.requestPermission().then(function(permission) {
                                if (permission === "granted") {
                                    console.log("Notification permission granted.");
                                } else {
                                    console.log("Notification permission denied.");
                                }
                            });
                        }

                        // Add event listener to button
                        if (Notification.permission === "granted") {
                            // Show a notification if permission is granted
                            new Notification("Database Synced!", {
                                body: "The database has been successfully synchronized!",
                                icon: "assets/librify-logo.png",
                            });
                        } else {
                            // Ask for permission if not granted yet
                            requestNotificationPermission();
                        }
                    } else {
                        alert("Your browser does not support notifications.");
                    }
                    // end notification
                }

                localStorage.setItem(cachedDataVariable, JSON.stringify(data));  // Cache data in localStorage
            } else {
                // console.log('No new data to fetch.');
            }
            retryCount = 0;  // Reset retry count on success
        })
        .catch(error => {
            console.error('API error:', error);
            const cachedData = localStorage.getItem(cachedDataVariable);
            if (cachedData) {
                console.log('Using cached data:', JSON.parse(cachedData));  // Fallback to cached data
            } else {
                console.log('No cached data available.');
            }

            // Retry logic with exponential backoff if the request fails
            if (retryCount < maxRetries) {
                const delay = Math.pow(2, retryCount) * 1000;  // Exponential backoff (2^retryCount seconds)
                console.log(`Retrying in ${delay / 1000} seconds...`);
                setTimeout(fetchData, delay);  // Retry after delay
                retryCount++;
            } else {
                console.log('Max retries reached, giving up.');
            }
        })
        .finally(() => {
            isRequestInProgress = false;
            const pollingInterval = setPollingInterval();  // Get dynamic polling interval
            setTimeout(fetchData, pollingInterval);  // Poll again after the calculated interval
        });
}

// Function to dynamically adjust polling interval based on network type
function setPollingInterval() {
    const connectionType = getConnectionType();
    let pollingInterval;

    switch (connectionType) {
        case '4g':
            pollingInterval = 5000;  // 5 seconds for fast connections
            break;
        case '3g':
            pollingInterval = 10000;  // 10 seconds for moderate connections
            break;
        case '2g':
        case 'slow-2g':
            pollingInterval = 20000;  // 20 seconds for slow connections
            break;
        default:
            pollingInterval = 10000;  // Default interval for unknown connections
    }

    // console.log(`Polling interval set to ${pollingInterval / 1000} seconds based on connection type: ${connectionType}`);
    return pollingInterval;  // Return calculated polling interval
}

// Handle online/offline events
window.addEventListener('offline', () => {
    console.log('You are offline. Polling is paused.');
});

window.addEventListener('online', () => {
    console.log('You are back online. Fetching data...');
    fetchData();  // Try fetching data once the user is back online
});

// Start the polling process
fetchData();