let longitude = null, latitude = null;
const QrManager = {
    latitude: 0,
    longitude: 0,
    location: null,
    init() {
        this.checkLocation();
    },
    async checkLocation() {
        if (!('geolocation' in navigator)) {
            return;
        }
        const storedLocation = localStorage.getItem('userLocation');
        if (storedLocation) {
            try {
                const locationData = JSON.parse(storedLocation);
                let proceed = false;
                if(typeof locationData._lastUpdated !== 'undefined') {
                    let lastChecked = new Date().getTime() - locationData._lastUpdated;
                    let lastCheckedSeconds = Math.floor(lastChecked / 1000);
                    if(lastCheckedSeconds < 900) {
                        proceed = true;
                    }
                }
                if ((locationData.latitude && locationData.longitude) && proceed) {
                    this.longitude = locationData.longitude;
                    this.latitude = locationData.latitude;
                    return;
                }
            } catch (e) {}
        }
        if ('permissions' in navigator) {
            try {
                const permission = await navigator.permissions.query({ name: 'geolocation' });
                if (permission.state === 'denied') {
                    return;
                }
                if (permission.state === 'granted') {
                    await this.getCurrentLocation();
                    return;
                }
            } catch (e) {}
        }
        await this.getCurrentLocation();
    },
    async getCurrentLocation() {
        try {
            const position = await new Promise((resolve, reject) => {
                const options = {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 300000
                };
                navigator.geolocation.getCurrentPosition(resolve, reject, options);
            });

            if (position && position.coords) {
                this.location = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    _lastUpdated: new Date().getTime()
                };
                localStorage.setItem('userLocation', JSON.stringify(this.location));
                this.longitude = this.location.longitude;
                this.latitude = this.location.latitude;
            }
        } catch (error) { }
    },
    async refreshLocation() {
        localStorage.removeItem('userLocation');
        await this.getCurrentLocation();
    }
};
// Initialize the app
document.addEventListener('DOMContentLoaded', () => {
    QrManager.init();
});