const registerServiceWorker = async() => {
	const swRegistration = await navigator.serviceWorker.register(`${baseUrl}sw.js`);
	return swRegistration;
}

const main = async() => {
	const swRegistration = await registerServiceWorker();
}

if ('serviceWorker' in navigator) {
	main();
}